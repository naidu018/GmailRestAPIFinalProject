<?php

class Email_MailAjaxController extends Zend_Controller_Action
{
	const DEADLINE_NEVER = "-1";
	
	public function init()
	{
		Bugs_Auth_Session::check();
		
		$this->_helper->ajaxContext()
			->addActionContext('get-data-table', 'json')
			->addActionContext('show-full-email', 'html')
			->addActionContext('edit-mail-properties', 'json')
			->addActionContext('send-reply', 'json')
			->addActionContext('multi-delete', 'json')
			->addActionContext('set-pubb', 'json')
			->addActionContext('set-deadline', 'json')
			->initContext();
	}
	
	public function getDataTableAction()
	{
		$params = $this->_getAllParams();
		
		$is_deleted = $this->_getParam("is_deleted");
		
		$user_column = ($is_deleted ? "user" : "assignuser");
		$cols = array("time", "from", "attachs", "subject", "assign_psetup", $user_column, "deadline", "deadline_type");
		$other_cols = array(
			"time_read",
			"id",
			"assigntime",
			new Zend_Db_Expr("if(`m`.`deadline` > 0, `m`.`deadline`, if(`m`.`deadline` < 0, 9999999999, `m`.`time`)) as `deadline_ordering`"),
			new Zend_Db_Expr("if(`m`.`assigntime` > 0, 1, 0) as is_assigned"),
			"log",
		);
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$order = (isset($params["iSortCol_0"])) ? "{$cols[$params["iSortCol_0"]]} {$params["sSortDir_0"]}" : null;
		$count = (isset($params["iDisplayLength"]) && $params["iDisplayLength"]) ? $params["iDisplayLength"] : 1000;
		$offset = (isset($params["iDisplayStart"]) && $params["iDisplayStart"]) ? $params["iDisplayStart"] : 0;
		
		$select = $db->select()->from(array("m"=>"show_mails"), array(), "nm_paper");
		
		if($this->_getParam("sSearch"))
		{
			$select->where("text LIKE ? OR subject LIKE ? OR assignuser LIKE ? OR assign_psetup LIKE ? OR {$user_column} LIKE ?", array("%".$this->_getParam("sSearch")."%"));
		}
		if($is_deleted)
		{
			$select->where("time_read != 0");
		}
		else
		{
			$select->where("time_read = 0");
		}
		
		$select_count_total = clone $select;
		$select_count_total->columns(new Zend_Db_Expr("count(*)"));
		$iTotalDisplayRecords = $db->fetchOne($select_count_total);
		
		$select->columns(array_merge($cols, $other_cols));
		
		
		if($order)
		{
//			var_dump($order);
			
			if($order == "deadline desc" || $order == "deadline asc")
			{
				$order = new Zend_Db_Expr("deadline_ordering {$params["sSortDir_0"]}");
			}
			if(!$is_deleted)
			{
				$select->order(array("is_assigned asc", $order));
			}
			else
			{
				$select->order($order);
			}
		}
		$select->limit($count, $offset);
		
//		echo $select;exit;
		
		$output = array(
			"sEcho" => intval($params['sEcho']),
			"iTotalRecords" => 0,
			"iTotalDisplayRecords" => $iTotalDisplayRecords,
			"aaData" => array()
		);
		
		foreach($db->fetchAll($select) as $email)
		{
			$extra_classes = array();
			$color = "";
			$icon = "";
			$is_assigned = "";
			$assign_icon = "";
			
			if($email["time_read"])
			{
				if(stripos($email["log"], "(operation: reply)")) // Has a Reply
				{
					$icon = '<span class="glyphicon glyphicon-share-alt"></span> ';
				}
				else
				{
					$icon = '<span class="glyphicon glyphicon-eye-open"></span> ';
				}
			}
			elseif($email["assigntime"])
			{
				if(stripos($email["log"], "(operation: reply)")) // Has a Reply
				{
					$icon = '<span class="glyphicon glyphicon-share-alt"></span> ';
				}
				else
				{
					$icon = '<span class="glyphicon glyphicon-eye-open"></span> ';
				}
				
				
				if((Bugs_Auth_Session::getIdentity()->nickname == $email[$user_column]))
				{
					$is_assigned = "is_assigned";
					
					$assign_icon = ' <span class="glyphicon glyphicon-star warning"></span> ';
				}
				
				if($email["deadline"] > 0)
				{
					if((time() > $email["deadline"] - 60 * 60 * 2) && (time() < $email["deadline"] - 60 * 60 * 1) ) { $color = "warning"; }
					elseif((time() > $email["deadline"] - 60 * 60 * 1)) { $color = "danger"; }
				}
			}
			else 
			{
				$icon = '<span class="glyphicon glyphicon-envelope"></span> ';
				
				// Show as not read only if deadline is not expired
				if(($email["deadline"] - time() < 0))
				{
					$extra_classes[] = "is_not_read";
				}
				else
				{
					$email["assignuser"] = ($email["deadline_type"] == Email_Model_DeadlineType::_6_NEXT_SHIFT) ? "[ NEXT SHIFT ]" : "";
				}
				
				$time_to_compare = ($email["deadline"]) ? $email["deadline"] : $email["time"];
				
				if((time() - $time_to_compare > 60 * 20) ) { $color = "danger"; }
				elseif((time() - $time_to_compare > 60 * 10) ) { $color = "warning"; }
				
			}
			$output["iTotalRecords"]++;
			
			$email_from = explode("<", $email["from"]);
			$name_from = trim($email_from[0]);
			$email_from = (count($email_from) > 1) ? trim(str_replace(">", "", $email_from[1])) : "";
			$name_from = $name_from ? $name_from : $email_from;
			
			
			$extra_classes[] = $color;
			$extra_classes[] = $is_assigned;
			
			
			$extra_classes = trim(implode(" ", $extra_classes));
			
			
			
			$date = new DateTime();
			$date->setTimezone(new DateTimeZone("GMT"));
			$date->setDate(date("Y", $email["time"]), date("m", $email["time"]), date("d", $email["time"]));
			$date->setTime(date("H", $email["time"]), date("i", $email["time"]), date("s", $email["time"]));
			$date->setTimezone(new DateTimeZone(Bugs_Auth_Session::getTimezone()));
			$email_timestamp = strtotime($date->format("Y-m-d H:i:s"));
			
			$checkbox = "";
			if(!$is_deleted)
			{

				$checkbox = '<div class="squaredFour bbe_prevent_default">'
					.'<input class="bbe_prevent_default bbe_bugs_email_multicheck" type="checkbox" id="bbe_bugs_email_multicheck_'.$email["id"].'"/>'
					.'<label class="bbe_prevent_default" for="squaredFour"></label>'
				.'</div>';
				
			}
			
			$time_ago = Bugs_Common_Date::howManyUnitsAgo($email["time"]);
			$time_ago = ($time_ago) ? $time_ago : "Now";
			
			// $assigned_ago = ($email["assigntime"]) ? " (" . Bugs_Common_Date::howManyUnitsAgo($email["assigntime"], true) . ")" : "";
			$assigned_ago = "";
			
			$output_data_row = array(
				$checkbox . '<span class="has-poshytip" title="'.date("D, d M H:i:s", $email_timestamp).'" onclick="event.preventDefault();">' 
					. "&nbsp;&nbsp;" . $icon . "&nbsp;&nbsp;" . $time_ago .
				'</span>',
				'<span class="has-poshytip" title="'.$email_from.'">' . trim($name_from, ",") . '</span>',
				"<span class=\"email_subject\" extra_classes=\"{$extra_classes}\" id=\"email_element_{$email["id"]}\">{$email["subject"]}</span>",
				(($email["attachs"]) ? '<span class="glyphicon glyphicon-paperclip"></span>' : ""),
				'<span>' . (($email["assign_psetup"]) ? $email["assign_psetup"] : "-") . '</span>',
				'<span>' . (($email[$user_column]) ? $email[$user_column] : "-") . $assigned_ago . $assign_icon .  '</span>',
			);
			
			if(!$is_deleted)
			{
				if($email["assigntime"] || $email["deadline"] /*($email["deadline_type"] == Email_Model_DeadlineType::_6_NEXT_SHIFT)*/)
				{
					if($email["deadline"] > 0)
					{
						$deadline = ($email["deadline"]) ? $email["deadline"] : ($email["assigntime"] + 60 * 60 * 2);
						$deadline_expired = ((time() - $deadline) > 0);
						$output_data_row[] = '<span>'
							. Bugs_Common_Date::howManyUnitsAgo($deadline, true)
							. (($deadline_expired) ? ' ago <span class="glyphicon glyphicon-warning-sign right"></span>' : "")
							. '</span>';
					}
					else
					{
						$output_data_row[] = ($email["deadline"] < 0) ? "NEVER" : "";
					}
				}
				else
				{
					$output_data_row[] = '<span style="color: #ccc;">' 
						. Bugs_Common_Date::howManyUnitsAgo($email["time"], true) . " ago"
						. ' <span class="glyphicon glyphicon-warning-sign right"></span>'
						. '</span>';
				}
				
				$output_data_row[] = isset(Email_Model_DeadlineType::$icons[$email["deadline_type"]])
					? Email_Model_DeadlineType::$icons[$email["deadline_type"]] : "-";
				
			}
			
			$output["aaData"][] = $output_data_row;
			
			
			
		}
		
		$this->_helper->json($output);
	}
	
	public function showFullEmailAction()
	{
		$email_MODEL = new Email_Model_ShowMails($this->_getParam("id"));
		
		$email_MODEL->text = str_replace("<script ", "&lt;script ", $email_MODEL->text);
		
		$this->view->showMmails_MODEL = $email_MODEL;
	}
	
	public function editMailPropertiesAction()
	{
		$showMmails_MODEL = new Email_Model_ShowMails($this->_getParam("id"));
		
		$action_type = $this->_getParam("action_type");
		$base_msg = "The email has been marked as";
		$title = "Success!";
		$msg_type = "success";
		$user_name = Bugs_Auth_Session::getIdentity()->nickname;
		$date_str = date("D, d M H:i:s O");
		$operation = "";
		
		if($action_type == "delete")
		{
			$message = $base_msg . " deleted";
			$showMmails_MODEL->time_read = time();
			$showMmails_MODEL->user = $user_name;
		}
		if($action_type == "undelete")
		{
			$message = $base_msg . " undeleted";
			$showMmails_MODEL->time_read = 0;
			$showMmails_MODEL->user = "";
		}
		if($action_type == "read")
		{
			$message = $base_msg . " read";
			$showMmails_MODEL->assigntime = time();
			$showMmails_MODEL->assignuser = $user_name;
			
			if(!$showMmails_MODEL->deadline_type)
			{
				$showMmails_MODEL->deadline = time() + (60 * 60 * 3);
				$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_3_DEFAULT_3_h;
			}
		}
		if($action_type == "unread")
		{
			$message = $base_msg . " unread";
			$showMmails_MODEL->assigntime = 0;
			$showMmails_MODEL->assignuser = "";
			
//			$showMmails_MODEL->deadline = null;
		}
		$extrainfo = $this->_getParam("extrainfo");
		$extrainfo = $extrainfo ? "[extrainfo: {$extrainfo}]" : "";
		$showMmails_MODEL->log .= "<b>[{$date_str}] - User: {$user_name} (Operation: {$action_type}){$extrainfo}</b>\r\n";
		$showMmails_MODEL->save();
		
		$this->view->msg_type = $msg_type;
		$this->view->title = $title;
		$this->view->message = $message . " [subject: {$showMmails_MODEL->subject}]";
	}
	
	public function sendReplyAction()
	{
		$user_name = Bugs_Auth_Session::getIdentity()->nickname;
		$date_str = date("D, d M H:i:s O");
		$action_type = $this->_getParam("action_type");
		$showMmails_MODEL = new Email_Model_ShowMails($this->_getParam("id"));
		
		$mail_content = $this->_getParam("content");
		
		if($action_type != "compose")
		{
			$padding = " - ";
			$fwd_info = in_array($action_type, array("forward", "reply")) 
				? "<br><br>"
					. "<br>{$padding}From: {$showMmails_MODEL->from}"
					. "<br>{$padding}Sent: " . date("D, d M Y H:i:s", $showMmails_MODEL->time)
					. "<br>{$padding}To: Newsmemory Service Desk <newsmemory@tecnavia.com>"
					. (($showMmails_MODEL->cc) ? "<br>{$padding}CC: {$showMmails_MODEL->cc}" : "")
					. "<br>{$padding}Subject: {$showMmails_MODEL->subject}<br><br>"
				: "";
			
			$mail_content .= "<br>-------------------------<br>Original message:<br><br>" 			
				. $fwd_info
				. str_ireplace(array("<br><br>", "<br/><br/>", "<br /><br />"), "<br>", nl2br($showMmails_MODEL->text));
		}
		
		// We are adding this tag in the email to know if this is a reply to a previous mail.
		// In that case, it can be deleted without a reason.
		$unique_mail_id = date("Y-m-d_H-i-s", $showMmails_MODEL->time) . "_" . md5($showMmails_MODEL->from . $showMmails_MODEL->time);
		$mail_content .= "<br><br>ver:" . md5("tecnavia_email_signature") . "---" . $unique_mail_id . "---";
		
		$matches = array();
		$pattern = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i';
		preg_match_all($pattern, $this->_getParam("email_to"), $matches);
		
		$email_to = current($matches);
		
		$subject = $this->_getParam("subject");
		
		if(!$subject)
		{
			$ouput["title"] = "Error:";
			$ouput["msg"] = "Missing subject!";
			$ouput["msg_type"] = "danger";
		}
		else
		{
			if($email_to)
			{
				$zend_mail = new Zend_Mail('UTF-8');
				$zend_mail->addTo($email_to);
				
				$email_cc = array();
				preg_match_all($pattern, $this->_getParam("email_cc"), $email_cc);
				$email_cc = current($email_cc);
				$zend_mail->addCc($email_cc);
				
				$email_bcc = array();
				preg_match_all($pattern, $this->_getParam("email_bcc"), $email_bcc);
				$email_bcc = current($email_bcc);
				$zend_mail->addBcc($email_bcc);
				
				
//				$subject = ($subject) ? $subject : "RE:" . $showMmails_MODEL->subject;
				$zend_mail->setSubject($subject);
				$zend_mail->setFrom("newsmemory@tecnavia.com", "Newsmemory Service Desk");
				
				$zend_mail->setBodyHtml($mail_content, "UTF-8");
				
				$html2text = new Bugs_Common_HtmlTotext();
				$html2text->html = $mail_content;
				$plain_text = $html2text->get_text();
				
				$zend_mail->setBodyText($plain_text, "UTF-8");
				
				try
				{
					$zend_mail->send();
					
					$ouput["title"] = "Success:";
					$ouput["msg"] = "Message sent!";
					$ouput["msg_type"] = "success";
					
					if($action_type == "compose")
					{
						$showMmails_MODEL->subject = $subject . " [Message from newsmemory@tecnavia.com]";
						$showMmails_MODEL->from = trim($this->_getParam("email_to"), ",");
						$showMmails_MODEL->date = date("D, d M H:i:s");
						$showMmails_MODEL->attachs = "";
						$showMmails_MODEL->text = $this->_getParam("content");
						$showMmails_MODEL->time = time();
						$showMmails_MODEL->time_read = time();
						$showMmails_MODEL->user = $user_name;
						$showMmails_MODEL->cc = $this->_getParam("email_cc");
						$showMmails_MODEL->bcc = $this->_getParam("email_bcc");
					}
					
					if(!$showMmails_MODEL->assigntime)
					{
						$showMmails_MODEL->assigntime = time();
						$showMmails_MODEL->assignuser = $user_name;
					}
					
					$email_to = "TO: " . implode(", ", $email_to) . "\r\n";
					
					$email_cc = ($email_cc) ? "CC: " . implode(", ", $email_cc) . "\r\n" : "";
					$email_bcc = ($email_bcc) ? "BCC: " . implode(", ", $email_bcc)  . "\r\n" : "";
					
					$log_mail_content = "{$email_to}"
						. "{$email_cc}"
						. "{$email_bcc}"
						. "\r\n#### Message start: ####\r\n\r\n"
						. "{$mail_content}"; 
					
					$showMmails_MODEL->log .= "<b>[{$date_str}] - User: {$user_name} (Operation: {$action_type})\r\n{$log_mail_content}</b>\r\n";
					$showMmails_MODEL->save();
				}
				catch (Exception $e)
				{
					$ouput["title"] = "Error:";
					$ouput["msg"] = $e->getMessage();
					$ouput["msg_type"] = "danger";
				}
			}
			else
			{
				$ouput["title"] = "Error:";
				$ouput["msg"] = "Missing mail recipient ";
				$ouput["msg_type"] = "danger";
			}
		}
			
		$this->view->title = $ouput["title"];
		$this->view->msg = $ouput["msg"];
		$this->view->msg_type = $ouput["msg_type"];
		
	}
	
	public function setPubbAction()
	{
		$psetups = "";
		if($this->_getParam("psetup"))
		{
			$psetups = implode(", ", $this->_getParam("psetup"));
		}
		$user_name = Bugs_Auth_Session::getIdentity()->nickname;
		$date_str = date("D, d M H:i:s O");
		$action_type = "Assign-Paper";
		
		$showMmails_MODEL = new Email_Model_ShowMails($this->_getParam("id"));
		
//		Zend_Debug::dump($this->_getParam("id"));
//		Zend_Debug::dump($showMmails_MODEL->__toArray());
		
		$showMmails_MODEL->assign_psetup = $psetups;
//		$showMmails_MODEL->assign_paper_name = $this->_getParam("pubb_name");
		$showMmails_MODEL->assign_psetup_time = time();
		$showMmails_MODEL->log .= "<b>[{$date_str}] - User: {$user_name} (Operation: {$action_type}){$psetups}</b>\r\n";
		$showMmails_MODEL->save();
		
		$matches = array();
		$pattern = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i';
		preg_match_all($pattern, $showMmails_MODEL->from, $matches);
		$email = current($matches);
		
		if($email && $psetups)
		{
			$email = current($email);
			$url = Bugs_Auth_Session::getIdentity()->bbe_base_url . "services/pubb/add-related-user-to-pubb/";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array(
				"email" => $email,
				"psetups" => $psetups
			));
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec ($ch);
			curl_close ($ch);
		}
		
		$this->view->title = "Success: ";
		$this->view->msg = "Paper assigned successfully";
		$this->view->msg_type = "success";
	}
	
	
	public function multiDeleteAction()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$user_name = Bugs_Auth_Session::getIdentity()->nickname;
		$date_str = date("D, d M H:i:s O");
		$action_type = "Multi-Delete";
		$reson = "<b>[{$date_str}] - User: {$user_name} (Operation: {$action_type}) - Reason: {$this->_getParam("reason")}</b>\r\n";
		
		$data = array(
			'time_read' => time(),
			'user' => $user_name,
			'log' => new Zend_Db_Expr("concat(log,'{$reson}')"));
		$db->update("nm_paper.show_mails", $data, array('id IN(?)' => $this->_getParam("email_ids")));
		
		
		$this->view->title = "Success: ";
		$this->view->msg = "All elements deleted successfully!";
		$this->view->msg_type = "success";
	}
	
	
	
	public function setDeadlineAction()
	{
		$showMmails_MODEL = new Email_Model_ShowMails($this->_getParam("id"));
		
		$user_name = Bugs_Auth_Session::getIdentity()->nickname;
		$date_str = date("D, d M H:i:s O");
		$action_type = "Deadline-Set";
		
		$deadline = $this->_getParam("deadline");
		$time = $this->_getParam("custom_time");
		$date = $this->_getParam("custom_date");
		
		
		if($deadline == "reset")
		{
			$showMmails_MODEL->deadline_type = null;
			$deadline = null;
		}
		elseif($deadline == "custom")
		{
			$deadline = strtotime($date . " " . $time);
			Bugs_Common_Date::convertTime($deadline, Bugs_Auth_Session::getIdentity()->timezone, "GMT");
			$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_7_CUSTOM;
		}
		
		elseif($deadline == "post_pub")
		{
			// We did not implement this type
			$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_4_POST_PUB;
		}
		elseif($deadline == "after_prod")
		{
			$deadline = strtotime(date("Y") . "-" . date("m") . "-" . date("d"). " 17:00:00");
			$deadline =  Bugs_Common_Date::convertTime($deadline, Bugs_Auth_Session::getIdentity()->timezone, "GMT");
			$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_5_AFTER_PROD;
		}
		elseif($deadline == "next_shift")
		{
			$now = time();
			
			$current_day_minute = date("Hi", $now);
			
			// USA production, before 4:30 AM [GMT] => sends to next shift at 5:00 AM [GMT]
			if($current_day_minute <= 430)
			{
				$deadline = mktime(5, 0, 0, date("m", $now), date("d", $now), date("Y", $now));
			}
			// Europe production, between 4:30 AM and 3:00 PM GMT => sends to next shift at 3:00 PM [9:00 AM CET] 
			elseif(($current_day_minute > 430) && ($current_day_minute <= 1500))
			{
				$deadline = mktime(15, 0, 0, date("m", $now), date("d", $now), date("Y", $now));
			}
			// USA production after 3:00 PM => sends to next shift (day after at 5:00 AM [GMT])
			else
			{
				$tomorrow_ts = $now + 86400;
				$deadline = mktime(5, 0, 0, date("m", $tomorrow_ts), date("d", $tomorrow_ts), date("Y", $tomorrow_ts));
			}
			
			
			$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_6_NEXT_SHIFT;
		}
		elseif($deadline == "never")
		{
			$deadline = self::DEADLINE_NEVER;
			$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_8_NEVER;
		}
		elseif(is_numeric($deadline))
		{
			if($deadline == 1800){
				$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_1_URGENT_30_m;
			}elseif($deadline == 3600){
				$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_2_ASAP_1_h;
			}elseif($deadline == 10800){
				$showMmails_MODEL->deadline_type = Email_Model_DeadlineType::_3_DEFAULT_3_h;
			}
			
			$deadline = time() + $deadline;
		}
		
		$deadline_str = "reset";
		if($deadline)
		{
			$deadline_str = date("D, d M H:i:s O", $deadline);
		}
		
		
		$showMmails_MODEL->deadline = $deadline;
		
		if($showMmails_MODEL->deadline_type == Email_Model_DeadlineType::_6_NEXT_SHIFT)
		{
			$showMmails_MODEL->assignuser = null;
			$showMmails_MODEL->assigntime = null;
		}
		else
		{
//			$showMmails_MODEL->assignuser = $user_name;
//			$showMmails_MODEL->assigntime = time();
		}
		
		$showMmails_MODEL->log .= "<b>[{$date_str}] - User: {$user_name} (Operation: {$action_type}) {$deadline_str}</b>\r\n";
		$showMmails_MODEL->save();
		
		$this->view->title = "Success: ";
		$this->view->msg = "Deadline set successfully!";
		$this->view->msg_type = "success";
	}
	
}	
