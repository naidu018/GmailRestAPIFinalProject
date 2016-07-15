<?php

class Bugs_Pubb_Service
{
	/**
	 * Gets all publications from BBE split in 2 groups: Suggested / All other Publications
	 * @param string $email_from: string containing an email
	 * 	(the email will be extracted from the string 
	 * 	eg "Anthony Oppedisano <aoppedisano@tecnavia.com>" => "aoppedisano@tecnavia.com")
	 */
	public static function getAll_json($email_from)
	{
		$url = Bugs_Auth_Session::getIdentity()->bbe_base_url . "services/pubb/get-all/email_from/" . urlencode($email_from);
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
}