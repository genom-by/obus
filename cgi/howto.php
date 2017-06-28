<?php
namespace obus;

include_once 'auth.inc.php';

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

?>
<!DOCTYPE html>
<html>
<head>
<title>Obus How to use page</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/bootstrap.min.js"></script>

<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
<script>
/* use: var obus_text = getSelectedText('obusSel');
*/
function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);

    if (elt.selectedIndex == -1)
        return null;

    return elt.options[elt.selectedIndex].text;
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
.oror{
	margin:auto;
	width:50px;
}
img{
    max-width: 100%;
    max-height: 100%;
}
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
			<!-- error messages -->
	<div class="clearfix"></div>
<?php
if(! empty($retval)){$b_class='alert alert-danger';$b_hidden=null;}
if(isset($_GET['result']) AND $_GET['result'] == 0) {$b_class='alert alert-danger';$b_hidden=null;}
if(isset($_GET['result']) AND $_GET['result'] == 1) {$b_class='alert alert-success';$b_hidden=null;
$_GET['msg'] = $_GET['msg']."Please move to <a href='/tt/obus/'>main page</a>.";}
if( ! isset($_GET['result']) ) {$b_class='alert alert-success';$b_hidden='hidden="true"';}
?>
	<div id="obus_formErrors" class="<?=$b_class;?>" <?=$b_hidden;?>">
	<a class="close" href="#" onclick="$('#obus_formErrors').prop('hidden', true);">×</a>
	<p id="obus_register_baloon"><?= unserialize($_GET['msg']);?></p>
	</div>
	<!-- /error messages -->	
		<img src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/Obus_howto1.png';?>"/>
		<img src="http://<?=$_SERVER['HTTP_HOST'].'/'.SITE_ROOT.'/img/Obus_howto2.png';?>"/>
		</div>	
	</div>
</div>	
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