<?php
/*+**********************************************************************************
 * BranchUsers - partner/manager user administration.
 * This module is designed as an extension module and does not introduce a CRMEntity table.
 ************************************************************************************/

include_once 'vtlib/Vtiger/Module.php';

class BranchUsers {
	/**
	 * vtlib lifecycle handler.
	 *
	 * @param string $moduleName
	 * @param string $eventType
	 */
	public function vtlib_handler($moduleName, $eventType) {
		// Keep the handler light and delegate to a custom installer.
		$customFile = 'modules/BranchUsers/BranchUsersCustomFile.php';
		if (file_exists($customFile)) {
			require_once $customFile;
			$installer = new BranchUsersCustomFile();
		} else {
			$installer = null;
		}

		switch ($eventType) {
			case 'module.postinstall':
				if ($installer) $installer->postInstall();
				break;
			case 'module.enabled':
				if ($installer) $installer->postEnable();
				break;
			case 'module.disabled':
				if ($installer) $installer->postDisable();
				break;
			case 'module.postupdate':
				if ($installer) $installer->postUpdate();
				break;
			default:
				break;
		}
	}
}

