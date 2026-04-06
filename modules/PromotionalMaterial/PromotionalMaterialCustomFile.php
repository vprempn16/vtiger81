<?php 
class PromotionalMaterialCustomFile{
	private $moduleName = 'PromotionalMaterial';
	private $moduleInstance = null;
	private $adb = null;
	private $tabid = null;
	private $logFile = 'storage/promotional_material_install.log';

	public function __construct(){
		global $adb;
		$this->adb = $adb;
		$this->moduleInstance = Vtiger_Module::getInstance($this->moduleName);
		$this->tabid = $this->moduleInstance ? $this->moduleInstance->id : null;
	}
	function postInstall(){
		$this->registerModuleInApp();
		$this->configureModuleSettings();
		$this->initTables();
		$this->createTables();
		$this->addFilters();
		$this->updateEntityNames();
		$this->updateWsEntity();
		self::registerWorkflowEntityMethods();
	}	
	function postEnable(){
		self::registerWorkflowEntityMethods();
	}
	function postDisable(){
		global $adb;
		$this->unRegisterWorkflowEntityMethods();
	}
	function postUpdate(){
		$this->updateEntityNames();
		$this->updateWsEntity();
		self::registerWorkflowEntityMethods();
	}

	private function registerModuleInApp()
	{
		try {
			if (!$this->moduleInstance) {
				$this->log("Module instance not available");
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
		}
	}
	private function configureModuleSettings()
	{
		try {
			if (!$this->moduleInstance) {
				$this->log("Module instance not available");
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
		}
	}
	private function initTables()
	{
		try {
			if (!$this->moduleInstance) {
				$this->log("Module instance not available");
			}

			// Initialize tables using Vtiger's built-in method
			$this->moduleInstance->initTables();
			$this->log("  ✓ Database tables initialized successfully");
		} catch (Exception $e) {
			$this->log("  ✗ Error initializing tables: " . $e->getMessage());
		}
	}
	private function createTables(){
		$tables = array(
			// Main PromotionalMaterial table with all columns
			'vtiger_promotionalmaterial' => "    
			CREATE TABLE `vtiger_promotionalmaterial` (
				`promotionalmaterialid` int NOT NULL,
				`title` varchar(255) DEFAULT NULL,
				`description` longtext,
				`promotional_status` varchar(25) DEFAULT NULL,
				`promotional_document` varchar(500) DEFAULT NULL,
				`tags` varchar(255) DEFAULT NULL,
				PRIMARY KEY (`promotionalmaterialid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3	",
			// Custom fields table
			'vtiger_promotionalmaterialcf' => "
				CREATE TABLE `vtiger_promotionalmaterialcf` (
					`promotionalmaterialid` int NOT NULL,
					PRIMARY KEY (`promotionalmaterialid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3");

			foreach ($tables as $tableName => $sql) {
				try {
					$this->adb->query($sql);
					$this->log("  ✓ Table verified/created: $tableName");
				} catch (Exception $e) {
					$this->log("  ⚠ Table $tableName: " . $e->getMessage());
				}
			}
	}
	private function addFilters()
	{
		try {
			if (!$this->moduleInstance) {
				$this->log("Module instance not available");
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
		}
	}
	private function updateEntityNames()
	{
		try {
			if (!$this->tabid) {
				$this->log("Tab ID not available");
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
		}
	}
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
		}
	}
	/**
	 * Register workflow "Invoke custom function" methods (com_vtiger_workflowtasks_entitymethod).
	 */
	public static function registerWorkflowEntityMethods() {
		global $adb;
		$tbl = $adb->pquery("SHOW TABLES LIKE 'com_vtiger_workflowtasks_entitymethod'", array());
		if (!$tbl || $adb->num_rows($tbl) === 0) {
			return;
		}
		require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
		$emm = new VTEntityMethodManager($adb);
		$module = 'PromotionalMaterial';
		$path = 'modules/PromotionalMaterial/workflow/PromotionalMaterialWorkflowMethods.php';
		$map = array(
			'PMNotifyPartners' => 'pmwf_notifyPartnerUsers',
			'PMSendPartnerEmail' => 'pmwf_sendPartnerEmail',
		);
		foreach ($map as $methodName => $functionName) {
			$emm->removeEntityMethod($module, $methodName);
			$emm->addEntityMethod($module, $methodName, $path, $functionName);
		}
	}
	public static function unRegisterWorkflowEntityMethods(){
		global $adb;
		$tbl = $adb->pquery("SHOW TABLES LIKE 'com_vtiger_workflowtasks_entitymethod'", array());
		if (!$tbl || $adb->num_rows($tbl) === 0) {
			return;
		}
		require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
		$emm = new VTEntityMethodManager($adb);
		$module = 'PromotionalMaterial';
		$map = array(
			'PMNotifyPartners' => 'pmwf_notifyPartnerUsers',
			'PMSendPartnerEmail' => 'pmwf_sendPartnerEmail',
		);
		foreach ($map as $methodName => $functionName) {
			$emm->removeEntityMethod($module, $methodName);
		}
	}
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
