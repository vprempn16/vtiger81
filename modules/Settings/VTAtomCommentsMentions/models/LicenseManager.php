<?php

class Settings_VTAtomCommentsMentions_LicenseManager_Model extends Vtiger_Base_Model
{
	private $license_key;
	private $url = 'https://demo.gamma.atomlines.com/wp/wpstore/wp-json/atomlicense-manageraddon/v1/';
	public function getLicenseKey()
	{
		return $this->license_key;
	}
	public function getLicenseViewUrl()
	{
		global $site_URL;
		return $site_URL . 'index.php?module=VTAtomCommentsMentions&parent=Settings&view=LicenseManagerEdit';
	}
	public function apiCall($action = '')
	{
		$license_key = $this->license_key;
		if ($license_key != '' && $action != '') {
			global $site_URL;
			$url = $this->url . $action . '?license_key=' . urlencode($license_key) . '&site_Url=' . urlencode($site_URL);
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
		}
		else {
			return ["License key or Action empty"];
		}
	}
	public function getInstance($request)
	{
		global $adb;
		$moduleName = $request->getModule();
		if ($moduleName) {
			$result = $adb->pquery("SELECT meta_value FROM atom_license_manager where meta_key=? ", array($moduleName));
			$num_rows = $adb->num_rows($result);
			if ($num_rows > 0) {
				$meta_value = $adb->query_result($result, 0, 'meta_value');
			}
			$license_key = Vtiger_Functions::fromProtectedText($meta_value);
			$this->license_key = $license_key;
		}
	}
	public function ActivateSettings()
	{
		include_once 'modules/VTAtomCommentsMentions/VTAtomCommentsMentionsCustomFile.php';
		$VTAtomCommentsMentionsCustom = new VTAtomCommentsMentionsCustom();
		$VTAtomCommentsMentionsCustom->SettingsLink();
		$VTAtomCommentsMentionsCustom->createCustomTables();
		$VTAtomCommentsMentionsCustom->registerEventHandler();
	}
	public function DeactivateSettings()
	{
		include_once 'modules/VTAtomCommentsMentions/VTAtomCommentsMentionsCustomFile.php';
		$VTAtomCommentsMentionsCustom = new VTAtomCommentsMentionsCustom();
		$VTAtomCommentsMentionsCustom->removeHeaderJsAndTypes();
		$VTAtomCommentsMentionsCustom->removeSettingsLink();
		$VTAtomCommentsMentionsCustom->unregisterEventHandler();
	}
}

?>

