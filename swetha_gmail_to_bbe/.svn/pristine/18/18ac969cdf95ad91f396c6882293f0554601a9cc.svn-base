<?php

class Email_Model_Service_ShowMails
{
	public static function countUnread()
	{
		$now = time();
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array("m"=>"show_mails"), array("count(*) as count", "min(time) as oldest_mail_time"), "nm_paper")
			->where("(time_read is null OR time_read = 0 OR time_read = '')")
			->where("(assigntime is null OR assigntime = 0 OR assigntime = '')")
			->where("(deadline is null or deadline < {$now})");
		
		return $db->fetchRow($select);
	}
	
	public static function getInbox()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()->from(array("m"=>"show_mails"), array("id", "date", "from", "subject"), "nm_paper")
			->where("(time_read is null OR time_read = 0 OR time_read = '')")
//			->where("(deadline is null or deadline < {$now})")
			->where("(assigntime is null OR assigntime = 0 OR assigntime = '')")
			->order("time");
		
		return $db->fetchAll($select);
	}
}