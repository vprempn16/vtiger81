<?php
class TSWhatsapp_SaveMessages_View extends Vtiger_Index_View{
    function process(Vtiger_Request $request){
	global $adb,$current_user;
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $commentText = $request->get('commentText');
        $recordid = $request->get('recordid');
	$date_var = date("Y-m-d H:i:s");
	$datetime = $adb->formatDate($date_var, true);
        $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
        $recordModel->set('title',$commentText);
        $recordModel->set('wapaid',NULL);
        $recordModel->set('contactid',$recordid);
        $recordModel->set('status','sent');
        $recordModel->set('type',"outgoing");
        $recordModel->set('content',$commentText );
        $recordModel->set('media_type',NULL);
        $recordModel->set('datetime',$adb->formatDate($date_var, true));
        $recordModel->save();
	$sendTime = date('h:i A', strtotime($datetime));
        $recordId = $recordModel->getId();
	$viewer->assign('TEXT',$commentText);
	$viewer->assign('TIME', $sendTime);
        $viewer->assign("MESSAGES",$return);
        $viewer->assign("RECORD",$recordId);
        $viewer->view('SaveMessage.tpl', $moduleName);
    }
}
