-- Remove custom workflow *task types* (not entity methods).
-- Run after switching workflows to: Add task → Invoke custom function → PMNotifyPartners / PMSendPartnerEmail.
-- If any workflow still references these task types, edit those workflows first and remove/replace the tasks.

DELETE FROM com_vtiger_workflow_tasktypes
WHERE tasktypename IN ('PMPartnerNotificationTask', 'PMPartnerEmailTask');
