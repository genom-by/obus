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

	private static $objectType; // what kind of table is the class
	
	public static function getAllRecords($object_, $byID=-1){
		switch($object_){
			case 'obus': $table='obus';break;
			case 'station': $table='station';break;
			case 'itinerary': $table='itinerary';break;
			case 'pitstop_type': $table='pitstop_type';break;
			case 'way': $table='pitstop';break;
			case 'destination': $table='destination';break;
			case 'sequences': $table='sequences';break;
			case 'sequencesStations': $table='sequencesStations';break;
			case 'sequencesStationsBYID': $table='sequencesStationsBYID';break;
			default: self::$errormsg = "No such table: {$object_}"; return false;
		}
		$objects = LinkBox\DataBase::getAll($table, $byID);
		if($objects === false){
			self::$errormsg = "No {$object_} data: ".LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
				return false;
			}
		else return $objects;		
	}
	//TODO : get by ID
	public static function getEntryByID( $id_){
//LiLogger::log("self - static".static::$objectType);			
//LiLogger::log("res".serialize($dest));		
		switch(static::$objectType){
			case 'obus': $table='obus';break;
			case 'station': $table='station';break;
			case 'itinerary': $table='itinerary';break;
			case 'pitstop_type': $table='pitstop_type';break;
			case 'way': $table='pitstop';break;
			case 'destination': $table='destination';break;
			case 'sequences': $table='sequences';break;
			case 'sequencesStations': $table='sequencesStations';break;
			default: self::$errormsg = "No such table: {$object_}"; return false;
		}
//LiLogger::log( "DBObject.getEntryByID: {$id_}".self::$objectType );		
		$object = LinkBox\DataBase::getEntryByID($table, $id_);
		if($object === false){
			self::$errormsg = "No {$table} data: ".LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
				return false;
			}
		else return $object;		
	}
	
	public static function deleteEntry($table, $id, $id_column=""){
		if (empty($table)) return false;
		if (empty($id)) return false;
		if (empty($id_column) ) $id_column = "id_{$table}";
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		//$conn->beginTransaction();
		if ($db->executeDelete("DELETE FROM {$table} WHERE {$id_column}={$id}") ){
			if ($table == 'itinerary'){
				// delete all pitstops for this itinerary
				if (false !== $db->executeDelete("DELETE FROM pitstop WHERE `id_itinerary`={$id}") )
					{return true;}else{
				self::$errormsg = 'error while deleting pitstops: '.LinkBox\DataBase::$errormsg;
				LiLogger::log( self::$errormsg );
				return false;}
			}
			
			return true;}
		else{
			self::$errormsg = 'error while deleting DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;		
		}
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

	protected static $objectType = 'obus';
		
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
class PitType  extends DBObject{
	
	public function __construct($type_){
		$this->name = Ut::cleanInput($type_);
		$this->sqlPDOSave = "INSERT INTO pitstop_type(type) VALUES(':1:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		return $this->saveObject($pdosql);
	}
	
	public static function getAll(){
		return self::getAllRecords('pitstop_type');		
	}
}

class Station extends DBObject{

	protected static $objectType = 'station';	
	private $shortname="-";
	
	public function __construct($name_, $short_){
		$this->name = Ut::cleanInput($name_);
		$this->shortname = Ut::cleanInput($short_);
		$this->sqlPDOSave = "INSERT INTO station(name, shortName) VALUES(':1:', ':2:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		$pdosql = str_replace(':2:', $this->shortname, $pdosql);
		return $this->saveObject($pdosql);
	}
		
	public static function getAll(){
		return self::getAllRecords('station');	
	}
}
class Destination extends DBObject{
	
	protected static $objectType = 'destination';
	
	private $sequence="-";
	
	public function __construct($name_, $seq_){
		$this->name = Ut::cleanInput($name_);
		$this->sequence = Ut::cleanInput($seq_);
		$this->sqlPDOSave = "INSERT INTO destination(name, dest_seq) VALUES(':1:', ':2:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		$pdosql = str_replace(':2:', $this->shortname, $pdosql);
		return $this->saveObject($pdosql);
	}
		
	public static function getAll(){
		return self::getAllRecords('destination');	
	}
}

class Way extends DBObject{
	
	private $itinerary = 0; //main itinerary
	private $pitstops = null; //['pitstop_id'=>time]
	private $pitstopsTotal = 0;
	
	public function __construct($post_){
		$this->name = "way";//Ut::cleanInput($name_);
		$this->sqlPDOSave = "";//"INSERT INTO station(name) VALUES(':1:')";
		$this->pitstopsTotal = $post_['totalstops'];
		$this->itinerary = $post_['itinerarySelect'];
		for($i=1; $i <= $this->pitstopsTotal; $i++) {
			if(!empty($post_['stationTime'.$i])){
			$this->pitstops[$i] = array(
				'station'=>$post_['station'.$i], 
			'time'=>Ut::HHmm2Int( Ut::cleanInput( $post_['stationTime'.$i])) ,
				'pitType'=>$post_['pitType'.$i],
									);
									}
		}
	}
	public function save($post_){
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//phpnet//$conn = new PDO('sqlite:C:\path\to\file.sqlite');
		//phpnet//$stmt = $conn->prepare('INSERT INTO my_table(my_id, my_value) VALUES(?, ?)');
		//$conn = $this->getPDO(); //get raw connection
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		
		if($conn === false){
			self::$errormsg = 'error while saving pitstops into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
		}
		$stmt = $conn->prepare('INSERT INTO pitstop(id_station, time, id_pittype, id_itinerary) VALUES(:id_stat, :time, :id_pittyp, :id_itin)');
		
		try {
        $conn->beginTransaction();
			//var_dump($this->pitstops);
        foreach($this->pitstops as $pit_num => $pitstop) {
            $stmt->bindValue(':id_stat', $pitstop['station'], PDO::PARAM_INT);
            $stmt->bindValue(':time', $pitstop['time'], PDO::PARAM_INT);
            $stmt->bindValue(':id_pittyp', $pitstop['pitType'], PDO::PARAM_INT);
            $stmt->bindValue(':id_itin', $this->itinerary, PDO::PARAM_INT);
            $stmt->execute();
            sleep(1);
        }
        $conn->commit();
    } catch(PDOException $e) {
        if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
            // This should be specific to SQLite, sleep for 0.25 seconds
            // and try again.  We do have to commit the open transaction first though
            $conn->commit();
            usleep(250000);
        } else {
            $conn->rollBack();
            //throw $e;
			self::$errormsg = 'error performing pitstops transaction: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
        }
    }
		
		//return $this->saveObject($pdosql);
	}
		
	public static function getAll(){
		return self::getAllRecords('way');	
	}
	
	public static function getPitstopsByItinerary(){
		$pitstops = self::getAll();
		if( empty($pitstops) ) return false;
		
		$ways = array();
		$id__it_name = array();
		$id__stat_name = array();
		//$id__name = array();
		//groub by itinerary
		//LiLogger::log('here');
		foreach($pitstops as $stop){
			//TODO group records into array { 'itiner1'=>[  stop1=>123, stop2=>124], 'itiner2'=> [] ...}
			//LiLogger::log("stop[id_itinerary]=".$stop['id_itinerary']);
			$id__it_name[$stop['id_itinerary']] = $stop['itinName']; //a:3:{i:1;N;i:5;N;i:6;N;}
			$id__stat_name[$stop['id_station']] = $stop['statName'];
			//array_push($ways[$stop['id_itinerary']], array($stop['id_station']=>$stop['time']) );
		}
		//LiLogger::log(serialize($id__it_name));
		foreach($id__it_name as $it_id => $val){
			$ways[(string)$it_id] = array();
			$ways[((string)$it_id)]['name'] = $val;
			
			// $ways['1'=>(), '5'=>(), '6'=>()]
//a:4:{s:12:"id_itinerary";a:0:{}i:1;N;i:5;N;i:6;N;}
		}//Logger::log(serialize($ways));
		foreach($pitstops as $stop){
			array_push($ways[(string)$stop['id_itinerary']], array($stop['shortName']=>$stop['time']) );
			//array_push($ways[(string)$stop['id_itinerary']], array("itin_name"=>$stop['itinName'] ) );
			//$ways["1"=>('1'=>'444', '2'=>'888')]
		}
		
		return $ways;
	}
	
	public static function DeletePitstop($pit_id){
		if(empty($pit_id)) return false;
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		return ( $db->executeDelete("DELETE FROM pitstop WHERE id_pitstop={$pit_id}") );
	}
}

/*							sequencesStations
* set of stations for X-axis of graph
*/
class sequencesStations extends DBObject{

	protected static $objectType = 'sequencesStations';	
	private $sequence = 0; //main sequence
	private $seq_stations = null; //['pitstop_id'=>time]
	private $pitstopsTotal = 0;
	
	public function __construct($post_){
		$this->name = "sequencesStations";//Ut::cleanInput($name_);
		$this->sqlPDOSave = "";//"INSERT INTO station(name) VALUES(':1:')";
		$this->sequence = $post_['sequencesSelect'];
		$this->pitstopsTotal = $post_['totalstops'];
			
		for($i=1; $i <= $this->pitstopsTotal; $i++) {

			$this->seq_stations[$i] = array(
				'station'=>Ut::cleanInput($post_['station'.$i]), 
				'orderal'=>Ut::cleanInput($post_['orderal'.$i]),
				'pitType'=>Ut::cleanInput($post_['pitType'.$i]),
									);									
		}
	}
	public function save($post_){

		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		
		if($conn === false){
			self::$errormsg = 'error while saving seq-stations into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
		}
		$stmt = $conn->prepare('INSERT INTO seq_stations(id_seq, id_station, id_pitstoptype, orderal) VALUES(:id_seq, :id_stat, :id_pittyp, :orderal)');
		
		try {
        $conn->beginTransaction();
			//var_dump($this->pitstops);
        foreach($this->seq_stations as $stat_order => $station) {
            $stmt->bindValue(':id_stat', $station['station'], PDO::PARAM_INT);
            $stmt->bindValue(':orderal', $station['orderal'], PDO::PARAM_INT);
            $stmt->bindValue(':id_pittyp', $station['pitType'], PDO::PARAM_INT);
            $stmt->bindValue(':id_seq', $this->sequence, PDO::PARAM_INT);
            $stmt->execute();
            sleep(1);
        }
        $conn->commit();
    } catch(PDOException $e) {
        if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
            // This should be specific to SQLite, sleep for 0.25 seconds
            // and try again.  We do have to commit the open transaction first though
            $conn->commit();
            usleep(250000);
        } else {
            $conn->rollBack();
            //throw $e;
			self::$errormsg = 'error performing sequences transaction: '.$e->getMessage();//LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
        }
    }
		
		//return $this->saveObject($pdosql);
	}
		
	public static function getAll(){
		return self::getAllRecords('sequencesStations');	
	}
	
	public static function getSequenceStationsBySequence($seq_id){
		
/*
SELECT seq_stations.id_station, orderal, station.shortName, station.name AS statName 
FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station 
WHERE seq_stations.id_seq = 1
ORDER BY orderal ;
*/
		$seqstats = self::getAllRecords('sequencesStationsBYID', $seq_id );
/*echo'<pre>'; 	var_dump($seqstats);	echo'</pre>';*/
		if( empty($seqstats) ) {self::$errormsg='could not obtain sequences by id';
		return false;}
		
		$statNames = array();

		foreach($seqstats as $t=>$seq){
			array_push($statNames, $seq['shortName']);
			//$statNames[] = $seq['shortName'];
		}
//LiLogger::log( "seqstat".serialize($statNames) );		
		return $statNames;
	}
	
	public static function DeleteSeqStations($seq_id){
		if(empty($seq_id)) return false;
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		return ( $db->executeDelete("DELETE FROM seq_stations WHERE id_seq={$seq_id}") );
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
class Sequence extends DBObject{
	//private $obus;
	protected static $objectType = 'sequence';
	private $destination;

	public function __construct($seqName_, $dest_){
		$this->name = Ut::cleanInput($seqName_);
		$this->destination = Ut::cleanInput($dest_);
		$this->sqlPDOSave = "INSERT INTO sequences(name, destination) VALUES(':iName:', :iDest:)";
	}
	public function save(){
//LiLogger::log("seq save. dest id{$this->destination}");	
		$dest = Destination::getEntryByID($this->destination);
//LiLogger::log("res".serialize($dest));		
		$destName = $dest['name'];
		if(empty($destName)){
			self::$errormsg = 'could not obtain destination name.';
			LiLogger::log(self::$errormsg);
			return false;
		}
		$seqDestName = "[ {$destName} ] via {$this->name}";
		$arrParameters = array(
		":iName:"=>$seqDestName,
		":iDest:"=>$this->destination);
		$pdosql = strtr($this->sqlPDOSave, $arrParameters);
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//print_r($pdosql);
		return $this->saveObject($pdosql);
	}
		
	public static function getAll(){
		return self::getAllRecords('sequences');	
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