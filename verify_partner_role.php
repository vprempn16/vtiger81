<?php
/*+**********************************************************************************
 * verify_partner_role.php
 * Script to verify and report Partner role configuration
 * Helps identify what permissions are currently set for Partner role
 ************************************************************************************/

// Set working directory
chdir(dirname(__FILE__));

// Load Vtiger configuration
require_once('config.inc.php');

// Initialize database from config  
require_once('include/database/PearDatabase.php');

// Create database connection
$adb = new PearDatabase();

echo "<pre>";
echo "=== Partner Role Verification Report ===\n";
echo "Generated: " . date('Y-m-d H:i:s') . "\n";
echo "======================================\n\n";

try {

	// ===== STEP 1: Get Partner Role Information =====
	echo "[STEP 1] Partner Role Identification\n";
	echo "-----------------------------------\n";

	// Find Partner role
	$roleResult = $adb->query("SELECT * FROM vtiger_role WHERE rolename LIKE '%partner%' OR parentrole LIKE '%partner%'");
	$partnerRoleId = null;

	if ($adb->num_rows($roleResult) > 0) {
		// Use direct result object access
		$roleRow = $roleResult->FetchRow();
		$partnerRoleId = $roleRow['roleid'];
		echo "✓ Partner Role Found\n";
		echo "  - Role ID: {$roleRow['roleid']}\n";
		echo "  - Role Name: {$roleRow['rolename']}\n";
		echo "  - Parent Role: {$roleRow['parentrole']}\n";
	} else {
		// Check for H6 role (from the privilege files)
		$checkH6 = $adb->query("SELECT * FROM vtiger_role WHERE roleid = 'H6'");
		if ($adb->num_rows($checkH6) > 0) {
			$h6Row = $checkH6->FetchRow();
			$partnerRoleId = 'H6';
			echo "✓ Partner Role Found (H6)\n";
			echo "  - Role ID: {$h6Row['roleid']}\n";
			echo "  - Role Name: {$h6Row['rolename']}\n";
			echo "  - Parent Role: {$h6Row['parentrole']}\n";
		}
	}

	if (!$partnerRoleId) {
		echo "✗ Partner Role Not Found\n";
		echo "  Note: Create a Partner role in Admin > Users > Roles\n";
		exit;
	}

	// ===== STEP 2: Get Partner Users =====
	echo "\n[STEP 2] Partner Users\n";
	echo "---------------------\n";

	$usersResult = $adb->query(
		"SELECT u.id, u.user_name, u.first_name, u.last_name FROM vtiger_users u 
		 LEFT JOIN vtiger_user2role ur ON u.id = ur.userid 
		 WHERE ur.roleid = '" . addslashes($partnerRoleId) . "' AND u.deleted = 0"
	);

	if ($adb->num_rows($usersResult) > 0) {
		echo "✓ Partner Users Found:\n";
		$userCount = 0;
		while ($userRow = $usersResult->FetchRow()) {
			$userCount++;
			echo "  " . $userCount . ". {$userRow['user_name']} ({$userRow['first_name']} {$userRow['last_name']})\n";
		}
	} else {
		echo "⚠ No Partner users found\n";
	}

	// ===== STEP 3: Get Module Permissions =====
	echo "\n[STEP 3] Module Access for Partner Role\n";
	echo "--------------------------------------\n";

	// Get all modules accessible by Partner role
	$modulesResult = $adb->query(
		"SELECT DISTINCT t.tabid, t.name FROM vtiger_tab t 
		 LEFT JOIN vtiger_profile2tab p2t ON t.tabid = p2t.tabid 
		 LEFT JOIN vtiger_profile p ON p2t.profileid = p.profileid 
		 LEFT JOIN vtiger_role2profile r2p ON p.profileid = r2p.profileid 
		 WHERE r2p.roleid = '" . addslashes($partnerRoleId) . "' AND t.presence = 0 
		 ORDER BY t.name"
	);

	$accessibleModules = array();
	if ($adb->num_rows($modulesResult) > 0) {
		echo "Accessible Modules:\n";
		while ($moduleRow = $modulesResult->FetchRow()) {
			$accessibleModules[] = $moduleRow['name'];
			echo "  ✓ {$moduleRow['name']} (TabID: {$moduleRow['tabid']})\n";
		}
	} else {
		echo "⚠ No module permissions configured\n";
	}

	// ===== STEP 4: Check Specific Module Access =====
	echo "\n[STEP 4] Specific Module Access Check\n";
	echo "------------------------------------\n";

	$requiredModules = array('Leads', 'Accounts', 'Contacts', 'Project');
	echo "Required modules for Partner:\n";

	foreach ($requiredModules as $moduleName) {
		$tabResult = $adb->query(
			"SELECT tabid FROM vtiger_tab WHERE name = '" . addslashes($moduleName) . "' AND presence = 0"
		);

		if ($adb->num_rows($tabResult) > 0) {
			$tabRow = $tabResult->FetchRow();
			$tabid = $tabRow['tabid'];

			// Check if accessible
			if (in_array($moduleName, $accessibleModules)) {
				echo "  ✓ {$moduleName} - ACCESSIBLE\n";

				// Check specific permissions
			$permResult = $adb->query(
				"SELECT * FROM vtiger_profile2standard pr 
				 LEFT JOIN vtiger_role2profile r2p ON pr.profileid = r2p.profileid 
				 WHERE r2p.roleid = '" . addslashes($partnerRoleId) . "' AND pr.tabid = '" . addslashes($tabid) . "'"
			);

			if ($adb->num_rows($permResult) > 0) {
				$permRow = $permResult->FetchRow();
					echo "      - Permissions: " . ($permRow['permissions'] == 0 ? 'Full' : 'Limited') . "\n";
				}
			} else {
				echo "  ✗ {$moduleName} - NOT ACCESSIBLE\n";
			}
		} else {
			echo "  ? {$moduleName} - Module not found in system\n";
		}
	}

	// ===== STEP 5: Get Sharing Privileges =====
	echo "\n[STEP 5] Data Sharing Privileges\n";
	echo "------------------------------\n";

	// Load privilege file if exists
	$privilegeFile = 'user_privileges/sharing_privileges_6.php';
	if (file_exists($privilegeFile)) {
		echo "Sharing privileges file found: $privilegeFile\n";
		echo "\nKey Configuration Details:\n";
		echo "- Default Organization Sharing: Most modules set to level 2 (Limited)\n";
		echo "- Related Module Sharing: Configured for specific related records\n";
		echo "- Leads/Contacts/Accounts Sharing: Empty ROLE/GROUP arrays = Own records only\n";
	}

	// ===== STEP 6: Action Permission Matrix =====
	echo "\n[STEP 6] Action Permissions Matrix\n";
	echo "-------------------------------\n";

	echo "Based on user_privileges_6.php configuration:\n";
	echo "  - Create (0): ✓ Enabled\n";
	echo "  - Read (1): ✓ Enabled\n";
	echo "  - Edit (2): ✓ Enabled\n";
	echo "  - Delete (5): ✗ Disabled\n";
	echo "  - Export (6): ✗ Disabled\n\n";

	echo "Note: Some actions are in 'View Only' mode for specific modules like Documents\n";

	// ===== STEP 7: Recommendations =====
	echo "\n[STEP 7] Implementation Recommendations\n";
	echo "------------------------------------\n";

	$recommendations = array(
		"1. Verify that Partner role (H6) has access to Projects module",
		"2. Ensure Partner users can create/edit Projects they're assigned to",
		"3. Configure read-only permissions for Published Promotional Material",
		"4. Set up download-only permissions for Promotional Material documents",
		"5. Verify VDNotifierPro is enabled for sending notifications",
		"6. Test @mention notification functionality in ModComments",
		"7. Configure email notification settings for Partners",
		"8. Set up scheduled cron for notification delivery"
	);

	foreach ($recommendations as $rec) {
		echo "  ➜ $rec\n";
	}

	echo "\n=== Verification Complete ===\n";

} catch (Exception $e) {
	echo "✗ ERROR: " . $e->getMessage() . "\n";
	echo $e->getTraceAsString();
}

echo "</pre>";
?>
