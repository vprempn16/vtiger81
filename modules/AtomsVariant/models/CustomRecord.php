<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtomsVariant_CustomRecord_Model extends Vtiger_Base_Model {

    public function getAllProducts(){
        global $adb;
        $return = array();
        $sql  = $adb->pquery("SELECT vtiger_products.productname, vtiger_products.productcode, vtiger_products.commissionrate, vtiger_products.qtyinstock, vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_products.productid, vtiger_crmentity_user_field.starred FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_products.productid = vtiger_crmentity.crmid LEFT JOIN vtiger_crmentity_user_field ON vtiger_products.productid = vtiger_crmentity_user_field.recordid AND vtiger_crmentity_user_field.userid=1 WHERE vtiger_crmentity.deleted=0 AND vtiger_products.productid > 0 ORDER BY vtiger_crmentity.modifiedtime DESC",array());
        $num_rows = $adb->num_rows($sql);
        if($num_rows > 0 ){
            for($i=0;$i<$num_rows; $i++){
                $productid = $adb->query_result($sql,$i,'productid');
                $productname = $adb->query_result($sql,$i,'productname');
                $return[$productid] = $productname; 
            }
        }
        return $return;
    }


}

?>
