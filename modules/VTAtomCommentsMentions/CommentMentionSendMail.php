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
					// Create notifications for Record Owner and Creator
					$this->createRecordOwnerAndCreatorNotifications($data, $related_to, $relatedmodule, $current_user);
					
					// Create reply notification for original comment creator
					//$this->createReplyNotification($data, $current_user);
				}
			}
		}
	}
	
	/**
	 * Create notifications for Record Owner and Creator
	 */
	private function createRecordOwnerAndCreatorNotifications($commentData, $related_to, $relatedmodule, $current_user) {
		global $site_URL;
		
		// Get record details
		$recordModel = Vtiger_Record_Model::getInstanceById($related_to, $relatedmodule);
		$recordOwnerId = $recordModel->get('assigned_user_id');
		
		// Get record creator ID from vtiger_crmentity table
		global $adb;
		$crmentityResult = $adb->pquery("SELECT smcreatorid FROM vtiger_crmentity WHERE crmid = ?", array($related_to));
		$recordCreatorId = $adb->num_rows($crmentityResult) > 0 ? $adb->query_result($crmentityResult, 0, 'smcreatorid') : null;
		
		// Get current user ID
		$currentUserId = $current_user->id;
		
		// Prepare notification data
		$notificationTitle = "New comment posted on " . vtranslate($relatedmodule, $relatedmodule);
		$link = "module=" . $relatedmodule . "&view=Detail&record=" . $related_to;
		
		// Send notification to Record Owner (if different from current user)
		if ($recordOwnerId && $recordOwnerId != $currentUserId) {
			$this->sendNotificationToUser($recordOwnerId, $notificationTitle, $link, $commentData->getId(), $currentUserId);
		}
		
		// Send notification to Record Creator (if different from current user and record owner)
		if ($recordCreatorId && $recordCreatorId != $currentUserId && $recordCreatorId != $recordOwnerId) {
			$this->sendNotificationToUser($recordCreatorId, $notificationTitle, $link, $commentData->getId(), $currentUserId);
		}
	}
	
	/**
	 * Send notification to specific user
	 */
	private function sendNotificationToUser($userId, $title, $link, $commentId, $currentUserId) {
		if (file_exists('modules/VDNotifierPro/models/Record.php') && vtlib_isModuleActive('VDNotifierPro')) {
			require_once 'modules/VDNotifierPro/models/Record.php';
			
			$VDNotifier = new VDNotifierPro_Record_Model();
			$VDNotifier->userid = $userId;
			$VDNotifier->modulename = 'ModComments';
			$VDNotifier->crmid = $commentId;
			$VDNotifier->modiuserid = $currentUserId;
			$VDNotifier->action = 'COMMENT';
			$VDNotifier->modifiedtime = date('Y-m-d H:i:s');
			$VDNotifier->title = $title;
			$VDNotifier->link = $link;
			$VDNotifier->save();
		}
	}
	
	/**
	 * Create reply notification for original comment creator
	 */
	private function createReplyNotification($commentData, $current_user) {
		global $adb, $site_URL;
		
		// Check if this is a reply (has parent comment)
		$parentCommentId = $commentData->get('parent_comments');
		
		if ($parentCommentId) {
			// Get parent comment details
			$parentCommentResult = $adb->pquery(
				"SELECT creator FROM vtiger_modcomments WHERE modcommentsid = ?", 
				array($parentCommentId)
			);
			
			if ($adb->num_rows($parentCommentResult) > 0) {
				$parentCommentCreatorId = $adb->query_result($parentCommentResult, 0, 'creator');
				$currentUserId = $current_user->id;
				
				// Send reply notification to parent comment creator (if different from current user)
				if ($parentCommentCreatorId && $parentCommentCreatorId != $currentUserId) {
					$related_to = $commentData->get('related_to');
					$relatedmodule = getSalesEntityType($related_to);
					
					$notificationTitle = "Replied to your comment";
					$link = "module=" . $relatedmodule . "&view=Detail&record=" . $related_to;
					
					$this->sendNotificationToUser($parentCommentCreatorId, $notificationTitle, $link, $commentData->getId(), $currentUserId);
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
