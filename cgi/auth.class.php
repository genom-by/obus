<?php
//==========================================
// Auth class
// ver 1.0
// © genom_by
// last updated 27 jun 2017
//==========================================

namespace obus;
session_start();

use PDO;
use PDOException;
use LinkBox;
use LinkBox\Logger as Logger;
use LinkBox\Utils as Utils;

//include_once 'settings.inc.php';
include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';

class Auth{
	public static $errormsg;	//error(s) when executing
	
	protected static $uid;	// database id of registered user 
	
	private $db;	// database connection
	
	private static $objectType; // what kind of table is the class
	private static $sqlGetAll;	// SQL query for getting all records without WHERE clause
	private static $sqlGetAllOrdered;	// SQL query for getting all records ordered (if applicable)

	public function __construct($name_, $uid_){
		$this->name = Utils::cleanInput($name_);
		$this->uid = Utils::cleanInput($uid_);
	}
	
	public static function whoLoggedID(){
		if( ! empty($_SESSION["user_id"]) ){
			return $_SESSION["user_id"];
		}else{
			return false;
		}
	}
	public static function notLogged(){
		if( empty($_SESSION["user_id"]) ){
			return true;
		}else{
			return false;
		}
	}
	
	public static function logout(){
		unset(self::$uid);
		self::rememberMe('user', false);
		session_destroy();
	}
	
	public static function loginUser($user, $pwd, $remember=false){
		if( ! empty($user) ){
			if( empty($pwd) ){
				return false;
			}else{
				if(self::userKnowPassword($user, $pwd)){
					session_start();
					$_SESSION["user_id"] = $user->id;
					$_SESSION["user_name"] = $user->name;
					self::$uid = $user->id;
					
					if($remember == true){
						self::rememberMe($user, true);
					}
					
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}	
	}
	
	public static function userKnowPassword($user, $pwd){
		
		$res = Utils::compareStringHash($pwd, $user->pwdHash);

		Logger::log('user'.$user->pwdHash);
		if($res){return true;}else{return false;}
	}
	//TODO clearance
	public static function rememberMe( $user, $re ){
		
		if( $re ) {
			setcookie ("member_login",$user->name,time()+ (10 * 365 * 24 * 60 * 60));
			setcookie ("member_password",$user->pwdHash,time()+ (10 * 365 * 24 * 60 * 60));
		} else {
			if(isset($_COOKIE["member_login"])) {
				setcookie ("member_login","");
			}
			if(isset($_COOKIE["member_password"])) {
				setcookie ("member_password","");
			}
		}
	}
	
}//class Auth