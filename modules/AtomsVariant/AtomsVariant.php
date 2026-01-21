<?php

include_once 'modules/Vtiger/CRMEntity.php';

class AtomsVariant extends Vtiger_CRMEntity {
    var $table_name = 'vtiger_atomsvariant';
    var $table_index= 'atomsvariantid';

    var $customFieldTable = Array('vtiger_atomsvariantcf', 'atomsvariantid');

    var $tab_name = Array('vtiger_crmentity', 'vtiger_atomsvariant', 'vtiger_atomsvariantcf');

    var $tab_name_index = Array(
            'vtiger_crmentity' => 'crmid',
            'vtiger_atomsvariant' => 'atomsvariantid',
            'vtiger_atomsvariantcf'=>'atomsvariantid');

    var $list_fields = Array (
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'Variant Name' => Array('atomsvariant', 'variant_name'),
            'Assigned To' => Array('crmentity','smownerid')
            );
    var $list_fields_name = Array (
            /* Format: Field Label => fieldname */
            'Variant Name' => 'variant_name',
            'Assigned To' => 'assigned_user_id',
            );

    // Make the field link to detail view
    var $list_link_field = 'variant_name';

    // For Popup listview and UI type support
    var $search_fields = Array(
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'Variant Name' => Array('atomsvariant', 'variant_name'),
            'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
            );
    var $search_fields_name = Array (
            /* Format: Field Label => fieldname */
            'Variant Name' => 'variant_name',
            'Assigned To' => 'assigned_user_id',
            );

    // For Popup window record selection
    var $popup_fields = Array ('variant_name');

    // For Alphabetical search
    var $def_basicsearch_col = 'variant_name';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'variant_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('variant_name','assigned_user_id');

    var $default_order_by = 'variant_name';
    var $default_sort_order='ASC';
}
