<?php
/*+**********************************************************************************
 * PromotionalMaterialHandler.php
 * WebServices handler for PromotionalMaterial module
 * Allows REST API access to Promotional Material records
 ************************************************************************************/

class PromotionalMaterialHandler extends VTEntityHandler {

	/**
	 * Create a new Promotional Material record via Web Service
	 */
	function create($elementType, $body) {
		global $adb, $current_user;

		$entity = new PromotionalMaterial();
		return parent::create($elementType, $body);
	}

	/**
	 * Retrieve Promotional Material record via Web Service
	 */
	function retrieve($id) {
		global $adb, $current_user;

		$wsEntityId = $id;
		$entity = CRMEntity::getInstance('PromotionalMaterial');

		// Check if record exists
		$result = $adb->pquery(
			'SELECT * FROM vtiger_promotionalmaterial WHERE promotionalmaterialid = ?',
			array($id)
		);

		if ($adb->num_rows($result) == 0) {
			throw new WebServiceException('INVALID_RECORD', 'Record not found');
		}

		// Check permission for Partner
		$this->checkPartnerPermission('read', $id);

		return parent::retrieve($id);
	}

	/**
	 * Update a Promotional Material record via Web Service
	 */
	function update($elementType, $id, $body) {
		global $adb, $current_user;

		$roleRes = $adb->pquery(
			'SELECT roleid FROM vtiger_user2role WHERE userid = ?',
			array($current_user->id)
		);
		$isPartner = ($adb->num_rows($roleRes) > 0 && $adb->query_result($roleRes, 0, 'roleid') === 'H6');

		if ($isPartner) {
			$result = $adb->pquery(
				'SELECT promotional_status FROM vtiger_promotionalmaterial WHERE promotionalmaterialid = ?',
				array($id)
			);
			if ($adb->num_rows($result) > 0) {
				$row = $adb->fetch_array($result);
				if ($row['promotional_status'] === 'Published') {
					throw new WebServiceException(
						'UPDATE_DENIED',
						'Partners cannot update published promotional materials'
					);
				}
			}
		}

		return parent::update($elementType, $id, $body);
	}

	/**
	 * Check if Partner has permission for the record
	 */
	private function checkPartnerPermission($action, $recordId) {
		global $adb, $current_user;

		// Get current user's role
		$userRoleResult = $adb->pquery(
			'SELECT roleid FROM vtiger_user2role WHERE userid = ?',
			array($current_user->id)
		);

		if ($adb->num_rows($userRoleResult) == 0) {
			return;
		}

		$userRole = $adb->fetch_array($userRoleResult);

		// If not Partner role, allow
		if ($userRole['roleid'] !== 'H6') {
			return;
		}

		$result = $adb->pquery(
			'SELECT promotional_status FROM vtiger_promotionalmaterial WHERE promotionalmaterialid = ?',
			array($recordId)
		);

		if ($adb->num_rows($result) > 0) {
			$row = $adb->fetch_array($result);

			if ($action === 'read' && $row['promotional_status'] !== 'Published') {
				throw new WebServiceException(
					'ACCESS_DENIED',
					'Partners can only view published promotional materials'
				);
			}

			if ($action !== 'read') {
				throw new WebServiceException(
					'ACTION_DENIED',
					'Partners can only read promotional materials'
				);
			}
		}
	}
}
?>
