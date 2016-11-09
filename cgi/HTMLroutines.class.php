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
		default:
			$htmlTable = "no such table";	
	}
	return $htmlTable;
	}
	
	public static function getPitStopsTable(){
		
	$htmlTable = '';	
	
	$list = Station::getAll() ;
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			
			$row_selStation = "<select name='station".$item['id_station']."' id='stationSel".$item['id_station']."'>".self::getSelectItems('station')."</select>";//self::getSelectItems('station')
			$row_Time = "<input type='text' name='stationTime'".$item['id_station']."' id='stationTime".$item['id_station']."' size='10'/>";
			
			$htmlItem = "<tr><td>{$row_selStation}</td><td>{$row_Time}</td></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>Station</th><th>Time(HH:mm)</th></tr>";
	return "<table>".$htmlheader.$htmlTable."</table>";
	}
}

?>