<?php

class Email_Model_DeadlineType
{
	const _1_RESET			= 0;
	const _1_URGENT_30_m	= 10;
	const _2_ASAP_1_h		= 20;
	const _3_DEFAULT_3_h	= 30;
	const _4_POST_PUB		= 40;
	const _5_AFTER_PROD		= 50;
	const _6_NEXT_SHIFT		= 60;
	const _7_CUSTOM			= 70;
	const _8_NEVER			= 80;
	
	public static $assoc = array(
		1800 => self::_1_URGENT_30_m,
		3600 => self::_2_ASAP_1_h,
		10800 => self::_3_DEFAULT_3_h,
		'post_pub' => self::_4_POST_PUB,
		'after_prod' => self::_5_AFTER_PROD,
		'next_shift' => self::_6_NEXT_SHIFT,
		'never' => self::_8_NEVER,
		'custom' => self::_7_CUSTOM,
	);
	
	public static $icons = array(
		self::_1_URGENT_30_m => '<span class="glyphicon glyphicon-fire priority-switch danger"></span>',
		self::_2_ASAP_1_h => '<span class="glyphicon glyphicon-fire priority-switch off"></span>',//'<button class="btn btn-warning btn-xs" type="button"><span class="glyphicon glyphicon-flash"></span></button>',
		self::_3_DEFAULT_3_h => '<span class="glyphicon glyphicon-fire priority-switch off"></span>',//'<button class="btn btn-info btn-xs" type="button"><span class="glyphicon glyphicon-leaf"></span></button>',
		self::_4_POST_PUB => '<span class="glyphicon glyphicon-fire priority-switch off"></span>',//'<button class="btn btn-info btn-xs" type="button"><span class="glyphicon glyphicon-book"></span></button>',
		self::_5_AFTER_PROD => '<span class="glyphicon glyphicon-fire priority-switch off"></span>',//'<button class="btn btn-info btn-xs" type="button"><span class="glyphicon glyphicon-play"></span></button>',
		self::_6_NEXT_SHIFT => '<span class="glyphicon glyphicon-fire priority-switch off"></span>',//'<button class="btn btn-primary btn-xs" type="button"><span class="glyphicon glyphicon-forward"></span></button>',
		self::_8_NEVER => '<span class="glyphicon glyphicon-fire priority-switch off"></span>',//'<button class="btn btn-success btn-xs" type="button"><span class="glyphicon glyphicon-ban-circle"></span></button>',
		self::_7_CUSTOM => '<span class="glyphicon glyphicon-fire priority-switch off"></span>',//'<button class="btn btn-primary btn-xs" type="button"><span class="glyphicon glyphicon-calendar"></span></button>',
	);
}