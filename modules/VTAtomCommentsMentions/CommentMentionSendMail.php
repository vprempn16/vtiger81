<?php 
include_once( "modules/Emails/class.smtp.php" );
include_once( "modules/Emails/class.phpmailer.php" );
require_once 'vtlib/Vtiger/Mailer.php';
class CommentMentionSendMail extends VTEventHandler {

	function handleEvent($eventName, $data) {
		if($eventName == 'vtiger.entity.beforesave') {
		}
		if($eventName == 'vtiger.entity.aftersave') {
			// Entity has been saved, take next action
			$moduleName = $data->getModuleName();
			

			if ($moduleName == 'ModComments') {
				global $current_user,$adb,$site_URL;
				
				// Construct mock request for event trigger where $request is undefined
				$request = new Vtiger_Request(array('module' => 'VTAtomCommentsMentions'));
				$isMailSendPermission = $this->isMailSendPermission('comment_mentions');
				

				if($isMailSendPermission){
					$commentcontent	= $data->get('commentcontent');
					

					// Use regex to find all @mentions in the comment
					$usernames = array();
					preg_match_all('/@([^\s]+)/', $commentcontent, $matches);
					if (!empty($matches[1])) {
						$usernames = $matches[1];
						// Clean up usernames - remove any trailing punctuation
						$usernames = array_map(function($username) {
							return rtrim($username, '.,!?;:');
						}, $usernames);
					}
					$related_to = $data->get('related_to');
					$relatedmodule = getSalesEntityType($related_to);
					$additional_content = "This is your record detail.<br>";
					$additional_content .= 'Click <a href='.$site_URL.'index.php?module='.$relatedmodule.'&relatedModule='.$moduleName.'&view=Detail&record='.$related_to.'&mode=showRelatedList&relationId='.$data->getID().'&tab_label='.$moduleName.'&app=MARKETING&commentid='.$data->getID().'>here</a> to view.';
					$commentcontent = $commentcontent . "<br><br>" . $additional_content;
					foreach($usernames as $key => $username){
						$userdetails = $this->getUserDetailsByUsername($username);
						
						// VDNotifierPro Notification logic with hierarchy-based permissions
						$userId = isset($userdetails['id']) ? $userdetails['id'] : false;
						
						if (file_exists('modules/VDNotifierPro/models/Record.php') && vtlib_isModuleActive('VDNotifierPro') && $userId) {
							require_once 'modules/VDNotifierPro/models/Record.php';
							
						
							$notificationTitle = "Mentioned you in a comment";
					
							$VDNotifier = new VDNotifierPro_Record_Model();
							$VDNotifier->userid = $userId;
							$VDNotifier->modulename = 'ModComments';
							$VDNotifier->crmid = $data->getId(); 
							$VDNotifier->modiuserid = $current_user->id; 
							$VDNotifier->action = 'MENTION';
							$VDNotifier->modifiedtime = date('Y-m-d H:i:s');
							$VDNotifier->title = $notificationTitle;
							$VDNotifier->link = "module=" . $relatedmodule . "&view=Detail&record=" . $related_to;
							$VDNotifier->save();
						}

						$toEmail = $userdetails['email'];
						$isMailSendPermission = $this->isMailSendPermission('send_commentmail');
						if($toEmail != '' && $isMailSendPermission){
							$fullname = $userdetails['first_name'] . ' ' . $userdetails['last_name'];
							$commentcontent_with_name = str_replace("@$username", "@$fullname", $commentcontent);
							
							try{
								$mail = new PHPMailer(true);
								$mail->SMTPDebug = 2;
								$mail->isSMTP();
								$mail->Host  = 'smtp.gmail.com;';
								$mail->SMTPAuth = true;
								$mail->Username = 'sureshm@atomlines.com';
								$mail->Password = "izborfinocgsqdfz";
								$mail->SMTPSecure = 'tls';
								$mail->Port  = 587;
								$mail->setFrom('sureshm@atomlines.com', 'prem');
								$mail->addAddress($toEmail);
								$mail->isHTML(true);
								$mail->Subject = 'EmailFunction';
								$mail->Body = $commentcontent_with_name;
								$mail->AltBody = 'Body in plain text for non-HTML mail clients';
								$result = $mail->send();
							}
							catch (Exception $e){
								echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
							}
						}
					}
				}

			}
		}
	}
	function isMailSendPermission($type){
		global $adb;
		$res = $adb->pquery("SELECT * FROM atom_vtcommenton_rel WHERE type = ?",array($type));
		if($adb->num_rows($res) > 0){
			if($adb->query_result($res,0,'is_checked') == 'on'){
				$return = true;
			}else{
				$return = false;
			}
		}
		return $return;
	}
	function getUserDetailsByUsername($username){
		global $current_user,$adb;
		$email = '';
		if($username != ''){
			$usermailquery = $adb->pquery('SELECT id, email1,first_name,last_name FROM vtiger_users WHERE user_name=?',array($username));
			$return['id'] = $adb->query_result($usermailquery,0,'id');
			$return['email'] = $adb->query_result($usermailquery,0,'email1');
			$return['first_name'] =  $adb->query_result($usermailquery,0,'first_name');
			$return['last_name'] = $adb->query_result($usermailquery,0,'last_name');
		}
		return $return;
	}
}
?>
