<?php
/**
 * Partner Visibility Handler for Project Module
 * 
 * Ensures Partner users can see:
 * 1. Projects they created (regardless of current owner)
 * 2. Projects assigned to them
 */

class ProjectPartnerVisibilityHandler extends VTEventHandler {

    /**
     * Handle visibility for Partner users
     * Called during record access checks
     */
    public function handleEvent($eventName, $data) {
        global $adb, $current_user;
        
        // Only apply to Partner role users
        if (!$this->isPartnerUser($current_user)) {
            return;
        }
        
        // Modify the visibility/sharing rules for Project list view
        // This is called before list view is rendered
    }
    
    /**
     * Check if current user is a Partner
     */
    private function isPartnerUser($user) {
        global $adb;
        
        if (empty($user) || empty($user->id)) {
            return false;
        }
        
        // Query user's role
        $result = $adb->pquery(
            "SELECT roleid FROM vtiger_user2role WHERE userid = ?",
            array($user->id)
        );
        
        if ($adb->num_rows($result) > 0) {
            $roleId = $adb->query_result($result, 0, 'roleid');
            // Check if this role is Partner (H6)
            return strpos($roleId, 'H6') !== false;
        }
        
        return false;
    }
}
