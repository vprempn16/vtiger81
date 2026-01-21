
<?php 
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 */
class TSWhatsapp_Record_Model extends Vtiger_Record_Model {
	public function getAllContacts(){
		global $adb;
		$contacts = [];	
		$query = "SELECT contactid,firstname, lastname FROM vtiger_contactdetails
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			WHERE vtiger_crmentity.deleted = 0"; 
		$result = $adb->pquery($query, []); 
		while ($row = $adb->fetch_array($result)) {
			$contacts[] = $row;
		}
		return $contacts;
	}
}
