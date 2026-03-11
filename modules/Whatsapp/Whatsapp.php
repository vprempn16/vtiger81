<?php

include_once 'modules/Vtiger/CRMEntity.php';

class Whatsapp extends Vtiger_CRMEntity
{
    var $table_name = 'vtiger_whatsapp';
    var $table_index = 'whatsappid';

    var $customFieldTable = array('vtiger_whatsappcf', 'whatsappid');

    var $tab_name = array('vtiger_crmentity', 'vtiger_whatsapp', 'vtiger_whatsappcf');

    var $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_whatsapp' => 'whatsappid',
        'vtiger_whatsappcf' => 'whatsappid'
    );

    var $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Message' => array('whatsapp', 'message'),
        'Direction' => array('whatsapp', 'direction'),
        'Status' => array('whatsapp', 'status'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Message' => 'message',
        'Direction' => 'direction',
        'Status' => 'status',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    var $list_link_field = 'message';

    // For Popup listview and UI type support
    var $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Message' => array('whatsapp', 'message'),
        'Direction' => array('whatsapp', 'direction'),
    );
    var $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Message' => 'message',
        'Direction' => 'direction',
    );

    // For Popup window record selection
    var $popup_fields = array('message');

    // For Alphabetical search
    var $def_basicsearch_col = 'message';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'message';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = array('message', 'assigned_user_id');

    var $default_order_by = 'message';
    var $default_sort_order = 'ASC';

    function vtlib_handler($moduleName, $eventType)
    {
        if ($moduleName == 'Whatsapp') {
            include_once 'modules/Whatsapp/WhatsappCustom.php';
            $WhatsappCustom = new WhatsappCustom();
            if ($eventType == 'module.disabled') {
                $WhatsappCustom->postDisable();
            } else if ($eventType == 'module.enabled') {
                $WhatsappCustom->postEnable();
            } else if ($eventType == 'module.preuninstall') {
                $WhatsappCustom->postDisable();
            } else if ($eventType == 'module.postinstall') {
                $WhatsappCustom->postInstall();
            } else if ($eventType == 'module.postupdate') {
                $WhatsappCustom->postUpdate();
            }
        }
    }
}
?>