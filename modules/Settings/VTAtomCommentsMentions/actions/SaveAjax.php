<?php
class Settings_VTAtomCommentsMentions_SaveAjax_Action extends Settings_Vtiger_Basic_Action {

	public function process(Vtiger_Request $request) {
		global $current_user,$adb;
		$response = new Vtiger_Response();
		$type = $request->get('type');
		$linklabel = $request->get('linklabel');
		$return = array('success'=>false,'message'=>'Invalid Request');
		$is_checked = $request->get('ischecked');
		if($type != ''){
			if($type =='send_commentmail'){
				$res  = $this->commentsOnRel($is_checked,$type);
				$return = array('success'=>$res,);
			}else{
				if($linklabel != ''){
				$adb->pquery("DELETE FROM vtiger_links WHERE `vtiger_links`.`linklabel` =?",array($linklabel));
				$return = array('success'=>false,'message'=> $linklabel.'link off');
				if($is_checked == 'true'){
					$sql = $adb->pquery('Select MAX(linkid) as id from vtiger_links',array());
					$res = $adb->pquery('SELECT * FROM vtiger_links WHERE linklabel =?',array($linklabel));
					if($adb->num_rows($res) == 0){
						$linkid = $adb->query_result($sql,'0','id');
						$linkid++; 
						$adb->pquery("INSERT INTO `vtiger_links`( `linkid`,`tabid`, `linktype`, `linklabel`, `linkurl`, `linkicon`, `sequence`, `handler_path`, `handler_class`, `handler`, `parent_link`) VALUES (?,?,?,?,?,?,?,?,?,?,?)",array($linkid,'3','HEADERSCRIPT',$linklabel,'layouts/v7/modules/VTAtomCommentsMentions/resources/'.$linklabel.'.js','','0','','','',NULL));
						$return = array('success'=>true,'message'=> $linklabel.'link on');
					}
				}
				$this->commentsOnRel($is_checked,$type);
				}
			}
		}
		$response->setResult($return);
		$response->emit();		
	}
	function commentsOnRel($ischecked,$type){
		 global $current_user,$adb;
		$result = $adb->pquery('SELECT * FROM atom_vtcommenton_rel WHERE type=?',array($type));
		$value = 'off';
		if($ischecked == 'true'){
			 $value = 'on';
		}	
		if($adb->num_rows($result) > 0){
			$id = $adb->query_result($result,'0','id');
			$adb->pquery('UPDATE atom_vtcommenton_rel SET is_checked = ? WHERE id = ? AND type=?',array($value,$id,$type));
		}else{
			$adb->pquery('INSERT INTO atom_vtcommenton_rel(is_checked,type) VALUES (?,?)',array($value,$type));
		}
		return true;
	}
}
?>
