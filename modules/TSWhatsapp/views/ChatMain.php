
<?php 
class TSWhatsapp_ChatMain_View extends Vtiger_Index_View {

    function process(Vtiger_Request $request) {
        global $adb, $current_user,$site_URL;

        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $recordid = $request->get('recordid');
        $related_module = getSalesEntityType($recordid);
        $Cont_details = Vtiger_Record_Model::getInstanceById($recordid, $related_module);
        $module_relation = true;

        if($module_relation){
            $result = $adb->pquery("SELECT * FROM `vtiger_whatsappintegration`
                INNER JOIN `vtiger_crmentity` ON vtiger_whatsappintegration.whatsappintegrationid = vtiger_crmentity.crmid
                AND contactid = ?", [$recordid]);
            $return = [];
            $last_module_relation = null;
	     $groupedMessages = [];

            if ($adb->num_rows($result) > 0) {
                    while ($row = $adb->fetch_array($result)) {
                            $createdTime = $row['createdtime'];
                            $dayLabel = $this->formatChatDate($createdTime);

                            $groupedMessages[$dayLabel][] = [
                                    'id' => $row['crmid'],
                                    'title' => $row['title'],
                                    'comment' => $row['comment'],
                                    'contactid' => $row['contactid'],
                                    'related_crmid' => $row['related_crmid'],
                                    'type' => $row['type'],
                                    'time' => date('h:i A', strtotime($createdTime)),
                                    'file_path' => $site_URL . $row['file_path'],
                            ];
                    }
	    }

	    $relatedRec = $adb->pquery('SELECT DISTINCT module_relation  FROM `vtiger_whatsappintegration` WHERE `contactid` = ?',array( $recordid));
            $relatedRecords = [];

            $recordModel = Vtiger_Record_Model::getInstanceById($recordid, 'Contacts');
            $viewer->assign("LAST_MODULE_RELATION", $last_module_relation);
            $viewer->assign("MODULE_RELATION", $last_module_relation);
            $viewer->assign("REL_RECORDS", $relatedRecords);

            $viewer->assign("MESSAGES", $groupedMessages);

            $viewer->assign("LABEL",$Cont_details->getData()['firstname']." " .$Cont_details->getData()['lastname']);
        }else{
            $viewer->assign("LABEL",$Cont_details->getData()['firstname']." " .$Cont_details->getData()['lastname']);
        }
        $viewer->assign("RECORD", $recordid);

        $viewer->view('ChatMain.tpl', $moduleName);
    }
	 public function formatChatDate($timestamp){
            $msgDate = strtotime(date('Y-m-d', strtotime($timestamp)));
            $today = strtotime(date('Y-m-d'));
            $yesterday = strtotime('-1 day', $today);

            if ($msgDate === $today) {
                    return 'Today';
            } elseif ($msgDate === $yesterday) {
                    return 'Yesterday';
            } else {
                    return date('d-m-Y', $msgDate);
            }
    }
}
?>
