<?php
/**
 * Project Assignment Notification Handler
 * 
 * Sends notifications when a Project is assigned to a user
 * Uses VDNotifierPro for internal notifications and email
 */

class ProjectAssignmentNotificationHandler extends VTEventHandler {

    /**
     * Handle record assignment events
     * Sends notification when smownerid (owner) changes
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
            // Get the newly saved record
            $recordId = $data->getId();
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            
            if (!$recordModel) {
                return;
            }
            
            // Get the current owner (assigned user)
            $assignedUserId = $recordModel->get('assigned_user_id');
            if (empty($assignedUserId)) {
                $assignedUserId = $recordModel->get('smownerid');
            }
            
            if (empty($assignedUserId)) {
                return;
            }
            
            // Check if this is a new assignment (the assigned user changed)
            $oldAssignedUser = $this->getFieldHistory($recordId, 'smownerid');
            
            // If assigned user changed, send notification
            if ($oldAssignedUser !== false && $oldAssignedUser !== $assignedUserId) {
                $this->sendAssignmentNotification($recordId, $assignedUserId, $recordModel, $current_user);
            }
            
        } catch (Exception $e) {
            error_log("ProjectAssignmentNotificationHandler Error: " . $e->getMessage());
        }
    }
    
    /**
     * Send assignment notification to assignee
     */
    private function sendAssignmentNotification($recordId, $assignedUserId, $recordModel, $currentUser) {
        global $adb;
        
        // Check if VDNotifierPro is available and active
        if (file_exists('modules/VDNotifierPro/models/Record.php') && vtlib_isModuleActive('VDNotifierPro')) {
            require_once 'modules/VDNotifierPro/models/Record.php';
            
            $assigneeName = $this->getUserName($assignedUserId);
            $projectName = $recordModel->get('projectname');
            $creatorName = $currentUser->first_name . ' ' . $currentUser->last_name;
            
            // Create notification record
            $VDNotifier = new VDNotifierPro_Record_Model();
            $VDNotifier->userid = $assignedUserId;
            $VDNotifier->modulename = 'Project';
            $VDNotifier->crmid = $recordId;
            $VDNotifier->modifyuserid = $currentUser->id;
            $VDNotifier->action = 'ASSIGNMENT';
            $VDNotifier->modifiedtime = date('Y-m-d H:i:s');
            $VDNotifier->title = "You have been assigned to Project: $projectName";
            $VDNotifier->description = "$creatorName assigned you to the project \"$projectName\"";
            $VDNotifier->link = "module=Project&view=Detail&record=$recordId";
            $VDNotifier->priority = 1;
            
            $VDNotifier->save();
            
            // Send email notification
            $this->sendEmailNotification($assignedUserId, $projectName, $recordId, $creatorName);
        }
    }
    
    /**
     * Send email notification about project assignment
     */
    private function sendEmailNotification($userId, $projectName, $recordId, $creatorName) {
        global $adb;
        
        try {
            // Get user email
            $userResult = $adb->pquery(
                "SELECT email1 FROM vtiger_users WHERE id = ?",
                array($userId)
            );
            
            if ($adb->num_rows($userResult) == 0) {
                return;
            }
            
            $userEmail = $adb->query_result($userResult, 0, 'email1');
            if (empty($userEmail)) {
                return;
            }
            
            // Prepare email
            $subject = "You have been assigned to Project: $projectName";
            $body = "Hi,\n\n";
            $body .= "$creatorName has assigned you to the project: $projectName\n\n";
            $body .= "You can view the project details by clicking the link below:\n";
            $body .= "https://" . $_SERVER['HTTP_HOST'] . "/index.php?module=Project&view=Detail&record=$recordId\n\n";
            $body .= "Best regards,\nVtiger CRM";
            
            $headers = "From: " . $GLOBALS['default_email'] . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            // Send email
            if (function_exists('mail')) {
                mail($userEmail, $subject, $body, $headers);
            }
            
            error_log("Assignment email sent to $userEmail for project $projectName");
            
        } catch (Exception $e) {
            error_log("Error sending assignment email: " . $e->getMessage());
        }
    }
    
    /**
     * Get previous field value from history
     */
    private function getFieldHistory($recordId, $fieldName) {
        global $adb;
        
        try {
            $result = $adb->pquery(
                "SELECT oldvalue FROM vtiger_audit WHERE crmid = ? AND fieldname = ? ORDER BY id DESC LIMIT 1",
                array($recordId, $fieldName)
            );
            
            if ($adb->num_rows($result) > 0) {
                return $adb->query_result($result, 0, 'oldvalue');
            }
        } catch (Exception $e) {
            // Audit table might not exist, return false
        }
        
        return false;
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
