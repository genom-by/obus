<?php
namespace LinkBox;

//==========================================
// DB access class
// ver 2.0
// © genom_by
// last updated 28 oct 2015
//==========================================

use PDO;
use PDOException;

include_once 'utils.inc.php';
include_once 'settings.inc.php';

class DataBase{
	protected static $instance;
	public $connection;
	public static $errormsg = '';
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
				$this->connection->exec("SET CHARACTER SET 'utf8'");
				$this->status = 'connected';
				if(defined('DEBUG_MODE')){
					$this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					//$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
				}
			}catch(PDOException $pe){
				self::$errormsg = 'Not connected to DB. Error: '.$pe->getMessage();
			}
		}		
	}
	public function __destruct()
	{
		$this->disconnect();
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
			Logger::Log('created and connected');
		}
		return self::$instance;
	}
	public static function getPDO(){
		if(! is_null(self::$instance) ){	
			return self::$instance->connection;
		}
		else return false;
	}
	public static function checkConnect(){
		
		if(! self::connect() ){
			self::$errormsg = 'Not connected to db.';
			Logger::log(self::$errormsg);
			return false;
		} else return true;	
	}
	
	public static function executeInsert($query){
		if(empty($query)){
			self::$errormsg = 'No query provided.';
			return false;
		}
		
		if(! self::checkConnect() ){
			return false;
		}
		//print_r($pdosql);
		try{
			$res = self::getPDO()->exec($query);
		}catch(\Exception $e){
			self::$errormsg = 'Error while saving into DB: '.$e->getMessage();
			Logger::log(self::$errormsg);
			return false;
		}
		//$res = $this->db->connection->exec($pdosql);
		if($res != 1){
			Logger::log('Error while saving into DB.');
			return false;
		}
		else return true;
	}	
	public static function executeDelete($query){
		if(empty($query)){
			self::$errormsg = 'No query provided.';
			return false;
		}
		
		if(! self::checkConnect() ){
			self::$errormsg = 'No connection to DB.';
			Logger::log(self::$errormsg);		
			return false;
		}
		//print_r($pdosql);
		try{
			$res = self::getPDO()->exec($query);
		}catch(\Exception $e){
			self::$errormsg = 'Error while deleting from DB: '.$e->getMessage();
			Logger::log(self::$errormsg);
			return false;
		}
		//$res = $this->db->connection->exec($pdosql);
		if($res === false){
			self::$errormsg = 'Error while deleting from DB.';
			Logger::log(self::$errormsg);
			return false;
		}
		else return true;
	}
	
	public static function getAll($table){
	
		if(empty($table)){
			self::$errormsg = 'No table provided.';
			return false;
		}
		if(! self::checkConnect() ){
			return false;
		}
		switch($table){
			case 'obus': $sql = "SELECT id_obus, name from obus ORDER BY name"; break;
			case 'destination': $sql = "SELECT id_dest, name, dest_seq from destination ORDER BY name"; break;
			case 'sequences': $sql = "SELECT id_seq, name, destination from sequences ORDER BY name"; break;
			case 'sequencesDestination': $sql = "SELECT (destination.name  || ' via ' || sequences.name) AS dest, id_seq, sequences.name 
from sequences LEFT JOIN destination ON sequences.destination = destination.id_dest 
ORDER BY dest"; break;
			case 'station': $sql = "SELECT id_station, name, shortName from station"; break;
			case 'pitstop_type': $sql = "SELECT id_pittype, type from pitstop_type"; break;
			case 'itinerary': $sql = "SELECT id_itin, itinerary.name, start_station, start_time, station.name AS statName from itinerary LEFT JOIN station ON itinerary.start_station = station.id_station"; break;
			case 'pitstop': $sql = "SELECT id_pitstop, pitstop.id_station, station.shortName, station.name AS statName, id_itinerary, itinerary.name AS itinName, `time` FROM pitstop LEFT JOIN station ON pitstop.id_station = station.id_station LEFT JOIN itinerary ON pitstop.id_itinerary = itinerary.id_itin"; break;
			default: $sql = '';
		}
		try{
		//-----
			$statement = self::getPDO()->prepare($sql);
			if(!$statement){
				self::$errormsg = implode(' ', self::getPDO()->errorInfo() );
				return false;
			}
			if(!$statement->execute() ){
				self::$errormsg = implode(' ', $statement->errorInfo() );
				return false;
			}
			
			$obuses = $statement->fetchAll();
			if($obuses === false ){
				self::$errormsg = implode(' ', $statement->errorInfo() );
				return false;
			}else{
				return $obuses;
			}		
		//-----
		}catch(\Exception $e){
			self::$errormsg = "Error while getting all records from {$table}: ".$e->getMessage();
			Logger::log(self::$errormsg);
			return false;		
		}
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
echo 'sql:'.$sql;		
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