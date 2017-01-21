<?php
namespace obus;

//==========================================
// HTML routines and snippets classes
// ver 1.0
// © genom_by
// last updated 30 sep 2016
//==========================================

use PDO;
use PDOException;
use LinkBox;
use LinkBox\Logger as LiLogger;

//include_once 'settings.inc.php';
include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';

Interface iHTML{
	function createNode();
	function deleteNode();
	function appendNode();
}


class HTML{
	
	public static function getSelectItems($table){
	
	$htmlList = '';	
	
	switch ($table){
		case 'obus':{
			$list = Obus::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$htmlItem = "<option value='{$item['id_obus']}'>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}
			else $htmlList = "<option disabled value='-1'>No Obuses</option>";
		}
		break;
		case 'station':{
			$list = Station::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$htmlItem = "<option value='{$item['id_station']}'>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}
			else $htmlList = "<option disabled value='-1'>No Stations</option>";
		}
		break;
		case 'pitstopType':{
			$list = PitType::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					If($item['type'] == 'trans')
						$htmlItem = "<option selected='selected' value='{$item['id_pittype']}'>{$item['type']}</option>";
						else
						$htmlItem = "<option value='{$item['id_pittype']}'>{$item['type']}</option>";						
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}
			else $htmlList = "<option disabled value='-1'>No Stations</option>";
		}
		break;
		case 'itinerary':{
			$list = Itinerary::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$htmlItem = "<option value='{$item['id_itin']}'>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}
			else $htmlList = "<option disabled value='-1'>No Stations</option>";
		}
		break;
		default:
			$htmlList = "<option disabled value='-1'>No data</option>";	
		}
	return $htmlList;	

	}
	
	public static function getTableItems($table){
		
	$htmlTable = '';	
	
	switch ($table){
		case 'itinerary':{
			$list = Itinerary::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$time = LinkBox\Utils::Int2HHmm($item['start_time']);
$htmlItem = "<tr><td>{$item['id_itin']}</td><td>{$item['name']}</td>".
			"<td>{$item['statName']}</td><td>{$time}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'obus':{
			$list = Obus::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$time = LinkBox\Utils::Int2HHmm($item['start_time']);
//$htmlItem = "<tr><td>{$item['id_obus']}</td><td>{$item['name']}</td>"."</tr>";
$htmlItem = "<tr><td>{$item['name']}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'stations':{
			$list = Station::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$time = LinkBox\Utils::Int2HHmm($item['start_time']);
$htmlItem = "<tr><td>{$item['name']}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		default:
			$htmlTable = "no such table";	
	}
	return $htmlTable;
	}
	
	public static function getPitStopsTable(){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;
			$row_selStation = "<select name='station".$item['id_station']."' id='stationSel".$item['id_station']."'>".self::getSelectItems('station')."</select>";//self::getSelectItems('station')
			$row_Time = "<input type='text' autocomplete='off' name='stationTime".$item['id_station']."' id='stationTime".$item['id_station']."' size='10'/>";
			$row_selpitType = "<select name='pitType".$item['id_station']."' id='pitType".$item['id_station']."'>".self::getSelectItems('pitstopType')."</select>";
			
			$htmlItem = "<tr><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>Station</th><th>Time(HH:mm)</th></tr>";
	return "<table>".$htmlheader.$htmlTable."</table>".PHP_EOL."<input name='totalstops' value='{$totalstops}' type='hidden'>";
	}
	
	public static function timeTableTest($startHour = 6, $endHour = 10){
			
			$htmlTable="";
			
		for($row = $startHour ; $row <= $endHour; $row++){
			$minHTML = "";
			$rowHTML = "";
			for($min = 0; $min < 60; $min++){
				$minHTML = $minHTML."<td>&nbsp;<sup>".str_pad($min,2,'0',STR_PAD_LEFT)."</sup></td>";
				//$minHTML = $minHTML."<td><sub>{$row}</sub>".str_pad($min,2,'0',STR_PAD_LEFT)."</td>";
				//$minHTML = $minHTML."<td>{$row}<sup>".str_pad($min,2,'0',STR_PAD_LEFT)."</sup></td>";
			}
			
			$rowHTML = "<tr><td>{$row}</td>{$minHTML}</tr>";
			$htmlTable = $htmlTable.PHP_EOL.$rowHTML;
		}
		
		return "<table>".$htmlTable."</table>";
	}
	
	public static function getPitstops($itir = 'all'){
		$htmlTable = '';	
		$totalstops = 0;
		if ($itir == 'all'){

			$list = Way::getAll() ;	
			
				if(false !== $list){//var_dump($list);
				$htmlItem = '';
				foreach($list as $item){
					$totalstops++;
					
					$row_Time = $item['time'];
					
					$htmlItem = "<tr><td>{$item['itinName']}</td><td>{$item['statName']}</td><td>{$row_Time}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}	
			
		}
		return $htmlTable;
	}
	
	/*
	// get nested aray
	// gives json array {var:val,...}
	array(3) {
  [1]=>
  array(4) {
    ["itin_name"]=>
    string(17) "a47c_Зел_07:20"
    [0]=>
    array(1) {
      [1]=>
      string(1) "5"
    }
    [1]=>
    array(1) {
      [2]=>
      string(1) "5"
    }
    [2]=>
    array(1) {
      [3]=>
      string(1) "5"
    }
  }
  to
  var cars = [
{name:"chevrolet chevelle malibu", mpg:18, cyl:8, dsp:307, hp:130, lbs:3504, acc:12, year:70, origin:1},
	*/
	public static function normalizeWays2JSON($ways){
		if (empty($ways)) return false;
	
		$json_arr = array();
		$js_arr_string = "seed";
		
		foreach($ways as $pit){
			
			//$js_arr_string = $js_arr_string.'{name:'.$pit['name'] ;
			$js_arr_string = $js_arr_string + '{name:'.$pit['name'] ;
				
				foreach($pit as $key=>$val){

					$js_arr_string = $js_arr_string + ", {$key}:{$val}";
				}
				$js_arr_string = $js_arr_string + '},';
		}
		//$js_arr_string1 = rtrim($js_arr_string, ",");
echo $js_arr_string1;		
		//$js_string = "var cars = [{$js_arr_string}]";
		$js_string = "var cars = [".$js_arr_string."]";
		
		return $js_string;
	}
	
	
	
	
} //HTML class

?>