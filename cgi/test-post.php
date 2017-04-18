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
			print_r("result: ".obus::$errormsg);
		}
		break;
	case 'station':
		//echo 'staaation';
		if(!empty($_POST['stationName'])){
			$station= new Station($_POST['stationName'], $_POST['statShortName']);
			$retval = $station->save();
			if(!$retval)
			print_r("result: ".station::$errormsg);
		}		
		break;
	case 'itinerary':
		//echo 'itinerary';
		//print_r($_POST);
		if(!empty($_POST['itineraryName'])){
			// [itineraryName] => a47c_Зел_ [obus] => 1 [station] => 1 [startTime] => 07:20 [action] => itinerary 
			$iName = $_POST['itineraryName'];
			$itiner = new Itinerary($iName, $_POST['obus'], $_POST['station'], $_POST['startTime']);
			$retval = $itiner->save();
			if(!$retval)
				print_r("result: ".itinerary::$errormsg);			
		}			
	break;
	case 'pitstops':
				//print_r($_POST);
			if( !empty($_POST['itinerarySelect']) ){
				$way = new Way($_POST);
				$retval = $way->save($_POST);
				if(!$retval)
					print_r("result: ".way::$errormsg);				
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
</style>
</head>
<body>
<button onclick="postTest();">test post</button>
<button onclick="postTest2();">test post 2</button>
</body>
</html>