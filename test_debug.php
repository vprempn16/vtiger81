<?php
// Simple test accessible via web browser
include_once 'config.php';
require_once 'vendor/autoload.php';
include_once 'include/Webservices/Relation.php';

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
require_once 'modules/Project/helpers/MentionPermissionHelper.php';

// Get current user from session
$currentUser = Users_Record_Model::getCurrentUserModel();
$userId = 8;

$projectId = 224; // The project you're testing

echo "<h2>Debug Test Results</h2>";
echo "<p><strong>Current User ID:</strong> $userId</p>";
echo "<p><strong>Project ID:</strong> $projectId</p>";

// Test if user was mentioned
$wasMentioned = Project_MentionPermissionHelper::wasUserMentionedInProject($projectId, $userId);
echo "<p><strong>Was user mentioned:</strong> " . ($wasMentioned ? "YES" : "NO") . "</p>";

// Test permission details
$details = Project_MentionPermissionHelper::getMentionPermissionDetails($projectId, $userId);
echo "<p><strong>Permission Details:</strong> <pre>" . print_r($details, true) . "</pre></p>";

// Test direct database check
global $adb;
$directCheck = $adb->pquery(
    "SELECT COUNT(*) as count FROM vtiger_vdnotifierpro 
     WHERE userid = ? AND crmid IN (
         SELECT modcommentsid FROM vtiger_modcomments WHERE related_to = ?
     ) AND action = 'MENTION' AND modifiedtime > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
    array($userId, $projectId)
);

if ($adb->num_rows($directCheck) > 0) {
    $count = $adb->query_result($directCheck, 0, 'count');
    echo "<p><strong>Direct DB Check:</strong> Found $count mentions</p>";
} else {
    echo "<p><strong>Direct DB Check:</strong> Found 0 mentions</p>";
}

// Show recent notifications for this user
$recentNotifs = $adb->pquery(
    "SELECT * FROM vtiger_vdnotifierpro 
     WHERE userid = ? AND action = 'MENTION' 
     ORDER BY modifiedtime DESC LIMIT 5",
    array($userId)
);

echo "<p><strong>Recent notifications for user $userId:</strong></p>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Time</th><th>Title</th><th>CRM ID</th><th>Description</th></tr>";
while ($row = $adb->fetchByAssoc($recentNotifs)) {
    $description = $row['description'] ? json_decode($row['description'], true) : null;
    echo "<tr>";
    echo "<td>{$row['modifiedtime']}</td>";
    echo "<td>{$row['title']}</td>";
    echo "<td>{$row['crmid']}</td>";
    echo "<td>" . ($description ? print_r($description, true) : 'N/A') . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
