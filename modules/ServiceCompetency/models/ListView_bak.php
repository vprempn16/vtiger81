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
 * Vtiger ListView Model Class
 */
class ServiceCompetency_ListView_Model extends Vtiger_Base_Model {
        public static function getSortParamsSession($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
            }
    public function getSideBarLinks($linkParams) {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $moduleLinks = $this->getModule()->getSideBarLinks($linkParams);

        $listLinkTypes = array('LISTVIEWSIDEBARLINK', 'LISTVIEWSIDEBARWIDGET');
        $listLinks = Vtiger_Link_Model::getAllByType($this->getModule()->getId(), $listLinkTypes);

        if($listLinks['LISTVIEWSIDEBARLINK']) {
            foreach($listLinks['LISTVIEWSIDEBARLINK'] as $link) {
                $moduleLinks['SIDEBARLINK'][] = $link;
            }
        }

        if($listLinks['LISTVIEWSIDEBARWIDGET']) {
            foreach($listLinks['LISTVIEWSIDEBARWIDGET'] as $link) {
                $moduleLinks['SIDEBARWIDGET'][] = $link;
            }
        }

        return $moduleLinks;
    }
    public function getModule() {
        return $this->get('module');

    }
     public function getListViewMassActions($linkParams) {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $moduleModel = $this->getModule();

        $linkTypes = array('LISTVIEWMASSACTION');
        $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);


        $massActionLinks = array();
        if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_EDIT',
                'linkurl' => 'javascript:Vtiger_List_Js.triggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");',
                'linkicon' => ''
            );
        }
        if($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DELETE',
                'linkurl' => 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");',
                'linkicon' => ''
            );
        }

        $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
        if($moduleModel->isCommentEnabled() && $modCommentsModel->isPermitted('CreateView')) {
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_ADD_COMMENT',
                'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showAddCommentForm',
                'linkicon' => ''
            );
        }

        foreach($massActionLinks as $massActionLink) {
            $links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        return $links;
    }

    public function getAdvancedLinks(){
        $moduleModel = $this->getModule();
        $createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'CreateView');
        $advancedLinks = array();
        $importPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Import');
        if($importPermission && $createPermission) {
            $advancedLinks[] = array(
                            'linktype' => 'LISTVIEW',
                            'linklabel' => 'LBL_IMPORT',
                            'linkurl' => $moduleModel->getImportUrl(),
                            'linkicon' => ''
            );
        }

        $duplicatePermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'DuplicatesHandling');
        $editPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView');
        if($duplicatePermission && $editPermission) {
            $advancedLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_FIND_DUPLICATES',
                'linkurl' => 'Javascript:Vtiger_List_Js.showDuplicateSearchForm("index.php?module='.$moduleModel->getName().
                                '&view=MassActionAjax&mode=showDuplicatesSearchForm")',
                'linkicon' => ''
            );
        }

        $exportPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'Export');
        if($exportPermission) {
            $advancedLinks[] = array(
                    'linktype' => 'LISTVIEW',
                    'linklabel' => 'LBL_EXPORT',
                    'linkurl' => 'javascript:Vtiger_List_Js.triggerExportAction("'.$this->getModule()->getExportUrl().'")',
                    'linkicon' => ''
                );
        }

        return $advancedLinks;
    }
    public function getSettingLinks() {
        return $this->getModule()->getSettingLinks();
    }
     public function getBasicLinks(){
        $basicLinks = array();
        $moduleModel = $this->getModule();
        $createPermission = Users_Privileges_Model::isPermitted($moduleModel->getName(), 'CreateView');
        if($createPermission) {
            $basicLinks[] = array(
                    'linktype' => 'LISTVIEWBASIC',
                    'linklabel' => 'LBL_ADD_RECORD',
                    'linkurl' => $moduleModel->getCreateRecordUrl(),
                    'linkicon' => ''
            );
        }
        return $basicLinks;
    }
      public function getListViewLinks($linkParams) {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $moduleModel = $this->getModule();

        $linkTypes = array('LISTVIEWBASIC', 'LISTVIEW', 'LISTVIEWSETTING');
        $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);

        $basicLinks = $this->getBasicLinks();

        foreach($basicLinks as $basicLink) {
            $links['LISTVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
        }

        $advancedLinks = $this->getAdvancedLinks();

        foreach($advancedLinks as $advancedLink) {
            $links['LISTVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($advancedLink);
        }

        if($currentUserModel->isAdminUser()) {

            $settingsLinks = $this->getSettingLinks();
            foreach($settingsLinks as $settingsLink) {
                $links['LISTVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
            }
        }

        return $links;
    }
    public function getListViewHeaders() {
        $listViewContoller = $this->get('listview_controller');
        $module = $this->getModule();
        $headerFieldModels = array();
        $headerFields = $listViewContoller->getListViewHeaderFields();
        foreach($headerFields as $fieldName => $webserviceField) {
            if($webserviceField && !in_array($webserviceField->getPresence(), array(0,2))) continue;
            if($webserviceField && isset($webserviceField->parentReferenceField) && !in_array($webserviceField->parentReferenceField->getPresence(), array(0,2))){
                continue;
            }
            if($webserviceField->getDisplayType() == '6') continue;
            // check if the field is reference field
            preg_match('/(\w+) ; \((\w+)\) (\w+)/', $fieldName, $matches);
            if(php7_count($matches) > 0) {
                list($full, $referenceParentField, $referenceModule, $referenceFieldName) = $matches;
                $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModule);
                $referenceFieldModel = Vtiger_Field_Model::getInstance($referenceFieldName, $referenceModuleModel);
                $referenceFieldModel->set('webserviceField', $webserviceField);
                // added tp use in list view to see the title, for reference field rawdata key is different than the actual field
                // eg: in rawdata its account_idcf_2342 (raw column name used in querygenerator), actual field name (account_id ;(Accounts) cf_2342)
                // When generating the title we use rawdata and from field model we have no way to find querygenrator raw column name.

                $referenceFieldModel->set('listViewRawFieldName', $referenceParentField.$referenceFieldName);

                // this is added for picklist colorizer (picklistColorMap.tpl), for fetching picklist colors we need the actual field name of the picklist
                $referenceFieldModel->set('_name', $referenceFieldName);
                $headerFieldModels[$fieldName] = $referenceFieldModel->set('name', $fieldName); // resetting the fieldname as we use it to fetch the value from that name
                $matches=null;
            } else {
                $fieldInstance = Vtiger_Field_Model::getInstance($fieldName,$module);
                $fieldInstance->set('listViewRawFieldName', $fieldInstance->get('column'));
                $headerFieldModels[$fieldName] = $fieldInstance;
            }
        }
        return $headerFieldModels;
    }
    public function getListViewEntriesForPopup($pagingModel){
      $db = PearDatabase::getInstance();
        $moduleName = $this->getModule()->get('name');
        $moduleFocus = CRMEntity::getInstance($moduleName);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $queryGenerator = $this->get('query_generator');
        $listViewContoller = $this->get('listview_controller');

         $searchParams = $this->get('search_params');
        if(empty($searchParams)) {
            $searchParams = array();
        }
        $glue = "";
        if(php7_count($queryGenerator->getWhereFields()) > 0 && (php7_count($searchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);

        $searchKey = $this->get('search_key');
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if(!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
        }

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(!empty($orderBy)){
            $queryGenerator = $this->get('query_generator');
            $fieldModels = $queryGenerator->getModuleFields();
            $orderByFieldModel = $fieldModels[$orderBy];
            if($orderByFieldModel && ($orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE ||
                    $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::OWNER_TYPE)){
                $queryGenerator->addWhereField($orderBy);
            }
        }
        $serviceId = $this->get('serviceid');
        $salesOrderId = $this->get('src_record');
     $listQuery = $this->getCustomQueryForPopup($serviceId,$salesOrderId);
    $sourceModule = $this->get('src_module');
        if(!empty($sourceModule)) {
            if(method_exists($moduleModel, 'getQueryByModuleField')) {
                $overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery,$this->get('relationId'));
                if(!empty($overrideQuery)) {
                    $listQuery = $overrideQuery;
                }
            }
        }

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $paramArray = array();

        if(!empty($orderBy) && $orderByFieldModel) {
            if($orderBy == 'roleid' && $moduleName == 'Users'){
                $listQuery .= ' ORDER BY vtiger_role.rolename '.' '. $sortOrder;
            } else {
                $listQuery .= ' ORDER BY '.$queryGenerator->getOrderByColumn($orderBy).' '.$sortOrder;
            }

            if ($orderBy == 'first_name' && $moduleName == 'Users') {
                $listQuery .= ' , last_name '.' '. $sortOrder .' ,  email1 '. ' '. $sortOrder;
            }
        } else if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            //List view will be displayed on recently created/modified records
            $listQuery .= ' ORDER BY ce.modifiedtime DESC';
        }

        $viewid = ListViewSession::getCurrentView($moduleName);
        if(empty($viewid)) {
            $viewid = $pagingModel->get('viewid');
        }
        $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);
        $listQuery .= " LIMIT ?, ?";
        array_push($paramArray, $startIndex);
        array_push($paramArray, ($pageLimit+1));
       // array_push($paramArray, $serviceId);

        $listResult = $db->pquery($listQuery, $paramArray);
        $listViewRecordModels = array();
        $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $listResult);
         $pagingModel->calculatePageRange($listViewEntries);
          if($db->num_rows($listResult) > $pageLimit){
            array_pop($listViewEntries);
            $pagingModel->set('nextPageExists', true);
        }else{
            $pagingModel->set('nextPageExists', false);
        }

        $index = 0;
        foreach($listViewEntries as $recordId => $record) {
            $rawData = $db->query_result_rowdata($listResult, $index++);
            $record['id'] = $recordId;
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }
        return $listViewRecordModels;
    }
    public function getCustomQueryForPopup($serviceId,$salesOrderId){
        if (!empty($serviceId)) {
            // Get SO start date to determine the month
            global $adb;
            // Ensure we only proceed if a Service is selected
            if (empty($serviceId) || empty($salesOrderId)) {
                return "SELECT sc.servicecompetencyid, sc.consultantname, sc.servicename, sc.consultantrole,
                       u.first_name, u.last_name
                           FROM vtiger_servicecompetency sc
                           INNER JOIN vtiger_crmentity ce ON ce.crmid = sc.servicecompetencyid AND ce.deleted = 0
                           LEFT JOIN vtiger_users u ON u.id = sc.consultantname
                           WHERE 1=0"; // no data
            }

            // 🔹 Get Sales Order start date (to calculate month)
            $soQuery = "SELECT startdate FROM vtiger_salesorder WHERE salesorderid = ?";
            $soResult = $adb->pquery($soQuery, [$salesOrderId]);
            $soStartDate = ($adb->num_rows($soResult) > 0)
                ? $adb->query_result($soResult, 0, 'startdate')
                : date('Y-m-d');

            $soMonth = date('m', strtotime($soStartDate));
            $soYear = date('Y', strtotime($soStartDate));

            // 🔹 Main query to fetch consultants linked to this Service
            //     but excluding consultants who already have >22 tickets in that month
            $query = "
                SELECT *
                    FROM vtiger_servicecompetency sc
                    INNER JOIN vtiger_crmentity ce 
                    ON ce.crmid = sc.servicecompetencyid AND ce.deleted = 0
                    INNER JOIN vtiger_users u 
                    ON u.id = sc.consultantname
                    WHERE sc.servicename = ? 
                    AND sc.consultantname NOT IN (
                            SELECT e2.smownerid 
                            FROM vtiger_troubletickets tt
                            INNER JOIN vtiger_crmentity e2 ON e2.crmid = tt.ticketid AND e2.deleted = 0
                            WHERE MONTH(e2.createdtime) = ? AND YEAR(e2.createdtime) = ?
                            GROUP BY e2.smownerid
                            HAVING COUNT(*) > 22
                            )
                    ";

            // Replace ? with bound params when executing in getListViewEntries
            // (they will be passed to $adb->pquery later)
            $query = $adb->convert2Sql($query, [$serviceId, $soMonth, $soYear]);
            return $query;
        }

    }
    public function getListViewEntries($pagingModel) {
        $db = PearDatabase::getInstance();
        $moduleName = $this->getModule()->get('name');
        $moduleFocus = CRMEntity::getInstance($moduleName);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $queryGenerator = $this->get('query_generator');
        $listViewContoller = $this->get('listview_controller');

         $searchParams = $this->get('search_params');
        if(empty($searchParams)) {
            $searchParams = array();
        }
        $glue = "";
        if(php7_count($queryGenerator->getWhereFields()) > 0 && (php7_count($searchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);

        $searchKey = $this->get('search_key');
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if(!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
        }

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if(!empty($orderBy)){
            $queryGenerator = $this->get('query_generator');
            $fieldModels = $queryGenerator->getModuleFields();
            $orderByFieldModel = $fieldModels[$orderBy];
            if($orderByFieldModel && ($orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE ||
                    $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::OWNER_TYPE)){
                $queryGenerator->addWhereField($orderBy);
            }
        }
        $serviceId = $this->get('serviceid');
        $salesOrderId = $this->get('src_record');
     $listQuery = $this->getQuery($serviceId,$salesOrderId);

        $sourceModule = $this->get('src_module');
        if(!empty($sourceModule)) {
            if(method_exists($moduleModel, 'getQueryByModuleField')) {
                $overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery,$this->get('relationId'));
                if(!empty($overrideQuery)) {
                    $listQuery = $overrideQuery;
                }
            }
        }

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();
        $paramArray = array();

        if(!empty($orderBy) && $orderByFieldModel) {
            if($orderBy == 'roleid' && $moduleName == 'Users'){
                $listQuery .= ' ORDER BY vtiger_role.rolename '.' '. $sortOrder;
            } else {
                $listQuery .= ' ORDER BY '.$queryGenerator->getOrderByColumn($orderBy).' '.$sortOrder;
            }

            if ($orderBy == 'first_name' && $moduleName == 'Users') {
                $listQuery .= ' , last_name '.' '. $sortOrder .' ,  email1 '. ' '. $sortOrder;
            }
        } else if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
            //List view will be displayed on recently created/modified records
            $listQuery .= ' ORDER BY ce.modifiedtime DESC';
        }

        $viewid = ListViewSession::getCurrentView($moduleName);
        if(empty($viewid)) {
            $viewid = $pagingModel->get('viewid');
        }
        $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);
        $listQuery .= " LIMIT ?, ?";
        array_push($paramArray, $startIndex);
        array_push($paramArray, ($pageLimit+1));
       // array_push($paramArray, $serviceId);

        $listResult = $db->pquery($listQuery, $paramArray);
        $listViewRecordModels = array();
        $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $listResult);
         $pagingModel->calculatePageRange($listViewEntries);

        if($db->num_rows($listResult) > $pageLimit){
            array_pop($listViewEntries);
            $pagingModel->set('nextPageExists', true);
        }else{
            $pagingModel->set('nextPageExists', false);
        }

        $index = 0;
        foreach($listViewEntries as $recordId => $record) {
            $rawData = $db->query_result_rowdata($listResult, $index++);
            $record['id'] = $recordId;
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }
        return $listViewRecordModels;
    }
    function getQuery(){
         $queryGenerator = $this->get('query_generator');
        $listQuery = $queryGenerator->getQuery();
        return $listQuery;
    }
     public function isImportEnabled() {
        $linkParams = array('MODULE'=>$this->getModule()->getName(), 'ACTION'=>'LIST');
        $listViewLinks = $this->getListViewLinks($linkParams);
        $listViewActions = $listViewLinks['LISTVIEW'];
        if (is_array($listViewActions)) {
            foreach($listViewActions as $linkAction) {
                if($linkAction->getLabel() == 'LBL_IMPORT'){
                    return true;
                }
            }
        }
        return false;
    }
}
