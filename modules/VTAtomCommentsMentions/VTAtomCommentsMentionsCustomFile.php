<?php
class VTAtomCommentsMentionsCustom{

    function postEnable(){
        $validator = new Settings_VTAtomCommentsMentions_LicenseManager_Model();
        $licensekey_records = $validator->getRecordDetails();
        $license_key = $licensekey_records['cmtmention_license_key'];
        $license_key = Vtiger_Functions::fromProtectedText($license_key);
        $is_validate = $validator->apiCall($license_key,'validate');
        $is_active = $validator->apiCall($license_key,'is_active');
        if($is_validate['iskeyvalid'] && $is_active['iskeyactive']){
            $this->SettingsLink();
            $this->registerEventHandler();
        }
    }

    function postDisable(){
        global $adb;
        $this->unregisterEventHandler();
        $this->removeSettingsLink();
        $this->removeLicenseSettingsLink();
        $this->removeHeaderJsAndTypes();
    }
	public function LicenseSettingsLink(){
        global $adb;
        $name = "VTAtom Comments Mention License Manager";
        $description = "Configure License Manager";
        $linkto = "index.php?parent=Settings&module=VTAtomCommentsMentions&view=LicenseManagerEdit";
        $result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name= ?",array($name));
        $num_rows = $adb->num_rows($result);
        if($num_rows == 0) {
            $otherSettingsBlock = $adb->pquery('SELECT * FROM vtiger_settings_blocks WHERE label=?', array('LBL_OTHER_SETTINGS'));
            $otherSettingsBlockCount = $adb->num_rows($otherSettingsBlock);

            if ($otherSettingsBlockCount > 0) {
                $blockid = $adb->query_result($otherSettingsBlock, 0, 'blockid');
                $sequenceResult = $adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_blocks WHERE blockid=?", array($blockid));
                if ($adb->num_rows($sequenceResult)) {
                    $sequence = $adb->query_result($sequenceResult, 0, 'sequence');
                }
            }
            $fieldid = $adb->getUniqueID('vtiger_settings_field');

            $adb->pquery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active , pinned) VALUES(?,?,?,?,?,?,?,?,?)", array($fieldid, $blockid, $name, '',$description, $linkto, $sequence++, 0 , 1));

            $adb->pquery("UPDATE vtiger_settings_field_seq SET id = ?",array($fieldid));
        }
	}
    public function createCustomTables(){
        global $adb;
        $table_sql['atom_vtcommenton_rel'] = "CREATE TABLE `atom_vtcommenton_rel` (
            `id` int NOT NULL AUTO_INCREMENT,
            `is_checked` varchar(100) NOT NULL,
            `type` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
                );";
         $table_sql['atom_license_manager'] = "CREATE TABLE `atom_license_manager` (
            `id` int NOT NULL AUTO_INCREMENT,
            `meta_key` varchar(255) NOT NULL,
            `meta_value` longtext NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3";

        foreach($table_sql as $table_name => $sql){
            $table_exist_result = $adb->pquery("SHOW TABLES LIKE '$table_name'",array());
            $num_rows = $adb->num_rows($table_exist_result);
            if($num_rows == 0){
                $adb->pquery($sql,array());
            }
        }
        $adb->pquery("UPDATE vtiger_tab SET customized = 0 WHERE name = 'VTAtomCommentsMentions'",array());
    }
     public function SettingsLink(){
        global $adb;
        $name = "Atom Comments Mentions";
        $description = "Configure Comments";
        $linkto = "index.php?parent=Settings&module=VTAtomCommentsMentions&view=Edit";
        $result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name= ?",array($name));
        error_log( "SELECT * FROM vtiger_settings_field WHERE name= '{$name}'" , 3 , "/tmp/VTAtomCommentsMentions.log" );
        $num_rows = $adb->num_rows($result);
        if($num_rows == 0) {
            $otherSettingsBlock = $adb->pquery('SELECT * FROM vtiger_settings_blocks WHERE label=?', array('LBL_CONFIGURATION'));
            $otherSettingsBlockCount = $adb->num_rows($otherSettingsBlock);

            if ($otherSettingsBlockCount > 0) {
                $blockid = $adb->query_result($otherSettingsBlock, 0, 'blockid');
                $sequenceResult = $adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_blocks WHERE blockid=?", array($blockid));
                if ($adb->num_rows($sequenceResult)) {
                    $sequence = $adb->query_result($sequenceResult, 0, 'sequence');
                }
            }

            $fieldid = $adb->getUniqueID('vtiger_settings_field');
            $adb->pquery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active , pinned) VALUES(?,?,?,?,?,?,?,?,?)", array($fieldid, $blockid, $name, '',$description, $linkto, $sequence++, 0 , 1));

            $adb->pquery("UPDATE vtiger_settings_field_seq SET id = ?",array($fieldid));
        }else{
        }
    }
    public function registerEventHandler(){
         global $adb;
        $Vtiger_Utils_Log = true;
        include_once('vtlib/Vtiger/Event.php');
        $class = 'CommentMentionSendMail';
        $result = $adb->pquery("SELECT * FROM vtiger_eventhandlers WHERE handler_class= ?",array($class));
        error_log( "SELECT * FROM vtiger_eventhandlers WHERE handler_class='{$class}'" , 3 , "/tmp/VTAtomCommentsMentions.log" );
        $num_rows = $adb->num_rows($result);
        if($num_rows == 0){
            Vtiger_Event::register('ModComments', 'vtiger.entity.aftersave', $class, 'modules/VTAtomCommentsMentions/CommentMentionSendMail.php');
        }else{
        }
    }
     public function removeHeaderJsAndTypes(){
        global $adb;

        $adb->pquery('UPDATE atom_vtcommenton_rel SET is_checked = ? WHERE type=?',array('off','comment_mentions'));
        $adb->pquery('UPDATE atom_vtcommenton_rel SET is_checked = ? WHERE type=?',array('off','send_commentmail'));

        $linklabel = "VtAtomCommentMentions";
        $linkurl = 'layouts/v7/modules/VTAtomCommentsMentions/resources/'.$linklabel.'.js';
        Vtiger_Link::deleteLink(3,'HEADERSCRIPT',$linklabel,$linkurl);
    }
    public function removeSettingsLink(){
        global $adb;
        $name = "Atom Comments Mentions";
        $description = "Configure Comments";
        $linkto = "index.php?parent=Settings&module=VTAtomCommentsMentions&view=Edit";
        $result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name= ?",array($name));
        $num_rows = $adb->num_rows($result);
        if($num_rows == 1) {
            $adb->pquery("DELETE FROM vtiger_settings_field WHERE name = ?", array($name));
        }
    }
    public function unregisterEventHandler(){
        global $adb;
        $Vtiger_Utils_Log = true;
        include_once('include/events/VTEventsManager.inc');
        $class = 'CommentMentionSendMail';
        $result  = $adb->pquery('SELECT * FROM vtiger_eventhandlers WHERE handler_class =?',array($class));
        if($adb->num_rows($result) > 0){
            $eventsManager = new VTEventsManager($adb);
            $result = $eventsManager->unregisterHandler($class);
            return "success";
        }else{
            return "handler not found";
        }
    }
    public function removeLicenseSettingsLink(){
        global $adb;
        $name = "Atom Pipeline License Manager";
        $description = "Configure License Manager";
        $linkto = "index.php?parent=Settings&module=AtomPipeline&view=LicenseManagerEdit";
        $result = $adb->pquery("SELECT * FROM vtiger_settings_field WHERE name= ?",array($name));
        $num_rows = $adb->num_rows($result);
        if($num_rows == 1) {
            $adb->pquery("DELETE FROM vtiger_settings_field WHERE name = ?", array($name));
        }
    }
}

