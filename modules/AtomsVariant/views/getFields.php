<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtomsVariant_getFields_View extends Vtiger_Index_View{
    function __construct() {
        parent::__construct();
        $this->exposeMethod('getSelectedFields');
        $this->exposeMethod('getVariantFieldAndValue');
        $this->exposeMethod('getFinalRecords');
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
    public function getFinalRecords(Vtiger_Request $request){
        global $adb;
        $viewer = $this->getViewer($request);

        $productId = $request->get('productid');
        $selectedValues = $request->get('selectedValues');

        $conditions = "products = ?";
        $params = [$productId];

        foreach ($selectedValues as $key => $value) {
            $conditions .= " AND {$key} = ?";
            $params[] = $value;
        }


        $selectedColumns = array_keys($selectedValues); // Get column names from selected values
        $selectedColumnsString = implode(",", $selectedColumns); // Convert to string for SQL

        $query = "SELECT {$selectedColumnsString},products,atomsvariantid FROM vtiger_atomsvariant WHERE {$conditions}";
        $result = $adb->pquery($query, $params);

        $moduleModel = Vtiger_Module_Model::getInstance($request->getModule());
        $moduleFields = $moduleModel->getFields();

        $fieldLabels = [];
        foreach ($moduleFields as $fieldName => $fieldModel) {
            if (in_array($fieldName, $selectedColumns)) { 
                $fieldLabels[$fieldName] = $fieldModel->get('label');
            }
        }
        $records = [];
        while ($row = $adb->fetch_array($result)) {
            $recordData = [];
            foreach ($selectedColumns as $column) {
                $recordData[$column] = $row[$column]; 
                $label = $fieldLabels[$column] ?? $column; 
                $variantDetails[$label] = $row[$column];
            }
            $recordData['productid'] = $row['products'];
            $recordData['atomsvariantid'] = $row['atomsvariantid'];
            $recordData['productname'] = Vtiger_Util_Helper::getRecordName($row['products']);
            $recordData['variantinfo'] = htmlspecialchars(json_encode($variantDetails),ENT_QUOTES, 'UTF-8');
            
            $records[] = $recordData;
        }

        $productInfo  = $this->getProductDetails($productId);
        $dataInfo = htmlspecialchars(json_encode($productInfo), ENT_QUOTES, 'UTF-8');
        $dataUrl = "index.php?module=Inventory&action=GetTaxes&record={$productId}&sourceModule=Quotes";

        $viewer->assign("DATA_INFO", $dataInfo);
        $viewer->assign("DATA_URL", $dataUrl);
        $viewer->assign("HEADERS", $fieldLabels); 
        $viewer->assign("RECORDS", $records);

        $html = $viewer->fetch('layouts/v7/modules/AtomsVariant/VariantTable.tpl');
        return ['success' => true, 'html' => $html];
    }
    public function getProductDetails($productId){
        global $adb;
        if($productId){
            $productQuery = "SELECT productname, product_no, unit_price, qtyinstock, productcategory 
                FROM vtiger_products WHERE productid = ?";
            $productResult = $adb->pquery($productQuery, [$productId]);
            $productDetails = $adb->fetch_array($productResult);

            $productInfo = [
                "productname" => $productDetails['productname'],
                "product_no" => $productDetails['product_no'],
                "unit_price" => $productDetails['unit_price'],
                "qtyinstock" => $productDetails['qtyinstock'],
                "productcategory" => $productDetails['productcategory'],
                "productid" => $productId
            ];
        }
        return $productInfo;
    }
    public function getVariantFieldAndValue(Vtiger_Request $request){
        global $adb;
        $viewer = $this->getViewer($request);

        $productId = $request->get('productid'); 
        $selectedValues = $request->get('selectedValues'); 

        $result = $adb->pquery("SELECT * FROM `atom_variants_options` WHERE meta_key = ?", array('variant_fields'));
        if ($adb->num_rows($result) > 0) {
            $options = unserialize(base64_decode($adb->query_result($result, 0, 'meta_value')));
        }

        $selectedKeys = array_keys($selectedValues);
        $lastSelectedField = end($selectedKeys);
        $currentIndex = array_search($lastSelectedField, $options);
        $nextField = isset($options[$currentIndex + 1]) ? $options[$currentIndex + 1] : null;

        $isLastField = ($nextField === null);

        if ($isLastField) {
            return ['success' => true, 'lastField' => true]; 
        }

        $moduleModel = Vtiger_Module_Model::getInstance($request->getModule());
        $moduleFields = $moduleModel->getFields();
        $fieldList = [$nextField => $moduleFields[$nextField]->label];

        $fieldValues = $this->getVariantValues($nextField, $selectedValues, $productId);
        $flatValues = array_filter(array_map(function($value) {
                    return is_array($value) ? array_filter($value) : $value;
                    }, $fieldValues));

        if (empty($flatValues)) {
            return ['success' => true, 'lastField' => true];
        }

        $viewer->assign("OPTIONS", $fieldList);
        $viewer->assign("OPTIONS_VALUES", $fieldValues);
        $html = $viewer->fetch('layouts/v7/modules/AtomsVariant/FieldRow.tpl');
        $result = ['success'=> true, 'html'=> $html , 'lastField' => false];
        return $result;
    }
    public function getVariantValues($field, $selectedValues, $productId) {
        global $adb;

        $conditions = "";
        $params = [$productId];

        if (!empty($selectedValues)) {
            foreach ($selectedValues as $key => $value) {
                $conditions .= " AND {$key} = ?"; // Add condition dynamically
                $params[] = $value; // Add value to query parameters
            }
        }
        $query = "SELECT DISTINCT {$field} FROM vtiger_atomsvariant WHERE products = ? {$conditions}";
        $result = $adb->pquery($query, $params);

        $fieldValues = array();
        if($adb->num_rows($result) > 0){
            while ($row = $adb->fetch_array($result)) {
                $fieldValues[] = $row[$field];
            }
        }
        return $fieldValues;
    }
   public function getFieldsBySettings($request){
        global $adb;
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $productId = $request->get('id');
        $result = $adb->pquery("SELECT * FROM `atom_variants_options` WHERE meta_key =?",array('variant_fields'));
        $num_rows = $adb->num_rows($result);
        if( $adb->num_rows($result) > 0){
            $options = $adb->query_result($result,0,'meta_value');
            $options = unserialize(base64_decode($options));
        }
        $firstOpt[] = $options[0];
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $moduleFields = $moduleModel->getFields();
        foreach($moduleFields as $name =>  $data){
            if(in_array($name,$firstOpt)){
                $fieldList[$name] = $data->label;
            }
        }
        $fieldValues =  $this->getValuesByField($firstOpt,$productId);
        $viewer->assign("OPTIONS",$fieldList);
        $viewer->assign("OPTIONS_VALUES",$fieldValues);
        $html= $viewer->fetch('layouts/v7/modules/AtomsVariant/FieldRow.tpl');
        return $html;
   }
   public function getValuesByField($fields,$productId){
       global $adb;
       $fieldsList = implode(",", array_map('trim', $fields));
       if (empty($fieldsList)) {
           return [];
       }
       $result = $adb->pquery("SELECT DISTINCT {$fieldsList}  FROM vtiger_atomsvariant WHERE products = ?",array($productId));
       $fieldValues = [];
       while ($row = $adb->fetch_array($result)) {
           foreach ($fields as $field) {
               if (!empty($row[$field])) {
                   $fieldValues[] = $row[$field];
               }
           }
       }
       return $fieldValues;
   }
}
?>
