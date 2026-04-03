<?php
class ProjectUpdateNotifier extends VTEventHandler {
function handleEvent(\, \) {
if (\ == 'vtiger.entity.aftersave') {
\ = \->getModuleName();
if (\ == 'Project') {
global \, \;
if (!file_exists('modules/VDNotifierPro/models/Record.php') || !vtlib_isModuleActive('VDNotifierPro')) {
return;
}
require_once 'modules/VDNotifierPro/models/Record.php';
\ = \->getId();
\ = \->get('assigned_user_id');
\ = \->isNew();
if (\) {
if (\ != \->id) {
\->sendNotification(\, \, \->id, 'ASSIGN', 'Assigned you a new Project', 'Project');
}
} else {
\ = \->getPreviousValues();
if (isset(\['assigned_user_id'])) {
\ = \['assigned_user_id'];
\ = \;
if (\ != \->id) {
\->sendNotification(\, \, \->id, 'ASSIGN', 'Reassigned a Project to you', 'Project');
}
if (\ != \->id && \ != \) {
\->sendNotification(\, \, \->id, 'UPDATE', 'Project has been reassigned from you', 'Project');
}
}
\ = \->getRecordCreator(\);
if (\ && \ != \->id && \ != \) {
\->sendNotification(\, \, \->id, 'UPDATE', 'Updated a Project you created', 'Project');
}
if (!isset(\['assigned_user_id']) && \ != \->id) {
\->sendNotification(\, \, \->id, 'UPDATE', 'Updated a Project assigned to you', 'Project');
}
}
}
}
}
private function sendNotification(\, \, \, \, \, \) {
try {
\ = new VDNotifierPro_Record_Model();
\->userid = \;
\->modulename = \;
\->crmid = \;
\->modiuserid = \;
\->action = \;
\->modifiedtime = date('Y-m-d H:i:s');
\->title = \;
\->link = 'module=' . \ . '&view=Detail&record=' . \;
\->save();
} catch (Exception \) {}
}
private function getRecordCreator(\) {
global \;
\ = \->pquery('SELECT smcreatorid FROM vtiger_crmentity WHERE crmid = ?', array(\));
if (\->num_rows(\) > 0) {
return \->query_result(\, 0, 'smcreatorid');
}
return false;
}
}
?>
