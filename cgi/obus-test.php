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
function btnDel_onClick(id_pitstop){
	//var transblock = document.getElementById("transblock");
	console.info("id pitstop: "+id_pitstop);
	//$("#post-btn").click(function(){ $.post("process.php", $("#reg-form").serialize(), function(data) { alert(data); }); });
	$.post(
		"post.routines.php",
		{id:id_pitstop, table:'pitstop'},
		function(data){
		console.log(data.result);
		alert(data.result);}
		,"json"
	);
}
function btnDelItin_onClick(id_itin){
	//var transblock = document.getElementById("transblock");
	console.info("id itinerary: "+id_itin);
	//$("#post-btn").click(function(){ $.post("process.php", $("#reg-form").serialize(), function(data) { alert(data); }); });
	$.post(
		"post.routines.php",
		{id:id_itin, table:'itinerary'},
		function(data){
			console.log(data.result);
			alert(data.result);}
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
		<div class="col-md-6"><div class="obus_numbers">
<fieldset>
<legend>Transport names</legend>
<form name="form1" method="post">
<label for="obusName">Tran Name</label>
<input type="text" name="obusName" id="obusName"/>
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
			<div class="obus_destinations">
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
	</div>
</div><!-- itineraries end -->

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