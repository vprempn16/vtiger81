<?php
/**
 * Project Update Notification Handler
 * 
 * Sends notifications to team members when a Project is updated
 * Notifies all users involved in the project (assigned users, team members)
 */

class ProjectUpdateNotificationHandler extends VTEventHandler {

    /**
     * Handle record update events
     */
    public function handleEvent($eventName, $data) {
        global $adb, $current_user;
        
        if ($eventName !== 'vtiger.entity.aftersave') {
            return;
        }
        
        $moduleName = $data->getModuleName();
        if ($moduleName !== 'Project') {
            return;
        }
        
        try {
            $recordId = $data->getId();
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            
            if (!$recordModel) {
                return;
            }
            
            // Get all users involved in this project
            $involvedUsers = $this->getInvolvedUsers($recordId);
            
            // Get updated fields
            $changedFields = $this->getChangedFields($recordId);
            
            if (empty($changedFields)) {
                return;
            }
            
            // Notify each team member about the changes
            foreach ($involvedUsers as $userId => $userName) {
                // Don't notify the person who made the change
                if ($userId == $current_user->id) {
                    continue;
                }
                
                $this->sendUpdateNotification(
                    $recordId,
                    $userId,
                    $recordModel,
                    $changedFields,
                    $current_user
                );
            }
            
        } catch (Exception $e) {
            error_log("ProjectUpdateNotificationHandler Error: " . $e->getMessage());
        }
    }
    
    /**
     * Get all users involved in the project
     */
    private function getInvolvedUsers($recordId) {
        global $adb;
        
        $involvedUsers = array();
        
        // Get project owner/assigned user
        try {
            $result = $adb->pquery(
                "SELECT smownerid FROM vtiger_crmentity WHERE crmid = ?",
                array($recordId)
            );
            
            if ($adb->num_rows($result) > 0) {
                $ownerId = $adb->query_result($result, 0, 'smownerid');
                if (!empty($ownerId)) {
                    $involvedUsers[$ownerId] = $this->getUserName($ownerId);
                }
            }
        } catch (Exception $e) {
            error_log("Error getting project owner: " . $e->getMessage());
        }
        
        // Get related project members (if using related lists)
        try {
            // Get project members via related records (e.g., ProjectTask assignments)
            $result = $adb->pquery(
                "SELECT DISTINCT pt.smownerid FROM vtiger_projecttask pt 
                 JOIN vtiger_crmentity cr ON pt.crmid = cr.crmid 
                 WHERE pt.projectid = ?",
                array($recordId)
            );
            
            while ($row = $adb->fetchByAssoc($result)) {
                $memberId = $row['smownerid'];
                if (!empty($memberId)) {
                    $involvedUsers[$memberId] = $this->getUserName($memberId);
                }
            }
        } catch (Exception $e) {
            error_log("Error getting project members: " . $e->getMessage());
        }
        
        return $involvedUsers;
    }
    
    /**
     * Get fields that were changed in this update
     */
    private function getChangedFields($recordId) {
        global $adb;
        
        $changedFields = array();
        
        try {
            // Check audit trail for recent changes
            $result = $adb->pquery(
                "SELECT DISTINCT fieldname, newvalue FROM vtiger_audit 
                 WHERE crmid = ? AND operation = 'Update' 
                 ORDER BY id DESC LIMIT 10",
                array($recordId)
            );
            
            while ($row = $adb->fetchByAssoc($result)) {
                $fieldname = $row['fieldname'];
                $newvalue = $row['newvalue'];
                
                // Only include important fields
                if (in_array($fieldname, array('projectname', 'projectstatus', 'duedate', 'startdate', 'type'))) {
                    $changedFields[$fieldname] = $newvalue;
                }
            }
        } catch (Exception $e) {
            error_log("Error getting changed fields: " . $e->getMessage());
        }
        
        return $changedFields;
    }
    
    /**
     * Send update notification
     */
    private function sendUpdateNotification($recordId, $userId, $recordModel, $changedFields, $currentUser) {
        global $adb;
        
        // Check if VDNotifierPro is available
        if (file_exists('modules/VDNotifierPro/models/Record.php') && vtlib_isModuleActive('VDNotifierPro')) {
            require_once 'modules/VDNotifierPro/models/Record.php';
            
            $projectName = $recordModel->get('projectname');
            $updaterName = $currentUser->first_name . ' ' . $currentUser->last_name;
            
            // Build description of changes
            $description = "$updaterName updated the project \"$projectName\".<br>";
            $description .= "Changed fields: " . implode(", ", array_keys($changedFields));
            
            // Create notification
            $VDNotifier = new VDNotifierPro_Record_Model();
            $VDNotifier->userid = $userId;
            $VDNotifier->modulename = 'Project';
            $VDNotifier->crmid = $recordId;
            $VDNotifier->modifyuserid = $currentUser->id;
            $VDNotifier->action = 'UPDATE';
            $VDNotifier->modifiedtime = date('Y-m-d H:i:s');
            $VDNotifier->title = "Project Updated: $projectName";
            $VDNotifier->description = $description;
            $VDNotifier->link = "module=Project&view=Detail&record=$recordId";
            $VDNotifier->priority = 0;  // Normal priority for updates
            
            $VDNotifier->save();
        }
    }
    
    /**
     * Get user name by ID
     */
    private function getUserName($userId) {
        global $adb;
        
        $result = $adb->pquery(
            "SELECT first_name, last_name FROM vtiger_users WHERE id = ?",
            array($userId)
        );
        
        if ($adb->num_rows($result) > 0) {
            $firstName = $adb->query_result($result, 0, 'first_name');
            $lastName = $adb->query_result($result, 0, 'last_name');
            return $firstName . ' ' . $lastName;
        }
        
        return "User #" . $userId;
    }
}
