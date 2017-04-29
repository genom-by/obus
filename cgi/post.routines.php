<?php
namespace obus;

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

function returnPOSTError($err_msg="unpredicted error"){
	//http://localhost/tt/obus/cgi/post.routines.php
	\LinkBox\Logger::log('POST Routines returned error');
	\LinkBox\Logger::log('POST array: '.serialize($_POST) );
	
	ob_end_clean();
	echo json_encode(array('result'=>'failed', 'message'=>$err_msg) );
	die();
}

ob_start();
\LinkBox\Logger::log('POST Routines file running');
\LinkBox\Logger::log('POST : '.serialize($_POST) );

if(!empty($_POST['id'])){
	$res = false;
	$err = "unpredicted error";
	
if(! is_numeric($_POST['id'])){
	$res = false;
	$err = "id should be numeric";
	returnPOSTError($err);
}
	switch($_POST['table']){
		case 'itinerary':
		$res = DBObject::deleteEntry('itinerary', $_POST['id'], 'id_itin');
		break;
		case 'sequences':
		$res = DBObject::deleteEntry('sequences', $_POST['id'], 'id_seq');
		break;
		case 'destination':
		$res = DBObject::deleteEntry('destination', $_POST['id'], 'id_dest');
		break;
		case 'station':
		$res = DBObject::deleteEntry('station', $_POST['id']);
		break;
		case 'test':
		$res = true;
		break;
		case 'pitstop':
		$res = Way::DeletePitstop($_POST['id']);		
		break;
		case 'seq_stations_delete':
		$res = sequencesStations::DeleteSeqStations($_POST['id']);		
		break;
		case 'chart_redraw_seq':
			$seqstats = sequencesStations::getSequenceStationsBySequence($_POST['id']);
			if(false === $seqstats){returnPOSTError('could not obtain sequences');die();}
			else{
				$seqstats = HTML::arrayLineChartCategories($seqstats);
				echo json_encode(array('result'=>'ok', 'payload'=>$seqstats) );
				die();
				}
		break;
		
		default:
		$res=false;
		$err = "table not proceeded";
		returnPOSTError($err);
	}
}else{
	$res = false;
	$err = "no entry id";
	returnPOSTError($err);
}
	\LinkBox\Logger::log('json : '.json_encode(array('result'=>$res?'ok':'failed') ) );
ob_end_clean();
	echo json_encode(array('result'=>$res?'ok':'failed:'.DBObject::$errormsg) );


?>