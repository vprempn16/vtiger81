<?php
/*+**********************************************************************************
 * BranchUsers installer hooks.
 *
 * Notes:
 * - This module is an extension and relies on the core Users module for storage.
 * - It ensures vtiger_user_branch_map and (via ensureProjectBranchRbacSchema) vtiger_project_branch_scope.
 ************************************************************************************/

class BranchUsersCustomFile {
	private $moduleName = 'BranchUsers';
	private $moduleInstance = null;
	private $adb = null;
	private $tabid = null;

	public function __construct() {
		global $adb;
		$this->adb = $adb;
		$this->moduleInstance = Vtiger_Module::getInstance($this->moduleName);
		$this->tabid = $this->moduleInstance ? $this->moduleInstance->id : null;
	}

	public function postInstall() {
		$this->createTables();
		$this->registerModuleInApp();
	}

	public function postEnable() {
		$this->registerModuleInApp();
	}

	public function postDisable() {
		// No-op (do not drop tables).
	}

	public function postUpdate() {
		// Code-only update path: skip DDL/backfill during module update.
		$this->registerModuleInApp();
	}

	private function createTables() {
		if (!$this->adb) {
			return;
		}
		$sql = "CREATE TABLE IF NOT EXISTS `vtiger_user_branch_map` (
			`user_id` INT(11) NOT NULL,
			`parent_user_id` INT(11) DEFAULT NULL,
			`partner_id` INT(11) NOT NULL,
			`status` TINYINT(1) NOT NULL DEFAULT 1,
			PRIMARY KEY (`user_id`),
			KEY `idx_partner` (`partner_id`),
			KEY `idx_parent` (`parent_user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
		$this->adb->pquery($sql, array());
		$this->ensureProjectBranchRbacSchema();
	}

	/**
	 * Idempotent DDL for private team project scope (vtiger_project_branch_scope).
	 * Mirrors statements in sql/private_team_project_rbac.create.sql.
	 */
	private function ensureProjectBranchRbacSchema() {
		if (!$this->adb) {
			return;
		}
		$this->adb->pquery(
			"CREATE TABLE IF NOT EXISTS `vtiger_project_branch_scope` (
				`projectid` INT(11) NOT NULL,
				`branch_root_user_id` INT(11) NOT NULL,
				`createdtime` DATETIME DEFAULT NULL,
				`modifiedtime` DATETIME DEFAULT NULL,
				PRIMARY KEY (`projectid`),
				KEY `idx_branch_root` (`branch_root_user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
			array()
		);
		$helper = dirname(__FILE__) . '/helpers/HierarchyAccess.php';
		if (file_exists($helper)) {
			require_once $helper;
			BranchUsers_HierarchyAccess::resetCache();
		}
	}

	private function registerModuleInApp() {
		if (!$this->adb || !$this->tabid) {
			return;
		}
		// Register in TOOLS app for v7 menu grouping (upgrade-safe DB insert).
		$maxSequenceResult = $this->adb->pquery(
			"SELECT MAX(sequence) AS max_sequence FROM vtiger_app2tab WHERE appname = ?",
			array('TOOLS')
		);
		$maxSequence = (int)$this->adb->query_result($maxSequenceResult, 0, 'max_sequence');

		$checkResult = $this->adb->pquery(
			"SELECT 1 FROM vtiger_app2tab WHERE tabid = ?",
			array($this->tabid)
		);
		if ($this->adb->num_rows($checkResult) == 0) {
			$this->adb->pquery(
				"INSERT INTO vtiger_app2tab (tabid, appname, sequence, visible) VALUES (?, ?, ?, ?)",
				array($this->tabid, 'TOOLS', $maxSequence + 1, 1)
			);
		}
	}
}

