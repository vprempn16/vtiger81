<?php 
/* +***********************************************************************************
 * Custom Popup View for ServiceCompetency Module
 * Shows consultants related to a service, excluding those with >22 tickets in the month.
 * *********************************************************************************** */

class ServiceCompetency_Popup_View extends Vtiger_Popup_View {

    function process(Vtiger_Request $request) {
        global $adb;

        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);

        $serviceId = $request->get('service_id'); // selected service in SO line item
        $salesOrderId = $request->get('src_record'); // current SO record ID

        // Default popup header
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('SOURCE_MODULE', $request->get('src_module'));
        $viewer->assign('SOURCE_RECORD', $salesOrderId);
        $this->initializeListViewContents($request, $viewer);

        $records = [];


        $viewer->assign('RECORDS', $records);
        $viewer->view('Popup.tpl', $moduleName);
    }
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
        global $adb;
        $moduleName = $this->getModule($request);
        $cvId = $request->get('cvid');
        $pageNumber = $request->get('page');
        $orderBy = $request->get('orderby');
        $sortOrder = $request->get('sortorder');
        $sourceModule = $request->get('src_module');
        $sourceField = $request->get('src_field');
        $sourceRecord = $request->get('src_record');
        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $currencyId = $request->get('currency_id');
        $relatedParentModule = $request->get('related_parent_module');
        $relatedParentId = $request->get('related_parent_id');
        $serviceId = $request->get('service_id');
        $startDate = $request->get('startDate');
        $manday = $request->get('manday');
        $endDate = $request->get('endDate');
                $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                $searchParams=$request->get('search_params');

        $relationId = $request->get('relationId');

        //To handle special operation when selecting record from Popup
        $getUrl = $request->get('get_url');
        $autoFillModule = $moduleModel->getAutoFillModule($moduleName);

        //Check whether the request is in multi select mode
        $multiSelectMode = $request->get('multi_select');
        if(empty($multiSelectMode)) {
            $multiSelectMode = false;
        }

        if(empty($getUrl) && !empty($sourceField) && !empty($autoFillModule) && !$multiSelectMode) {
            $getUrl = 'getParentPopupContentsUrl';
        }
         if(empty($cvId)) {
            $cvId = '0';
        }
        if(empty ($pageNumber)){
            $pageNumber = '1';
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);

        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);

        $isRecordExists = Vtiger_Util_Helper::checkRecordExistance($relatedParentId);

        if($isRecordExists) {
            $relatedParentModule = '';
            $relatedParentId = '';
        } else if($isRecordExists === NULL) {
            $relatedParentModule = '';
            $relatedParentId = '';
        }

        if(!empty($relatedParentModule) && !empty($relatedParentId)) {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($relatedParentId, $relatedParentModule);
            $listViewModel = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, $label,$relationId);
            $searchModuleModel = $listViewModel->getRelatedModuleModel();
        }else{
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName);
            $searchModuleModel = $listViewModel->getModule();
        }
                if($moduleName == 'Documents' && $sourceModule == 'Emails') {
            $listViewModel->extendPopupFields(array('filename'=>'filename'));
        }
        if(!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder', $sortOrder);
        }
        if(!empty($sourceModule)) {
            $listViewModel->set('src_module', $sourceModule);
            $listViewModel->set('src_field', $sourceField);
            $listViewModel->set('src_record', $sourceRecord);
        }
        if((!empty($searchKey)) && (!empty($searchValue)))  {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if((!empty($serviceId)))  {
            $listViewModel->set('serviceid', $serviceId);
        }
        if((!empty($serviceId)))  {
            $listViewModel->set('startdate', $startDate);
            $listViewModel->set('enddate', $endDate);
            $listViewModel->set('manday', $manday);
        }
        $listViewModel->set('relationId',$relationId);

        if(!empty($searchParams)){
            $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParams, $searchModuleModel);
            $listViewModel->set('search_params',$transformedSearchParams);
        }
          $this->listViewHeaders = $listViewModel->getListViewHeaders();
            $this->listViewEntries = $listViewModel->getListViewEntriesForPopup($pagingModel);
            //echo"<pre>";print_r([$this->listViewEntries]);die('#');
        //$this->listViewEntries = $this->getListViewEntries($pagingModel);
        if(empty($searchParams)) {
                    $searchParams = array();
                }
               //To make smarty to get the details easily accesible
                foreach($searchParams as $fieldListGroup){
                    foreach($fieldListGroup as $fieldSearchInfo){
                        $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                        $fieldSearchInfo['fieldName'] = $fieldName = $fieldSearchInfo[0];
                        $fieldSearchInfo['comparator'] = $fieldSearchInfo[1];
                        $searchParams[$fieldName] = $fieldSearchInfo;
                    }
        }   
          $noOfEntries = php7_count($this->listViewEntries);

        if(empty($sortOrder)){
            $sortOrder = "ASC";
        }
        if($sortOrder == "ASC"){
            $nextSortOrder = "DESC";
            $sortImage = "icon-chevron-down";
            $faSortImage = "fa-sort-desc";
        }else{
            $nextSortOrder = "ASC";
            $sortImage = "icon-chevron-up";
            $faSortImage = "fa-sort-asc";
        }

        $viewer->assign('MODULE', $moduleName);
                $viewer->assign('RELATED_MODULE', $moduleName);
        $viewer->assign('MODULE_NAME',$moduleName);

        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SOURCE_FIELD', $sourceField);
        $viewer->assign('SOURCE_RECORD', $sourceRecord);
        $viewer->assign('RELATED_PARENT_MODULE', $relatedParentModule);
        $viewer->assign('RELATED_PARENT_ID', $relatedParentId);

        $viewer->assign('SEARCH_KEY', $searchKey);
        $viewer->assign('SEARCH_VALUE', $searchValue);

        $viewer->assign('RELATION_ID',$relationId);
        $viewer->assign('ORDER_BY',$orderBy);
        $viewer->assign('SORT_ORDER',$sortOrder);
        $viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
        $viewer->assign('SORT_IMAGE',$sortImage);
        $viewer->assign('FASORT_IMAGE',$faSortImage);
        $viewer->assign('GETURL', $getUrl);
        $viewer->assign('CURRENCY_ID', $currencyId);

        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER',$pageNumber);
        $viewer->assign('LISTVIEW_ENTRIES_COUNT',$noOfEntries);
        $viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        $viewer->assign('SEARCH_DETAILS', $searchParams);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('VIEW', $request->get('view'));

        if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
            if(!$this->listViewCount){
                $this->listViewCount = $listViewModel->getListViewCount();
            }
            $totalCount = $this->listViewCount;
            $pageLimit = $pagingModel->getPageLimit();
            $pageCount = ceil((int) $totalCount / (int) $pageLimit);

            if($pageCount == 0){
                $pageCount = 1;
            }
            $viewer->assign('PAGE_COUNT', $pageCount);
            $viewer->assign('LISTVIEW_COUNT', $totalCount);
        }

        $viewer->assign('MULTI_SELECT', $multiSelectMode);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
    
        
    }

    function getEntries( $serviceId ){
        if (!empty($serviceId)) {
            // Get SO start date to determine the month
            $soQuery = "SELECT startdate FROM vtiger_salesorder WHERE salesorderid=?";
            $soResult = $adb->pquery($soQuery, [$salesOrderId]);
            $soStartDate = ($adb->num_rows($soResult) > 0) ? $adb->query_result($soResult, 0, 'startdate') : date('Y-m-d');

            $month = date('Y-m', strtotime($soStartDate));
/*
            // Fetch consultants linked to this service
            $query = "
                SELECT sc.servicecompetencyid, sc.consultantname, sc.servicename, sc.consultantrole, u.first_name, u.last_name
                FROM vtiger_servicecompetency sc
                INNER JOIN vtiger_crmentity ce ON ce.crmid = sc.servicecompetencyid AND ce.deleted = 0
                LEFT JOIN vtiger_users u ON u.id = sc.consultantname
                WHERE sc.servicename = ?
                AND sc.consultantname NOT IN (
                        SELECT smownerid 
                        FROM vtiger_troubletickets tt
                        INNER JOIN vtiger_crmentity ctt ON ctt.crmid = tt.ticketid
                        WHERE ctt.deleted = 0 AND DATE_FORMAT(ctt.createdtime, '%Y-%m') = ?
                        GROUP BY smownerid
                        HAVING COUNT(*) > 22
                        )
                ";
*/

$adb->setDebug(true);
            $query = "SELECT 
    sc.servicecompetencyid, 
    sc.consultantname, 
    sc.servicename, 
    sc.consultantrole, 
    u.first_name, 
    u.last_name
FROM vtiger_servicecompetency sc
INNER JOIN vtiger_crmentity ce 
    ON ce.crmid = sc.servicecompetencyid 
    AND ce.deleted = 0
LEFT JOIN vtiger_users u 
    ON u.id = sc.consultantname
WHERE sc.servicename = ?
AND sc.consultantname NOT IN (
    SELECT 
        tt.smownerid
    FROM vtiger_troubletickets tt
    INNER JOIN vtiger_crmentity ctt 
        ON ctt.crmid = tt.ticketid 
        AND ctt.deleted = 0
    LEFT JOIN vtiger_user_workingdays wd 
        ON wd.userid = tt.smownerid
    WHERE DATE_FORMAT(ctt.createdtime, '%Y-%m') = ?
    GROUP BY tt.smownerid, wd.working_days
    HAVING COUNT(*) > COALESCE(wd.working_days, 22)
);
";

            $result = $adb->pquery($query, [$serviceId, $month]);
            $count = $adb->num_rows($result);
$adb->setDebug(false);

            for ($i = 0; $i < $count; $i++) {
                $record = [];
                $record['id'] = $adb->query_result($result, $i, 'servicecompetencyid');
                $record['consultant_userid'] = $adb->query_result($result, $i, 'consultantname');
                $record['consultant_role'] = $adb->query_result($result, $i, 'consultantrole');
                $firstName = $adb->query_result($result, $i, 'first_name');
                $lastName = $adb->query_result($result, $i, 'last_name');
                $record['name'] = trim($firstName . ' ' . $lastName);
                $records[] = $record;
            }
        }
    }
    /**
     * Format data for popup return (called via JS callback)
     */
    public function getRecordStructure(Vtiger_Request $request) {
        $recordId = $request->get('record');
        if (!$recordId) return [];

        global $adb;
        $query = "SELECT sc.servicecompetencyid, sc.consultantname, sc.consultantrole, u.first_name, u.last_name
                  FROM vtiger_servicecompetency sc
                  LEFT JOIN vtiger_users u ON u.id = sc.consultantname
                  WHERE sc.servicecompetencyid=?";
        $result = $adb->pquery($query, [$recordId]);
        if ($adb->num_rows($result) > 0) {
            $firstName = $adb->query_result($result, 0, 'first_name');
            $lastName = $adb->query_result($result, 0, 'last_name');
            $return = [
                'id' => $adb->query_result($result, 0, 'servicecompetencyid'),
                'name' => trim($firstName . ' ' . $lastName),
                'consultant_userid' => $adb->query_result($result, 0, 'consultantname'),
                'consultant_role' => $adb->query_result($result, 0, 'consultantrole')
            ];
            echo json_encode(['success' => true, 'result' => $return]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}












?>
