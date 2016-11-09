<?php
namespace obus;

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

\LinkBox\Logger::log('Start logging');

if(!empty($_POST['action'])){
		
switch ($_POST['action']){
	case 'obus':
		echo 'obus';		
		if(!empty($_POST['obusName'])){
			$obus= new Obus($_POST['obusName']);
			$retval = $obus->save();
			if(!$retval)
			print_r("result: ".obus::$errormsg);
		}
		break;
	case 'station':
		echo 'staaation';
		if(!empty($_POST['stationName'])){
			$station= new Station($_POST['stationName']);
			$retval = $station->save();
			if(!$retval)
			print_r("result: ".station::$errormsg);
		}		
		break;
	case 'itinerary':
		echo 'itinerary';
		print_r($_POST);
		if(!empty($_POST['itineraryName'])){
			// [itineraryName] => a47c_Çåë_ [obus] => 1 [station] => 1 [startTime] => 07:20 [action] => itinerary 
			$iName = $_POST['itineraryName'];
			$itiner = new Itinerary($iName, $_POST['obus'], $_POST['station'], $_POST['startTime']);
			$retval = $itiner->save();
			if(!$retval)
				print_r("result: ".itinerary::$errormsg);			
		}			
	break;
	}
}
echo( 'action:'.$_POST['action'] );
//parse_str($_POST["lbx_form_addlink"], $ajax);
//print_r($ajax);
?>
<html>
<head>
<title>Obus scheduling</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script>
function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);

    if (elt.selectedIndex == -1)
        return null;

    return elt.options[elt.selectedIndex].text;
}

function btnItinerName_onClick(){

	var obus_text = getSelectedText('obusSel');
	var stat_text = getSelectedText('stationSel');

	//var obus = document.getElementById("obusSel").value;
    //var station = document.getElementById("stationSel").value;
    var starttime = document.getElementById("startTime").value;
    
	var itinerName = document.getElementById("itineraryName");
	itinerName.value = obus_text+'_'+stat_text.substring(0,3)+'_'+starttime;
	//document.getElementById("testSpan").innerHTML = "sdsd";//itinerName.value;
}
</script>
</head>
<body>
<fieldset>
<legend>Transport names</legend>
<form name="form1" method="post">
<label for="obusName">Tran Name</label>
<input type="text" name="obusName" id="obusName"/>
<input type="hidden" name="action" value="obus">
<p>
</p>
<input type="submit" value="Send"/>
</form>
</fieldset>
<!-- stations -->
<fieldset>
<legend>Station names</legend>
<form name="form2" method="post">
<label for="stationName">Station Name</label>
<input type="text" name="stationName" id="stationName"/>
<input type="hidden" name="action" value="station">
<p>
</p>
<input type="submit" value="Send"/>
</form>
</fieldset>
<!-- itineraries -->
<fieldset>
<legend>Itineraries</legend>
<form name="form3" method="post">
<label for="itineraryName">Itinerary Name</label>
<input type="text" name="itineraryName" id="itineraryName" readonly="readonly"/>
<button type="button" onclick="btnItinerName_onClick()">itir</button>
<br/><p></p>
<select name="obus" id="obusSel">
<?php echo HTML::getSelectItems('obus');?>
</select>
<select name="station" id="stationSel">
<?php echo HTML::getSelectItems('station');?>
</select>
<label for="startTime">Start time (HH:mm)</label>
<input type="text" name="startTime" id="startTime" size="10"/>
<input type="hidden" name="action" value="itinerary">
<p>
</p>
<input type="submit" value="Send"/>
</form>
<hr/>
<table>
<?php echo HTML::getTableItems('itinerary');?>
</table>
</fieldset>
<!-- pitstops -->
<fieldset>
<legend>Pitstops</legend>
<form name="form4" method="post">
<select name="itinerarySelect" id="itinerarySelect">
<?php echo HTML::getSelectItems('itinerary');?>
</select>
<p></p>
<?php echo HTML::getPitStopsTable();?>
<p></p>
<input type="hidden" name="action" value="pitstops">
<input type="submit" value="Send"/>
</form>
</fieldset>
<span id="testSpan"></span>
</body>
</html>