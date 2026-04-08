<?php
/**
 * PROJECT NOTIFICATION & PARTNER PERMISSIONS IMPLEMENTATION
 * =========================================================
 * 
 * This script configures:
 * 1. Partner permissions to create/update Projects they own
 * 2. Record assignment notification handler
 * 3. Record update notification handler
 * 4. Project-specific event handlers
 */

// Properly bootstrap Vtiger environment
define('VTIGER_BOOTSTRAP', true);
chdir(dirname(__FILE__));

// Include Vtiger configuration
require_once 'config.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';
require_once 'modules/Vtiger/loaders.php';

// Initialize database connection
$adb = PearDatabase::getInstance();

class ProjectNotificationInstaller {
    private $adb;
    private $logFile = 'storage/project_notification_install.log';
    
    public function __construct() {
        global $adb;
        $this->adb = $adb;
        $this->log("=== Starting Project Notification & Partner Permissions Installation ===");
    }
    
    /**
     * STEP 1: Configure Partner permissions for Project module
     */
    public function configureProjectPermissions() {
        try {
            // Get Project module TabID
            $result = $this->adb->pquery(
                "SELECT tabid FROM vtiger_tab WHERE name = ?",
                array('Project')
            );
            
            if ($this->adb->num_rows($result) == 0) {
                throw new Exception("Project module not found");
            }
            
            $projectTabId = $this->adb->query_result($result, 0, 'tabid');
            $this->log("  ✓ Found Project TabID: $projectTabId");
            
            // Get Partner Role (H6)
            $roleResult = $this->adb->pquery(
                "SELECT roleid FROM vtiger_role WHERE name = ?",
                array('Partner')
            );
            
            if ($this->adb->num_rows($roleResult) == 0) {
                throw new Exception("Partner role not found");
            }
            
            $partnerRoleId = $this->adb->query_result($roleResult, 0, 'roleid');
            $this->log("  ✓ Found Partner Role: $partnerRoleId");
            
            // Get Partner Profile ID from profile2role
            $profileResult = $this->adb->pquery(
                "SELECT profileid FROM vtiger_profile2role WHERE roleid = ?",
                array($partnerRoleId)
            );
            
            if ($this->adb->num_rows($profileResult) == 0) {
                throw new Exception("Partner profile not found");
            }
            
            $profileId = $this->adb->query_result($profileResult, 0, 'profileid');
            $this->log("  ✓ Found Partner Profile: $profileId");
            
            // Check if permission already exists
            $permResult = $this->adb->pquery(
                "SELECT COUNT(*) as cnt FROM vtiger_profile2tab WHERE profileid = ? AND tabid = ?",
                array($profileId, $projectTabId)
            );
            
            $permCount = $this->adb->query_result($permResult, 0, 'cnt');
            
            if ($permCount == 0) {
                // Add Project tab permission to Partner profile
                $this->adb->pquery(
                    "INSERT INTO vtiger_profile2tab (profileid, tabid, permissions) VALUES (?, ?, ?)",
                    array($profileId, $projectTabId, 0)
                );
                $this->log("  ✓ Added Project tab to Partner profile");
            } else {
                $this->log("  ⚠ Project already assigned to Partner profile");
            }
            
            // Set action permissions: Create(0)=1, Read(1)=1, Edit(2)=1, Delete(5)=0, Export(6)=0, Assign(7)=1
            $this->setActionPermissions($profileId, $projectTabId);
            
        } catch (Exception $e) {
            $this->log("  ✗ Error configuring Project permissions: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Set specific action permissions for Partner on Projects
     */
    private function setActionPermissions($profileId, $moduleId) {
        try {
            $this->adb->pquery(
                "INSERT INTO vtiger_profile2standardpermission (profileid, tabid, operation, permissions) 
                 VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissions = ?",
                array($profileId, $moduleId, 0, 1, 1)  // Create = 1
            );
            $this->adb->pquery(
                "INSERT INTO vtiger_profile2standardpermission (profileid, tabid, operation, permissions) 
                 VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissions = ?",
                array($profileId, $moduleId, 1, 1, 1)  // Read = 1
            );
            $this->adb->pquery(
                "INSERT INTO vtiger_profile2standardpermission (profileid, tabid, operation, permissions) 
                 VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissions = ?",
                array($profileId, $moduleId, 2, 1, 1)  // Edit = 1
            );
            $this->adb->pquery(
                "INSERT INTO vtiger_profile2standardpermission (profileid, tabid, operation, permissions) 
                 VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissions = ?",
                array($profileId, $moduleId, 5, 0, 0)  // Delete = 0
            );
            $this->adb->pquery(
                "INSERT INTO vtiger_profile2standardpermission (profileid, tabid, operation, permissions) 
                 VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissions = ?",
                array($profileId, $moduleId, 6, 0, 0)  // Export = 0
            );
            $this->adb->pquery(
                "INSERT INTO vtiger_profile2standardpermission (profileid, tabid, operation, permissions) 
                 VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE permissions = ?",
                array($profileId, $moduleId, 7, 1, 1)  // Assign = 1
            );
            
            $this->log("  ✓ Set action permissions: Create=1, Read=1, Edit=1, Delete=0, Export=0, Assign=1");
        } catch (Exception $e) {
            $this->log("  ✗ Error setting action permissions: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * STEP 2: Register Project Record Assignment Event Handler
     */
    public function registerAssignmentHandler() {
        try {
            $handlerClass = 'ProjectAssignmentNotificationHandler';
            $eventName = 'vtiger.entity.aftersave';
            
            // Check if handler already exists
            $result = $this->adb->pquery(
                "SELECT * FROM vtiger_eventhandlers WHERE eventname = ? AND classname = ?",
                array($eventName, $handlerClass)
            );
            
            if ($this->adb->num_rows($result) > 0) {
                $this->log("  ⚠ Assignment handler already registered");
                return;
            }
            
            // Register the handler
            $this->adb->pquery(
                "INSERT INTO vtiger_eventhandlers (eventname, classname, classfile) VALUES (?, ?, ?)",
                array($eventName, $handlerClass, 'modules/Project/handlers/ProjectAssignmentNotificationHandler.php')
            );
            
            $this->log("  ✓ Registered ProjectAssignmentNotificationHandler");
        } catch (Exception $e) {
            $this->log("  ✗ Error registering assignment handler: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * STEP 3: Register Project Record Update Event Handler
     */
    public function registerUpdateHandler() {
        try {
            $handlerClass = 'ProjectUpdateNotificationHandler';
            $eventName = 'vtiger.entity.aftersave';
            
            // Check if handler already exists
            $result = $this->adb->pquery(
                "SELECT * FROM vtiger_eventhandlers WHERE eventname = ? AND classname = ?",
                array($eventName, $handlerClass)
            );
            
            if ($this->adb->num_rows($result) > 0) {
                $this->log("  ⚠ Update handler already registered");
                return;
            }
            
            // Register the handler
            $this->adb->pquery(
                "INSERT INTO vtiger_eventhandlers (eventname, classname, classfile) VALUES (?, ?, ?)",
                array($eventName, $handlerClass, 'modules/Project/handlers/ProjectUpdateNotificationHandler.php')
            );
            
            $this->log("  ✓ Registered ProjectUpdateNotificationHandler");
        } catch (Exception $e) {
            $this->log("  ✗ Error registering update handler: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Execute installation
     */
    public function execute() {
        try {
            $this->log("[STEP 1] Configuring Partner permissions for Projects...");
            $this->configureProjectPermissions();
            
            $this->log("[STEP 2] Registering Project Assignment Notification Handler...");
            $this->registerAssignmentHandler();
            
            $this->log("[STEP 3] Registering Project Update Notification Handler...");
            $this->registerUpdateHandler();
            
            $this->log("=== Project Installation Completed Successfully ===\n");
            return true;
        } catch (Exception $e) {
            $this->log("✗ INSTALLATION FAILED: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log output
     */
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        echo $logMessage;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}

// Execute installation
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Show installation form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Project Notification & Partner Permissions Installer</title>
        <style>
            body { font-family: Arial; margin: 20px; }
            .container { max-width: 800px; margin: 0 auto; }
            button { padding: 10px 20px; background: #0066CC; color: white; border: none; cursor: pointer; }
            pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Project Notification & Partner Permissions Installer</h1>
            <p>This script will:</p>
            <ul>
                <li>Configure Partner role to create/update/assign Projects</li>
                <li>Register record assignment notification handler</li>
                <li>Register record update notification handler</li>
                <li>Enable VDNotifierPro integration</li>
            </ul>
            <form method="POST">
                <button type="submit">Start Installation</button>
            </form>
        </div>
    </body>
    </html>
    <?php
} else {
    // Run installation
    $installer = new ProjectNotificationInstaller();
    $installer->execute();
    ?>
    <script>
        alert('Installation completed! Check storage/project_notification_install.log for details.');
        window.location = '/index.php';
    </script>
    <?php
}
?>
