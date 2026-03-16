<?php 
class WhatsappOptinHandler {

	public function handleEvent($eventName, $entityData) {
		global $adb;

		if ($eventName !== 'vtiger.entity.aftersave') {
			return;
		}

		$module = $entityData->getModuleName();
		if (!in_array($module, ['Contacts', 'Leads'])) {
			return;
		}


		$crmid = $entityData->getId();

		$optin = $entityData->get('whatsapp_optin');
		$optin = ($optin == '1') ? 'YES' : 'NO';

		$phoneData = $this->getWhatsappPhone($entityData);

		if (!$phoneData) {
			return;
		}

		$phone = $phoneData['phone'];
		$phoneField = $phoneData['field'];
		if (empty($phone)) {
			return;
		}

		$res = $adb->pquery(
			"SELECT optin_id FROM sc_whatsapp_optin WHERE crmid=? AND phone=?",
			[$crmid, $phone]
		);

		if ($adb->num_rows($res)) {

			// Update existing
			if ($optin === 'YES') {
				$adb->pquery(
					"UPDATE sc_whatsapp_optin
					SET optin_status='YES',
					revoked_time=NULL
					WHERE crmid=? AND phone=?",
					[$crmid, $phone]
				);
			} else {
				$adb->pquery(
					"UPDATE sc_whatsapp_optin
					SET optin_status='NO',
					revoked_time=NOW()
					WHERE crmid=? AND phone=?",
					[$crmid, $phone]
				);
			}

		} else {

			if ($optin === 'YES') {
				$adb->pquery(
					"INSERT INTO sc_whatsapp_optin
					(crmid, phone, optin_status, optin_source, optin_time)
					VALUES (?,?,?,?,NOW())",
					[$crmid, $phone, 'YES', 'crm_checkbox']
				);
			}
		}
	}
	function getWhatsappPhone($entityData){
		$priorityFields = ['mobile','phone','homephone','otherphone'];

		foreach ($priorityFields as $field) {
			$value = $entityData->get($field);
			if (!empty($value)) {
				return ['field' => $field,'phone' => $this->normalizePhone($value) ];
			}
		}
		return null;
	}
	function normalizePhone($phone) {
		$phone = preg_replace('/\D+/', '', $phone);

		// Example: India default
		if (strlen($phone) === 10) {
			$phone = '91' . $phone;
		}

		return '+' . $phone;
	}
}
?>
