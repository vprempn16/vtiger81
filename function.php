<?php 

public function getCustomQueryForPopup($serviceId, $salesOrderId, $startDate, $endDate, $manday, $role) {
        global $adb;
        if (empty($serviceId)) return '';
        $soStartDate = $startDate;
        $soEndDate   = $endDate;
        if (empty($soStartDate)) $soStartDate = date('Y-m-d');
        if (empty($soEndDate))   $soEndDate   = date('Y-m-d');
        $startDT = DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($soStartDate)));
        $endDT   = DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($soEndDate)));
        if (!$startDT || !$endDT) {
            $monthsDiff = 1;
        } else {
            $startYM = intval($startDT->format('Y')) * 12 + intval($startDT->format('m'));
            $endYM   = intval($endDT->format('Y')) * 12 + intval($endDT->format('m'));
            $monthsDiff = max(1, ($endYM - $startYM + 1)); // inclusive months
        }
        $monthsDiff = (int)$monthsDiff;
        $sampleDateResult = $adb->pquery("SELECT cf_792 FROM vtiger_ticketcf WHERE cf_792 IS NOT NULL AND cf_792 != '' LIMIT 1", []);
        $sampleDate = ($adb->num_rows($sampleDateResult) > 0) ? $adb->query_result($sampleDateResult, 0, 'cf_792') : '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampleDate)) {
            $format = '%Y-%m-%d';
        } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $sampleDate)) {
            $format = '%m-%d-%Y';
        } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $sampleDate)) {
            $format = '%m/%d/%Y';
        } else {
            $format = '%Y-%m-%d'; // fallback
        }
        $searchParams = $this->get('search_params');
        $searchSql = $this->getSearchSql($searchParams); // assumes this returns a string prefixed with spaces/ANDs

        $roleByRate = array(1 =>'Not Started',2 => 'Learner',3 => 'Implementer',4 =>'Reviewer',5 => 'Project Manager');
        $selectedRoleLevel = array_search($role, $roleByRate);
        if (!$selectedRoleLevel) { $selectedRoleLevel = 1; }

        $allowedRoles = [];
        foreach ($roleByRate as $rate => $rName) {
            if ($rate >= $selectedRoleLevel) {
                            $allowedRoles[] = $rName;
            }
        }
        $roleQMarks = $this->generateQuestionMarks($allowedRoles);
        $monthlyWorkingDaysColumn = "IFNULL(uwd.working_days, 0)";
        $totalWorkingDaysExpr = $this->buildWorkingDaysExpression($soStartDate, $soEndDate, $monthlyWorkingDaysColumn);

         $calcStart = date('Y-m-01', strtotime($soStartDate));
        $calcEnd   = date('Y-m-t', strtotime($soEndDate));
 /*       $ticketsJoin = "
            LEFT JOIN (
                SELECT e.smownerid AS owner_id, COUNT(*) AS tickets_in_range
                FROM vtiger_troubletickets tt
                INNER JOIN vtiger_ticketcf tcf ON tcf.ticketid = tt.ticketid
                INNER JOIN vtiger_crmentity e ON e.crmid = tt.ticketid AND e.deleted = 0
                WHERE STR_TO_DATE(tcf.cf_792, '{$format}') BETWEEN ? AND ?
                GROUP BY e.smownerid
            ) ticketStats ON ticketStats.owner_id = sc.consultantname
        ";

*/	

// count no of days on which tickets created.
$ticketsJoin = "
    LEFT JOIN (
        SELECT e.smownerid AS owner_id,
               COUNT(DISTINCT DATE(STR_TO_DATE(tcf.cf_792, '{$format}'))) AS tickets_in_range
        FROM vtiger_troubletickets tt
        INNER JOIN vtiger_ticketcf tcf ON tcf.ticketid = tt.ticketid
        INNER JOIN vtiger_crmentity e ON e.crmid = tt.ticketid AND e.deleted = 0
        WHERE STR_TO_DATE(tcf.cf_792, '{$format}') BETWEEN ? AND ?
        GROUP BY e.smownerid
    ) ticketStats ON ticketStats.owner_id = sc.consultantname
";

        $query = "
            SELECT sc.*, u.first_name, u.last_name,
                   {$monthlyWorkingDaysColumn} AS monthly_working_days,
                   {$totalWorkingDaysExpr} AS total_working_days,
                   IFNULL(ticketStats.tickets_in_range, 0) AS tickets_in_range,
                   ({$totalWorkingDaysExpr} - IFNULL(ticketStats.tickets_in_range, 0)) AS freeDays
            FROM vtiger_servicecompetency sc
            INNER JOIN vtiger_crmentity ce ON ce.crmid = sc.servicecompetencyid AND ce.deleted = 0
            INNER JOIN vtiger_users u ON u.id = sc.consultantname
            LEFT JOIN sc_userworkingdays uwd ON uwd.userid = sc.consultantname
            {$ticketsJoin}
            WHERE sc.servicename = ?
              AND sc.scstatus = 'Active'
              AND sc.consultantrole IN ($roleQMarks)
              {$searchSql}
            HAVING freeDays >= ?
        ";
        $params = [
            //$soStartDate, $soEndDate,
            $calcStart, $calcEnd,
            $serviceId,
        ];
        if (!empty($allowedRoles)) {
            foreach ($allowedRoles as $r) $params[] = $r;
        }
        $params[] = $manday;
        $query = $adb->convert2Sql($query, $params);
        return $query;
    }
?>
