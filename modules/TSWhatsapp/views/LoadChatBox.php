<?php
class TSWhatsapp_LoadChatBox_View extends Vtiger_Index_View {
    public function process(Vtiger_Request $request) {
	$model = new TSWhatsapp_Record_Model();
	$mode = $request->get('chatview');
	$contacts = $model->getAllContacts();
        $viewer = $this->getViewer($request);
        $viewer->assign('CONTACTS', $contacts);
	if($mode == 'full-main'){
		 $viewer->view('ChatMain.tpl', 'TSWhatsapp');
	}else{
		$viewer->view('LoadChatBox.tpl', 'TSWhatsapp');
	}
    }
    public function checkPermission(Vtiger_Request $request) {
        return true; // Add ACL if needed
    }

}

