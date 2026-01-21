<?php
class BOM extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "vtiger_bom";
	var $table_index= 'bomid';
	var $tab_name = Array('vtiger_crmentity','vtiger_bom','vtiger_bomcf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_bom'=>'bomid','vtiger_bomcf'=>'bomid','vtiger_inventoryproductrel'=>'id');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_bomcf', 'bomid');
	var $entity_table = "vtiger_crmentity";

	var $billadr_table = "";

	var $object_name = "BOM";

	var $new_schema = true;

	var $update_product_array = Array();

	var $column_fields = Array();

	var $sortby_fields = Array('subject','smownerid','accountname','lastname');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				// Module Sequence Numbering
				//'Order No'=>Array('crmentity'=>'crmid'),
				//'BOM No'=>Array('bom','bom_no'),
				// END
				//'Subject'=>Array('bom'=>'subject'),
				'Account Name'=>Array('account'=>'accountid'),
				
				'Total'=>Array('bom'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid'),
			
				);

	var $list_fields_name = Array(
				        'BOM No'=>'bom_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
					    'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				'BOM No'=>Array('bom'=>'bom_no'),
				'Subject'=>Array('bom'=>'subject'),
				'Account Name'=>Array('account'=>'accountid'),
				);

	var $search_fields_name = Array(
					'BOM No'=>'bom_no',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				      );

	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("account_id"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'subject';
	var $default_sort_order = 'ASC';
	//var $groupTable = Array('vtiger_sogrouprelation','bomid');

	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime', 'assigned_user_id','quantity', 'listprice', 'productid');
	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// For workflows update field tasks is deleted all the lineitems.
	var $isLineItemUpdate = true;

	/** Constructor Function for BOM class
	 *  This function creates an instance of LoggerManager class using getLogger method
	 *  creates an instance for PearDatabase class and get values for column_fields array of BOM class.
	 */
        function __construct() {
            $this->log =Logger::getLogger('BOM');
            $this->db = PearDatabase::getInstance();
            $this->column_fields = getColumnFields('BOM');
        }
	function BOM() {
            self::__construct();
	}

	function save_module($module)
	{
		/* $_REQUEST['REQUEST_FROM_WS'] is set from webservices script.
		 * Depending on $_REQUEST['totalProductCount'] value inserting line items into DB.
		 * This should be done by webservices, not be normal save of Inventory record.
		 * So unsetting the value $_REQUEST['totalProductCount'] through check point
		 */
		if (isset($_REQUEST['REQUEST_FROM_WS']) && $_REQUEST['REQUEST_FROM_WS']) {
			unset($_REQUEST['totalProductCount']);
		}

		$_REQUEST['ajxaction'] = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
		//in ajax save we should not call this function, because this will delete all the existing product values
		if($_REQUEST['action'] != 'BOMAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW'
				&& $_REQUEST['action'] != 'MassEditSave' && $_REQUEST['action'] != 'ProcessDuplicates'
				&& $_REQUEST['action'] != 'SaveAjax' && $this->isLineItemUpdate != false) {
			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'BOM');
		}

		// Update the currency id and the conversion rate for the sales order
		$update_query = "update vtiger_bom set currency_id=?, conversion_rate=? where bomid=?";
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$this->db->pquery($update_query, $update_params);
	}
	function vtlib_handler($moduleName, $eventType) {
		if ($moduleName == 'BOM') {
			$db = PearDatabase::getInstance();
			include_once 'modules/BOM/BOMCustom.php';
			$BOMCustom = new BOMCustom();
			if ($eventType == 'module.disabled') {
				$BOMCustom->postDisable();
			} else if ($eventType == 'module.enabled') {
				$BOMCustom->postEnable();
			} else if( $eventType == 'module.preuninstall' ) {
				$BOMCustom->postDisable();
			} else if( $eventType == 'module.postinstall' ) {
				$BOMCustom->postInstall();
			} else if( $eventType == 'module.postupdate' ) {
				$BOMCustom->postUpdate();
			}
		}
	}
}

?>
