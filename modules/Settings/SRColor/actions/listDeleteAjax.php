<?php

class Settings_SRColor_listDeleteAjax_Action extends Settings_Vtiger_Basic_Action {

        public function process(Vtiger_Request $request) {
                global $current_user,$adb;
                $response = new Vtiger_Response();
                $id = $request->get('id');
                try{
                        $adb->pquery("DELETE FROM sr_color_configuration WHERE id=?",array($id));
                        $response->setResult('OK');
                }catch(Exception $e) {
                        $response->setError($e->getCode(), $e->getMessage());
                }
                $response->emit();
        }
}
?>
