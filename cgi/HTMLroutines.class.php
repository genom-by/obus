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
		case 'destination':{
			$list = Destination::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$htmlItem = "<option value='{$item['id_dest']}'>{$item['name']}</option>";
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
/* ==============================
*								getTableItems
* ============================== */	
	public static function getTableItems($table){
		
	$htmlTable = '';	
	
	switch ($table){
		case 'itinerary':{
			$list = Itinerary::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
			$btnDel = self::createDELbutton($item['id_itin'], 'btnDelItin_onClick');
					$time = LinkBox\Utils::Int2HHmm($item['start_time']);
$htmlItem = "<tr><td>{$item['id_itin']}</td><td>{$item['name']}</td>".
			"<td>{$item['statName']}</td><td>{$time}</td><td>{$btnDel}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'destination':{
			$list = Destination::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
			$btnDel = self::createDELbutton($item['id_dest'], 'btnDelDest_onClick');
					
$htmlItem = "<tr><td>{$item['id_dest']}</td><td>{$item['name']}</td>".
			"<td>{$item['dest_seq']}</td><td>{$btnDel}</td></tr>";
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
					$btnDel = self::createDELTablebutton('station', $item['id_station']);
$htmlItem = "<tr id='station_id_{$item['id_station']}'><td>{$item['name']}</td><td>{$item['shortName']}</td><td>{$btnDel}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'sequences':{
			$list = Sequence::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
				$btnDel = self::createDELTablebutton('sequences', $item['id_seq']);
$htmlItem = "<tr id='sequences_id_{$item['id_seq']}'><td>{$item['name']}</td><td>{$item['destName']}</td><td>{$btnDel}</td></tr>";
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
					
					//$btnDel = "<button type='button' onclick='btnDel_onClick({$item['id_pitstop']})'>del</button>";
					$btnDel = self::createDELbutton($item['id_pitstop'], 'btnDel_onClick');
					$row_Time = $item['time'];
					
					$htmlItem = "<tr><td>{$item['itinName']}</td><td>{$item['statName']}</td><td>{$row_Time}</td><td>{$btnDel}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}	
			
		}
		return $htmlTable;
	}
	
	/* create html button for deleting table row
	*   input: +id, ?js-procedure
	*/
	public static function createDELbutton($id, $js_routine='alert("no js-routine to delete");'){
		if(empty($id)) return "<span>xDELx</span>";
		return "<button type='button' class='btn_del' onclick='{$js_routine}({$id})'>del</button>";
	}
	/* create html button wih common function btnDelFromTable for deleting table row
	*   input: table, id
	*/
	public static function createDELTablebutton($table='no_table_provided',$id=0){
		if(empty($id)) return "<span>xDELx</span>";
		return "<button type='button' class='btn_del' onclick='btnDelFromTable(`{$table}`,{$id});'>del</button>";
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
      [3]=> -- stat_id
      string(1) "5"
    }
  }
  to
  var cars = [
{name:"chevrolet chevelle malibu", mpg:18, cyl:8, dsp:307, hp:130, lbs:3504, acc:12, year:70, origin:1},
{name : 'kol1',  value : kol1 },  	{name : 'nem2',  value : nem2 },  	{name : 'mas3',  value : mas3 }, 	{name : 'akd4',   value : akd4  }, 
{name : 'spu5',  value : spu5 }, 	{name : 'kaz6',  value : kaz6 }, 	{name : 'tra7', value : tra7}
	*/
	public static function normalizeWays2JSON($ways){
		if (empty($ways)) return false;
	
		$json_arr = array();
		$js_arr_string = "";
		
		foreach($ways as $pit){
		
			//$js_arr_string = $js_arr_string.'{name:'.$pit['name'] ;
			$js_arr_string = $js_arr_string.'{name:"'.$pit['name'].'"' ;
				
				foreach($pit as $key=>$val){
					if($key !== 'name'){
						foreach($val as $stat_shrtname => $stat_time){
							$js_arr_string = $js_arr_string.", {$stat_shrtname}:{$stat_time}";
						}
					}
				}
				$js_arr_string = $js_arr_string.'},';
		}
		$js_arr_string = rtrim($js_arr_string, ",");
//echo $js_arr_string;		
//var cars = [{name:a47c_Зел_07:20, name:a47c_Зел_07:20, 0:Array, 1:Array, 2:Array},{name:a47c_ака_7:46, name:a47c_ака_7:46, 0:Array, 1:Array, 2:Array},{name:t46_Кол_7:04, name:t46_Кол_7:04, 0:Array, 1:Array, 2:Array}]		
		$js_string = "var cars = [".$js_arr_string."]";
		
		return $js_string;
	}
	
	/* return such array:
	[{         
		name: 'Tokyo',
		data: [7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
	}, {
		name: 'London',
		data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8, 3.3, 4.4]
	}]
	source: Way::getPitstopsByItinerary()
	array(9) {
  [1]=>
  array(5) {
    ["name"]=>
    string(16) "t46_Кол_07:04"
    [0]=>
    array(1) {
      ["zel0"]=>
      string(3) "417"
    }
    [1]=>
    array(1) {
      ["kol1"]=>
      string(3) "424"
    }
    [2]=>
    array(1) {
      ["nem2"]=>
      string(3) "446"
    }
    [3]=>
    array(1) {
      ["mas3"]=>
      string(3) "452"
    }
  }
	*/

	
	public static function arrayLineChart($ways){
		if (empty($ways)) return false;
	
	$name2index = array ('zel0'=>0, 'kol1'=>1, 'nem2'=>2, 'mas3'=>3, 'akd4'=>4, 'spu5'=>5, 'kaz6'=>6, 'tra7'=>7);
	$linechartXaxis = array(); //of 8 entries

		$json_arr = array();
		$js_arr_string = "";
		
		foreach($ways as $pit){
			
			$linechartXaxis = array_fill(0,8,'null'); // var_dump($linechartXaxis ); - array(8) { [0]=>   int(0)		

			$lineArName = "name:'".$pit['name']."'";
			//$lineArData = "data:[";
				
				foreach($pit as $key=>$val){
					if($key !== 'name'){
						foreach($val as $stat_shrtname => $stat_time){
						$linechartXaxis[$name2index[$stat_shrtname]] = $stat_time;
						//$js_arr_string = $js_arr_string.", {$stat_shrtname}:{$stat_time}";
						}
					}
				}
			$lineArData = implode(",",$linechartXaxis);	
	
			$line_arr_string = "{".$lineArName.",data:[".$lineArData."]},".PHP_EOL;
			
			$js_arr_string = $js_arr_string.$line_arr_string;

		}
		
		$js_arr_string = rtrim($js_arr_string, PHP_EOL);
		$js_arr_string = rtrim($js_arr_string, ",");
		
\LinkBox\Logger::log( $js_arr_string);
		$js_string = "[".$js_arr_string."]";
		
//{name:'a73_Нем_07:32',433,440,452,458,461,465,480]}[{name:'a73_Нем_07:32',433,440,452,458,461,465,480]}]		
		return $js_string;	
	}
	
} //HTML class

?>