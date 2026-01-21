<?php 
class Settings_AtomsVariant_LicenseManager_Model extends Vtiger_Base_Model{

    public function getLicenseViewUrl() {
        global $site_URL;
        return $site_URL.'index.php?module=AtomsVariant&parent=Settings&view=LicenseManagerEdit';
    }

    public function apiCall($license_key,$action) {
        if($license_key != '' && $action != ''){
            global $site_URL;
            $url = 'https://demo.gamma.atomlines.com/wp/wpstore/wp-json/atomlicense-manageraddon/v1/' .$action. '?license_key=' . urlencode($license_key).'&site_Url='.urlencode($site_URL);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
            ]);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                return [
                    'status' => false,
                    'error' => curl_error($ch),
                ];
            }
            curl_close($ch);
            return json_decode($response, true);
        }else{
            return ["License key or Action empty"];
        }
    }
    public function getRecordDetails($id=""){
        global $adb;
        $columns = Array("cmtmention_license_key");
        $return = Array();

        $result = $adb->pquery("SELECT * FROM atom_license_manager",array());
        $num_rows = $adb->num_rows($result);
        if($num_rows > 0){
            for($i=0;$i<$num_rows; $i++){
                $meta_key = $adb->query_result($result,$i,'meta_key');
                $meta_value = $adb->query_result($result,$i,'meta_value');
                $return[$meta_key] =  $meta_value;
            }
        }
        return $return;

    }
    public function ActivateSettings(){
         include_once 'modules/AtomsVariant/AtomsVariantCustomFile.php';
        $AtomsVariantCustom = new AtomsVariantCustom();
        $AtomsVariantCustom->SettingsLink();
        $AtomsVariantCustom->createCustomTables(); 
        $AtomsVariantCustom->registerEventHandler();
    }
    public function DeactivateSettings(){
         include_once 'modules/AtomsVariant/AtomsVariantCustomFile.php';
        $AtomsVariantCustom = new AtomsVariantCustom();
        $AtomsVariantCustom->removeHeaderJsAndTypes();
        $AtomsVariantCustom->removeSettingsLink(); 
        $AtomsVariantCustom->unregisterEventHandler();
    }
}

?>

