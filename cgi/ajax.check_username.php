<?php
namespace obus;

//==========================================
// ajax checkings vor frontend validations
// ver 1.0
// © genom_by
// last updated 3 dec 2015
//==========================================


include_once 'utils.inc.php';
include_once 'dbObjects.class.php';

use LinkBox\Logger as Logger;
use LinkBox\Utils as Utils;
//print_r($_GET);
//parse_str($_POST["lbx_registerForm"], $ajax);
//print_r($ajax);
/*
Logger::log('POSTname:'.$_POST['inputName']);
Logger::log('POSTemail:'.$_POST['inputEmail']);
Logger::log('val_type:'.$_GET['val_type']);
*/


ob_start();
Logger::log('ajax:'.$ajax);
$goodForRegister = false; // true - good for using. false - validation will fail
$ret_val = null;

switch( Utils::cleanInput($_GET['val_type']) ){

	case "name":
	try{
		$ret_val = User::isThereSameUser(Utils::cleanInput($_POST['inputName']),"");
		}catch(\Exception $e){$ret_val = false;}
		if ( $ret_val === false ) {
			$goodForRegister = true;
		}
		break;
	case "email":
	try{	
		$ret_val = User::isThereSameUser("", Utils::cleanInput($_POST['inputEmail']));
		}catch(\Exception $e){$ret_val = false;}		
		if ( $ret_val === false ) {
			$goodForRegister = true;
		}	
		break;
	default:
		$goodForRegister = false;
}
if(is_null($ret_val)){
	Logger::log('Error attempting the same user:'.User::$errormsg);	
}
sleep(1);

ob_end_clean();
//Logger::log('$goodForRegister = '.$goodForRegister ? 'true':'false');
header('Content-type: application/json');

//=========


//Logger::log("reply sent for user [{$_POST['inputName']}] and mail [{$_POST['inputEmail']}]:".$goodForRegister);	
echo(json_encode($goodForRegister)); // true - good for using. false - validation will fail


//User::isThereSameUser("user12","1v@v.v");

die();
//=========