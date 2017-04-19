<?php
namespace obus;

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

\LinkBox\Logger::log('POST Routines file running');
\LinkBox\Logger::log('POST : '.serialize($_POST) );

if(!empty($_POST['id'])){
$res = false;
$err = "";
	switch($_POST['table']){
		case 'itinerary':
		$res = DBObject::deleteEntry('itinerary', $_POST['id'], 'id_itin');
		break;
		case 'sequences':
		$res = DBObject::deleteEntry('sequences', $_POST['id'], 'id_seq');
		break;
		case 'station':
		$res = DBObject::deleteEntry('station', $_POST['id']);
		\LinkBox\Logger::log('res:'.$res);
		break;
		case 'test':
		$res = true;
		break;
		case 'pitstop':
		$res = Way::DeletePitstop($_POST['id']);		
		break;
		
		default:

	}
	\LinkBox\Logger::log('json : '.json_encode(array('result'=>$res?'ok':'failed') ) );
	echo json_encode(array('result'=>$res?'ok':'failed:'.DBObject::$errormsg) );
}

?>