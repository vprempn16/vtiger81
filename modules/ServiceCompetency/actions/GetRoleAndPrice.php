<?php 
class ServiceCompetency_GetRoleAndPrice_Action extends Vtiger_Action_Controller {

    public function process(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->get('module');
        $recordId = $request->get('record');
        $user_id = $request->get('user_id');
        $result =array();
        $que = $adb->pquery("SELECT * FROM vtiger_servicecompetency where consultantname =? AND servicename=?",array($user_id,$recordId));
        $roles = array("Not Started"=>"notstarted","Learner"=>"learner","Implementer"=>"implementer","Reviewer"=>"reviewer","Project Manager"=>"projectmanager");
        if($adb->num_rows($que) > 0){
            $consultantrole = $adb->query_result($que,0,'consultantrole');
            $user_role = $roles[$consultantrole];
            if($user_role != ''){
                $sql = $adb->pquery("SELECT * FROM atom_role_pricing where meta_key =?",array($user_role));
                if($adb->num_rows($sql) > 0){
                    $price = $adb->query_result($sql,0,'meta_value');
                    $result = array('selling_price'=>$price,'consultantrole'=>$consultantrole);
                }
            }

        }
        $response = new Vtiger_Response();
        $response->setResult([
                'success' => true,
                'details' => json_encode($result),
        ]);
        $response->emit();

    }
}



?>
