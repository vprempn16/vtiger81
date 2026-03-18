<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtomsVariant_AtomVariantPopupView_View extends Vtiger_Footer_View {
	function __construct() {
		parent::__construct();
	}


	public function process(Vtiger_Request $request) {
		global $adb;
        $moduleName = $request->getModule();
        $recordModel = new AtomsVariant_CustomRecord_Model();
        $products = $recordModel->getAllProducts();
        
		$viewer = $this->getViewer($request);
		$viewer->assign('FLAG', $flag);
        $viewer->assign('PRODUCTS',$products);
		$viewer->assign('MODULE',$moduleName);
        

	 echo $viewer->view('AtomVariantPopupView.tpl', $moduleName);
	}

}
