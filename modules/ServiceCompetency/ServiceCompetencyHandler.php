<?php 
class ServiceCompetencyHandler{
    function handleEvent($eventName, $entityData) {
        global $adb,$currentUser;
        $recordid = $entityData->getId();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $entityDelta = new VTEntityDelta();
        $moduleName = $entityData->getModuleName();
        if ($eventName == 'vtiger.entity.beforesave') {
            if($moduleName == 'ServiceCompetency'){  
                $roleMapping = array(1 =>'Not Started',2 => 'Learner',3 => 'Implementer',4 =>'Reviewer',5 => 'Project Manager'); 
                $recordId = $entityData->getId();
                $consultantrating = $entityData->get('consultantrating');
                $consultantrole = $roleMapping[$consultantrating];
                if($consultantrole != ''){
                    $entityData->set('consultantrole',$consultantrole);
                }
                $approvalstatus = $entityData->get('approvalstatus');      
                if($approvalstatus == 'Approval'){
                    $entityData->set('scstatus','Active');
                }
                if($approvalstatus == 'Declined Approval' || $approvalstatus == 'Pending Approval' ){
                    $entityData->set('scstatus','Inactive');
                }
                $old_values = $entityDelta->getOldEntity($moduleName, $recordId);
                if (!$entityData->isNew()) {
                    $modBasicRes = $adb->pquery("SELECT id FROM vtiger_modtracker_basic WHERE crmid = ? ORDER BY id DESC LIMIT 1", [$recordId]);
                    $old_datas = [];
                    if ($adb->num_rows($modBasicRes) > 0) {
                        $lastId = $adb->query_result($modBasicRes, 0, 'id');
                        $modDetailRes = $adb->pquery("SELECT fieldname, prevalue, postvalue FROM vtiger_modtracker_detail WHERE id = ?", [$lastId]);
                        while ($row = $adb->fetch_array($modDetailRes)) {
                            $old_datas[$row['fieldname']] = [
                                'prevalue' => $row['prevalue'],
                                'postvalue' => $row['postvalue']
                            ];
                        }
                    }
                    $oldApproval = $old_values->get('approvalstatus');
                    $oldRating   = $old_values->get('consultantrating');
                    $oldStatus   = isset($old_datas['scstatus']['prevalue']) ? $old_datas['scstatus']['prevalue'] : $old_values->get('scstatus');

                    $isApprovalChanged = ($oldApproval != $approvalstatus);
                    $isRatingChanged   = ($oldRating != $consultantrating);

                    if ($approvalstatus == 'Declined Approval' && $isApprovalChanged && $currentUser->isAdminUser()) {
                        $oldApproval = isset($old_datas['approvalstatus']['prevalue']) ? $old_datas['approvalstatus']['prevalue'] : $old_values->get('approvalstatus');
                        $oldRating = isset($old_datas['consultantrating']['prevalue']) ? $old_datas['consultantrating']['prevalue'] : $old_values->get('consultantrating');
                        $entityData->set('consultantrating', $oldRating);
                        $entityData->set('scstatus', $oldStatus);
                        $consultantrole = $roleMapping[$oldRating];
                        $entityData->set('consultantrole',$consultantrole);
                    }
                    if ($isRatingChanged) {
                        $entityData->set('approvalstatus', 'Pending Approval');
                        $entityData->set('scstatus', 'Inactive');
                    }
                }else{
                    $entityData->set('approvalstatus', 'Pending Approval');
                    $entityData->set('scstatus', 'Inactive');
                }

            }
        }
        if($eventName == 'vtiger.entity.aftersave'){
            $moduleName = $entityData->getModuleName();
            $requestData = $_POST;
            if($moduleName == "ServiceCompetency" && $currentUser->isAdminUser() ){
                    $recordId = $entityData->getId();
                    /*$approvalstatus = $entityData->get('approvalstatus');
                    $isapprovalStatusChanged = $entityDelta->hasChanged($moduleName, $recordId, 'approvalstatus');
                    if($approvalstatus == 'Declined Approval' && $isapprovalStatusChanged){
                        $oldRating = $entityDelta->getOldValue($moduleName,$entityData->getId(),'consultantrating');
                        $scModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
                        $scModel->set('id', $recordId);
                        $scModel->set('mode', 'edit');
                        $scModel->set('consultantrating', $oldRating);
                        $scModel->save();
                    }
                    */
            }
            if (in_array($moduleName, ['SalesOrder'])) {
                $totalProductsCount = $requestData['totalProductCount'];
                $inventoryData = [];
                $productLists = [];
                foreach ($requestData as $key => $value) {
                    if (preg_match('/^hdnProductId(\d+)$/', $key, $matches) && $key != "hdnProductId0"  ) {
                        $rowNum = $matches[1];
                        if (isset($requestData["lineItemType{$rowNum}"]) && $requestData["lineItemType{$rowNum}"] == "Services" ) {
                            $productLists[] = $value;
                        }
                    }
                }
                $inventoryData = [];
                $atomglobalpricehike = $requestData['global_pricehike'];
                foreach ($requestData as $key => $value) {
                    if( preg_match('/^consultantname(\d+)$/', $key, $matches)){
                        $consultantList[] = $value;
                    }
                    if( preg_match('/^servicecompetencyid(\d+)$/', $key, $matches)){
                        $compentencyidList[] = $value; 
                    }
                    if( preg_match('/^start_date(\d+)$/', $key, $matches)){
                        $start_dateList[] = $value;
                    }
                    if( preg_match('/^end_date(\d+)$/', $key, $matches)){
                        $end_dateList[] = $value;
                    }
                }
                foreach ($productLists as $index => $productId) {
                    $inventoryData[] = [
                        'productid' => $productId,
                        'consultantname' => $consultantList[$index] ?? '',
                        'servicecompetencyid'=> $compentencyidList[$index] ?? '',
                        'start_date' => $start_dateList[$index] ?? '',
                        'end_date' => $end_dateList[$index] ?? '',
                    ];
                }
                $query = "SELECT lineitem_id, productid, sequence_no FROM vtiger_inventoryproductrel WHERE id = ? ORDER BY sequence_no";
                $result = $adb->pquery($query, [$recordid]);
                $inventoryIndex = 0;
                while ($row = $adb->fetch_array($result)) {
                    $lineItemId = $row['lineitem_id'];
                    $productId = $row['productid'];
                    $sequenceNo = $row['sequence_no'];
                    if (!isset($inventoryData[$inventoryIndex])) {
                        continue;
                    }
                    $requestData["lineitem_id{$sequenceNo}"] = $lineItemId;
                    $variant = $inventoryData[$inventoryIndex];
                    if ($variant['productid'] == $productId) {
                        $consultantname = $variant['consultantname'] ?? null;
                        $servicecompetencyid = $variant['servicecompetencyid'] ?? null;
                        $start_date = $variant['start_date'];
                        $end_date = $variant['end_date'];
                        $updateQuery = "UPDATE vtiger_inventoryproductrel SET consultantname =? , servicecompetencyid =? , consultant_startdate =?, consultant_enddate =? WHERE id = ? AND lineitem_id = ?";
                        $adb->pquery($updateQuery, [$consultantname,$servicecompetencyid,$start_date,$end_date,$recordid, $lineItemId]);
                        $inventoryIndex++;
                    }
                }
                if (empty($productLists)) {
                    $query = "
                        SELECT *
                        FROM vtiger_inventoryproductrel AS rel
                        WHERE rel.id = ? 
                        ORDER BY rel.sequence_no
                        ";
                    $result = $adb->pquery($query, [$recordid]);
                    while ($row = $adb->fetch_array($result)) {
                        $productId    = $row['productid'];
                        $sequence_no  = $row['sequence_no'];
                        $consultant   = $row['consultantname'];
                        $serviceComp  = $row['servicecompetencyid'];
                        $listPrice    = $row['listprice'] ?? '';
                        $qty          = $row['quantity'] ?? '';
                        $startdate    = $row['consultant_startdate'] ?? '';
                        $enddate      = $row['consultant_enddate'] ?? '';
                        $lineitem_id  = $row['lineitem_id'] ?? '';

                        $productLists[]     = $productId;
                        $consultantList[]   = $consultant;
                        $compentencyidList[]= $serviceComp;
                        $idx = (int)$sequence_no;
                        $requestData["consultantname{$idx}"]       = $consultant;
                        $requestData["listPrice{$idx}"]            = $listPrice;
                        $requestData["servicecompetencyid{$idx}"]  = $serviceComp;
                        $requestData["hdnProductId{$idx}"]         = $productId;
                        $requestData["qty{$idx}"]                  = $qty;
                        $requestData["start_date{$idx}"]            = $startdate;
                        $requestData["end_date{$idx}"]              = $enddate;
                        $requestData["lineitem_id{$idx}"]              = $lineitem_id;
                    }
                }

                $i = 1;
                $wsId = $entityData->getId();
                $entityDelta = new VTEntityDelta();
                $sostatus = $entityData->get('sostatus');
                $isStatusChanged = $entityDelta->hasChanged($moduleName, $wsId, 'sostatus');
                $oldStatus = $entityDelta->getOldValue($moduleName,$entityData->getId(),'sostatus');
                if ($sostatus != 'Approved' && $isStatusChanged ) {
                   return; // stop if SO not approved
                }
                if($sostatus == 'Created' || $sostatus == 'Delivered' || $sostatus == 'Cancelled'){
                    return;
                }
                foreach($productLists as $index => $serviceid){
                    $servieModel = Vtiger_Record_Model::getInstanceById($serviceid,"Services");
                    if($servieModel && $requestData["consultantname{$i}"] != ''){
                        $assigned_user_id = $requestData["consultantname{$i}"];
                        $subject = (!empty($requestData['subject'])) ? $requestData['subject'] : $entityData->get('subject');
                        $account_id =  (!empty($requestData['account_id'])) ? $requestData['account_id'] : $entityData->get('account_id');
                        $servicecompetencyid = (!empty($requestData["servicecompetencyid{$i}"])) ? $requestData["servicecompetencyid{$i}"] : '';
                        $lineItemId = $requestData["lineitem_id{$i}"];
                        $org_name = Vtiger_Functions::getCRMRecordLabel($account_id);
                        if($entityData->get('enable_recurring') == 'on' ){
                           // $startdate = (!empty($requestData['start_period'])) ? $requestData['start_period'] : $entityData->get('start_period');
                           // $duedate = (!empty($requestData['end_period'])) ? $requestData['end_period'] : $entityData->get('end_period');
                        }else{ 
                        }
                        $startdate = $requestData["start_date{$i}"];
                        $duedate = $requestData["end_date{$i}"];
                        
                        $sc_related_to = $requestData["record"];
                        
                        if($sc_related_to == ''){
                            $sc_related_to = $entityData->getId();
                        }
                        $role = '';
                        if(!empty($servicecompetencyid)){
                            $competencyModel = Vtiger_Record_Model::getInstanceById($servicecompetencyid,"ServiceCompetency");
                            $role = $competencyModel->get('consultantrole');
                        }
                        $planned_duration = 0;
                        if (!empty($startdate) && !empty($duedate)) {
                                $start_date =  $this->convertDateToMDY($startdate);
                                $due_date =  $this->convertDateToMDY($duedate);
                                $start = DateTime::createFromFormat('m-d-Y', $start_date);
                                $end =  DateTime::createFromFormat('m-d-Y', $due_date);
                                $interval = $start->diff($end);
                                $planned_duration = $interval->days; // number of days between
                        }
                        $checkServiceContractsql = $adb->pquery("SELECT * FROM `vtiger_servicecontracts` INNER JOIN vtiger_crmentity ON  vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid WHERE vtiger_crmentity.deleted = ? AND vtiger_servicecontracts.servicename = ? AND vtiger_servicecontracts.sc_related_to = ? AND vtiger_crmentity.smownerid = ?",array(0,$serviceid,$sc_related_to,$assigned_user_id));
                         if($adb->num_rows($checkServiceContractsql) == 0){
                            $recordModel = Vtiger_Record_Model::getCleanInstance("ServiceContracts");
                            $subject = $subject .' for '. $org_name;
                            $recordModel->set('mode','');
                            $recordModel->set('subject',$subject);
                            $recordModel->set('start_date',$startdate);
                            $recordModel->set('due_date',$duedate);
                            $recordModel->set('planned_duration',$planned_duration);
                            $recordModel->set('assigned_user_id',$assigned_user_id);
                            $recordModel->set('total_units',$requestData["qty{$i}"]);
                            $recordModel->set('contract_status','In Planning');
                            $recordModel->set('permandaycost',$requestData["listPrice{$i}"]);
                            $recordModel->set('servicename',$requestData["hdnProductId{$i}"]);
                            $recordModel->set('sc_related_to',$sc_related_to);
                            $recordModel->set('cf_1471',$role);
                            $recordModel->set('lineItemId',$lineItemId);
                            $recordModel->save();
                            $serviceContractId = $recordModel->getId();
                            $adb->pquery("INSERT INTO scid_rel(crmid, rel_id,module) VALUES (?,?,?)",array($serviceContractId,$lineItemId,'ServiceContracts'));
                        }
                    }
                    $i++;
                }
            }
            if($moduleName == 'ServiceContracts' && $entityData->isNew() ){
                $consultantname = $requestData['assigned_user_id'];
                $servicename = $requestData['servicename'];
                $ticketcount = $entityData->get('total_units');
                $lineItemId = $entityData->get('lineItemId');
                $serviceContractRecordId = $entityData->getId();
                $ticket_count = $this->createTicketsForServiceContract($serviceContractRecordId,$ticketcount,$lineItemId);
                $serviceContractId = $entityData->getId();
                $recordModel = Vtiger_Record_Model::getInstanceById($serviceContractId, $moduleName);
                $recordModel->set('id', $serviceContractId);
                $recordModel->set('mode', 'edit');
                $recordModel->set('total_units', $ticket_count);
                $recordModel->save();
            }

        }
        if($eventName == 'vtiger.entity.afterdelete'){
            if($moduleName  == 'HelpDesk' ||  $moduleName == 'ServiceContracts'){
                $recordId = $entityData->getId();
                $sql = "DELETE FROM scid_rel WHERE crmid = ?";
                $adb->pquery($sql, [$recordId]);
            } 
        }
    }
    
    private function createTicketsForServiceContract($serviceContractRecordId,$ticketcount,$lineItemId){
        global $adb;
        // Get Sales Order start and due date
        $soRes = $adb->pquery("SELECT smownerid,servicename,start_date,due_date,sc_related_to,subject,total_units FROM vtiger_servicecontracts
                    INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_servicecontracts.servicecontractsid
                    WHERE servicecontractsid = ? AND deleted = 0", [$serviceContractRecordId]);

        if ($adb->num_rows($soRes) == 0) return;

        $consultantId = $adb->query_result($soRes, 0, 'smownerid');
        $serviceId = $adb->query_result($soRes, 0, 'servicename');
        $sc_related_to = $adb->query_result($soRes, 0, 'sc_related_to');

        if (empty($consultantId) || empty($serviceId) || empty($sc_related_to)) return;
        $sc_rel_moduleame = Vtiger_Functions::getCRMRecordType($sc_related_to);
        if( $sc_rel_moduleame != 'SalesOrder'){
            return;
        } else {
            $salesOrderId = $sc_related_to;
            $SalesOrderModel = Vtiger_Record_Model::getInstanceById($salesOrderId, 'SalesOrder');
            $accountid = $SalesOrderModel->get('account_id');
            $contactid = $SalesOrderModel->get('contact_id');
        }

        $startDate = $adb->query_result($soRes, 0, 'start_date');
        $dueDate   = $adb->query_result($soRes, 0, 'due_date');
        $ticketcount = $adb->query_result($soRes, 0,'total_units');
        $subject = $adb->query_result($soRes, 0,'subject'); 
        $org_name = Vtiger_Functions::getCRMRecordLabel($accountid);
        $start = new DateTime($startDate);
        $end   = new DateTime($dueDate);

        if ($end < $start) return;

        $ticketCount = (int)$ticketcount;
        if ($ticketCount <= 0) return;

        // Get all dates between start and end
        $dates = [];
        $period = new DatePeriod($start, new DateInterval('P1D'), (clone $end)->modify('+1 day'));
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // Find consultant's already used ticket dates
        $usedRes = $adb->pquery("
                SELECT DATE(cf_792) AS tdate
                FROM vtiger_ticketcf tt
                        INNER JOIN vtiger_troubletickets tk ON tk.ticketid = tt.ticketid
                INNER JOIN vtiger_crmentity ce ON tt.ticketid = ce.crmid
                WHERE ce.deleted = 0 AND ce.smownerid = ? ", [$consultantId]);
        $usedDates = [];
        while ($row = $adb->fetch_array($usedRes)) {
            $usedDates[] = $row['tdate'];
        }

        // Determine free dates within range 
        $finalDates = $this->calculateTicketDates($startDate, $dueDate, $usedDates, $ticketCount);


        // Double check we have enough dates
        if (empty($finalDates)) return;

        $ticket_counts = count($finalDates);
        // Create tickets
        foreach ($finalDates as $ticketDate) {
            try {
                $ticket_title = $subject .' for '. $org_name;
                $ticketModel = Vtiger_Record_Model::getCleanInstance('HelpDesk');
                $ticketModel->set('mode', '');
               // $ticketModel->set('ticket_title', 'Service Ticket - ' . $ticketDate);
                $ticketModel->set('ticket_title', $ticket_title);
                $ticketModel->set('assigned_user_id', $consultantId);
                $ticketModel->set('parent_id',$accountid);
                $ticketModel->set('contact_id',$contactid);
                $ticketModel->set('ticketstatus', 'Planned');
                $ticketModel->set('ticketpriorities', 'Low');
                $ticketModel->set('cf_792', $ticketDate);
                $ticketModel->save();
                $ticketId = $ticketModel->getId();
                // Insert relation entry between ServiceContract and Ticket
                $adb->pquery("INSERT INTO scid_rel(crmid, rel_id,module) VALUES (?,?,?)",array($ticketId,$lineItemId,'HelpDesk'));
                $checkRel = $adb->pquery(
                        "SELECT 1 FROM vtiger_crmentityrel
                        WHERE crmid = ? AND relcrmid = ? AND module = ? AND relmodule = ?",
                        [$serviceContractRecordId, $ticketId, 'ServiceContracts', 'HelpDesk']
                        );

                if($adb->num_rows($checkRel) == 0) {
                    $adb->pquery(
                            "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES (?, ?, ?, ?)",
                            [$serviceContractRecordId, 'ServiceContracts', $ticketId, 'HelpDesk']
                            );
                }
            } catch (Exception $e) {
                error_log("[ServiceContracts Ticket Creation] " . $e->getMessage());
            }
        }
        return $ticket_counts;
    }
    function calculateTicketDates($startDate, $endDate, $usedDates, $ticketCount){
        $dates = [];
        $start = new DateTime($startDate);
        $end   = new DateTime($endDate);

        $period = new DatePeriod($start, new DateInterval('P1D'), (clone $end)->modify('+1 day'));
        foreach ($period as $d) {
            $dates[] = $d->format('Y-m-d');
        }

        // Available free dates
        $freeDates = array_values(array_diff($dates, $usedDates));
        if (empty($freeDates)) return [];

        // Month-wise grouping
        $monthWiseFree = [];
        foreach ($freeDates as $d) {
            $key = date('Y-m', strtotime($d));
            $monthWiseFree[$key][] = $d;
        }
        $months = array_keys($monthWiseFree);
        $monthCount = count($months);

        if ($monthCount == 0) return [];

        // Step A — Base split
        $baseTickets = floor($ticketCount / $monthCount);
        $remaining = $ticketCount % $monthCount;

        $monthTickets = [];
        foreach ($months as $m) {
            $monthTickets[$m] = $baseTickets;
        }

        // Step B — Distribute remainders to earliest months
        foreach ($months as $m) {
            if ($remaining <= 0) break;
            $monthTickets[$m]++;
            $remaining--;
        }

        // Step C — Assign based only on free days
        $finalDates = [];
        foreach ($months as $m) {
            $need = $monthTickets[$m];
            $available = $monthWiseFree[$m];

            if (count($available) >= $need) {
                $finalDates = array_merge($finalDates, array_slice($available, 0, $need));
            } else {
                $finalDates = array_merge($finalDates, $available);
            }
        }

        // Step D — Still short? Fill from earliest free dates
        $selectedCount = count($finalDates);

        if ($selectedCount < $ticketCount) {
            $short = $ticketCount - $selectedCount;

            foreach ($months as $m) {
                foreach ($monthWiseFree[$m] as $d) {
                    if (in_array($d, $finalDates)) continue;
                    if ($short <= 0) break;

                    $finalDates[] = $d;
                    $short--;
                }
                if ($short <= 0) break;
            }
        }
        return array_slice($finalDates, 0, $ticketCount);
    }


    function convertDateToMDY($date) {
        $formats = ['Y-m-d', 'd-m-Y', 'Y/m/d', 'd/m/Y', 'm/d/Y', 'm-d-Y'];
        foreach ($formats as $fmt) {
            $d = DateTime::createFromFormat($fmt, $date);
            if ($d && $d->format($fmt) === $date) {
                return $d->format('m-d-Y');
            }
        }
        // fallback if no match
        return date('m-d-Y', strtotime($date));
    }
}
?>
