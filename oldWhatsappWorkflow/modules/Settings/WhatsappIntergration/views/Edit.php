<?php
class Settings_WhatsappIntergration_Edit_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
	 require_once 'modules/WhatsappIntergration/WhatsappHelper.php';
                $helper       = new WhatsappHelper();
        $viewer = $this->getViewer($request);


        $records = $helper->getRecordDetails();
	$app_id = $records['app_id'];
	$app_secret = $records['app_secret'];
	$phone_number_id = $records['phone_number_id'];
	$business_id = $records['business_id'];
	$access_token = $records['access_token'];
	$access_token = Vtiger_Functions::fromProtectedText($access_token);
	$viewer->assign("APP_ID",$app_id);
	$viewer->assign("APP_SECRET",$app_secret);
        $viewer->assign("PHONE_ID",$phone_number_id);
        $viewer->assign("BUSI_ID",$business_id);
        $viewer->assign("ACCESS_TOKEN",$access_token);


        $viewer->view("Edit.tpl", $qualifiedModuleName);
    }

}






?>
