<?php
class ServiceCompetency_GetLineItemDetails_Action extends Vtiger_Action_Controller {
    public function process(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->get('module');
        $recordId = $request->get('record');
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

        $query = "SELECT lineitem_id,listprice,productid,sequence_no,consultantname,servicecompetencyid,consultant_startdate,consultant_enddate FROM vtiger_inventoryproductrel WHERE id = ?";
        $result = $adb->pquery($query, [$recordId]);
        $lineItemDetails = [];

        while ($row = $adb->fetch_array($result)) {
            $id = $row['lineitem_id'];
            $sequence_no = $row['sequence_no'];
            $productid = $row['productid'];
            $consultantsAvailable = [];
            $consultantname = $row['consultantname'];
            $servicecompetencyid = $row['servicecompetencyid'];
            $startdate = $row['consultant_startdate'];
            $enddate = $row['consultant_enddate']; 
            $consultantName = '';
            if($consultantname != ''){
                $consultantLabel = Vtiger_Functions::getUserRecordLabel($consultantname);
            }
            $module = getSalesEntityType($productid);
            $roleQue = $adb->pquery("SELECT * FROM vtiger_servicecompetency where consultantname =? AND  servicecompetencyid =?",array($consultantname,$servicecompetencyid));
            if($adb->num_rows($roleQue) > 0){
                     $consultantrole = $adb->query_result($roleQue,0,'consultantrole');
                     $serviceid = $adb->query_result($roleQue,0,'servicename');
                     if($servicecompetencyid != ''){
                        $servicename = Vtiger_Functions::getCRMRecordLabel($serviceid);
                     }
            }
            $ticketRes = $adb->pquery("SELECT COUNT(*) AS ticketscount FROM scid_rel WHERE rel_id = ? AND module =? ",array($id,'HelpDesk'));
            $ticketsCount = $adb->query_result($ticketRes, 0, 'ticketscount');

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
                    if($ticketCount <= 22) {
                        $consultantsAvailable[] = [
                            'id' => $consultantId,
                            'name' => $consultantName,
                            'servicecompetencyid' =>$servicecompetencyid,
                        ];
                    }
                }
            }
            $lineItemDetails[$sequence_no] = [
                'productid' => $row['productid'],
                'lineItemId' => $row['lineitem_id'],
                'consultantname' => $row['consultantname'], 
                'startdate' => $startdate,
                'enddate' => $enddate,
                'consultantName' =>  $consultantLabel, 
                'consultants_list' => $consultantsAvailable,
                'module'=>$module,
                'consultantrole' => $consultantrole,
                'servicecompetencyid' => $servicecompetencyid,
                'servicename' => $servicename,
                'ticketscount' => $ticketsCount
                ];
        }
        $response = new Vtiger_Response();
        $response->setResult([
                'success' => true,
                'lineItemDetails' => json_encode($lineItemDetails),
        ]);
        $response->emit();
    } 
}
