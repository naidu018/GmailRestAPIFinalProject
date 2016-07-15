<?php

class Email_Model_ShowMails extends Bugs_Common_Model
{
	protected $__dbTable_name = "Email_Model_DbTable_ShowMails";
	
	public $id;
	public $date;
	public $time;
	public $from;
	public $cc;
	public $subject;
	public $attachs;
	public $text;
	public $user;
	public $time_read;
	public $assignuser;
	public $assigntime;
	public $log;
	public $assign_psetup;
	public $assign_paper_name;
	public $assign_psetup_time;
	public $deadline;
	public $deadline_type;
}