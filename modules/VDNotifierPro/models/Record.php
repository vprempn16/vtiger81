<?php

/* * *******************************************************************************
 * The content of this file is subject to the Notificator Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is http://www.vordoom.net
 * Portions created by Vordoom.net are Copyright(C) Vordoom.net
 * All Rights Reserved.
 * ****************************************************************************** */

class VDNotifierPro_Record_Model extends Vtiger_Base_Model {
    public $userid;
    public $modulename;
    public $crmid;    
    public $modiuserid;
   
    public $title;
    public $link;
    public $action; 
    public $modifiedtime; 
    function __construct() {
        
    }
    public function save() {
            global $adb;
			$is_update = $this->checkUpdateNotifier();
			$fields = $this->column_fields;
			if (empty($this->userid) or empty($this->modiuserid)) return;
            $value = array();
			if (!$is_update){
				$id = $adb->getUniqueID('vtiger_vdnotifierpro');
				array_push($value, $id);
			}
           
			
            
            array_push($value, $this->userid);
            array_push($value, $this->modulename);
            array_push($value, $this->crmid);
            array_push($value, $this->modiuserid);
            array_push($value, $this->action);
            array_push($value, $this->modifiedtime);
            array_push($value, $this->link);
            array_push($value, $this->title);
            array_push($value, 0);
			if (!$is_update){
				$adb->pquery('INSERT INTO vtiger_vdnotifierpro (id,userid,modulename,crmid,modiuserid,action,modifiedtime,link,title,status) value(?,?,?,?,?,?,?,?,?,?)',array($value));
				return $adb->getLastInsertID();
			}  else {
				array_push($value, $is_update);
				$adb->pquery('UPDATE vtiger_vdnotifierpro SET userid = ?, modulename = ?, crmid = ?, modiuserid = ?, action = ?, modifiedtime = ?, link=?, title=?, status=? WHERE id = ?',array($value));
			    return $is_update;
			}
       
    }
	public function checkUpdateNotifier(){
		global $adb;
		$crmid = $this->crmid;
		$userid = $this->userid;
		$query = 'SELECT id FROM vtiger_vdnotifierpro WHERE userid = ? AND crmid = ? AND (status = ? or status = ?)';
		$result = $adb->pquery($query,array($userid,$crmid,0,5));
		$numRow = $adb->num_rows($result);
		if ($numRow == 0){
			return false;
		} else {
			return $adb->query_result($result,0,'id');
		}
	}
    static function getCalendarReminder() {
		$db = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		
		$recordModels = array();

		
			$date = date('Y-m-d');
			$time = date('H:i');
			$reminderActivitiesResult = "SELECT reminderid, recordid, vtiger_activity_reminder_popup.status FROM vtiger_activity_reminder_popup
            					INNER JOIN vtiger_activity on vtiger_activity.activityid = vtiger_activity_reminder_popup.recordid
								INNER JOIN vtiger_crmentity ON vtiger_activity_reminder_popup.recordid = vtiger_crmentity.crmid
								WHERE vtiger_activity_reminder_popup.status !=2 and vtiger_crmentity.smownerid = ? AND vtiger_crmentity.deleted = 0
								AND ((DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') < ?)
								OR (DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') = ? and (TIME_FORMAT(vtiger_activity_reminder_popup.time_start,'%H:%i') <= ?)))
                                AND vtiger_activity.eventstatus <> 'Held' AND (vtiger_activity.status <> 'Completed' OR vtiger_activity.status IS NULL) LIMIT 20";
			$result = $db->pquery($reminderActivitiesResult, array($currentUserModel->getId(), $date, $date, $time));
                       
			$rows = $db->num_rows($result);
			for($i=0; $i<$rows; $i++) {
				$recordId = $db->query_result($result, $i, 'recordid');
				$recordModels[$db->query_result($result, $i, 'reminderid')]['data'] = Vtiger_Record_Model::getInstanceById($recordId, 'Calendar');
                                $recordModels[$db->query_result($result, $i, 'reminderid')]['status'] = $db->query_result($result, $i, 'status');
                                	}
		
		return $recordModels;
	}
    public static function checkPopander($user_id){
        $recordModels = self::getCalendarReminder();
	foreach($recordModels as $id=>$record) {
		//$records[$id] = $record->getDisplayableValues();
		
	}
        //print_r ($recordModels);
        return  $recordModels;
        
    }
    static function findCrmid($recordId){
        global $adb;
        $result = $adb->pquery('SELECT smcreatorid, modifiedby, smownerid FROM vtiger_crmentity WHERE crmid = ?', array($recordId));
        return $adb->query_result_rowdata($result, 0);
    }
    static function findAll() {
		global $adb, $current_user;
		$user_id = $current_user->id;
                $popunder = self::checkPopander($user_id);
                $result = $adb->pquery("SELECT * FROM vtiger_vdnotifierpro WHERE userid = ? and (status = ? or status = ?);",array($user_id,0,5));
		
        
        $list = array();
       
        $numRow = $adb->num_rows($result);
        for($i=0; $i<$numRow;$i++){
            $list[$i]['id'] = $adb->query_result($result, $i, 'id');
            $modulename = $adb->query_result($result, $i, 'modulename');
            $crmid = $adb->query_result($result, $i, 'crmid');
            $list[$i]['module'] = vtranslate($modulename,$modulename);
            $list[$i]['title'] = $adb->query_result($result, $i, 'title');
            $list[$i]['link'] = $adb->query_result($result, $i, 'link');
            $modif_user = $adb->query_result($result, $i, 'modiuserid');
            $modif_user = self::getUserData($modif_user);
            $list[$i]['modiName'] = $modif_user['name'];
            $list[$i]['modiImg'] = $modif_user['image'];
            $list[$i]['modiuserid'] = $adb->query_result($result, $i, 'modiuserid');
            $list[$i]['item_summary'] = Zend_Json::decode(htmlspecialchars_decode($adb->query_result($result, $i, 'item_summary')));
            $list[$i]['action'] = vtranslate($adb->query_result($result, $i, 'action'),'VDNotifierPro');
            $list[$i]['modifiedtime'] = Vtiger_Date_UIType::getDisplayDateTimeValue($adb->query_result($result, $i, 'modifiedtime'));
            $list[$i]['status'] = $adb->query_result($result, $i, 'status');
            $list[$i]['type'] = 'Notifier';
        }
       if (count($popunder)>0){
           $i = count($list);
           foreach ($popunder as $id=>$value){
               //print_r ($value);die();
               $list[$i]['id'] = $id;
               $list[$i]['module'] = vtranslate('Calendar','Calendar');
               $list[$i]['title'] = $value['data']->get('subject');
               $list[$i]['link'] = 'module=Calendar&view=Detail&record='.$value['data']->getId();
               $modif_user = self::getUserData($current_user->id);
               $list[$i]['modiName'] = $modif_user['name'];
               $list[$i]['modiImg'] = $modif_user['image'];
               $list[$i]['modiuserid'] = $current_user->id;
               $list[$i]['action'] = vtranslate('Reminder','VDNotifierPro');
               $list[$i]['modifiedtime'] = Vtiger_Date_UIType::getDisplayDateTimeValue($value['data']->get('date_start').' '.$value['data']->get('time_start'));
               $list[$i]['type'] = 'Reminder';
               $list[$i]['status'] = $value['status']; 
               $list[$i]['Postponed'] = vtranslate('Postponed','VDNotifierPro');
               $i++;
               
           }
       }
        return $list;
    }
    static function getUserData($id){
        
        $user = Vtiger_DetailView_Model::getInstance('Users', $id);
        $recordModel = $user->getRecord();
       $return = array();
       $return['name'] = $recordModel->get('first_name').' '.$recordModel->get('last_name');
       $img = $recordModel->getImageDetails();
       if (!empty($img[0]['orgname'])){
            $return['image'] = $img[0]['path'].'_'.$img[0]['orgname'];
       }
       else {
            $return['image'] = vimage_path('summary_Contact.png');
       }
      
       return $return;
         
    }
    static function removing($id, $status=3){
        global $adb;
        var_dump($adb->pquery("UPDATE vtiger_vdnotifierpro SET status = 3 WHERE id = ?", array($id)));die;
    }
    static function update($id, $status=3){
            global $adb; 
            $adb->pquery("UPDATE vtiger_vdnotifierpro SET status = ? WHERE id = ?", array($status,$id));
    }
    static function clean(){
        global $adb,$current_user;
	$user_id = $current_user->id;
        $adb->pquery("UPDATE vtiger_vdnotifierpro SET status = 3 WHERE userid = ? AND (status = 0 OR status = 5)", array($user_id));
        $popunder = self::checkPopander($user_id);
                $id_popander = array();
                foreach ($popunder as $key=>$value){
                    array_push($id_popander, $key);
                }
        $sql = "UPDATE vtiger_activity_reminder_popup set status = 2 where reminderid= (".  implode(',', $id_popander).")";
        $adb->pquery($sql, array());
        return true;
    }
    static function cleanReminder($id, $status=2) {
		global $adb;
                $sql = "UPDATE vtiger_activity_reminder_popup set status = ? where reminderid= ?";
                $adb->pquery($sql, array($status,$id));
        }
    
    static function updateReminder($id) {
		global $adb;
                $currentUserModel = Users_Record_Model::getCurrentUserModel();
                $activityReminder = $currentUserModel->getCurrentUserActivityReminderInSeconds();
		if($activityReminder != '' ) {
			$currentTime = time();
			$date = date('Y-m-d', strtotime("+$activityReminder seconds", $currentTime));
			$time = date('H:i:00',   strtotime("+$activityReminder seconds", $currentTime));
		} else {
            $currentTime = time();
            $date = date('Y-m-d', strtotime("+600 seconds", $currentTime));
            $time = date('H:i:00',   strtotime("+600 seconds", $currentTime));
        }
               
                $sql = "UPDATE vtiger_activity_reminder_popup set status = 0, date_start = ?, time_start=? where reminderid= ?";
                $adb->pquery($sql, array($date,$time,$id));
		

	}
}