<?php
namespace obus;

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

\LinkBox\Logger::log('POST Routines file running');
\LinkBox\Logger::log('POST : '.serialize($_POST) );

if(!empty($_POST['id'])){
	Way::DeletePitstop($_POST['id']);
}

?>