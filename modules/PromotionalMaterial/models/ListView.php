<?php
/*+***********************************************************************************
 * Partner list: show all Published rows (not only owned), via query filter + access hook.
 *************************************************************************************/

class PromotionalMaterial_ListView_Model extends Vtiger_ListView_Model {

	public function getQuery() {
		$query = parent::getQuery();
		if (PromotionalMaterial::currentUserIsPartnerRole()) {
			if (stripos($query, ' WHERE ') !== false) {
				$query .= " AND vtiger_promotionalmaterial.promotional_status = 'Published' ";
			} else {
				$query .= " WHERE vtiger_promotionalmaterial.promotional_status = 'Published' ";
			}
		}
		return $query;
	}
}
