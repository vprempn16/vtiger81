<?php
/*+**********************************************************************************
 * Project mention-based permission helper
 * Controls project edit access based on comment mention hierarchy
 ************************************************************************************/

class Project_MentionPermissionHelper {

    private static function isAdminUser($userId) {
        global $adb;
        
        $userQuery = $adb->pquery(
            "SELECT is_admin FROM vtiger_users WHERE id = ? AND deleted = 0",
            array($userId)
        );
        
        if ($adb->num_rows($userQuery) > 0) {
            $isAdmin = $adb->query_result($userQuery, 0, 'is_admin');
            return $isAdmin === 'on' || $isAdmin === '1';
        }
        
        return false;
    }
    
    /**
     * Get project mention permission details
     * @param int $projectId
     * @param int $userId
     * @return array|null
     */
    public static function getMentionPermissionDetails($projectId, $userId = null) {
        global $current_user, $adb;
        
        $projectId = (int)$projectId;
        $userId = $userId ? (int)$userId : (int)$current_user->id;
        
        if ($projectId <= 0 || $userId <= 0) {
            return null;
        }
        // Check database for recent mention permissions (extend to 24 hours for view access)
        $recentMentions = $adb->pquery(
            "SELECT * FROM vtiger_vdnotifierpro 
             WHERE userid = ? AND crmid IN (
                 SELECT modcommentsid FROM vtiger_modcomments WHERE related_to = ?
             ) AND action = 'MENTION' AND modifiedtime > DATE_SUB(NOW(), INTERVAL 24 HOUR)
             ORDER BY modifiedtime DESC LIMIT 1",
            array($userId, $projectId)
        );
        
        if ($adb->num_rows($recentMentions) > 0) {
            $description = $adb->query_result($recentMentions, 0, 'description');
            $modifiedtime = $adb->query_result($recentMentions, 0, 'modifiedtime');
            $metadata = json_decode($description, true);
            
            if ($metadata && isset($metadata['can_edit_project'])) {
                return [
                    'can_edit' => $metadata['can_edit_project'],
                    'can_view' => true, // Mentioned users can always view
                    'source' => 'database',
                    'hierarchy_level' => $metadata['hierarchy_level'],
                    'mentioned_by' => $metadata['mentioned_by'],
                    'modifiedtime' => $modifiedtime
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Check if user was mentioned in project comments (for view access)
     * @param int $projectId
     * @param int $userId
     * @return bool
     */
    public static function wasUserMentionedInProject($projectId, $userId = null) {
        global $current_user, $adb;
        
       
        $projectId = (int)$projectId;
        $userId = $userId ? (int)$userId : (int)$current_user->id;
        
        if ($projectId <= 0 || $userId <= 0) {
            return false;
        }
        
        // Check if user was mentioned in project comments (last 24 hours)
        $mentionCheck = $adb->pquery(
            "SELECT COUNT(*) as count FROM vtiger_vdnotifierpro 
             WHERE userid = ? AND crmid IN (
                 SELECT modcommentsid  FROM vtiger_modcomments WHERE related_to = ?
             ) AND action = 'MENTION' AND modifiedtime > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
            array($userId, $projectId)
        );
        
        $count = 0;
        if ($adb->num_rows($mentionCheck) > 0) {
            $count = $adb->query_result($mentionCheck, 0, 'count');
        }
        
        return $count > 0;
    }
    
}
