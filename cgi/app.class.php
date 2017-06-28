<?php
//==========================================
// Application class
// ver 1.0
// © genom_by
// last updated 28 jun 2017
//==========================================

namespace obus;

use PDOException;
use LinkBox\Logger as Logger;

include_once 'auth.inc.php';
include_once 'settings.inc.php';

class App{
	public static $errormsg;	//error(s) when executing
	
	private $db;	// database connection
	protected static $availablePages = array('login', 'logout', 'register',
						'chart', 'dataset', 'profile', 'settings', 'howto');
	protected static $urlHead = 'http://';
	
	
	/* SETTINGS:
	http://".$_SERVER['HTTP_HOST'].'/'.SITE_ROOT."/".SITE_STARTPAGE
	define(SITE_ROOT, 'tt/obus');
	define(SITE_STARTPAGE, 'cgi/hchartLine.php');
	define(SITE_DIR, 'D:\Denwer3.5\home\localhost\www\tt\obus'); */
	
	public static function link($page){
		$p404 = self::$urlHead.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/cgi/404.php';
		if( empty($page) ) return $p404;
		if( ! in_array($page, self::$availablePages ) ){
			return $p404;
		}else{
			$mainBody = self::$urlHead.$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/';
			switch($page){
				case 'login': $link = $mainBody.'cgi/loginpage.php';	break;
				case 'logout': $link = $mainBody.'cgi/loginpage.php?action=logout';	break;
				case 'register': $link = $mainBody.'cgi/registerpage.php';	break;
				case 'chart': $link = $mainBody.'cgi/hchartLine.php';	break;
				case 'dataset': $link = $mainBody.'cgi/obus-test.php';	break;
				case 'profile': $link = $mainBody.'cgi/profile.php';	break;
				case 'settings': $link = $mainBody.'cgi/settings.php';	break;
				case 'howto': $link = $mainBody.'cgi/howto.php';	break;
			default:
				$link = $p404;
			}
			return $link;
		}
	}

}//class App