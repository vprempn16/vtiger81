-- Private team project RBAC — append new statements below as the feature evolves.
-- Apply on target DB (manual or via BranchUsersCustomFile postInstall/postUpdate).

-- 2026-04-10: Project branch scope (one row per project; no ALTER on vtiger_project).
CREATE TABLE IF NOT EXISTS `vtiger_project_branch_scope` (
  `projectid` INT(11) NOT NULL,
  `branch_root_user_id` INT(11) NOT NULL,
  `createdtime` DATETIME DEFAULT NULL,
  `modifiedtime` DATETIME DEFAULT NULL,
  PRIMARY KEY (`projectid`),
  KEY `idx_branch_root` (`branch_root_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- vtiger_user_branch_map does not store branch_root_user_id; branch root is derived from parent_user_id in PHP.

-- Backfill missing scope rows: PHP BranchUsers_HierarchyAccess::backfillMissingProjectScopes() after deploy.
