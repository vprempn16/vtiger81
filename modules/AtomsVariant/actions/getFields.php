<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtomsVariant_getFields_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getSelectedFields');
    }
    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        $response = new Vtiger_Response();
        if(!empty($mode)) {
            $result = $this->invokeExposedMethod($mode, $request);
        }
        $response->setResult($result);
        $response->emit();
    }
    public function getSelectedFields(Vtiger_Request $request){
        $return = true;
        $html = "";
        $html = $this->getFieldsBySettings( $request ); 
        $result = ['success'=> $return, 'html'=> $html];
        return $result;
   }
   public function getFieldsBySettings($request){
        global $adb;
        $viewer = $this->getViewer($request);
        $result = $adb->pquery("SELECT * FROM `atom_variants_options` WHERE meta_key =?",array('variant_fields'));
        $num_rows = $adb->num_rows($result);
        if( $adb->num_rows($result) > 0){
            $options = $adb->query_result($result,0,'meta_value');
            $options = unserialize(base64_decode($options));
        }
        return $options;
   }
}
?>
