<?php
namespace obus;

//==========================================
// DB objecs classes
// ver 1.5
// © genom_by
// last updated 05 may 2017
//==========================================

use PDO;
use PDOException;
use LinkBox;
use LinkBox\Logger as LiLogger;
use LinkBox\Utils as Utils;

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
	
	protected $id;	// database table id of object
	
	private $db;	// database connection
	private $sqlPDOSave;
	## ORM ## //protected static $orm = array('table'=>'obus', 'table_id'=>'id_obus'); 
	
	private static $objectType; // what kind of table is the class
	private static $sqlGetAll;	// SQL query for getting all records without WHERE clause
	private static $sqlGetAllOrdered;	// SQL query for getting all records ordered (if applicable)

/* ### REFACTORED ###
@sql
%array or %fale+%errormsg
*/
public static function getEntriesArrayBySQL($sql, $ref=-1){
	if (empty($sql)) {
		self::$errormsg="DBObject[getEntriesBySQL]: No SQL provided";
		return false;
	}
	$objects = LinkBox\DataBase::getArrayBySQL($sql, $ref);
	if($objects === false){
		self::$errormsg = "DBObject[getEntriesBySQL]: No data from DB: ".LinkBox\DataBase::$errormsg;
		LiLogger::log( self::$errormsg );
		return false;
		}
	else return $objects;		
}//getEntriesArrayBySQL

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
	## NEW ##
	/*input @array(field=>value)
	*/
	public function update($fields_values=null){
		if( empty($this->id) ){			self::$errormsg = 'DB[updateObject]: no id for update.';
			LiLogger::log( self::$errormsg );
			return false;
		}
		$values = self::buildUpdateValues($fields_values);
		if( empty($values) ){			self::$errormsg = 'DB[updateObject]: values are empty.';
			LiLogger::log( self::$errormsg );
			return false;
		}
		
		$tableName = static::$orm['table'];
		$tableId = static::$orm['table_id'];
		
		$whereClause = " WHERE {$tableId} = {$this->id}";
		$sql = "UPDATE {$tableName} SET {$values} {$whereClause}";

		$res = LinkBox\DataBase::executeUpdate($sql);
		if($res === false){
			self::$errormsg = 'DBO[update]: Error while updating into DB: '.LinkBox\DataBase::$errormsg;
			LiLogger::log( self::$errormsg );
			return false;
		}
		else return true;
	}
	private static function buildUpdateValues($fields_values){
		if( empty($fields_values) ){	self::$errormsg = 'DB[buildUpdateValues]: no values.';
			LiLogger::log( self::$errormsg );
			return false;
		}
		$str = "";
		foreach($fields_values as $key=>$value){
			if(gettype($value)=='string'){$valstr="'{$value}'";}else{$valstr=$value;}
			$str = $str."{$key} = {$valstr},";
		}
		$str = rtrim($str,',');
		return $str;
	}
	
	/* 
	returns all records for inherited class based on static sql query
	*/
	public static function getAll(){
		if(!empty(static::$sqlGetAllOrdered)){$sql = static::$sqlGetAllOrdered;
		}else{$sql = static::$sqlGetAll;}
		return self::getEntriesArrayBySQL($sql);		
	}
	/*
	returns filtered records for inherited class based on static sql query and provided filter
	*/
	public static function getAllWhere($whereSQL){
		if(empty($whereSQL)){self::$errormsg = "DBObject[getAllWhere] no where clause"; return false;}
		$sql = static::$sqlGetAll.' '.$whereSQL;		//LiLogger::log( $sql);		

		return self::getEntriesArrayBySQL($sql);		
	}
	/*
	returns class objext based on provided id
	*/
	public static function getFromDB($id){
		if(empty($id)){self::$errormsg = "DBObject[getFromDB] no id to load"; return false;}
		
		$tableId = static::$orm['table_id'];
		$whereClause = " WHERE {$tableId} = {$id}";
		$sql = static::$sqlGetAll.' '.$whereClause;
				//LiLogger::log( $sql);
		$result = self::getEntriesArrayBySQL($sql);
		if(false === result){return false;}else{return $result[0];}
	}

	public static function countFrom($table, $table_col_id, $where=""){
		if(empty($table)) return false;
		if(empty($table_col_id)) return false;

		//$tableName = static::$orm['table'];
		//$tableId = static::$orm['table_id'];
		
		$table_idcolumn_id = Utils::cleanInput($table_idcolumn_id);
		$sql = "SELECT COUNT ({$table_col_id}) FROM {$table} {$where}";
	
		$db = LinkBox\DataBase::connect(); //get raw connection		
		$conn = $db::getPDO(); //get raw connection
		$count = $conn->query($sql)->fetchColumn();
//var_dump($count);
		if($count > 0){return $count;}else{return 0;}
	}

	/*with where word*/
	public static function countWhere($where){
		if(empty($where)) return false;

		$tableName = static::$orm['table'];
		$tableId = static::$orm['table_id'];
		
		return self::countFrom($tableName, $tableId, $where);
		//$table_idcolumn_id = Utils::cleanInput($table_idcolumn_id);
		$sql = "SELECT COUNT ({$tableId}) FROM {$tableName} {$where}";
	LiLogger::log($sql);
		$db = LinkBox\DataBase::connect(); //get raw connection		
		$conn = $db::getPDO(); //get raw connection
		$count = $conn->query($sql)->fetchColumn();
//var_dump($count);
		if($count > 0){return $count;}else{return 0;}
	}
	
}//class DBObject
//implements IDBObjects, ObjectEntity
class Obus extends DBObject{

	protected static $orm = array('table'=>'obus', 'table_id'=>'id_obus');
	protected static $sqlGetAll = 'SELECT id_obus, name from obus';
	protected static $sqlGetAllOrdered = 'SELECT id_obus, name from obus ORDER BY name';
		
	public function __construct($name_){
		$this->name = Utils::cleanInput($name_);
		$this->sqlPDOSave = "INSERT INTO obus(name) VALUES(':1:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		return $this->saveObject($pdosql);
	}
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Obus($load['name']);
		$me->id = $load['id_obus'];
		return $me;}
	}

}
class PitType extends DBObject{

	protected static $orm = array('table'=>'pitstop_type', 'table_id'=>'id_pittype');
	protected static $sqlGetAll = 'SELECT id_pittype, type from pitstop_type';
			
	public function __construct($type_){
		$this->name = Utils::cleanInput($type_);
		$this->sqlPDOSave = "INSERT INTO pitstop_type(type) VALUES(':1:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		return $this->saveObject($pdosql);
	}
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new PitType($load['type']);
		$me->id = $load['id_pittype'];
		return $me;}
	}	
}

class Station extends DBObject{
	
	protected static $orm = array('table'=>'station', 'table_id'=>'id_station');
	protected static $sqlGetAll = 'SELECT id_station, name, shortName from station';
	protected static $sqlGetAllOrdered = 'SELECT id_station, name, shortName from station ORDER BY name COLLATE NOCASE';
		
	private $shortname="-";
	
	public function __construct($name_, $short_){
		$this->name = Utils::cleanInput($name_);
		$this->shortname = Utils::cleanInput($short_);
		$this->sqlPDOSave = "INSERT INTO station(name, shortName) VALUES(':1:', ':2:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		$pdosql = str_replace(':2:', $this->shortname, $pdosql);
		return $this->saveObject($pdosql);
	}
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Station($load['name'], $load['shortName'] );
		$me->id = $load['id_station'];
		return $me;}
	}	
}
class Destination extends DBObject{
	
	protected static $orm = array('table'=>'destination', 'table_id'=>'id_dest');
	protected static $sqlGetAll = 'SELECT id_dest, name, dest_seq from destination';
	protected static $sqlGetAllOrdered = 'SELECT id_dest, name, dest_seq from destination ORDER BY name';
	
	private $sequence="-";
	
	public function __construct($name_, $seq_){
		$this->name = Utils::cleanInput($name_);
		$this->sequence = Utils::cleanInput($seq_);
		$this->sqlPDOSave = "INSERT INTO destination(name, dest_seq) VALUES(':1:', ':2:')";
	}
	public function save(){
		$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		$pdosql = str_replace(':2:', $this->shortname, $pdosql);
		return $this->saveObject($pdosql);
	}
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Destination($load['name'], $load['dest_seq'] );
		$me->id = $load['id_dest'];
		return $me;}
	}	
}

class Way extends DBObject{
	
	protected static $orm = array('table'=>'pitstop', 'table_id'=>'id_pitstop');
	protected static $sqlGetAll = 'SELECT id_pitstop, pitstop.id_station, station.shortName, station.name AS statName, id_itinerary, itinerary.name AS itinName, `time` FROM pitstop LEFT JOIN station ON pitstop.id_station = station.id_station LEFT JOIN itinerary ON pitstop.id_itinerary = itinerary.id_itin';

	private $itinerary = 0; //main itinerary
	private $pitstops = null; //['pitstop_id'=>time]
	private $pitstopsTotal = 0;
	private $pitstopsMaxId = 0;
	
	public function __construct($post_){
		$this->name = "way";//Utils::cleanInput($name_);
		$this->sqlPDOSave = "";//"INSERT INTO station(name) VALUES(':1:')";
		$this->pitstopsTotal = $post_['totalstops'];
		$this->pitstopsMaxId = $post_['laststopID'];
		$this->itinerary = $post_['itinerarySelect'];
		/*for($i=1; $i <= $this->pitstopsTotal; $i++) {
			if(!empty($post_['stationTime'])){
			$this->pitstops[$i] = array(
				'station'=>$post_['station'], 
			'time'=>Utils::HHmm2Int( Utils::cleanInput( $post_['stationTime'])) ,
				'pitType'=>$post_['pitType'],
									);
									}
		}
		*/for($i=1; $i <= $this->pitstopsMaxId; $i++) {
			if(!empty($post_['stationTime'.$i])){
			$this->pitstops[$i] = array(
				'station'=>$post_['station'.$i], 
			'time'=>Utils::HHmm2Int( Utils::cleanInput( $post_['stationTime'.$i])) ,
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
/*
all pitstops for desired itin. for compatibility default == -2 (all)
for expanding -3 :: select by destination
*/	
	public static function getPitstopsByItinerary($id_itin = -2, $id_destin = -2){
		
		if($id_itin == -2){
			$pitstops = self::getAll();
		}else if($id_itin == -3){
			$sql = "SELECT id_itin FROM itinerary WHERE destination = {$id_destin}";
			$itins = self::getEntriesArrayBySQL($sql);
			if($itins === false){
		self::$errormsg = 'Way[getPitstopsByItinerary]::error';	LiLogger::log( self::$errormsg );
			return false;}
			$itinsA = array();	
			foreach($itins as $it){array_push($itinsA,$it['id_itin']);}
			$id_itins = implode(',',$itinsA);
			$clause="WHERE id_itinerary IN ( {$id_itins} )";		//LiLogger::log($clause);die();

			$pitstops = self::getAllWhere($clause); 		
		}
		else{
			$clause="WHERE id_itinerary = {$id_itin}";
			$pitstops = self::getAllWhere($clause); 
		}			
		if( empty($pitstops) ) {
		self::$errormsg = 'Way[getPitstopsByItinerary]::error2';LiLogger::log( self::$errormsg );
		return false;}
//var_dump($pitstops);	//die();
		$ways = array();
		$id__it_name = array();
		$id__stat_name = array();
		
		foreach($pitstops as $stop){
			$id__it_name[$stop['id_itinerary']] = $stop['itinName']; //a:3:{i:1;N;i:5;N;i:6;N;}
			$id__stat_name[$stop['id_station']] = $stop['statName'];
		}		//LiLogger::log(serialize($id__it_name));
		foreach($id__it_name as $it_id => $val){
			$ways[(string)$it_id] = array();
			$ways[((string)$it_id)]['name'] = $val;
		}		//Logger::log(serialize($ways));
		foreach($pitstops as $stop){
			array_push($ways[(string)$stop['id_itinerary']], array($stop['shortName']=>$stop['time']) );
		}		//var_dump($ways); 
		return $ways;
	}
/*
all pitstops for desired destination. proxy for getPitstopsByItinerary
*/
	public static function getPitstopsByDestination($id_destin){
		
		if( empty($id_destin) ) {
			self::$errormsg = 'getPitstopsByDestination:no id_destin';
			LiLogger::log( self::$errormsg );
		return false;}
		
		return self::getPitstopsByItinerary(-3, $id_destin);
	}		
/*
all pitstops for desired sequence. proxy for getPitstopsByDestination
*/
	public static function getPitstopsBySequence($id_seq){
		if( empty($id_seq) ) {
			self::$errormsg = 'getPitstopsByDestination:no id_seq';
			LiLogger::log( self::$errormsg );
		return false;}
		//get id destination for this sequence
		$sql = "SELECT destination FROM sequences WHERE id_seq = {$id_seq}";
		$destA = self::getEntriesArrayBySQL($sql);
		if($dest === false){
			self::$errormsg = 'Way[getPitstopsBySequence]::error';	LiLogger::log( self::$errormsg );
			return false;}	
	//var_dump($destA[0]['destination']);
		$id_dest = $destA[0]['destination'];
		
		return self::getPitstopsByDestination($id_dest);	
	}

	
	public static function GetPitsCountForItinerary($itin_id){
		if(empty($itin_id)) return false;
		
		$itin_id = Utils::cleanInput($itin_id);
		$sql = "SELECT COUNT (id_pitstop) FROM pitstop WHERE id_itinerary = {$itin_id}";
	
		$db = LinkBox\DataBase::connect(); //get raw connection		
		$conn = $db::getPDO(); //get raw connection
		$count = $conn->query($sql)->fetchColumn();
//var_dump($count);
		if($count > 0){return $count;}else{return 0;}
	}
	
	public static function DeletePitstop($pit_id){
		if(empty($pit_id)) return false;
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		return ( $db->executeDelete("DELETE FROM pitstop WHERE id_pitstop={$pit_id}") );
	}
	
	public static function DeleteItinStations($itin_id){
		if(empty($itin_id)) return false;
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		return ( $db->executeDelete("DELETE FROM pitstop WHERE id_itinerary={$itin_id}") );
	}
}
	
/*							sequencesStations
* set of stations for X-axis of graph
*/
class sequencesStations extends DBObject{

	protected static $orm = array('table'=>'seq_stations', 'table_id'=>'id_ss');
	protected static $sqlGetAll = 'SELECT id_ss, seq_stations.id_station, orderal, station.shortName, station.name AS statName FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station';
	protected static $sqlGetAllOrdered = 'SELECT id_ss, seq_stations.id_station, orderal, station.shortName, station.name AS statName FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station ORDER BY orderal';
	
	private $sequence = 0; //main sequence
	private $seq_stations = null; //['pitstop_id'=>time]
	private $seqTotal = 0;
	private $seqLastID = 0;
	
	public function __construct($post_){
		$this->name = "sequencesStations";//Utils::cleanInput($name_);
		$this->sqlPDOSave = "";//"INSERT INTO station(name) VALUES(':1:')";
		$this->sequence = $post_['sequencesSelect'];
		$this->seqTotal = $post_['totalsequences'];
		$this->seqLastID = $post_['lastseqID'];
			
		for($i=1; $i <= $this->seqLastID; $i++) {
		if($post_['station'.$i] <> -1){
			$this->seq_stations[$i] = array(
				'station'=>Utils::cleanInput($post_['station'.$i]), 
				'orderal'=>Utils::cleanInput($post_['orderal'.$i]),
				'pitType'=>Utils::cleanInput($post_['pitType'.$i]),
									);	
			}
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
		
	
	public static function getSeqStatNamesBySequenceID($seq_id){
		
/* SELECT id_ss, seq_stations.id_station, orderal, station.shortName, station.name AS statName 
FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station 
WHERE seq_stations.id_seq = 1	ORDER BY orderal ; */
		$clause = "WHERE seq_stations.id_seq = {$seq_id} ORDER BY orderal";
		$seqstats = self::getAllWhere($clause );
//echo'<pre>'; 	var_dump($seqstats);	echo'</pre>';
		if( $seqstats === false ) {self::$errormsg='SNames: Could not obtain sequences by id';
		return false;}
		
		$statNames = array();

		foreach($seqstats as $t=>$seq){
			array_push($statNames, $seq['shortName']);
			//$statNames[] = $seq['shortName'];
		}
//LiLogger::log( "seqstat".serialize($statNames) );		
		return $statNames;
	}
	
	public static function getSeqStationsBySequenceID($seq_id){
		
/* SELECT id_ss, seq_stations.id_station, orderal, station.shortName, station.name AS statName 
FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station 
WHERE seq_stations.id_seq = 1	ORDER BY orderal ; */
		$clause = "WHERE seq_stations.id_seq = {$seq_id} ORDER BY orderal";
		$seqstats = self::getAllWhere($clause );
//echo'<pre>'; 	var_dump($seqstats);	echo'</pre>';
		if( $seqstats === false ) {self::$errormsg='SStations: Could not obtain sequences by id';
		return false;}
		return $seqstats;
		/*
		$statNames = array();
		foreach($seqstats as $t=>$seq){
			array_push($statNames, $seq['shortName']);
			//$statNames[] = $seq['shortName'];
		}
//LiLogger::log( "seqstat".serialize($statNames) );		
		return $statNames;*/
	}
	
	public static function GetPitsCountForSequence($seq_id){
		if(empty($seq_id)) return false;
		
		$seq_id = Utils::cleanInput($seq_id);
		$sql = "SELECT COUNT (id_ss) FROM seq_stations WHERE id_seq = {$seq_id}";
	
		$db = LinkBox\DataBase::connect(); //get raw connection		
		$conn = $db::getPDO(); //get raw connection
		$count = $conn->query($sql)->fetchColumn();
//var_dump($count);
		if($count > 0){return $count;}else{return 0;}
	}
	
	public static function DeleteSeqStations($seq_id){
		if(empty($seq_id)) return false;
		
		$db = LinkBox\DataBase::connect(); //get raw connection
		$conn = $db::getPDO(); //get raw connection
		return ( $db->executeDelete("DELETE FROM seq_stations WHERE id_seq={$seq_id}") );
	}
}

class Itinerary extends DBObject{

	protected static $orm = array('table'=>'itinerary', 'table_id'=>'id_itin');
	protected static $sqlGetAll = 'SELECT id_itin, itinerary.name, start_station, start_time, destination, station.name AS statName from itinerary LEFT JOIN station ON itinerary.start_station = station.id_station';
	protected static $sqlGetAllOrdered = 'SELECT id_itin, itinerary.name, start_station, start_time, destination, station.name AS statName from itinerary LEFT JOIN station ON itinerary.start_station = station.id_station ORDER BY itinerary.name';

	private $obus;
	private $station;
	private $startTime;
	private $destination;
	public function __construct($itineraryName_, $obus_, $station_, $startTime_, $destination_){
		$this->name = Utils::cleanInput($itineraryName_);
		$this->obus = Utils::cleanInput($obus_);
		$this->station = Utils::cleanInput($station_);
		$this->destination = Utils::cleanInput($destination_);
		$this->startTime = Utils::HHmm2Int( Utils::cleanInput($startTime_) );
		$this->sqlPDOSave = "INSERT INTO itinerary(name, start_station, destination, start_time) VALUES(':iName:', :iStSt:, :iDest:, :iStTime:)";
	}
	public function save(){
		$arrParameters = array(
		":iName:"=>$this->name,
		":iStSt:"=>$this->station,
		":iDest:"=>$this->destination,
		":iStTime:"=>$this->startTime);
		$pdosql = strtr($this->sqlPDOSave, $arrParameters);
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//print_r($pdosql);
		return $this->saveObject($pdosql);
	}
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Itinerary($load['name'], 'no-obus',$load['start_station'], $load['start_time'],$load['destination'] );
		$me->id = $load['id_itin'];
		return $me;}
	}
}
class Sequence extends DBObject{
	
	protected static $orm = array('table'=>'sequences', 'table_id'=>'id_seq');
	protected static $sqlGetAll = 'SELECT id_seq, name, destination from sequences';
	protected static $sqlGetAllOrdered = 'SELECT id_seq, name, destination from sequences ORDER BY name';
	private $destination;

	public function __construct($seqName_, $dest_){
		$this->name = Utils::cleanInput($seqName_);
		$this->destination = Utils::cleanInput($dest_);
		$this->sqlPDOSave = "INSERT INTO sequences(name, destination) VALUES(':iName:', :iDest:)";
	}
	public function save(){
//LiLogger::log("seq save. dest id{$this->destination}");	
		//$dest = Destination::getEntryByID($this->destination);
		$destArr = Destination::getAllWhere("WHERE id_dest = {$this->destination}"); //### REFACTORED ###
		$dest = $destArr[0];
//LiLogger::log("res".serialize($dest));		
		$destName = $dest['name'];
		if(empty($destName)){
			self::$errormsg = 'Sequence[save]: could not obtain destination name.';
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
	public static function load($id){
		$load = self::getFromDB($id);
		if(empty($load)){return false;}else{
		$me = new Sequence($load['name'], $load['destination'] );
		$me->id = $load['id_seq'];
		return $me;}
	}
	
}

class User extends DBObject{

	private $db;
	
	protected static $orm = array('table'=>'user', 'table_id'=>'id_user');
	protected static $sqlGetAll = 'SELECT id_user, name from user';
	protected static $sqlGetAllOrdered = 'SELECT id_user, name from user ORDER BY name';
	
	public static $errormsg;
	private $errorEmptyFields;
	
	public $id;
	public $regDate;
	public $email;
	public $name;
	public $pwdHash;
	
	public function __construct($name_, $email_, $pwd, $regDate_ = 0 ){
	
		$this->name = Utils::cleanInput($name_);
		$this->sqlPDOSave = "INSERT INTO user(name, email, pwdHash) VALUES(':name:', ':email:', ':pwdhash:')";
		
		$this->regDate = time();//Utils::cleanInput($regDate_);
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

		if( ! $this->validateSave() ) {
			self::$errormsg = 'Validation error when saving: '.self::$errormsg;
			return false;
		}		
		$arrParameters = array(
			":name:"=>$this->name,
			":email:"=>$this->email,
			":pwdhash:"=>$this->pwdHash);
		$pdosql = strtr($this->sqlPDOSave, $arrParameters);
		//$pdosql = str_replace(':1:', $this->name, $this->sqlPDOSave);
		//print_r($pdosql);
		return $this->saveObject($pdosql);
		/*
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
		*/
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
		
		if(empty($email)){
			$where = "WHERE name='{$name}'";	
		}elseif(empty($name)){
			$where = "WHERE email='{$email}'";			
		}else{
			$where = "WHERE name='{$name}' OR email='{$email}'";		
		}

		$count = self::countWhere($where);
		
		if($count > 0) {return true;} else {return false;}
	
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