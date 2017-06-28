<?php
namespace obus;

include_once 'auth.inc.php';

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
			if(!$retval){
			\LinkBox\Logger::log("{$_POST['action']} error: ".$obus::$errormsg);
				$actionStatus = 'error';
				$message = $obus::$errormsg;
			}
		}
		break;
	case 'station':
		//echo 'staaation';
		if(!empty($_POST['stationName'])){
			$station= new Station($_POST['stationName'], $_POST['statShortName']);
			$retval = $station->save();
			if(!$retval){
				\LinkBox\Logger::log("{$_POST['action']} error: ".$station::$errormsg);
				$actionStatus = 'error';
				$message = $station::$errormsg;
			}
		}		
		break;
	case 'itinerary':
		//echo 'itinerary';
		//print_r($_POST);
		if(!empty($_POST['itineraryName'])){
			// [itineraryName] => a47c_Çåë_ [obus] => 1 [station] => 1 [startTime] => 07:20 [action] => itinerary 
			$iName = $_POST['itineraryName'];
			$itiner = new Itinerary($iName, $_POST['obus'], $_POST['station'], $_POST['startTime'], $_POST['itinDest']);
			$retval = $itiner->save();
			if(!$retval){
				\LinkBox\Logger::log("{$_POST['action']} error: ".$itiner::$errormsg);
				$actionStatus = 'error';
				$message = Itinerary::$errormsg;
				}
		}			
	break;
	case 'pitstops':
				//print_r($_POST);
			if( !empty($_POST['itinerarySelect']) ){
				$way = new Way($_POST);
				//var_dump($way);
				$retval = $way->save($_POST);
				if(!$retval){
					\LinkBox\Logger::log("{$_POST['action']} error: ".$way::$errormsg);
					$actionStatus = 'error';
					$message = $way::$errormsg;
				}
			}
	break;
	case 'destination':
				//print_r($_POST);
			if( !empty($_POST['destName']) ){
				$dest = new Destination($_POST['destName'], $_POST['destName']);
				$retval = $dest->save();
				if(!$retval){
					\LinkBox\Logger::log("{$_POST['action']} error: ".$dest::$errormsg);
					$actionStatus = 'error';
					$message = $dest::$errormsg;
				}				
			}
	break;
	case 'sequence':
				//print_r($_POST);
			if( !empty($_POST['seqName']) ){
				$seq = new Sequence($_POST['seqName'], $_POST['seqDest']);
				$retval = $seq->save();
				if(!$retval){
					\LinkBox\Logger::log("{$_POST['action']} error: ".$seq::$errormsg);
					$actionStatus = 'error';
					$message = $seq::$errormsg;
				}				
			}
	break;
	case 'sequencesStations':
				//var_dump($_POST);
			if( !empty($_POST['sequencesSelect']) ){
				$seq = new sequencesStations($_POST);
				$retval = $seq->save($_POST);
				if(!$retval){
					\LinkBox\Logger::log("{$_POST['action']} error: ".$seq::$errormsg);
					$actionStatus = 'error';
					$message = $seq::$errormsg;
				}				
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
/*
function itinerarySelect_onChange(){
	$('#submitNewPitstop').prop('disabled', false);	
}*/
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
	action_ = 'delete';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
	
	$.post(
		"post.routines.php",
		{action:action_ ,id:id_entry, table:table_},
		function(data){
		console.log("post returned: "+data.result);
		//alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#'+table_+'_id_'+id_entry;
			$(domID).toggle( "highlight" );
		}else{
			console.log('error message: ',data.message);
			alert(data.message);
		}
		}
		,"json"
	);	
}
/* save edited row
*/
function btnSaveTableItem(table_, id_entry){
	action_ = 'objectUpdate';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' input';
	var els = $(cssFtxt);
	//console.log(els);
var objArray = {};
	//toggleEditControls(rowid, true);
	
	$.each($(els),function(ind,el){

var orm = el.getAttribute("orm")+'2';

	objArray[orm]=el.value;
				//newInput = mountNewInput($(el));
                //$(el).html("");
                //$(el).append(newInput);
		//console.log(objArray['name']);
	})

	data_ = JSON.stringify(objArray);

	$.post(
		"post.routines.php",
		{action:action_ ,id:id_entry, table:table_, data: data_},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID_td = '#'+table_+'_id_'+id_entry+' td.rowtxt';
			var els2 = $(domID_td);
				$.each($(els2),function(ind,el3){
					$(el3).html( objArray[el3.getAttribute("orm")+'2'] );
				})
			//$(domID_td).html( objArray['name2'] );
			toggleEditControls(rowid, false);
		}else{
			console.log('error message: ',data.message);
			alert(data.message);
		}
		}
		,"json"
	);	
}
/* edit entry
*/
function btnEditTableItem(table_, id_entry){
	//console.info("edit into table: "+table_+" entry id:"+id_entry);
	action_ = 'edit';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' td.rowtxt';
	var els = $(cssFtxt);
	//console.log(els);

	toggleEditControls(rowid, true);
	
	$.each($(els),function(ind,el){
	//console.log(ind, el);
				newInput = mountNewInput($(el));
                $(el).html("");
                $(el).append(newInput);
	})
}
/* cancel editing entry
*/
function btnCancelTableItem(table_, id_entry){
	
	action_ = 'cancel';
	//console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' td.rowtxt input';
	var els = $(cssFtxt);

	toggleEditControls(rowid, false);
	
	$.each($(els),function(ind,el){
		var oldVal = el.getAttribute("valInit");
                $(el).parent().html(oldVal);
                //$(el).append(newInput);
	})
}

// sample edit grid
function mountNewInput(cell) {
	
	var element = document.createElement("input");
	//get string in attribute ref
	/*var attrsString = $(cell).attr("ref");
	if(attrsString != null){
		//split attributes
		var attrsArray = attrsString.split(",");

		var currentObj;
		for(n=0; n < attrsArray.length; n++){
			//separate name of attribute and value attribute
			currentObj = attrsArray[n].split(":");
			$(element).attr($.trim(currentObj[0]), $.trim(currentObj[1]));
		}
	}else{
		indexCell = $(cell).parent().children().index($(cell));
		element.setAttribute("name", "column_"+indexCell);
		element.setAttribute("type", "text");
	}*/
	var DB_column_name = $(cell).attr("orm");
	
	element.setAttribute("orm", DB_column_name);
	element.setAttribute("value", $(cell).text());
	element.setAttribute("valInit", $(cell).text());
	//element.setAttribute("style", "width:" + $(cell).width() + "px");
	element.setAttribute("size", "8");
	$(element).addClass("edit_from_te");
	return element;
}
// sample edit grid - end

	//toggleEditControls(table_, id_entry, true);
	/*
	$.post(
		"post.routines.php",
		{action:action_ ,id:id_entry, table:table_},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#'+table_+'_id_'+id_entry;
			$(domID).toggle( "highlight" );
		}else{
			console.log('error message: ',data.message);
			alert(data.message);
		}
		}
		,"json"
	);	//post - end
	*/

// oleg_
function toggleEditControls(rowid, to_dsc) {
/*var rowid = table + '_id_' + id;
var cssFtxt = '#' + rowid + ' td.txt';*/
var cssFed = '#' + rowid + ' .btnEditBl';
var cssFdsc = '#' + rowid + ' .btnDSCBl';
//console.log(cssFdsc, cssFed);

	if (true === to_dsc) {
//console.log('to_dsc:'+to_dsc);	
		$(cssFed).hide();
		$(cssFdsc).show();
        //$(".addnewbtn").hide();
	} else {
		$(cssFed).show();
		$(cssFdsc).hide();
        //$(".addnewbtn").show();
	}	
}
function postTest(){
	console.log('there');
	$.post( "post.routines.php", {action:'test', id: -1 }, function( data ) {
		console.log('here');
	  console.log( 'result: '+data.result ); // John
	  console.log( data.time ); // 2pm
	},"json");
}
function obusUPD(){
	console.log('obusUPD');
	$.post(
		"post.routines.php",
		{id:8, table:'obus_update'},
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
function btn_del_seq_stats_onClick(){
	
	if (! confirm('Are you sure to delete all stations for destination?') ) {return;}
	//console.info("delete from table: "+table_+" entry id:"+id_entry);
	var id_entry = $('#sequencesSelectEdit').val();
	console.info('data to send:', {id:id_entry, table:'seq_stations_delete'});
	
	$.post(
		"post.routines.php",
		{action:'delete', id:id_entry, table:'seq_stations_delete'},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#seqEditContent';
			$(domID).html( "" );
		}else{
			console.log('error message: ',data.message);
		}
		}
		,"json"
	);	
}
/*	delete pitstops for selected itinerary
*/
function btn_del_pits_stats_onClick(){
	
	if (! confirm('Are you sure to delete all stations for itinerary?') ) {return;}
	//console.info("delete from table: "+table_+" entry id:"+id_entry);
	var id_entry = $('#itinerarySelectEdit').val();
	console.info('data to send:', {id:id_entry, table:'itin_pitstops_delete'});
	
	$.post(
		"post.routines.php",
		{action:'delete', id:id_entry, table:'itin_pitstops_delete'},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#pitEditContent';
			$(domID).html( "" );
		}else{
			console.log('error message: ',data.message);
		}
		}
		,"json"
	);	
}

function html_setLastTableID(tablename='pitstops'){
	
	var elLastId = '';
	
	if(tablename=='pitstops'){
		elLastId = $('input[name=laststopID]');
		var lastrow = $('.pitstops_new .trpitnew').filter(":last");
		var lastrowid = lastrow.attr("data-id");
		
		elLastId.val(lastrowid);
		
	}else if(tablename=='seq'){
		elLastId = $('input[name=lastseqID]');
		var lastrow = $('.sequences_new .trseqnew').filter(":last");
		var lastrowid = lastrow.attr("data-id");
		
		elLastId.val(lastrowid);		
	}
	
	
}

/* delete row from html table pitstops 
*/
function btn_delPitstopNewRow(row_id){
console.log(row_id);
	//var domID = '#'+table_+'_id_'+id_entry;
	var domID = '#tbl_pitnew_row_'+row_id;
	$(domID).toggle( "highlight" );
	$(domID).remove();
	var input_total = $('input[name=totalstops]');
	var total_value = input_total.val();
	input_total.val(--total_value);
	
	html_setLastTableID();
}
function btn_delPitstopNewRow2(event){
	console.log(event.data.row_id);
	btn_delPitstopNewRow(event.data.row_id)
}
/* delete row from html table  itins
*/
function btn_delSequenceNewRow(row_id){
console.log(row_id);
	//var domID = '#'+table_+'_id_'+id_entry;
	var domID = '#tbl_seqnew_row_'+row_id;
	$(domID).toggle( "highlight" );
	$(domID).remove();
	var input_total = $('input[name=totalsequences]');
	var total_value = input_total.val();
	input_total.val(--total_value);
	
	html_setLastTableID('seq');
}
function btn_delSequenceNewRow2(event){
	console.log(event.data.row_id);
	btn_delSequenceNewRow(event.data.row_id)
}

/* add new row for html table pitstops 
*/
function btn_addPitstopNewRow(){

	var lastrow = $('.pitstops_new .trpitnew').filter(":last");

	var lastrowid = lastrow.attr("data-id");	//console.log(lastrowid);	
	var clonedrowid = 1+parseInt(lastrowid);	//console.log('clonedrowid:'+clonedrowid);
	
	var cloneable = $('.trpitnewcloneable').clone();
		cloneable.removeClass('trpitnewcloneable').addClass('trpitnew');
		cloneable.attr('data-id',clonedrowid);
		cloneable.attr('id','tbl_pitnew_row_'+clonedrowid);
	
	cloneable.find('button').on('click',{row_id:clonedrowid}, btn_delPitstopNewRow2);

	cloneable.find('input').attr('id','stationTime'+clonedrowid);	
	cloneable.find('input').attr('name','stationTime'+clonedrowid);	
	cloneable.find('select[name=station]').attr('id','stationSel'+clonedrowid);	
	cloneable.find('select[name=pitType]').attr('id','pitType'+clonedrowid);	
	cloneable.find('select[name=station]').attr('name','station'+clonedrowid);	
	cloneable.find('select[name=pitType]').attr('name','pitType'+clonedrowid);
	
	// add this element to table
	lastrow.after(cloneable.css('display','table-row'));

	var domID = '#tbl_pitnew_row_'+lastrowid;

	var input_total = $('input[name=totalstops]');
	var total_value = input_total.val();
		input_total.val(++total_value);
		
	html_setLastTableID();
}
/* add new row for html table sequences
*/
function btn_addSequenceNewRow(){

	var lastrow = $('.sequences_new .trseqnew').filter(":last");

	var lastrowid = lastrow.attr("data-id");	//console.log(lastrowid);	
	var clonedrowid = 1+parseInt(lastrowid);	//console.log('clonedrowid:'+clonedrowid);
	
	var cloneable = $('.trseqnewcloneable').clone();
		cloneable.removeClass('trseqnewcloneable').addClass('trseqnew');
		cloneable.attr('data-id',clonedrowid);
		cloneable.attr('id','tbl_seqnew_row_'+clonedrowid);
	
	cloneable.find('button').on('click',{row_id:clonedrowid}, btn_delSequenceNewRow2);

	cloneable.find('td.order').html(clonedrowid);	
	cloneable.find('input').attr('name','orderal'+clonedrowid);	
	cloneable.find('input').val(clonedrowid);	
	cloneable.find('select[name=station]').attr('id','stationSel'+clonedrowid);	
	cloneable.find('select[name=pitType]').attr('id','pitType'+clonedrowid);	
	cloneable.find('select[name=station]').attr('name','station'+clonedrowid);	
	cloneable.find('select[name=pitType]').attr('name','pitType'+clonedrowid);
	
	// add this element to table
	lastrow.after(cloneable.css('display','table-row'));

	var domID = '#tbl_seqnew_row_'+lastrowid;

	var input_total = $('input[name=totalsequences]');
	var total_value = input_total.val();
		input_total.val(++total_value);
		
	html_setLastTableID('seq');
}

function selPitSeqEdit_onChange(action, itin_id){
	console.log("selected id"+itin_id+" and action is "+action);
	if(action=='pitstops'){
	table_='pits_PitEdit_table'
	divID='#pitEditContent';
	}else if(action=='sequences'){
	table_='seq_SeqEdit_table'
	divID='#seqEditContent';
	}else{table:'no_table'; return false;}
	$.post(
		"post.routines.php",
		{action: 'pageUpdate', id:itin_id, table:table_},
		function(data){
			console.log("post returned: "+data.result);
		if (data.result == 'ok' ){
			//console.log(data.payload);
			$(divID).html(data.payload);
		}else{
			console.log('error message: ',data.message);
			$(divID).html(data.message);			
		}
		}
		,"json"
	);
}
/*	selector for adding new itin / sequence
*/
function itinerarySelect_onChange(action, itin_id){
	console.log("selected id"+itin_id+" and action is "+action);

	if(action=='pitstop'){
		table_='pitstop';
		data_='is_exists';
		divID='#selectResult_itin';
		submitButtonID='#submitNewPitstop';
	}else if(action=='sequences'){
		table_='sequence';
		data_='is_exists';
		divID='#selectResult_seq';
		submitButtonID='#submitNewSequence';
	}else{table:'no_table'; return false;}
	
	if(itin_id == -1){
		$('#submitNewPitstop').prop('disabled', true);
		$(divID).html('Select itinerary');
		return false;
	}	
	$.post(
		"post.routines.php",
		{action: 'inquire', id:itin_id, table:table_, question:data_},
		function(data){
			console.log("post returned: "+data.result);
		if (data.result == 'ok' ){
			console.log(data.payload);
			$(divID).html('Cannot create, records exist: '+data.payload);
			$(submitButtonID).prop('disabled', true);				
		}else{
			console.log('error message: ',data.message);
			$(divID).html('New entry allowed. '+data.message);
			$(submitButtonID).prop('disabled', false);				
		}
		}
		,"json"
	);

}
//SERIALIZE
function serialize(_obj)
{
   // Let Gecko browsers do this the easy way
   if (typeof _obj.toSource !== 'undefined' && typeof _obj.callee === 'undefined')
   {
      return _obj.toSource();
   }
   // Other browsers must do it the hard way
   switch (typeof _obj)
   {
      // numbers, booleans, and functions are trivial:
      // just return the object itself since its default .toString()
      // gives us exactly what we want
      case 'number':
      case 'boolean':
      case 'function':
         return _obj;
         break;

      // for JSON format, strings need to be wrapped in quotes
      case 'string':
         return '\'' + _obj + '\'';
         break;

      case 'object':
         var str;
         if (_obj.constructor === Array || typeof _obj.callee !== 'undefined')
         {
            str = '[';
            var i, len = _obj.length;
            for (i = 0; i < len-1; i++) { str += serialize(_obj[i]) + ','; }
            str += serialize(_obj[i]) + ']';
         }
         else
         {
            str = '{';
            var key;
            for (key in _obj) { str += key + ':' + serialize(_obj[key]) + ','; }
            str = str.replace(/\,$/, '') + '}';
         }
         return str;
         break;

      default:
         return 'UNKNOWN';
         break;
   }
}
//SERIALIZE
<?php if( Auth::notLogged()){ ?>
$(function(){
$('fieldset').prop('disabled',true);
})
<?}else{?>
$('fieldset').prop('disabled',false);
<?}?>
</script>
<style>
.hided{
	display:none;
}
.hided2{
	visibility:hidden;
}
.table-condensed button {
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
td.inp{
display:none;
}
table.pitstops_new td, table.sequences_new td{
padding:1px 2px;
}
.btn_new_tablerow{
width:100%;
}
.trpitnewcloneable, .trseqnewcloneable{
display:none;
}
.tbl_pitnew_row_del, .tbl_seqnew_row_del{
	margin-left:5px;
}
td.order{
	width:5px;
}
.hid_inp{
	display:none;
}
/* ... loading ...
.loading{
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
}
*/
</style>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="obus_header">
			<?php include_once '../tmplt/topmenu.inc.php' ?> 
			</div>
		</div>	
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php include_once '../tmplt/errorBlock.inc.php' ?> 
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
<button type="button" id="btn_showdest" data-toggle="collapse" data-target="#destblock">Show Dest</button>
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
<label for="statShortName">Short Name</label>
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
<select name="itinDest" id="itinDest">
<?php echo HTML::getSelectItems('destination');?>
</select>
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
		</div><!-- -----------------------------------sequences ------------------------------------ -->
		<div class="col-md-4">
			<div class="obus_sequense">
<fieldset>
<legend>Sequence</legend>
<form name="formSeq" method="post">
<select name="seqDest" id="seqDest">
<?php echo HTML::getSelectItems('destination');?>
</select>
<label for="seqName">Seq.Name</label>
via<input type="text" name="seqName" id="seqName" autocomplete="off"/>
<input type="submit" value="Send"/>
<input type="hidden" name="action" value="sequence">
<p>
</p>
<!--show existing-->
<button type="button" id="btn_showseq" data-toggle="collapse" data-target="#seqblock">Show Seq</button>
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
			<h3>Pitstops</h3>
	<ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#secPitNew">New pitstop</a></li>
        <li><a data-toggle="tab" href="#secPitEdit">Edit pitstops</a></li>
    </ul>
    <div class="tab-content">
<!-- ---------------- New Pitstop ------------------------ -->
<div id="secPitNew" class="tab-pane fade in active">
<fieldset>
<form name="form4" method="post">
<p>Select itinerary</p>
<select name="itinerarySelect" id="itinerarySelect" onChange="itinerarySelect_onChange('pitstop',this.value);">
<?php echo HTML::getSelectItems('itinerary');?>
</select>_<span id="selectResult_itin"></span>
<p></p>
<?php echo HTML::getPitStopsTable2();?>
<p></p>
<input type="hidden" name="action" value="pitstops">
<input type="submit" value="Send" id="submitNewPitstop" disabled/>
</form>
</fieldset>	
</div>
<!-- ---------------- Edit Pitstop ------------------------ -->
        <div id="secPitEdit" class="tab-pane fade">
<fieldset>
<form name="formPitEdit" method="post">
<p>Select itinerary</p>
<select name="itinerarySelectEdit" id="itinerarySelectEdit" onchange="selPitSeqEdit_onChange('pitstops',this.value)">
<?php echo HTML::getSelectItems('itinerary');?>
</select>___<button type="button" id="btn_del_pits_stats" onclick="btn_del_pits_stats_onClick();">Clear stations</button>
<p></p>
<table id="pitEditContent" class="table table-striped table-hover table-condensed small">
</table>
<p></p>
<input type="hidden" name="action" value="pitstopsEdit">
<input type="submit" value="Send"/>
</form>
</fieldset>	
        </div>
    </div> 		
			</div>
		</div>
<!-- ------------------------------------- pitstops -- end ---------   |   ----------------------------- sequenses --- begin -----------------------------------    -->
		<div class="col-md-6">
			<div class="obus_sequences">
			<h3>Sequences</h3>
	<ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#secSeqNew">New sequence</a></li>
        <li><a data-toggle="tab" href="#secSeqEdit">Edit sequences</a></li>
    </ul>
	<div class="tab-content">
<!-- ---------------- New sequence ------------------------ -->
	<div id="secSeqNew" class="tab-pane fade in active">
<fieldset>
<form name="formSeq" method="post">
<p>Select sequence</p>
<select name="sequencesSelect" id="sequencesSelect" onChange="itinerarySelect_onChange('sequences',this.value);">
<?php echo HTML::getSelectItems('sequences');?>
</select>_<span id="selectResult_seq"></span>
<p></p>
<?php echo HTML::getSequencesTable2();?>
<p></p>
<input type="hidden" name="action" value="sequencesStations">
<input type="submit" value="Send" id="submitNewSequence" disabled/>
</form>
</fieldset>	
	</div>
<!-- ---------------- Edit Sequence ------------------------ -->
        <div id="secSeqEdit" class="tab-pane fade">
<fieldset>
<form name="formSeqEdit" method="post">
<p>Select sequence</p>
<select name="sequencesSelectEdit" id="sequencesSelectEdit" onchange="selPitSeqEdit_onChange('sequences', this.value)">
<?php echo HTML::getSelectItems('sequences');?>
</select>___<button type="button" id="btn_del_seq_stats" onclick="btn_del_seq_stats_onClick();">Clear stations</button>
<p></p>
<table id="seqEditContent" class="table table-striped table-hover table-condensed small">
</table>
<p></p>
<input type="hidden" name="action" value="sequencesStationsEdit">
<input type="submit" value="Send"/>
</form>
</fieldset>	
        </div>	
	</div>


			</div>
		</div>	
	</div>
</div>	
<!-- ---------------------------------------------- pitstops -- end ---------   |   ----------------------------- sequenses --- end -----------------------------------    -->
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
<pre>
<?php
//$pitstops = Way::GetPitsCountForItinerary(13);
//var_dump($pitstops);
/*
$pitstops = Way::getPitstopsByDestination(1);
$seqstats = sequencesStations::getSeqStatNamesBySequenceID(1); echo "[".HTML::arrayLineChartCategories($seqstats)."]";

$pitstops = Way::getPitstopsBySequence(1); 

echo HTML::arrayLineChart($pitstops, 1); 
*/
//if((1 === false)OR(false===false)){echo 'bre';}
//\LinkBox\Logger::log(serialize($pitstops));
//
//echo json_encode($pitstops);
//echo HTML::normalizeWays2JSON($pitstops);
//<button onClick="obusUPD();">obusUpd(8)</button>
//<button onClick="postTest();">postTest</button>
?>
</pre>

<span id="testSpan"></span>
</body>
</html>