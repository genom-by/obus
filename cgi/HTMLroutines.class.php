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
	
	public static function getSelectItems($table, $selected_id = -1){
	
	$htmlList = '';	
	$htmlListFirstEntry = "<option value='-1' selected class='sel_first'>Select..</option>";	
	
	switch ($table){
		case 'obus':{
			$list = Obus::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
				if($item['id_obus'] == $selected_id){$is_selected=' selected ';}else{$is_selected='';}
					$htmlItem = "<option value='{$item['id_obus']}'{$is_selected}>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<option disabled value='-1'>No Obuses</option>";
		}
		break;
		case 'station':{
			$list = Station::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
//LiLogger::log("item inside selecttableid_station: {$item['id_station']} and name {$item['name']} and selected_id is {$selected_id}");				
				if($item['id_station'] === $selected_id){
		$htmlItem = "<option value='{$item['id_station']}' selected>{$item['name']}</option>";
				}else{
		$htmlItem = "<option value='{$item['id_station']}'>{$item['name']}</option>";				
				}	
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
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
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
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
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<option disabled value='-1'>No Stations</option>";
		}
		break;
		case 'destination':{
			$list = Destination::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
//LiLogger::log("id_dest: {$item['id_dest']} and name {$item['name']} and selected_id is {$selected_id}");
					if($item['id_dest'] == $selected_id){
			$htmlItem = "<option selected value='{$item['id_dest']}'>{$item['name']}</option>";		
					}else{
			$htmlItem = "<option value='{$item['id_dest']}'>{$item['name']}</option>";		
					}
					//$htmlItem = "<option value='{$item['id_dest']}'>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			if($selected_id == -1){
				$htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;
				}
			}
			else $htmlList = "<option disabled value='-1'>No Stations</option>";
		}
		break;
		case 'sequences':{
			$list = Sequence::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$htmlItem = "<option value='{$item['id_seq']}'>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;
			}
			else $htmlList = "<option disabled value='-1'>No Sequences</option>";
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
			//$btnDel = self::createDELbutton($item['id_itin'], 'btnDelItin_onClick');
			$btnDel = self::createDELTablebutton('itinerary', $item['id_itin']);		
					$time = LinkBox\Utils::Int2HHmm($item['start_time']);
					$destin = self::getSelectItems('destination',$item['destination']);
					$destinSelect = "<select>{$destin}</select>";
$htmlItem = "<tr id='itinerary_id_{$item['id_itin']}'><td>{$item['id_itin']}</td><td>{$item['name']}</td>".
			"<td>{$item['statName']}</td><td>{$time}</td><td>{$destinSelect}</td><td>{$btnDel}</td></tr>";
$htmlheader = "<tr><th>ID</th><th>Name</th><th>startStat.Name</th><th>Time(HH:mm)</th><th>destinID</th><th>del</th></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			$htmlTable = $htmlheader.$htmlTable;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'destination':{
			$list = Destination::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
			//$btnDel = self::createDELbutton($item['id_dest'], 'btnDelDest_onClick');
			$btnDel = self::createDELTablebutton('destination', $item['id_dest']);		
$htmlItem = "<tr id='destination_id_{$item['id_dest']}'><td>{$item['id_dest']}</td><td>{$item['name']}</td>".
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
					//$time = LinkBox\Utils::Int2HHmm($item['start_time']);
//$htmlItem = "<tr><td>{$item['id_obus']}</td><td>{$item['name']}</td>"."</tr>";
$btnDel = self::createDELTablebutton('obus', $item['id_obus']);	
$btnEd = self::createEDTablebutton('obus', $item['id_obus']);	
$btnBlock = self::createBlockOfButtons('obus', $item['id_obus']);	
$btnSav = '<span>save</span>';	
$htmlItem = "<tr id='obus_id_{$item['id_obus']}'><td class='rowtxt' orm='name'>{$item['name']}</td><td class='btnBlock'>{$btnBlock}</td></tr>";
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
	
	/* create html table rows for new pitstops
	*/
	public static function getPitStopsTable($type = "new"){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$htmlItem = '';
		
		$html_selectorStations = self::getSelectItems('station');
		$html_selectorPitTypes = self::getSelectItems('pitstopType');
		
		foreach($list as $item){
			$totalstops++;
			if($type == 'new'){
				/*$row_selStation = "<select name='station' id='stationSel".$item['id_station']."'>".$html_selectorStations."</select>";//self::getSelectItems('station')
				$row_Time = "<input type='text' autocomplete='off' name='stationTime' id='stationTime".$item['id_station']."' size='10'/>";
				$row_selpitType = "<select name='pitType' id='pitType".$item['id_station']."'>".$html_selectorPitTypes."</select>";
				$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delPitstopNewRow(`{$totalstops}`)'>X</button>";
				*/$row_selStation = "<select name='station".$item['id_station']."' id='stationSel".$item['id_station']."'>".$html_selectorStations."</select>";//self::getSelectItems('station')
				$row_Time = "<input type='text' autocomplete='off' name='stationTime".$item['id_station']."' id='stationTime".$item['id_station']."' size='10'/>";
				$row_selpitType = "<select name='pitType".$item['id_station']."' id='pitType".$item['id_station']."'>".$html_selectorPitTypes."</select>";
				$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delPitstopNewRow(`{$totalstops}`)'>X</button>";
			
			}else if($type == 'edit'){
				//refactored
			}
			$htmlItem = "<tr class='trpitnew' id='tbl_pitnew_row_{$totalstops}' data-id='{$totalstops}'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";

			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
		$cloneID = -1;
		$row_selStation = "<select name='station' id='stationSel{$cloneID}'>".$html_selectorStations."</select>";//self::getSelectItems('station')
		$row_Time = "<input type='text' autocomplete='off' name='stationTime' id='stationTime{$cloneID}' size='10'/>";
		$row_selpitType = "<select name='pitType' id='pitType{$cloneID}'>".$html_selectorPitTypes."</select>";
		$btn_delRow = "<button type='button' class='tbl_pitnew_row_del'>X</button>";
		$htmlItemToClone = "<tr class='trpitnewcloneable' id='tbl_pitnew_row_clone' data-id='-1'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
		
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>Station</th><th>Time(HH:mm)</th><th>Stat.Type</th></tr>";
	$htmlBtnAddRow = "<tr><td colspan='4'><button class='btn_new_tablerow' type='button' onclick='btn_addPitstopNewRow()'>Add new row</button></td></tr>";
	
	$htmlInputsTotal_Last = "<input name='totalstops' value='{$totalstops}' type='hidden'>".PHP_EOL."<input name='laststopID' value='{$totalstops}' type='hidden'>";
	
	return "<table class='pitstops_new'>".$htmlheader.$htmlTable.$htmlBtnAddRow.$htmlItemToClone."</table>".$htmlInputsTotal_Last;
	}	
	
	/* create html table rows for new pitstops - cycle FOR
	*/
	public static function getPitStopsTable2($type = "new", $lines=3){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$htmlItem = '';
		
		$html_selectorStations = self::getSelectItems('station');
		$html_selectorPitTypes = self::getSelectItems('pitstopType');
		
		for($row = 1; $row <= $lines; $row++){
			$totalstops++;
			if($type == 'new'){
				$row_selStation = "<select name='station{$row}' id='stationSel{$row}'>".$html_selectorStations."</select>";//self::getSelectItems('station')
				$row_Time = "<input type='text' autocomplete='off' name='stationTime{$row}' id='stationTime{$row}' size='10'/>";
				$row_selpitType = "<select name='pitType{$row}' id='pitType{$row}'>".$html_selectorPitTypes."</select>";
				$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delPitstopNewRow(`{$row}`)'>X</button>";
			
			}else if($type == 'edit'){
				//refactored
			}
			$htmlItem = "<tr class='trpitnew' id='tbl_pitnew_row_{$row}' data-id='{$row}'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";

			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
		$cloneID = -1;
		$row_selStation = "<select name='station'>".$html_selectorStations."</select>";//self::getSelectItems('station')
		$row_Time = "<input type='text' autocomplete='off' name='stationTime' size='10'/>";
		$row_selpitType = "<select name='pitType' >".$html_selectorPitTypes."</select>";
		$btn_delRow = "<button type='button' class='tbl_pitnew_row_del'>X</button>";
		$htmlItemToClone = "<tr class='trpitnewcloneable' id='tbl_pitnew_row_clone' data-id='-1'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
		
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>Station</th><th>Time(HH:mm)</th><th>Stat.Type</th></tr>";
	$htmlBtnAddRow = "<tr><td colspan='3'><button class='btn_new_tablerow' type='button' onclick='btn_addPitstopNewRow()'>Add new row</button></td></tr>";
	
	$htmlInputsTotal_Last = "<input name='totalstops' value='{$totalstops}' type='hidden'>".PHP_EOL."<input name='laststopID' value='{$totalstops}' type='hidden'>";
	
	return "<table class='pitstops_new'>".$htmlheader.$htmlTable.$htmlBtnAddRow.$htmlItemToClone."</table>".$htmlInputsTotal_Last;
	}	
	
	/* create html table rows for editing pitstops
	*/
	public static function getPitStopsEditRows($itin_id = -1){
		
	$htmlTable = '';	
	$totalstops = 0;

	$list = Way::getAllWhere("WHERE id_itin = {$itin_id}") ;
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;

	/*$row_selStation = "<select name='station".$item['id_station']."' id='stationSel".$item['id_station']."'>".self::getSelectItems('station', $item['id_station'])."</select>";//self::getSelectItems('station')
	$row_Time = "<input type='text' autocomplete='off' name='stationTime".$item['id_station']."' id='stationTime".$item['id_station']."' size='10'/>";
	$row_selpitType = "<select name='pitType".$item['id_station']."' id='pitType".$item['id_station']."'>".self::getSelectItems('pitstopType')."</select>";			
	$htmlItem = "<tr><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td></tr>";
	*/	
			$btnDel = self::createDELTablebutton('pitstop', $item['id_pitstop']);
			$row_Time = LinkBox\Utils::Int2HHmm($item['time']);	
			$htmlItem = "<tr id='pitstop_id_{$item['id_pitstop']}'><td>{$item['itinName']}</td><td>{$item['statName']}</td><td>{$row_Time}</td><td>{$btnDel}</td></tr>";
				
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>Itinerary</th><th>Stat. Name</th><th>Time(HH:mm)</th><th>Del.</th></tr>";
	return $htmlheader.$htmlTable;
	}	
	
	/* create html table rows for editing sequences
	*/
	public static function getSeqEditRows($seq_id = -1){
		
	$htmlTable = '';	
	$totalstops = 0;

	//$list = Way::getAllWhere("WHERE id_itin = {$itin_id}") ;
	$list = sequencesStations::getSeqStationsBySequenceID($seq_id);
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;

	/*$row_selStation = "<select name='station".$item['id_station']."' id='stationSel".$item['id_station']."'>".self::getSelectItems('station', $item['id_station'])."</select>";//self::getSelectItems('station')
	$row_Time = "<input type='text' autocomplete='off' name='stationTime".$item['id_station']."' id='stationTime".$item['id_station']."' size='10'/>";
	$row_selpitType = "<select name='pitType".$item['id_station']."' id='pitType".$item['id_station']."'>".self::getSelectItems('pitstopType')."</select>";			
	$htmlItem = "<tr><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td></tr>";
	*/	
			$btnDel = self::createDELTablebutton('seq_stations', $item['id_ss']);
			//$row_Time = LinkBox\Utils::Int2HHmm($item['time']);	
			$htmlItem = "<tr id='seq_stations_id_{$item['id_ss']}'><td>{$item['orderal']}</td><td>{$item['shortName']}</td><td>{$item['statName']}</td><td>{$btnDel}</td></tr>";
				
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>orderal</th><th>shrtN</th><th>Name</th><th>Del.</th></tr>";
	return $htmlheader.$htmlTable;
	}
	
	public static function getSequencesTable(){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;
			$row_selStation = "<select name='station".$item['id_station']."' id='stationSel".$item['id_station']."'>".self::getSelectItems('station')."</select>";//self::getSelectItems('station')
			$row_orderal = "<input type='text' hidden name='orderal".$item['id_station']."' id='orderal".$item['id_station']."' size='0' value='{$totalstops}'/>";
			$row_selpitType = "<select name='pitType".$item['id_station']."' id='pitType".$item['id_station']."'>".self::getSelectItems('pitstopType')."</select>";
			
			$htmlItem = "<tr><td class='order'>{$totalstops}</td><td>{$row_orderal}</td><td>{$row_selStation}</td><td>{$row_selpitType}</td></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>Orderal</th><th>Station</th></tr>";
	return "<table>".$htmlheader.$htmlTable."</table>".PHP_EOL."<input name='totalstops' value='{$totalstops}' type='hidden'>";
	}
	
	public static function getSequencesTable2($lines=3){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$html_selectorStations = self::getSelectItems('station');
		$html_selectorPitTypes = self::getSelectItems('pitstopType');
		
		for($row = 1; $row <= $lines; $row++){
			
			$htmlItem = '';		
			$totalstops++;
			$row_selStation = "<select name='station{$row}' id='stationSel{$row}'>{$html_selectorStations}</select>";//self::getSelectItems('station')
			$row_orderal = "<input type='text' hidden name='orderal{$row}' id='orderal{$row}' size='0' value='{$totalstops}'/>";
			$row_selpitType = "<select name='pitType{$row}' id='pitType{$row}'>{$html_selectorPitTypes}</select>";
			$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delSequenceNewRow(`{$row}`)'>X</button>";
							
			$htmlItem = "<tr class='trseqnew' id='tbl_seqnew_row_{$row}' data-id='{$row}'><td class='order'>{$totalstops}</td><td class='hid_inp'>{$row_orderal}</td><td>{$row_selStation}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;			
		}	
	}else{$htmlTable = "no data";}	
	
	$cloneID = -1;
	$row_selStation = "<select name='station'>{$html_selectorStations}</select>";
	$row_orderal = "<input type='text' size='0' hidden value='{$cloneID}'/>";	
	$row_selpitType = "<select name='pitType' >{$html_selectorPitTypes}</select>";
	$btn_delRow = "<button type='button' class='tbl_seqnew_row_del'>X</button>";
	
	$htmlItemToClone = "<tr class='trseqnewcloneable' id='tbl_seqnew_row_clone' data-id='-1'><td class='order'>-1</td><td class='hid_inp'>{$row_orderal}</td><td>{$row_selStation}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
	
	$htmlBtnAddRow = "<tr><td colspan='3'><button class='btn_new_tablerow' type='button' onclick='btn_addSequenceNewRow()'>Add new row</button></td></tr>";
	
	$htmlInputsTotal_Last = "<input name='totalsequences' value='{$totalstops}' type='hidden'>".PHP_EOL."<input name='lastseqID' value='{$totalstops}' type='hidden'>";
	
	$htmlheader = "<tr><th>##</th><th>Station</th><th>Stat.Type</th></tr>";
	
	return "<table class='sequences_new'>".$htmlheader.$htmlTable.$htmlBtnAddRow.$htmlItemToClone."</table>".$htmlInputsTotal_Last;
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

/*
getAll()
array(49) { [0]=> array(14) { ["id_pitstop"]=> string(2) "10" [0]=> string(2) "10" ["id_station"]=> string(1) "1" [1]=> string(1) "1" ["shortName"]=> string(4) "zel0" [2]=> string(4) "zel0" ["statName"]=> string(21) "Зелёный луг" [3]=> string(21) "Зелёный луг" ["id_itinerary"]=> string(1) "1" [4]=> string(1) "1" ["itinName"]=> string(16) "t46_Кол_07:04" [5]=> string(16) "t46_Кол_07:04" ["time"]=> string(3) "417" [6]=> string(3) "417" } [1]=> array(14) { ["id_pitstop"]=> string(2) "11" [0]=> string(2) "11" ["id_station"]=> string(1) "2" [1]=> string(1) "2" ["shortName"]=> string(4) "kol1" [2]=> string(4) "kol1" ["statName"]=> string(16) "Кольцова" [3]=> string(16) "Кольцова" ["id_itinerary"]=> string(1) "1" [4]=> string(1) "1" ["itinName"]=> string(16) "t46_Кол_07:04" [5]=> string(16) "t46_Кол_07:04" ["time"]=> string(3) "424" [6]=> string(3) "424" } [2]=> array(14) 

getPitstopsByItinerary
array(7) { ["id_pitstop"]=> string(2) "10" ["id_station"]=> string(1) "1" ["shortName"]=> string(4) "zel0" ["statName"]=> string(21) "Зелёный луг" ["id_itinerary"]=> string(1) "1" ["itinName"]=> string(16) "t46_Кол_07:04" ["time"]=> string(3) "417" } 
*/	
	public static function getPitstops($itir = 'all'){
		$htmlTable = '';	
		$totalstops = 0;
		
		if ($itir == 'all'){
			$list = Way::getAll() ;	
		}else if($itir > 0){
			//$list = Way::getPitstopsByItinerary($itir) ;		//var_dump($list);	- garbage here
			$list = Way::getAllWhere("WHERE id_itin = {$itin_id}") ; ### REFACTORED ###
		}
			
		if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;
			$btnDel = self::createDELTablebutton('pitstop', $item['id_pitstop']);
			$row_Time = LinkBox\Utils::Int2HHmm($item['time']);	
			$htmlItem = "<tr id='pitstop_id_{$item['id_pitstop']}'><td>{$item['itinName']}</td><td>{$item['statName']}</td><td>{$row_Time}</td><td>{$btnDel}</td></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}	
				
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
	/* create html button wih common function btnEditlFromTable for deleting table row
	*   input: table, id
	*/
	public static function createEdTablebutton($table='no_table_provided',$id=0){
		if(empty($id)) return "<span>xEdx</span>";
		return "<button type='button' class='btn_edit' onclick='btnEditTableItem(`{$table}`,{$id});'>edit</button>";
	}
	/* create html block of buttons for edit, save, cancel, delete  - row
	*   input: table, id
	*/
	public static function createBlockOfButtons($table='no_table_provided',$id=0){
		if(empty($id)) return "<span>xBlocKx</span>";
		
		$edSpan = "<span class='btnEditBl'>".self::createEdTablebutton($table,$id)."</span>";
		
	$btnDel = self::createDELTablebutton($table,$id);
	$btnSave = "<button type='button' class='btn_save' onclick='btnSaveTableItem(`{$table}`,{$id});'>save</button>";
	$btnCancel = "<button type='button' class='btn_cancel' onclick='btnCancelTableItem(`{$table}`,{$id});'>cancel</button>";
		$dscSpan = "<span class='btnDSCBl hided'>{$btnDel}{$btnSave}{$btnCancel}</span>";
		return $edSpan.$dscSpan;
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

	/* input
array(49) {
  [0]=>  array(7) {
    ["id_pitstop"]=>    string(2) "10"
    ["id_station"]=>    string(1) "1"
    ["shortName"]=>    string(4) "zel0"
    ["statName"]=>    string(21) "Зелёный луг"
    ["id_itinerary"]=>    string(1) "1"
    ["itinName"]=>    string(16) "t46_Кол_07:04"
    ["time"]=>    string(3) "417"
  }
  [1]=>  array(7) {
	*/
//TODO figyre out why extra 9th column is added
	public static function arrayLineChart($ways, $sequence_id=-1, $delimiter=','){
		if (empty($ways)) return "[no data1]";	//var_dump($ways);die();
		if (empty($sequence_id)) return "[no data2]";

	$seq_stations = sequencesStations::getSeqStatNamesBySequenceID($sequence_id);
	if ($seq_stations === false) { LiLogger::log("HTML::arrayLineChart error: no sequence stations obtained: ".sequencesStations::$errormsg); return "[no data3]";	}
	//$name2index = array ('zel0'=>0, 'kol1'=>1, 'nem2'=>2, 'mas3'=>3, 'akd4'=>4, 'spu5'=>5, 'kaz6'=>6, 'tra7'=>7);
	
	$name2index = array(); ###REFACTORED###
	$seq_orderal=0;
	foreach($seq_stations as $short){
		$name2index[$short] = $seq_orderal;
		$seq_orderal++;
	}	//var_dump($name2index);	
	
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
						if(array_key_exists($stat_shrtname,$name2index)){
							$linechartXaxis[$name2index[$stat_shrtname]] = $stat_time;
						}					
						//$js_arr_string = $js_arr_string.", {$stat_shrtname}:{$stat_time}";
						}
					}
				}
			$lineArData = implode(",",$linechartXaxis);	
			$line_arr_string = "{".$lineArName.",data:[".$lineArData."]}{$delimiter}".PHP_EOL;
			$js_arr_string = $js_arr_string.$line_arr_string;
		}
		$js_arr_string = rtrim($js_arr_string, PHP_EOL);
		$js_arr_string = rtrim($js_arr_string, $delimiter);
		
		$js_string = "[".$js_arr_string."]";
		
//{name:'a73_Нем_07:32',433,440,452,458,461,465,480]}[{name:'a73_Нем_07:32',433,440,452,458,461,465,480]}]		
		return $js_string;	
	}
	
	public static function arrayLineChartCategories($seqs){
		if (empty($seqs)) return false;
//echo'<pre>'; 	var_dump($seqs);	echo'</pre>';
		$arQuoted = array();
		
		foreach($seqs as $ss){
			$arQuoted[] = "'{$ss}'";
		}
		$lineArData = implode(",",$arQuoted);	
		$line_arr_string = $lineArData;
		//$line_arr_string = "[".$lineArData."]".PHP_EOL;
		//$line_arr_string = "{".$lineArData."}".PHP_EOL;
		
	return $line_arr_string; 
 //return "'zel0', 'kol1', 'nem2', 'mas3', 'akd4', 'spu5', 'kaz6', 'tra7'";
 
	}
	
} //HTML class

?>