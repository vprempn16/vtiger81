-- BranchUsers mapping table (single-table design)
-- Purpose:
-- - user_id: vtiger_users.id (the managed/managed-by user)
-- - parent_user_id: direct manager user id (vtiger_users.id)
-- - partner_id: tenant/group identifier (application-defined)
-- - status: 1=active, 0=inactive (soft-disable membership)

CREATE TABLE `vtiger_user_branch_map` (
  `user_id` int NOT NULL,
  `parent_user_id` int DEFAULT NULL,
  `partner_id` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  KEY `idx_parent_user_id` (`parent_user_id`),
  KEY `idx_partner` (`partner_id`)
)
CREATE TABLE IF NOT EXISTS `vtiger_project_branch_scope` (
  `projectid` INT(11) NOT NULL,
  `branch_root_user_id` INT(11) NOT NULL,
  `createdtime` DATETIME DEFAULT NULL,
  `modifiedtime` DATETIME DEFAULT NULL,
  PRIMARY KEY (`projectid`),
  KEY `idx_branch_root` (`branch_root_user_id`)
);
-- Branch root for users is computed from parent_user_id (HierarchyAccess::computeBranchRootByWalk).
-- Manual DDL if upgrading an old DB that had the cache column:
-- ALTER TABLE `vtiger_user_branch_map` DROP INDEX `idx_branch_root_user`, DROP COLUMN `branch_root_user_id`;
