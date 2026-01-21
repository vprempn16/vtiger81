<?php

include_once 'modules/Vtiger/CRMEntity.php';

class ServiceCompetency extends Vtiger_CRMEntity {
    var $table_name = 'vtiger_servicecompetency';
    var $table_index= 'servicecompetencyid';

    var $customFieldTable = Array('vtiger_servicecompetencycf', 'servicecompetencyid');

    var $tab_name = Array('vtiger_crmentity', 'vtiger_servicecompetency', 'vtiger_atomsvariantcf');

    var $tab_name_index = Array(
            'vtiger_crmentity' => 'crmid',
            'vtiger_servicecompetency' => 'servicecompetencyid',
            'vtiger_servicecompetencycf'=>'servicecompetencyid');

    var $list_fields = Array (
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'Service Name' => Array('servicecompetency', 'servicename'),
            'Assigned To' => Array('crmentity','smownerid'),
            'Consultant Name' => Array('servicecompetency','consultantname'),
            );
    var $list_fields_name = Array (
            /* Format: Field Label => fieldname */
            'Service Name' => 'servicename',
            'Assigned To' => 'assigned_user_id',
            'Consultant Name' => 'consultantname'
            );

    // Make the field link to detail view
    var $list_link_field = 'servicename';

    // For Popup listview and UI type support
    var $search_fields = Array(
            /* Format: Field Label => Array(tablename, columnname) */
            // tablename should not have prefix 'vtiger_'
            'Service Name' => Array('servicecompetency', 'servicename'),
            'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
            );
    var $search_fields_name = Array (
            /* Format: Field Label => fieldname */
            'Service Name' => 'servicename',
            'Assigned To' => 'assigned_user_id',
            );

    // For Popup window record selection
    var $popup_fields = Array ('servicename','consultantname');

    // For Alphabetical search
    var $def_basicsearch_col = 'servicename';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'servicename';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('servicename','assigned_user_id');

    var $default_order_by = 'servicename';
    var $default_sort_order='ASC';
    function vtlib_handler($moduleName, $eventType) {
        if ($moduleName == 'ServiceCompetency'){
            $db = PearDatabase::getInstance();
            include_once 'modules/ServiceCompetency/ServiceCompetencyCustom.php';
            $ServiceCompetencyCustom = new ServiceCompetencyCustom();
            if ($eventType == 'module.disabled') {
                $ServiceCompetencyCustom->postDisable();
            } else if ($eventType == 'module.enabled') {
                $ServiceCompetencyCustom->postEnable();
            } else if( $eventType == 'module.preuninstall' ) {
                $ServiceCompetencyCustom->postDisable();
            } else if( $eventType == 'module.postinstall' ) {
                $ServiceCompetencyCustom->postInstall();
            } else if( $eventType == 'module.postupdate' ) {
               $ServiceCompetencyCustom->postUpdate();
            }
        }
    }
}
