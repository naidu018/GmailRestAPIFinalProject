<?php

class Bugs_Common_Date
{
	public static function howManyUnitsAgo($time, $use_short_labels = false)
	{
	
	    $time = time() - $time; // to get the time since that moment
	    
	    if($time < 0)
	    {
	    	$time = -$time;
	    }
	
	    $tokens = array (
	        31536000 => 'year',
	        2592000 => 'month',
	        604800 => 'week',
	        86400 => 'day',
	        3600 => 'hour',
	        60 => 'minute',
	        1 => 'second'
	    );
	    $plural = "s";
	    
	    if($use_short_labels)
	    {
	    	$tokens = array (
		        31536000 => 'y',
		        2592000 => 'm',
		        604800 => 'w',
		        86400 => 'd',
		        3600 => 'h',
		        60 => 'm',
		        1 => 's'
		    );
		    $plural = "";
	    }
	    
	    
	
	    foreach ($tokens as $unit => $text) {
	        if ($time < $unit) continue;
	        $numberOfUnits = floor($time / $unit);
	        
	        $minutes = "";
	        if($unit == 3600)
	        {
	        	$minutes = ":". str_pad(floor(($time - ($numberOfUnits * $unit)) / 60), 2, "0", STR_PAD_LEFT);
	        }
	        
	        return $numberOfUnits.$minutes.' '.$text.(($numberOfUnits>1) ? $plural : '');
	    }
	}
	
	
	public static function convertTime($timestamp, $timezone_FROM, $timezone_TO)
	{
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone($timezone_FROM));
		$date->setDate(date("Y", $timestamp), date("m", $timestamp), date("d", $timestamp));
		$date->setTime(date("H", $timestamp), date("i", $timestamp), date("s", $timestamp));
		$date->setTimezone(new DateTimeZone($timezone_TO));
		
		return strtotime($date->format("Y-m-d H:i:s"));
	}
	
	
}