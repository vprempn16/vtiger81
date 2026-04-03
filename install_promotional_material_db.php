<?php
/**
 * PromotionalMaterial Module Installation Script
 * Class-based installer following Vtiger conventions
 * Handles table creation, module registration, block/field creation, and event handler setup
 */

// Set working directory
chdir(dirname(__FILE__));

// Load Vtiger configuration and required classes
require_once('config.inc.php');
require_once('includes/Loader.php');

// Import Vtiger classes for module creation
require_once('vtlib/Vtiger/Module.php');
require_once('vtlib/Vtiger/Block.php');
require_once('vtlib/Vtiger/Field.php');
require_once('vtlib/Vtiger/Filter.php');

// Installation class
class PromotionalMaterialInstaller
{
    private $moduleName = 'PromotionalMaterial';
    private $moduleInstance = null;
    private $adb = null;
    private $tabid = null;
    private $logFile = 'storage/promotional_material_install.log';

    public function __construct()
    {
        global $adb;
        $this->adb = $adb;
    }

    /**
     * Main installation orchestrator - called by installation script
     */
    public function install()
    {
        try {
            $this->log("=== Starting PromotionalMaterial Module Installation ===");
            
            // Step 1: Create module
            $this->log("[STEP 1] Creating module...");
            $this->createModule();
            
            // Step 2: Create block
            $this->log("[STEP 2] Creating module block...");
            $this->createBlock();
            
            // Step 2.5: Register module in app2tab (Tools)
            $this->log("[STEP 2.5] Registering module in Tools app...");
            
            $this->registerModuleInApp();

            // Step 2.6: Configure module settings
            $this->log("[STEP 2.6] Configuring module settings...");
            $this->configureModuleSettings();
          
            // Step 3: Initialize tables (creates base tables if not exists)

            $this->log("[STEP 4] Initializing database tables...");
            $this->initTables();
            
            // Step 4: Create fields
            $this->log("[STEP 3] Creating module fields...");
            $this->createFields();
            
          
            // Step 5: Create default entry in base table
            $this->log("[STEP 5] Creating database tables...");
            $this->createTables();
            
            // Step 6: Add filters
            $this->log("[STEP 6] Adding filters...");
            $this->addFilters();
            
            // Step 7: Register in vtiger_entityname
            $this->log("[STEP 7] Registering in vtiger_entityname...");
            $this->updateEntityNames();
            
            // Step 8: Register in vtiger_ws_entity
            $this->log("[STEP 8] Registering in vtiger_ws_entity...");
            $this->updateWsEntity();
            
            // Step 9: Register event handler
            $this->log("[STEP 9] Registering event handler...");
            $this->registerEventHandler();
            
            // Step 10: Configure picklist values
            $this->log("[STEP 10] Configuring picklist values...");
            //$this->createPicklists();
            
            // Step 11: Configure role permissions
            $this->log("[STEP 11] Configuring role permissions...");
            //$this->configureRolePermissions();
            
            $this->log("✓ PromotionalMaterial module installed successfully!");
            $this->log("  Module ID: " . ($this->moduleInstance ? $this->moduleInstance->id : 'N/A'));
            $this->log("  Tab ID: " . $this->tabid);
            $this->log("=== Installation Complete ===\n");
            
            return true;
        } catch (Exception $e) {
            $this->log("✗ ERROR: " . $e->getMessage());
            $this->log($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * STEP 4/5: Create database tables
     */
    private function createTables()
    {
        $tables = array(
            // Main PromotionalMaterial table with all columns
            'vtiger_promotionalmaterial' => "ALTER TABLE `vtiger_promotionalmaterial` ADD COLUMN IF NOT EXISTS (
                `promotional_document` VARCHAR(500),
                `source` VARCHAR(100),
                `tags` VARCHAR(255)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            // Documents attachment table
            'vtiger_promotionalmaterial_documents' => "CREATE TABLE IF NOT EXISTS `vtiger_promotionalmaterial_documents` (
                `documentid` INT NOT NULL AUTO_INCREMENT,
                `promotionalmaterialid` INT NOT NULL,
                `filename` VARCHAR(255) NOT NULL,
                `filepath` VARCHAR(500),
                `filetype` VARCHAR(50),
                `filesize` INT,
                `createdtime` DATETIME,
                PRIMARY KEY (`documentid`),
                INDEX `idx_promotionalmaterialid` (`promotionalmaterialid`),
                CONSTRAINT `fk_promotionalmaterial_documents` FOREIGN KEY (`promotionalmaterialid`) 
                    REFERENCES `vtiger_promotionalmaterial` (`promotionalmaterialid`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            // Custom fields table
            'vtiger_promotionalmaterialcf' => "CREATE TABLE IF NOT EXISTS `vtiger_promotionalmaterialcf` (
                `promotionalmaterialid` INT NOT NULL,
                PRIMARY KEY (`promotionalmaterialid`),
                CONSTRAINT `fk_promotionalmaterialcf` FOREIGN KEY (`promotionalmaterialid`) 
                    REFERENCES `vtiger_promotionalmaterial` (`promotionalmaterialid`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        foreach ($tables as $tableName => $sql) {
            try {
                $this->adb->query($sql);
                $this->log("  ✓ Table verified/created: $tableName");
            } catch (Exception $e) {
                $this->log("  ⚠ Table $tableName: " . $e->getMessage());
            }
        }
    }

    /**
     * STEP 2: Create module
     */
    private function createModule()
    {
        try {
            // Check if module already exists
            $this->moduleInstance = Vtiger_Module::getInstance($this->moduleName);
            
            if (!$this->moduleInstance) {
                // Create new module
                $this->moduleInstance = new Vtiger_Module();
                $this->moduleInstance->name = $this->moduleName;
                $this->moduleInstance->label = 'Promotional Material';
                $this->moduleInstance->parent = 'Tools';
                $this->moduleInstance->version = '1.0';
                $this->moduleInstance->presence = 0;
                $this->moduleInstance->category = 'internal';
                $this->moduleInstance->ownedby = 0;
                $this->moduleInstance->customized = 0;
                
                // Save module
                $this->moduleInstance->save();
                $this->tabid = $this->moduleInstance->id;
                $this->log("  ✓ Module created: {$this->moduleName} (ID: {$this->tabid})");
            } else {
                $this->tabid = $this->moduleInstance->id;
                $this->log("  ⚠ Module already exists: {$this->moduleName} (ID: {$this->tabid})");
            }
        } catch (Exception $e) {
            $this->log("  ✗ Error creating module: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 3: Create module block
     */
    private function createBlock()
    {
        try {
            if (!$this->moduleInstance) {
                throw new Exception("Module instance not available");
            }

            $blockLabel = 'LBL_PROMOTIONAL_MATERIAL_INFORMATION';
            $blockInstance = Vtiger_Block::getInstance($blockLabel, $this->moduleInstance);
            
            if (!$blockInstance) {
                $blockInstance = new Vtiger_Block();
                $blockInstance->label = $blockLabel;
                $this->moduleInstance->addBlock($blockInstance);
                $this->log("  ✓ Block created: $blockLabel");
            } else {
                $this->log("  ⚠ Block already exists: $blockLabel");
            }
        } catch (Exception $e) {
            $this->log("  ✗ Error creating block: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 2.5: Register module in vtiger_app2tab (Tools app)
     * Ensures the module is linked to its parent app (Tools)
     */
    private function registerModuleInApp()
    {
        try {
            if (!$this->moduleInstance) {
                throw new Exception("Module instance not available");
            }
            
             
            $maxSequenceResult = $this->adb->query("SELECT MAX(sequence) AS max_sequence FROM vtiger_app2tab WHERE appname = 'TOOLS'" );
            $maxSequence = $this->adb->query_result($maxSequenceResult, 0, 'max_sequence');
            // Check if app2tab entry already exists
            $checkResult = $this->adb->query(
                "SELECT * FROM vtiger_app2tab WHERE tabid = '" . addslashes($this->tabid) . "'"
            );
          
           
            if ($this->adb->num_rows($checkResult) == 0) {
                // Insert new entry into vtiger_app2tab
                $sequence = $maxSequence + 1;
                $this->adb->pquery(
                    "INSERT INTO vtiger_app2tab (tabid,appname,sequence,visible) VALUES (?, ?, ?, ?)",
                    array($this->tabid, 'TOOLS', $sequence, 1)
                );
                $this->log("  ✓ Registered module in Tools app (AppID: $toolsTabId, TabID: {$this->tabid})");
            } else {
                $this->log("  ⚠ Module already registered in Tools app");
            }
        } catch (Exception $e) {
            $this->log("  ✗ Error registering module in app: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 2.6: Configure module settings
     * Sets entity identifier and default sharing
     */
    private function configureModuleSettings()
    {
        try {
            if (!$this->moduleInstance) {
                throw new Exception("Module instance not available");
            }

            // Set the entity identifier field (the field that represents the record)
            $titleField = Vtiger_Field::getInstance('title', $this->moduleInstance);
            if ($titleField) {
                $this->moduleInstance->setEntityIdentifier($titleField);
                $this->log("  ✓ Entity identifier set to 'title'");
            }

            // Set default sharing (how records are shared by default)
            $this->moduleInstance->setDefaultSharing();
            $this->log("  ✓ Default sharing rules configured");

            // Save these settings to database
            $this->moduleInstance->save();
            $this->log("  ✓ Module settings saved");
        } catch (Exception $e) {
            $this->log("  ✗ Error configuring module settings: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 4: Initialize module tables
     * Calls the Vtiger framework's initTables to create base tables
     */
    private function initTables()
    {
        try {
            if (!$this->moduleInstance) {
                throw new Exception("Module instance not available");
            }

            // Initialize tables using Vtiger's built-in method
            $this->moduleInstance->initTables();
            $this->log("  ✓ Database tables initialized successfully");
        } catch (Exception $e) {
            $this->log("  ✗ Error initializing tables: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 6: Add filters for list view
     */
    private function addFilters()
    {
        try {
            if (!$this->moduleInstance) {
                throw new Exception("Module instance not available");
            }

            // Create default "All" filter
            $filter = new Vtiger_Filter();
            $filter->name = 'All';
            $filter->isdefault = true;
            $this->moduleInstance->addFilter($filter);

            $title = Vtiger_Field::getInstance('title', $this->moduleInstance);
            $promotional_status = Vtiger_Field::getInstance('promotional_status', $this->moduleInstance);
            $publisheddate = Vtiger_Field::getInstance('publisheddate', $this->moduleInstance);
            // Add fields to filter
            $filter->addField($title)->addField($promotional_status, 1)->addField($publisheddate, 2);
            
            $this->log("  ✓ Filter 'All' created with fields (title, status, publisheddate)");
        } catch (Exception $e) {
            $this->log("  ✗ Error creating filters: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 3: Create module fields
     */
    private function createFields()
    {
        try {
            if (!$this->moduleInstance) {
                throw new Exception("Module instance not available");
            }

            $blockInstance = Vtiger_Block::getInstance('LBL_PROMOTIONAL_MATERIAL_INFORMATION', $this->moduleInstance);
            
            if (!$blockInstance) {
                throw new Exception("Block instance not found");
            }

            // Defensive check: Ensure basetable is set
            if (empty($this->moduleInstance->basetable)) {
                $this->moduleInstance->basetable = 'vtiger_promotionalmaterial';
            }

            // Define fields to create
            $fields = array(
                'title' => array(
                    'label' => 'Title',
                    'uitype' => 1,
                    'typeofdata' => 'V~M',
                    'column' => 'title',
                    'columntype' => 'VARCHAR(255)',
                    'mandatory' => true
                ),
                'description' => array(
                    'label' => 'Description',
                    'uitype' => 21,
                    'column' => 'description',
                    'typeofdata' => 'V~O',
                    'columntype' => 'LONGTEXT',
                    'mandatory' => false
                ),
                'promotional_status' => array(
                    'label' => 'Status',
                    'uitype' => 16,
                    'typeofdata' => 'V~O',
                    'columntype' => 'VARCHAR(25)',
                    'column' => 'promotional_status',
                    'picklistvalues' => array('Draft', 'Published', 'Archived'),
                    'mandatory' => false
                ),
                'publisheddate' => array(
                    'label' => 'Published Date',
                    'uitype' => 5,
                    'typeofdata' => 'D~O',
                    'column' => 'publisheddate',
                    'columntype' => 'DATETIME',
                    'mandatory' => false
                ),
                'promotional_document' => array(
                    'label' => 'Document Upload',
                    'uitype' => 28,
                    'typeofdata' => 'V~O',
                    'column' => 'promotional_document',
                    'columntype' => 'VARCHAR(500)',
                    'mandatory' => false
                ),
                'assigned_user_id' => array(
                    'label' => 'Assigned To',
                    'uitype' => 53,
                    'typeofdata' => 'V~M',
                    'table' => 'vtiger_crmentity',
                    'column' => 'smownerid',
                    'mandatory' => true
                ),
                'createdtime' => array(
                    'label' => 'Created Time',
                    'uitype' => 70,
                    'typeofdata' => 'DT~O',
                    'table' => 'vtiger_crmentity',
                    'column' => 'createdtime',
                    'displaytype' => 2,
                    'mandatory' => false
                ),
                'modifiedtime' => array(
                    'label' => 'Modified Time',
                    'uitype' => 70,
                    'typeofdata' => 'DT~O',
                    'table' => 'vtiger_crmentity',
                    'column' => 'modifiedtime',
                    'displaytype' => 2,
                    'mandatory' => false
                ),
                'source' => array(
                    'label' => 'Source',
                    'uitype' => 16,
                    'typeofdata' => 'V~O',
                    'table' => 'vtiger_crmentity',
                    'column' => 'source',
                    'displaytype' => 2,
                    'mandatory' => false
                ),
                'starred' => array(
                    'label' => 'Starred',
                    'uitype' => 56,
                    'typeofdata' => 'C~O',
                    'table' => 'vtiger_crmentity_user_field',
                    'displaytype' => 6,
                    'mandatory' => false
                ),
                'tags' => array(
                    'label' => 'Tags',
                    'uitype' => 1,
                    'typeofdata' => 'V~O',
                    'column' => 'tags',
                    'columntype' => 'VARCHAR(255)',
                    'displaytype' => 6,
                    'mandatory' => false
                )
            );

            foreach ($fields as $fieldName => $fieldInfo) {
                try {
                    $fieldInstance = Vtiger_Field::getInstance($fieldName, $this->moduleInstance);
                    
                    if (!$fieldInstance) {
                        $fieldInstance = new Vtiger_Field();
                        $fieldInstance->name = $fieldName;
                        $fieldInstance->label = $fieldInfo['label'];
                        $fieldInstance->table = isset($fieldInfo['table']) ? $fieldInfo['table'] : $this->moduleInstance->basetable;
                        $fieldInstance->column = $fieldInfo['column'];
                        
                        // Set columntype only for custom fields (not system fields)
                        if (isset($fieldInfo['columntype']) && !isset($fieldInfo['table'])) {
                            $fieldInstance->columntype = $fieldInfo['columntype'];
                        }
                        
                        $fieldInstance->uitype = $fieldInfo['uitype'];
                        $fieldInstance->typeofdata = $fieldInfo['typeofdata'];
                        
                        // Set display type if specified (for system fields like createdtime, modifiedtime, source)
                        if (isset($fieldInfo['displaytype'])) {
                            $fieldInstance->displaytype = $fieldInfo['displaytype'];
                        }
                        
                        // Add field to block
                        $blockInstance->addField($fieldInstance);
                        $this->log("  ✓ Field created: $fieldName");

                        // Set picklist values if defined
                        if (isset($fieldInfo['picklistvalues'])) {
                            $fieldInstance->setPicklistValues($fieldInfo['picklistvalues']);
                            $this->log("    → Picklist values set for: $fieldName");
                        }
                    } else {
                        $this->log("  ⚠ Field already exists: $fieldName");
                    }
                } catch (Exception $e) {
                    $this->log("  ✗ Error creating field $fieldName: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            $this->log("  ✗ Error creating fields: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 7: Register module in vtiger_entityname
     */
    private function updateEntityNames()
    {
        try {
            if (!$this->tabid) {
                throw new Exception("Tab ID not available");
            }

            // Check if already registered
            $checkResult = $this->adb->query(
                "SELECT * FROM vtiger_entityname WHERE modulename = '" . addslashes($this->moduleName) . "'"
            );
            
            if ($this->adb->num_rows($checkResult) == 0) {
                // Insert new entry
                $this->adb->pquery(
                    "INSERT INTO vtiger_entityname (tabid, modulename, tablename, fieldname, entityidfield) 
                     VALUES (?, ?, ?, ?, ?)",
                    array(
                        $this->tabid,
                        $this->moduleName,
                        'vtiger_promotionalmaterial',
                        'title',
                        'promotionalmaterialid'
                    )
                );
                $this->log("  ✓ Registered in vtiger_entityname (TabID: {$this->tabid})");
            } else {
                $this->log("  ⚠ Already registered in vtiger_entityname");
            }
        } catch (Exception $e) {
            $this->log("  ✗ Error updating vtiger_entityname: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 8: Register module in vtiger_ws_entity
     */
    private function updateWsEntity()
    {
        try {
            // Check if already registered
            $checkResult = $this->adb->query(
		"SELECT * FROM vtiger_ws_entity WHERE name = 'PromotionalMaterial'"
            );
            
            if ($this->adb->num_rows($checkResult) == 0) {
                // Get unique ID
                $id = $this->adb->getUniqueID('vtiger_ws_entity');
                
                // Insert new entry
                $this->adb->pquery(
                    "INSERT INTO vtiger_ws_entity (id, name, handler_path, handler_class, ismodule) 
                     VALUES (?, ?, ?, ?, ?)",
                    array(
                        $id,
                        $this->moduleName,
                        'include/Webservices/VtigerModuleOperation.php',
                        'VtigerModuleOperation',
                        1
                    )
                );
                $this->log("  ✓ Registered in vtiger_ws_entity (ID: $id)");
            } else {
                $this->log("  ⚠ Already registered in vtiger_ws_entity");
            }
        } catch (Exception $e) {
            $this->log("  ✗ Error updating vtiger_ws_entity: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 9: Register event handler
     */
    private function registerEventHandler()
    {
        try {
            $eventName = 'vtiger.entity.aftersave.' . $this->moduleName;
            $handlerClass = $this->moduleName . 'EventHandler';
            
            // Check if already registered
            $checkResult = $this->adb->query(
                "SELECT * FROM vtiger_eventhandlers WHERE event_name = '" . addslashes($eventName) . "' 
                 AND handler_class = '" . addslashes($handlerClass) . "'"
            );
            
            if ($this->adb->num_rows($checkResult) == 0) {
                // Register new event handler
                $this->adb->pquery(
                    "INSERT INTO vtiger_eventhandlers (event_name, handler_path, handler_class) 
                     VALUES (?, ?, ?)",
                    array(
                        $eventName,
                        'modules/' . $this->moduleName . '/' . $handlerClass . '.php',
                        $handlerClass
                    )
                );
                $this->log("  ✓ Registered event handler: $eventName");
            } else {
                $this->log("  ⚠ Event handler already registered: $eventName");
            }
        } catch (Exception $e) {
            $this->log("  ✗ Error registering event handler: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 10: Create picklist values
     */
    private function createPicklists()
    {
        try {
            $picklistName = 'vtiger_promotionalmaterial_status';
            
            // Check if picklist exists
            $checkResult = $this->adb->query(
                "SELECT picklistid FROM vtiger_picklist WHERE name = '" . addslashes($picklistName) . "'"
            );
            
            if ($this->adb->num_rows($checkResult) == 0) {
                // Create picklist
                $this->adb->pquery(
                    "INSERT INTO vtiger_picklist (name) VALUES (?)",
                    array($picklistName)
                );
                
                // Get picklist ID
                $picklistId = $this->adb->getLastInsertID();
                
                // Add picklist values
                $statusValues = array('Draft', 'Published', 'Archived');
                $sequence = 1;
                
                foreach ($statusValues as $value) {
                    $this->adb->pquery(
                        "INSERT INTO vtiger_picklistvalues (picklistid, picklistvalue, sequence) 
                         VALUES (?, ?, ?)",
                        array($picklistId, $value, $sequence++)
                    );
                }
                
                $this->log("  ✓ Picklist created: $picklistName with values (Draft, Published, Archived)");
            } else {
                $this->log("  ⚠ Picklist already exists: $picklistName");
            }
        } catch (Exception $e) {
            $this->log("  ✗ Error creating picklist: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * STEP 11: Configure role permissions
     */
    private function configureRolePermissions()
    {
        try {
            if (!$this->tabid) {
                throw new Exception("Tab ID not available");
            }

            // Get all profiles
            $profileResult = $this->adb->query("SELECT * FROM vtiger_profile");
            $profiles = array();
            
            while ($profileRow = $profileResult->FetchRow()) {
                $profiles[] = $profileRow['profileid'];
            }

            // Add module to each profile
            foreach ($profiles as $profileid) {
                $checkResult = $this->adb->query(
                    "SELECT * FROM vtiger_profile2tab WHERE profileid = '" . addslashes($profileid) . "' 
                     AND tabid = '" . addslashes($this->tabid) . "'"
                );
                
                if ($this->adb->num_rows($checkResult) == 0) {
                    $this->adb->pquery(
                        "INSERT INTO vtiger_profile2tab (profileid, tabid, permissions) VALUES (?, ?, ?)",
                        array($profileid, $this->tabid, 0)
                    );
                }
            }
            
            $this->log("  ✓ Role permissions configured for all profiles");
        } catch (Exception $e) {
            $this->log("  ✗ Error configuring role permissions: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Logging helper
     */
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        
        // Write to file
        if (!is_dir('storage')) {
            mkdir('storage', 0755, true);
        }
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        
        // Also output to console/HTML
        echo $logMessage;
    }
}

// ===== EXECUTION =====
echo "<pre>";
echo "=== PromotionalMaterial Module Installation ===\n";
echo "Starting installation process...\n\n";

try {
    $installer = new PromotionalMaterialInstaller();
    $installer->install();
    
    echo "\n=== SUCCESS ===\n";
    echo "✓ PromotionalMaterial module has been successfully installed!\n";
    echo "✓ Next steps:\n";
    echo "  1. Clear cache: Admin > System > Maintenance > Clear Cache\n";
    echo "  2. Enable module: Admin > Modules & Packages > Module Manager\n";
    echo "  3. Configure Partner role permissions\n";
    echo "  4. Test by creating promotional materials\n";
    echo "\nLog file: storage/promotional_material_install.log\n";
} catch (Exception $e) {
    echo "\n=== INSTALLATION FAILED ===\n";
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nPlease check the log file: storage/promotional_material_install.log\n";
}

echo "</pre>";
?>
