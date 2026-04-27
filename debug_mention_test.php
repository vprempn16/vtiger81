<?php
// Simple test to check mention permissions
require_once 'include/DatabaseUtil.php';
require_once 'include/utils/CommonUtils.php';
require_once 'modules/Project/helpers/MentionPermissionHelper.php';

// Initialize Vtiger environment
require_once 'config.inc.php';
require_once 'include/fields/DateTime.php';
require_once 'include/fields/Currency.php';
require_once 'include/fields/Email.php';
require_once 'include/fields/Picklist.php';
require_once 'include/fields/Text.php';
require_once 'include/fields/Reference.php';
require_once 'include/fields/Owner.php';
require_once 'data/CRMEntity.php';
require_once 'include/Webservices/Utils.php';

// Initialize database
global $adb;
$adb = PearDatabase::getInstance();

// Mock current user for testing
$userId = 1; // Change this to suresh's actual user ID
echo "Testing mention permissions for user $userId on project 224\n";

// Test with project ID 224 (the one you're testing)
$projectId = 224;

echo "Testing mention permissions for user $userId on project $projectId\n";

// Check if user was mentioned
$wasMentioned = Project_MentionPermissionHelper::wasUserMentionedInProject($projectId, $userId);
echo "Was user mentioned: " . ($wasMentioned ? "YES" : "NO") . "\n";

// Check permission details
$details = Project_MentionPermissionHelper::getMentionPermissionDetails($projectId, $userId);
echo "Permission details: " . print_r($details, true) . "\n";

// Check database directly
global $adb;
$directCheck = $adb->pquery(
    "SELECT COUNT(*) as count FROM vtiger_vdnotifierpro 
     WHERE userid = ? AND crmid IN (
         SELECT commentid FROM vtiger_modcomments WHERE related_to = ?
     ) AND action = 'MENTION' AND modifiedtime > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
    array($userId, $projectId)
);

if ($adb->num_rows($directCheck) > 0) {
    $count = $adb->query_result($directCheck, 0, 'count');
    echo "Direct DB check found $count mentions\n";
} else {
    echo "Direct DB check found 0 mentions\n";
}

// Show recent notifications for this user
$recentNotifs = $adb->pquery(
    "SELECT * FROM vtiger_vdnotifierpro 
     WHERE userid = ? AND action = 'MENTION' 
     ORDER BY modifiedtime DESC LIMIT 5",
    array($userId)
);

echo "Recent notifications for user $userId:\n";
while ($row = $adb->fetchByAssoc($recentNotifs)) {
    echo "- {$row['modifiedtime']}: {$row['title']} (crmid: {$row['crmid']})\n";
}
?>
