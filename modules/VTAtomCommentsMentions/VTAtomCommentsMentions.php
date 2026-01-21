<?php

class VTAtomCommentsMentions extends CRMEntity {
    function vtlib_handler($moduleName, $eventType) {
        global $adb;
        include_once 'modules/VTAtomCommentsMentions/VTAtomCommentsMentionsCustomFile.php';
        $VTAtomCommentsMentionsCustom = new VTAtomCommentsMentionsCustom();
        if($eventType == 'module.postinstall') {
            $VTAtomCommentsMentionsCustom->createCustomTables();
            $VTAtomCommentsMentionsCustom->LicenseSettingsLink();
            $VTAtomCommentsMentionsCustom->postEnable();
            // TODO Handle actions after this module is installed.
        } else if($eventType == 'module.enabled') {
            $VTAtomCommentsMentionsCustom->postEnable();
            $VTAtomCommentsMentionsCustom->createCustomTables();
            // TODO Handle actions when this module is enabled.
        } else if($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
            $VTAtomCommentsMentionsCustom->postDisable();

        } else if($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
            $VTAtomCommentsMentionsCustom->postDisable();
        } else if($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($eventType == 'module.postupdate') {
            $VTAtomCommentsMentionsCustom->postEnable();
            $VTAtomCommentsMentionsCustom->createCustomTables();
            $VTAtomCommentsMentionsCustom->LicenseSettingsLink();
        }
    }
}

?>                                            	
