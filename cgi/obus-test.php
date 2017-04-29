<?php
namespace obus;

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

\LinkBox\Logger::log('Start logging');

if(!empty($_POST['action'])){
		
switch ($_POST['action']){
	case 'obus':
		//echo 'obus';		
		if(!empty($_POST['obusName'])){
			$obus= new Obus($_POST['obusName']);
			$retval = $obus->save();
			if(!$retval)
			print_r("result: ".$obus::$errormsg);
		}
		break;
	case 'station':
		//echo 'staaation';
		if(!empty($_POST['stationName'])){
			$station= new Station($_POST['stationName'], $_POST['statShortName']);
			$retval = $station->save();
			if(!$retval)
			print_r("result: ".$station::$errormsg);
		}		
		break;
	case 'itinerary':
		//echo 'itinerary';
		//print_r($_POST);
		if(!empty($_POST['itineraryName'])){
			// [itineraryName] => a47c_Çåë_ [obus] => 1 [station] => 1 [startTime] => 07:20 [action] => itinerary 
			$iName = $_POST['itineraryName'];
			$itiner = new Itinerary($iName, $_POST['obus'], $_POST['station'], $_POST['startTime']);
			$retval = $itiner->save();
			if(!$retval)
				print_r("result: ".$itinerary::$errormsg);			
		}			
	break;
	case 'pitstops':
				//print_r($_POST);
			if( !empty($_POST['itinerarySelect']) ){
				$way = new Way($_POST);
				$retval = $way->save($_POST);
				if(!$retval)
					print_r("result: ".$way::$errormsg);				
			}
	break;
	case 'destination':
				//print_r($_POST);
			if( !empty($_POST['destName']) ){
				$dest = new Destination($_POST['destName'], $_POST['destName']);
				$retval = $dest->save();
				if(!$retval)
					print_r("result: ".$dest::$errormsg);				
			}
	break;
	case 'sequence':
				//print_r($_POST);
			if( !empty($_POST['seqName']) ){
				$seq = new Sequence($_POST['seqName'], $_POST['seqDest']);
				$retval = $seq->save();
				if(!$retval)
					print_r("result: ".$seq::$errormsg);				
			}
	break;
	case 'sequencesStations':
				//var_dump($_POST);
			if( !empty($_POST['sequencesSelect']) ){
				$seq = new sequencesStations($_POST);
				$retval = $seq->save($_POST);
				if(!$retval)
					print_r("result: ".$seq::$errormsg);				
			}
	break;
	}
}
//echo( 'action:'.$_POST['action'] );
//parse_str($_POST["lbx_form_addlink"], $ajax);
//print_r($ajax);
?>
<html>
<head>
<title>Obus scheduling</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
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
	$('#itir_submit').prop('disabled', false);
}
function btn_showtrans_onClick(){
	var transblock = document.getElementById("transblock");
	//if(transblock)
}
function onTestPOSToperation(){
	/*test 
	data to send: Object { id: -1, table: "test" }
	data to send: Object { id: 9, table: "station" }
	
	if ( table_ == 'station' ){
		console.log('station '+id_entry);
		$.post(
			"post.routines.php",
			{id:-1, table:'test'},
			function(data){
			console.log("post returned: "+data.result);
			//console.table("post returned: "+data);
			alert(data.result);
			if (data.result == 'ok' ){
				var domID = '#'+table_+'_id_'+id_entry;
				//$(domID).background();
				$(domID).toggle( "highlight" );
			}
			}
			,"json"
		);
		return;
	}*/
}
function btnDelFromTable(table_, id_entry){
	console.info("delete from table: "+table_+" entry id:"+id_entry);
	console.info('data to send:', {id:id_entry, table:table_});

	$.post(
		"post.routines.php",
		{id:id_entry, table:table_},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#'+table_+'_id_'+id_entry;
			$(domID).toggle( "highlight" );
		}else{
			console.log('error message: ',data.message);
		}
		}
		,"json"
	);	
}

function postTest(){
	console.log('there');
	$.post( "post.routines.php", { id: "test" }, function( data ) {
		console.log('here');
	  console.log( data.result ); // John
	  console.log( data.time ); // 2pm
	},"json");
}
function btn_del_seq_stats_onClick(){
	
	if (! confirm('Are you sure to delete all stations for destination?') ) {return;}
	//console.info("delete from table: "+table_+" entry id:"+id_entry);
	var id_entry = $('#sequencesSelect').val();
	console.info('data to send:', {id:id_entry, table:'seq_stations_delete'});
	
	$.post(
		"post.routines.php",
		{id:id_entry, table:'seq_stations_delete'},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			//var domID = '#'+table_+'_id_'+id_entry;
			//$(domID).toggle( "highlight" );
		}else{
			console.log('error message: ',data.message);
		}
		}
		,"json"
	);	
}
</script>
<style>
.hided{
	display:none;
}
.table-condensed .btn_del{
    padding: 2px 5px;
    font-size: 10px;
	font-weight:bold;
    line-height: 1.1;
    border-radius: 3px;
	margin:-2px 0;
}
.obus_header{
	margin-bottom:20px;
}
.statName_send{
position:relative;
float:right;
width:20%;
}
.clearfix{
content:"";
display:block;
clear:both;
}
.statName_inputs{
float:left;
width:80%;
}
/* ... loading ...
<div style="
 margin: 10% auto;
 border-bottom: 6px solid #fff;
 border-left: 6px solid #fff;
 border-right: 6px solid #c30;
 border-top: 6px solid #c30;
 border-radius: 100%;
 height: 100px;
 width: 100px;
 -webkit-animation: spin .6s infinite linear;
 -moz-animation: spin .6s infinite linear;
 -ms-animation: spin .6s infinite linear;
 -o-animation: spin .6s infinite linear;
 animation: spin .6s infinite linear;
 ></div>
*/
</style>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="obus_header">
			</div>
		</div>	
	</div>
</div>	
<div class="container">
	<div class="row">
		<div class="col-md-3">
			<div class="obus_destination">
<fieldset>
<legend>Destinations</legend>
<form name="formDest" method="post">
<label for="destName">D.Name</label>
<input type="text" name="destName" id="destName" autocomplete="off"/>
<input type="submit" value="Send"/>
<input type="hidden" name="action" value="destination">
<p>
</p>
<!--show existing-->
<button type="button" id="btn_showdest" onclick="btn_showdest_onClick();" data-toggle="collapse" data-target="#destblock">Show Dest</button>
<div id="destblock" class="collapse">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('destination');?>
</table>
</div>
<!--end show existing-->
</form>
</fieldset>				
			</div>
		</div>	
		<div class="col-md-3"><div class="obus_numbers">
<fieldset>
<legend>Transport names</legend>
<form name="form1" method="post">
<label for="obusName">Tran Name</label>
<input type="text" name="obusName" id="obusName" size="10"/>
<input type="submit" value="Send"/>
<input type="hidden" name="action" value="obus">
<p>
</p>
<!--show existing-->
<button type="button" id="btn_showtrans" onclick="btn_showtrans_onClick();" data-toggle="collapse" data-target="#transblock">Show trans</button>
<div id="transblock" class="collapse">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('obus');?>
</table>
</div>
<!--end show existing-->
</form>
</fieldset>	
		</div></div>
		<div class="col-md-6"><div class="obus_stations">
<!-- stations -->
<fieldset>
<legend>Station names</legend>
<form name="form2" method="post">
<div class="statName_inputs">
<label for="stationName">Station Name</label>
<input type="text" name="stationName" id="stationName"/><br/>
<label for="statShortName">Station Short Name</label>
<input type="text" name="statShortName" id="statShortName"/>
</div>
<div class="statName_send">
<input type="submit" value="Send"/>
</div>
<div class="clearfix"></div>
<input type="hidden" name="action" value="station">
<p>
</p>
<!--show existing-->
<button type="button" data-toggle="collapse" data-target="#statsblock">Show stations</button>
<div id="statsblock" class="collapse">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('stations');?>
</table>
</div>
<!--end show existing-->
</form>
</fieldset>		
		</div></div>
	</div>
</div>
<!-- -------------------------------------------- itineraries --------------------------------------------  -->
<div class="container">
	<div class="row">
		<div class="col-md-8">
			<div class="obus_itineraries">
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
<input type="text" autocomplete="off" name="startTime" id="startTime" size="10"/>
<input type="hidden" name="action" value="itinerary">
<input id="itir_submit" type="submit" value="Send" disabled/>
<p>
</p>
</form>
<!--show existing-->
<button type="button" data-toggle="collapse" data-target="#itiblock">Show Itineraries</button>
<div id="itiblock" class="collapse">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('itinerary');?>
</table>
</div>
<!--end show existing-->
</fieldset>			
			</div>
		</div>	
		<div class="col-md-4">
			<div class="obus_sequense">
<fieldset>
<legend>Sequence</legend>
<form name="formSeq" method="post">
<select name="seqDest" id="seqDest">
<?php echo HTML::getSelectItems('destination');?>
</select>
<label for="seqName">Seq.Name</label>
<input type="text" name="seqName" id="seqName" autocomplete="off"/>
<input type="submit" value="Send"/>
<input type="hidden" name="action" value="sequence">
<p>
</p>
<!--show existing-->
<button type="button" id="btn_showseq" onclick="btn_showseq_onClick();" data-toggle="collapse" data-target="#seqblock">Show Seq</button>
<div id="seqblock" class="collapse">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getTableItems('sequences');?>
</table>
</div>
<!--end show existing-->
</form>
</fieldset>				
			</div>
		</div>	
	</div>
</div><!-- itineraries end -->
<!-- ---------------------------------------------- pitstops ----------------   |   ----------------------------- sequenses -------------------------------------------    -->
<div class="container">
	<div class="row">
		<div class="col-md-6">
			<div class="obus_pitstops">
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
			</div>
		</div>
		<div class="col-md-6">
			<div class="obus_sequences">
<fieldset>
<legend>Sequence Stations</legend>
<form name="formSeq" method="post">
<select name="sequencesSelect" id="sequencesSelect">
<?php echo HTML::getSelectItems('sequences');?>
</select>
<button type="button" id="btn_del_seq_stats" onclick="btn_del_seq_stats_onClick();">Clear stations</button>
<p></p>
<?php echo HTML::getSequencesTable();?>
<p></p>
<input type="hidden" name="action" value="sequencesStations">
<input type="submit" value="Send"/>
</form>
</fieldset>	
			</div>
		</div>	
	</div>
</div>	

<!-- timetable test-->
<fieldset>
<legend>Time Table</legend>
<!--show existing-->
<button type="button" data-toggle="collapse" data-target="#pitsblock">Show pitstops</button>
<div id="pitsblock" class="collapse">
<table class="table table-striped table-hover table-condensed small">
<?php echo HTML::getPitstops();?>
</table>
</div>
<!--end show existing-->
</fieldset>
<a href="hchartLine.php">line chart</a>
<pre>
<?php
//$pitstops = Way::getPitstopsByItinerary(); 
//\LinkBox\Logger::log(serialize($pitstops));
//var_dump($pitstops);
//echo json_encode($pitstops);
//echo HTML::normalizeWays2JSON($pitstops);
?>
</pre>
<span id="testSpan"></span>
</body>
</html>