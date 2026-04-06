<?php
/*+**********************************************************************************
 * PromotionalMaterial Module - Vtiger CRM 8.1
 * Allows admins to manage promotional materials with partner notifications and downloads
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';
	
class PromotionalMaterial extends Vtiger_CRMEntity {
	var $log;
        var $db;

	var $table_name = 'vtiger_promotionalmaterial';
	var $table_index = 'promotionalmaterialid';
	var $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = array('vtiger_promotionalmaterialcf', 'promotionalmaterialid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = array('vtiger_crmentity', 'vtiger_promotionalmaterial', 'vtiger_promotionalmaterialcf');

    /**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = array(
		'Title' => array('promotionalmaterial', 'title'),
		'Status' => array('promotionalmaterial', 'promotional_status'),
		'Created Date' => array('crmentity', 'createdtime'),
		'Assigned To' => array('crmentity', 'smownerid')
	);

    var $list_fields_name = array(
		'Title' => 'title',
		'Status' => 'promotional_status',
		'Created Date' => 'createdtime',
		'Assigned To' => 'smownerid'
	);

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_promotionalmaterial' => 'promotionalmaterialid',
		'vtiger_promotionalmaterialcf' => 'promotionalmaterialid'
	);

	
      // Make the field link to detail view
    var $list_link_field = 'title';

    // For Popup listview and UI type support
    var $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Title' => array('promotionalmaterial', 'title'),
        'Description' => array('promotionalmaterial', 'description'),
    );
    var $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Title' => 'title',
        'Description' => 'description',
    );

    // For Popup window record selection
    var $popup_fields = array('title');

    // For Alphabetical search
    var $def_basicsearch_col = 'title';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'title';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = array('title', 'assigned_user_id');

	var $default_order_by = 'title';
	var $default_sort_order = 'ASC';

	/** Partner role id (vtiger_role.roleid) — keep in sync with profile/role setup */
	public static function getPartnerRoleId() {
		return 'H6';
	}

	public static function currentUserIsPartnerRole() {
		global $current_user, $adb;
		if (empty($current_user) || empty($current_user->id)) {
			return false;
		}
		if (!empty($current_user->is_admin) && $current_user->is_admin == 'on') {
			return false;
		}
		$res = $adb->pquery('SELECT roleid FROM vtiger_user2role WHERE userid = ?', array($current_user->id));
		if ($adb->num_rows($res) < 1) {
			return false;
		}
		return $adb->query_result($res, 0, 'roleid') === self::getPartnerRoleId();
	}

	/**
	 * Partners skip private-sharing owner join; list/detail access is constrained by
	 * promotional_status = Published (ListView model + isPermitted).
	 */
	function getNonAdminAccessControlQuery($module, $user, $scope = '') {
		$res = $this->db->pquery('SELECT roleid FROM vtiger_user2role WHERE userid = ?', array($user->id));
		if ($this->db->num_rows($res) > 0 && $this->db->query_result($res, 0, 'roleid') === self::getPartnerRoleId()) {
			return ' ';
		}
		return parent::getNonAdminAccessControlQuery($module, $user, $scope);
	}


	/**
	 * Handle vtlib module events
	 */
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
		include_once 'modules/PromotionalMaterial/PromotionalMaterialCustomFile.php';
		$PromotionalMaterialCustomFile = new PromotionalMaterialCustomFile();
		if ($eventType == 'module.postinstall') {
			$PromotionalMaterialCustomFile->postInstall();
		} else if ($eventType == 'module.enabled') {
			$PromotionalMaterialCustomFile->postEnable();
		} else if ($eventType == 'module.disabled') {
			$PromotionalMaterialCustomFile->postDisable();
			// Module disabled
		} else if ($eventType == 'module.preuninstall') {
			$PromotionalMaterialCustomFile->postDisable();
			// Module uninstallation
		}
	}
	 public function uploadAndSaveFile($id, $module, $file_details, $attachmentType = 'Attachment')
	 {
		 global $log;
		 $log->debug("Entering into uploadAndSaveFile($id,$module) method.");

		 global $adb, $current_user;
		 global $upload_badext;

		 $date_var = date("Y-m-d H:i:s");

		 //to get the owner id
		 $ownerid = $this->column_fields['assigned_user_id'];
		 if (!isset($ownerid) || $ownerid == '')
			 $ownerid = $current_user->id;

		 if (isset($file_details['original_name']) && $file_details['original_name'] != null) {
			 $file_name = $file_details['original_name'];
		 } else {
			 $file_name = $file_details['name'];
		 }

		 // Check 1
		 $save_file = true;
		 //only images are allowed for Image Attachmenttype
		 $mimeType = vtlib_mime_content_type($file_details['tmp_name']);
		 $mimeTypeContents = explode('/', $mimeType);
		 // For contacts and products we are sending attachmentType as value
		 if ($attachmentType == 'Image' || ($file_details['size'] && $mimeTypeContents[0] == 'image')) {
			 $save_file = validateImageFile($file_details);
		 }
		 $log->debug("File Validation status in Check1 save_file => $save_file");
		 if (!$save_file) {
			 return false;
		 }

		 // Check 2
		 $save_file = true;
		 //only images are allowed for these modules
		 if ($module == 'Contacts' || $module == 'Products') {
			 $save_file = validateImageFile($file_details);
		 }
		 $log->debug("File Validation status in Check2 save_file => $save_file");
		 $binFile = sanitizeUploadFileName($file_name, $upload_badext);

		 $current_id = $adb->getUniqueID("vtiger_crmentity");

		 $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
		 $filetype = $file_details['type'];
		 $filetmp_name = $file_details['tmp_name'];

		 //get the file path inwhich folder we want to upload the file
		 $upload_file_path = decideFilePath();

		 // upload the file in server
		 $encryptFileName = Vtiger_Util_Helper::getEncryptedFileName($binFile);
		 $upload_status = copy($filetmp_name, $upload_file_path . $current_id . "_" . $encryptFileName);
		 // temporary file will be deleted at the end of request
		 $log->debug("Upload status of file => $upload_status");
		 if ($save_file && $upload_status == 'true') {
			 if($attachmentType != 'Image' && $this->mode == 'edit') {
				 //Only one Attachment per entity delete previous attachments
				 $res = $adb->pquery('SELECT vtiger_seattachmentsrel.attachmentsid FROM vtiger_seattachmentsrel
					 INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.attachmentsid AND vtiger_crmentity.setype = ?
					 WHERE vtiger_seattachmentsrel.crmid = ?',array($module.' Attachment',$id));
				 $oldAttachmentIds = array();
				 for($attachItr = 0;$attachItr < $adb->num_rows($res);$attachItr++) {
					 $oldAttachmentIds[] = $adb->query_result($res,$attachItr,'attachmentsid');
				 }
				 if(php7_count($oldAttachmentIds)) {
					 $adb->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
					 //TODO : revisit to delete actual file and attachment entry,as we need to see the deleted file in the history when its changed
					 //$adb->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
					 //$adb->pquery('DELETE FROM vtiger_crmentity WHERE crmid IN ('.generateQuestionMarks($oldAttachmentIds).')',$oldAttachmentIds);
				 }
			 }
			 //Add entry to crmentity
			 $sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
			 $params1 = array($current_id, $current_user->id, $ownerid, $module." ".$attachmentType, $this->column_fields['description'], $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
			 $adb->pquery($sql1, $params1);
			 //Add entry to attachments
			 $sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path, storedname) values(?, ?, ?, ?, ?, ?)";
			 $params2 = array($current_id, $filename, $this->column_fields['description'], $filetype, $upload_file_path, $encryptFileName);
			 $adb->pquery($sql2, $params2);
			 //Add relation
			 $sql3 = 'INSERT INTO vtiger_seattachmentsrel VALUES(?,?)';
			 $params3 = array($id, $current_id);
			 $adb->pquery($sql3, $params3);
			 $log->debug("File uploaded successfully with id => $current_id");
			 //additionally added for update promotional document
			 $sql4 = 'UPDATE vtiger_promotionalmaterial SET promotional_document = ? WHERE vtiger_promotionalmaterial.promotionalmaterialid = ?';
			 $params4 = array($current_id, $id);
			 $adb->pquery($sql4, $params4);
			 return $current_id;
		 } else {
			 //failed to upload file
			 $log->debug('File upload failed');
			 return false;
		 }
	 }
}
?>
