<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

vimport('~~/vtlib/Vtiger/Module.php');
class PromotionalMaterial_Detail_View extends Vtiger_Detail_View {
    function process(Vtiger_Request $request) {
                $mode = $request->getMode();
                if(!empty($mode)) {
                        echo $this->invokeExposedMethod($mode, $request);
                        return;
                }
                $currentUserModel = Users_Record_Model::getCurrentUserModel();
                echo $this->showModuleDetailView($request);
        }
}
