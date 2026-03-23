<?php
class ServiceCompetency_GetLineItemDetails_Action extends Vtiger_Action_Controller {
    public function process(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->get('module');
        $recordId = $request->get('record');
        $isDuplicate = $request->get('isDuplicate');

        $currentModel = Vtiger_Record_Model::getInstanceById($recordId);
        $conversionRate = $conversionRateForPurchaseCost = 1;
        if (!$recordId) {
            $response = new Vtiger_Response();
            $response->setResult(['success' => false, 'message' => 'No record ID found']);
            $response->emit();
            return;
        }

        $soResult = $adb->pquery("SELECT duedate FROM vtiger_salesorder WHERE salesorderid = ?", [$recordId]);
        $soDate = $adb->query_result($soResult, 0, 'duedate');
        $soMonth = date('m', strtotime($soDate));
        $soYear  = date('Y', strtotime($soDate));

        $query = "SELECT lineitem_id,listprice,productid,sequence_no,consultantname,servicecompetencyid,consultant_startdate,consultant_enddate,consultantrole FROM vtiger_inventoryproductrel WHERE id = ?";
        $result = $adb->pquery($query, [$recordId]);
        $lineItemDetails = [];

        while ($row = $adb->fetch_array($result)) {
            $id = $row['lineitem_id'];
            $sequence_no = $row['sequence_no'];
            $productid = $row['productid'];
            $consultantsAvailable = [];
            $consultantname = $row['consultantname'];
            $servicecompetencyid = $row['servicecompetencyid'];
            $compId       = $row['servicecompetencyid'];
            $startdate = $row['consultant_startdate'];
            $enddate = $row['consultant_enddate']; 
            $startdateObj = new DateTimeField($startdate);
            $enddateObj = new DateTimeField($enddate);
            $startdateDisplay =  $startdateObj->getDisplayDate();
            $enddateDisplay = $enddateObj->getDisplayDate();
            $consultantrole = $row['consultantrole'];
            $consultantName = '';
            if($consultantname != ''){
                $consultantLabel = Vtiger_Functions::getUserRecordLabel($consultantname);
            }
            $module = getSalesEntityType($productid);
            $roleQue = $adb->pquery("SELECT * FROM vtiger_servicecompetency where consultantname =? AND  servicecompetencyid =?",array($consultantname,$servicecompetencyid));
            if($adb->num_rows($roleQue) > 0){
                    // $consultantrole = $adb->query_result($roleQue,0,'consultantrole');
                     $serviceid = $adb->query_result($roleQue,0,'servicename');
                     if($servicecompetencyid != ''){
                        $servicename = Vtiger_Functions::getCRMRecordLabel($serviceid);
                     }
            }
            $scQuery = $adb->pquery("SELECT sc.servicecontractsid FROM vtiger_servicecontracts sc INNER JOIN vtiger_crmentity e ON e.crmid = sc.servicecontractsid AND e.deleted = 0 WHERE sc.sc_related_to = ? AND sc.servicename = ?", [$recordId, $productid]);
            $serviceContractId = ($adb->num_rows($scQuery) > 0) ? $adb->query_result($scQuery, 0, 'servicecontractsid') : 0;
            
            $contractsGrouped = $this->getContractsGrouped($productid,$recordId);

             $key = $sequence_no;

            $serviceContractId = 0;
            if (!empty($contractsGrouped[$key])) {
                // pop first in order → each line item receives next contract
                $serviceContractId = $contractsGrouped[$key];
            }
            $ticketsCount = 0;
            if ($serviceContractId > 0) {
                $tRes = $adb->pquery("
                    SELECT relcrmid AS ticketid FROM vtiger_crmentityrel WHERE crmid = ? AND module = 'ServiceContracts' AND relmodule = 'HelpDesk'
                ", [$serviceContractId]);

                $ticketsCount = (int)$adb->num_rows($tRes);
            }
            if($isDuplicate){
                $serviceContractId = 0;
                $ticketsCount = 0;
            }

            if($module == 'Services'){
                $scQuery = "SELECT sc.consultantname, u.first_name, u.last_name, u.id as userid,sc.consultantrole,sc.servicecompetencyid
                    FROM vtiger_servicecompetency sc
                    INNER JOIN vtiger_crmentity e ON e.crmid = sc.servicecompetencyid AND e.deleted = 0
                    INNER JOIN vtiger_users u ON u.id = sc.consultantname
                    WHERE sc.servicename = ?";
                $scRes = $adb->pquery($scQuery, [$productid]);
                while ($scRow = $adb->fetch_array($scRes)) {
                    $consultantId   = $scRow['userid'];
                    $servicecompetencyid = $scRow['servicecompetencyid'];
                    $consultantName = trim($scRow['first_name'].' '.$scRow['last_name']);
                    $ticketQuery = "
                        SELECT COUNT(*) as ticket_count
                        FROM vtiger_troubletickets tt
                        INNER JOIN vtiger_crmentity e2 ON e2.crmid = tt.ticketid AND e2.deleted = 0
                        WHERE e2.smownerid = ? 
                        AND MONTH(e2.createdtime) = ? 
                        AND YEAR(e2.createdtime) = ?";
                    $ticketRes = $adb->pquery($ticketQuery, [$consultantId, $soMonth, $soYear]);
                    $ticketCount = (int)$adb->query_result($ticketRes, 0, 'ticket_count');

                    // 🔹 Skip consultants with > 22 tickets
                    //if($ticketCount <= 22) {
                        $consultantsAvailable[] = [
                            'id' => $consultantId,
                            'name' => $consultantName,
                            'servicecompetencyid' =>$servicecompetencyid,
                        ];
                    //}
                }
            }
            $lineItemDetails[$sequence_no] = [
                'productid' => $row['productid'],
                'lineItemId' => $row['lineitem_id'],
                'consultantname' => $row['consultantname'], 
                'startdate' => $startdate,
                'enddate' => $enddate,
                'startdate_display'=>$startdateDisplay,
                'enddate_display'=>$enddateDisplay,
                'consultantName' =>  $consultantLabel, 
                'consultants_list' => $consultantsAvailable,
                'module'=>$module,
                'consultantrole' => $consultantrole,
                'servicecompetencyid' => $servicecompetencyid,
                'servicename' => $servicename,
                'ticketscount' => $ticketsCount,
                'servicecontractsid' => $serviceContractId
                ];
        }
        
        $response = new Vtiger_Response();
        $response->setResult([
                'success' => true,
                'lineItemDetails' => json_encode($lineItemDetails),
        ]);
        $response->emit();
    } 
    public function getContractsGrouped($productid,$recordId){
        global $adb;
        $contractsGrouped = []; // key: "{$productid}_{$consultant}_{$competency}"

        $contractQuery = "
            SELECT * 
                       FROM vtiger_servicecontracts sc
                       INNER JOIN vtiger_crmentity e ON e.crmid = sc.servicecontractsid AND e.deleted = 0
                       WHERE sc.sc_related_to = ?
                       ORDER BY e.createdtime ASC
                       ";
        $contractRes = $adb->pquery($contractQuery, [$recordId]);
        $i = 1;
        while ($cRow = $adb->fetch_array($contractRes)) {
            $key = $cRow['servicename'];
            $contractsGrouped[$i] = $cRow['servicecontractsid'];
            $i++;
        }
        return $contractsGrouped;
    }
}
