<?php
namespace obus;

//==========================================
// DB objecs classes
// ver 1.0
// © genom_by
// last updated 13 sep 2016
//==========================================

use PDO;
use PDOException;
use LinkBox;
use LinkBox\Logger as LiLogger;
use LinkBox\Utils as Ut;

//include_once 'settings.inc.php';
include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';

Interface IDBObjects{
	function save();
	function update();
	function delete();
	static function getByID($id);
	static function getBy($field, $value);
	static function get();
}

Interface ObjectEntity{
	function isThereSameObject();
	function validateSave();
	function validateUpdate();
}

class DBObject{
	public static $errormsg;	//error(s) when executing
	public $name;
	
	private $db;	// database connection
	private $sqlPDOSave;

	public static function getAllRecords($object_){
		switch($object_){
			case 'obus': $table='obus';break;
			case 'station': $table='station';break;
			case 'itinerary': $table='itinerary';break;
			default: self::$errormsg = "No such table: {$object_}"; return false;
		}
		$objects = LinkBox\DataBase::getAll($table);
		if($objects === false){
			self::$errormsg = "No {$object_} data: ".LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
				return false;
			}
		else return $objects;		
	}

	public function saveObject($pdosql){
		$res = LinkBox\DataBase::executeInsert($pdosql);
		if($res === false){
			self::$errormsg = 'error while saving into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
		}
		else return true;
	}
	
}
//implements IDBObjects, ObjectEntity
class Obus  extends DBObject{
	
	public function __construct($name_){
		$this->name = Ut::cleanInput($name_);
		$this->sqlPDOSave = "INSERT INTO obus(name) VALUES(':1:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		return $this->saveObject($pdosql);
	}
	
	public static function getAll(){
		return self::getAllRecords('obus');		
	}
}

class Station extends DBObject{
	
	public function __construct($name_){
		$this->name = Ut::cleanInput($name_);
		$this->sqlPDOSave = "INSERT INTO station(name) VALUES(':1:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		return $this->saveObject($pdosql);
	}
		
	public static function getAll(){
		return self::getAllRecords('station');	
	}
}

class Itinerary extends DBObject{
	private $obus;
	private $station;
	private $startTime;
	public function __construct($itineraryName_, $obus_, $station_, $startTime_){
		$this->name = Ut::cleanInput($itineraryName_);
		$this->obus = Ut::cleanInput($obus_);
		$this->station = Ut::cleanInput($station_);
		$this->startTime = Ut::HHmm2Int( Ut::cleanInput($startTime_) );
		$this->sqlPDOSave = "INSERT INTO itinerary(name, start_station, start_time) VALUES(':iName:', :iStSt:, :iStTime:)";
	}
	public function save(){
		$arrParameters = array(
		":iName:"=>$this->name,
		":iStSt:"=>$this->station,
		":iStTime:"=>$this->startTime);
		$pdosql = strtr($this->sqlPDOSave, $arrParameters);
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//print_r($pdosql);
		return $this->saveObject($pdosql);
	}
		
	public static function getAll(){
		return self::getAllRecords('itinerary');	
	}
}

class User implements IDBObjects, ObjectEntity{

	private $db;
	
	public static $errormsg;
	private $errorEmptyFields;
	
	public $id;
	public $regDate;
	public $email;
	public $name;
	public $pwdHash;
	
	public function __construct($regDate_, $email_, $name_, $pwd){
		$this->regDate = Utils::cleanInput($regDate_);
		$this->email = Utils::cleanInput($email_);
		$this->name = Utils::cleanInput($name_);
		//$this->pwdHash = Utils::cleanInput($pwdHash_);
		//fill empty string as pwd for further validation error when saving
		if(empty($pwd)) {$this->pwdHash = "";} else
			{$this->pwdHash = Utils::getHash($pwd);}
		$this->id = null;
		self::$errormsg = "just created {$this->name} {$this->email}";
	}
	public function save(){

	//-
		if( ! $this->validateSave() ) {
			self::$errormsg = 'Validation error when saving: '.self::$errormsg;
			return false;
		}
	//-
		$this->db = DataBase::connect();
	
		$SQLpart = "INSERT INTO users(regDate, email, pwdHash, name)";
		$PDOpart = " VALUES(:regDate, :email, :pwdHash, :name)";
		
		//Logger::log(serialize($fieldsWithoutPK));
		
		$sql = $SQLpart.$PDOpart;
		
		$statement = $this->db->connection->prepare($sql);
		if(!$statement){
			self::$errormsg = 'statement didn\'t prepare';
			return false;
		}
		
	$bindState = $statement->bindParam(":regDate", $this->regDate);
	$bindState = $bindState && $statement->bindParam(":email",$this->email, PDO::PARAM_STR);
	$bindState = $bindState && $statement->bindParam(":name",$this->name, PDO::PARAM_STR);
	$bindState = $bindState && $statement->bindParam(":pwdHash",$this->pwdHash, PDO::PARAM_STR);
		
		if(!$bindState){
			self::$errormsg = $statement->errorInfo();
			return false;
		}
		
		if ($statement->execute() ){
			$this->id = $this->db->connection->lastInsertId();
			self::$errormsg = 'saved';
			return true;
			}
		else{
			self::$errormsg = 'Save failed: '.implode(' ', $statement->errorInfo() );
			return false;
		}
	}
	public function update(){
	}
	public function delete(){}
	public static function getByID($id){}
	
	public static function getBy($field, $value){
		if(!isset($field) or !isset($value)){
			return null;
		}
		$field = Utils::cleanInput($field);
		$value = Utils::cleanInput($value);
		
		$db = DataBase::connect();
	
		$sql = "SELECT id_user FROM users WHERE {$field}=?";
		
		$stmt = $db->connection->prepare($sql);
		$stmt->bindValue(1, $value);
		$stmt->execute();

		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//Logger::log(serialize($fieldsWithoutPK));
//print_r($rows);
	}
	
	public static function get(){}
	
	// are there values for object or not?
	protected function isEmptyFields(){

		$this->errorEmptyFields = "";
	
	if(empty($this->regDate)) $this->errorEmptyFields .= " register date ";
	if(empty($this->email)) $this->errorEmptyFields .= " email ";
	if(empty($this->name)) $this->errorEmptyFields .= " name ";
	if(empty($this->pwdHash)) $this->errorEmptyFields .= " password ";

		if( 
			empty($this->regDate) or 
			empty($this->email) or
			empty($this->name) or
			empty($this->pwdHash) )
		{return true;} 
		else {return false;}
	}
	
	// interface ObjectEntity
	public function isThereSameObject(){
	//if($rows > 0) {return true;} else {return false;}
	if ( $this->isThereSameUser($this->name, $this->email) === true ) {return true;} else {return false;}
	}
	
	//returns false if can not save
	public function	validateSave(){
	
		if( $this->isEmptyFields() ) {
			self::$errormsg = 'Not all data is presented. Cannot create user. <br/> Empty data:'.$this->errorEmptyFields;
			return false;
		}
		
		if( $this->isThereSameObject() ) {
			self::$errormsg = 'User wth the same name or email exists now. Cannot create the same user.';
			return false;
		}
		
		if( ! Utils::isValidEmail($this->email ) ) {
			self::$errormsg = 'Provided email spelled incorrect. Re-type email or provide another one.';
			return false;
		}
	
		return true;
	}
	
	public function	validateUpdate(){}
	
	/*
	//checks if there's user with the same name OR email
	//if error occured - returns null
	//if exists - returns true, otherwise false
	*/
	public static function isThereSameUser($name, $email){
		
		if( (!isset($name)) and ( !isset($email)) ){
			return null;
		}
		$name = Utils::cleanInput($name);
		$email = Utils::cleanInput($email);
		
		try{
			$db = DataBase::connect();
		}catch(Exception $e){
			$this->errormsg = 'Couldn\'t open DB. Error: '.$e->getMessage();
			return null;
		}		
		$sql = "SELECT COUNT(id_user) FROM users WHERE name=? OR email=?";

		$stmt = $db->connection->prepare($sql);
		$bindState = $stmt->bindValue(1, $name, PDO::PARAM_STR);
		$bindState = $bindState && $stmt->bindValue(2, $email, PDO::PARAM_STR);
		if(!$bindState){
			self::$errormsg = "Fn isThereSameUser failed:". $statement->errorInfo();
			return null;
		}
		
		try{
			$stmt->execute();
		}catch(PDOException $pe){
			$this->errormsg = 'Couldn\'t select from DB. Error: '.$pe->getMessage();
			return null;
		}
		$rows = $stmt->fetchColumn();

		if($rows > 0) {return true;} else {return false;}
	
	}
}

class DataBase123{
	protected static $instance;
	public $connection;
	public $errormsg = '';
	public $status = 'disconnected';
	private function __construct()
	{
		if (is_null($this->connection)){
			try{
				if(defined(ISSQLITE))
				{
					if( ! file_exists(DBPATHSQLITE) ){
						//$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);
						//$this->createSQLITEtable($this->connection);
						throw new \Exception('Database not found');
					}else{
						$this->connection = new PDO(DSNSQLITE.DBPATHSQLITE);				
					}
				}else{
					$this->connection = new PDO(DSNMYSQL, DBUSER, DBPWD);
				}
				$this->connection->exec('SET NAMES utf8');
				$this->status = 'connected';
				if(defined('DEBUG_MODE')){
					$this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					//$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
				}
			}catch(PDOException $pe){
				$this->errormsg = 'Not connected to DB. Error: '.$pe->getMessage();
			}
		}		
	}
	
// dummy fn	
	public function close(){
	}	
// disconnect fn	
	public function disconnect(){
		if( ! is_null(self::$instance) ){
		//if( ! is_null($this->instance) ){
		//var_dump(self::$instance); //DataBase object
		//var_dump($this->instance); //NULL
		//self::$instance->__destruct();
		self::$instance = null;
		$this->status = 'disconnected';		
		}
		//$this->status = 'disconnected';		
		//}
	}
	public static function connect()
	{
		if(is_null(self::$instance) ){
			self::$instance = new DataBase();
		}
		return self::$instance;
	}
	
/*---------------------------------------------------------------------------------
* perform Update operation
* @table string what table to update
* @arrayFV array key=>value pairs where key is fieldname in database
* @condFVO array : fieldname=>(value=>operation)
* @return mixed count of affected rows or false on error
*/
	public function Update($table, $arrayFV, $condFVO)
	{
		$SQLpart = "UPDATE {$table} SET ";
		$CONDpart = " WHERE ";
		
		foreach($arrayFV as $field => $value){
			$SQLpart .= "$field=:{$field}, ";
		}
		$SQLpart = rtrim($SQLpart,', ');
		
		foreach($condFVO as $field => $valueOp){
			list($value, $operation) = each($valueOp);
			$CONDpart .= "$field $operation :{$field} AND ";
		}
		//$CONDpart = rtrim($CONDpart,' AND ');
		$CONDpart = substr($CONDpart, 0, -5);
		
		$sql = $SQLpart.$CONDpart;
//echo 'sql:'.$sql;		
		$statement = $this->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = 'statement didn\'t prepare';
			return false;
		}
		
		reset($arrayFV);
		foreach($arrayFV as $field => $value){
			$res = $statement->bindValue(":$field", $value);//, $this->sqlFieldsPDOTypes[$field]);
			if(!$res){
				$this->errormsg = $statement->errorInfo();
				return false;
			}
		}
		reset($condFVO);
		foreach($condFVO as $field => $valueOp){
			list($value, $operation) = each($valueOp);
			$res = $statement->bindValue(":$field", $value);//, $this->sqlFieldsPDOTypes[$field]);
			if(!$res){
				$this->errormsg = $statement->errorInfo();
				return false;
			}
		}
		
		if ($statement->execute() ){
			$affCount = $statement->rowCount();
			
			return $affCount;
			}
		else{
			$this->errormsg = 'Save failed: '.implode(' ', $statement->errorInfo() );
			return false;
		}
	}
/*---------------------------------------------------------------------------------
* perform Insert operation
* @table string to what save
* @arrayFV array key=>value pairs where key is fieldname in database
* @return mixed last inserted id or false on error
*/
	public function Insert($table, $arrayFV)
	{
		$SQLpart = "INSERT INTO {$table}(";
		$PDOpart = " VALUES(";
		
		//Logger::log(serialize($fieldsWithoutPK));
		foreach($arrayFV as $field => $value){
			$SQLpart .= $field;
			$SQLpart .= ', ';
			$PDOpart .= ':'.$field;
			$PDOpart .= ', ';			
		}
		$SQLpart = rtrim($SQLpart,', ');
		$SQLpart .= ') ';
		$PDOpart = rtrim($PDOpart,', ');
		$PDOpart .= ') ';
		
		$sql = $SQLpart.$PDOpart;
		
		$statement = $this->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = 'statement didn\'t prepare';
			return false;
		}
		reset($arrayFV);
		foreach($arrayFV as $field => $value){
			$res = $statement->bindValue(":$field", $value);//, $this->sqlFieldsPDOTypes[$field]);
			if(!$res){
				$this->errormsg = $statement->errorInfo();
				return false;
			}
		}
		
		if ($statement->execute() ){
			return $this->connection->lastInsertId();
			}
		else{
			$this->errormsg = 'Save failed: '.implode(' ', $statement->errorInfo() );
			return false;
		}
	}
	
	/*
	//
	*/
	public function getLinks($orderby = '', $currentpage=1)
	{
		$sqlorder = " ORDER BY ";
		switch($orderby){
		case 'user': $sqlorder .= 'user'; break;
		case 'email': $sqlorder .= 'email'; break;
		case 'date': 
		default:
			$sqlorder .= 'time DESC';
		}
		/*if($currentpage == 0)
			$sql = "SELECT msg_id, UNIX_TIMESTAMP(time) AS time, IP, agent, user, email, message, homepage FROM messages" . $sqlorder;
			else{
			$sql = "SELECT msg_id, UNIX_TIMESTAMP(time) AS time, IP, agent, user, email, message, homepage FROM messages" . $sqlorder . " LIMIT ".(($currentpage - 1) * PAGING_NUM_PER_PAGE) .", ". PAGING_NUM_PER_PAGE;
			}
		*/
		if( ! defined(ISSQLITE))
		{
			$sql = "SELECT msg_id, UNIX_TIMESTAMP(time) AS time, IP, link, name, isfolder FROM links" . $sqlorder . " LIMIT ".(($currentpage - 1) * PAGING_NUM_PER_PAGE) .", ". PAGING_NUM_PER_PAGE;
		}else{
		//strftime('%s', 'now', 'localtime')
				$sql = "SELECT msg_id, time, IP, link, name, isfolder FROM links" . $sqlorder . " LIMIT ".(($currentpage - 1) * PAGING_NUM_PER_PAGE) .", ". PAGING_NUM_PER_PAGE;
				//$sql = "SELECT msg_id, strftime('%s', time, 'localtime') AS time, IP, agent, user, email, message, homepage FROM messages" . $sqlorder . " LIMIT ".(($currentpage - 1) * PAGING_NUM_PER_PAGE) .", ". PAGING_NUM_PER_PAGE;	
		}	
		//echo $sql;
		if(!$this->connection) 	{
			return false;
		}
		$statement = $this->connection->prepare($sql);
		if(!$statement){
			$this->errormsg = implode(' ', $this->connection->errorInfo() );
			return false;
		}
		if(!$statement->execute() ){
			$this->errormsg = implode(' ', $statement->errorInfo() );
			return false;
		}
		
		$messages = $statement->fetchAll();
		if($messages === false ){
			$this->errormsg = implode(' ', $statement->errorInfo() );
			return false;
		}else{
			return $messages;
		}		
	}
	
	
}
?>