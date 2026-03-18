<?php

include_once 'modules/Vtiger/CRMEntity.php';

class WhatsappIntergration extends Vtiger_CRMEntity {
    var $table_name = 'vtiger_whatsappintergration';
    var $table_index= 'whatsappintergrationid';

    var $customFieldTable = Array('vtiger_whatsappintergrationcf', 'whatsappintergrationid');

    var $tab_name = Array('vtiger_crmentity', 'vtiger_whatsappintergration', 'vtiger_whatsappintergrationcf');

    var $tab_name_index = Array(
            'vtiger_crmentity' => 'crmid',
            'vtiger_whatsappintergration' => 'whatsappintergrationid',
            'vtiger_whatsappintergrationcf'=>'whatsappintergrationid');

    var $list_fields = Array (
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'Title' => Array('whatsappintergration', 'title'),
            'Assigned To' => Array('crmentity','smownerid')
            );
    var $list_fields_name = Array (
            /* Format: Field Label => fieldname */
            'Title' => 'title',
            'Assigned To' => 'assigned_user_id',
            );

    // Make the field link to detail view
    var $list_link_field = 'title';

    // For Popup listview and UI type support
    var $search_fields = Array(
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'Title' => Array('whatsappintergration', 'title'),
            'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
            );
    var $search_fields_name = Array (
            /* Format: Field Label => fieldname */
            'Title' => 'title',
            'Assigned To' => 'assigned_user_id',
            );

    // For Popup window record selection
    var $popup_fields = Array ('title');

    // For Alphabetical search
    var $def_basicsearch_col = 'title';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'title';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('title','assigned_user_id');

    var $default_order_by = 'title';
    var $default_sort_order='ASC';
}
