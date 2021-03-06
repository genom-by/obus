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
<title>Obus User page</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/obusRegister.js"></script>
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
.notifylink{
	text-decoration:underline;
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
			<?php include_once '../tmplt/errorBlock.inc.php' ?> 
	<!-- /error messages -->	
			<div class="obus_userform">
			<div class="centered"><h3>Provide information about himself:</h3></div>
<form class="form-horizontal" name="obus_registerForm" id="obus_registerForm" method="post" action="registerpage.php">
	<div class="form-group has-feedback">
		<label class="control-label col-xs-2" for="userName">User Name</label><div class="col-xs-6">
			<input type="text" placeholder="User Name" class="form-control" id="userName" name="userName" autocomplete="off" autocorrect="off"><span class="val_msg glyphicon form-control-feedback"></span>
		</div><div class="col-xs-2"></div>	
	</div>
	<div class="form-group has-feedback">
		<label class="control-label col-xs-2" for="userEmail">Email</label><div class="col-xs-6">
			<input type="text" placeholder="Email" class="form-control" id="userEmail" name="userEmail"autocomplete="off" autocorrect="off"><span class="val_msg glyphicon form-control-feedback"></span>
		</div><div class="col-xs-2"></div>	
	</div>
	<div class="form-group has-feedback">
		<label class="control-label col-xs-2" for="inputPWD">Password</label><div class="col-xs-6">
			<input type="password" placeholder="Password" class="form-control" id="inputPWD" name="inputPWD" autocomplete="off" autocorrect="off"><span class="val_msg glyphicon form-control-feedback"></span>
		</div><div class="col-xs-2"></div>	
	</div>
	<div class="form-group has-feedback">
		<label class="control-label col-xs-2" for="inputPWD2">Confirm Password</label><div class="col-xs-6">
			<input type="password" placeholder="Confirm Password" class="form-control" id="inputPWD2" name="inputPWD2" autocomplete="off" autocorrect="off"><span class="val_msg glyphicon form-control-feedback"></span>
		</div><div class="col-xs-2"></div>	
	</div>
	<div class="form-group">
		<div class="col-xs-offset-3 col-xs-9">
			<label class="checkbox-inline" for="inputAgree">
		<input type="checkbox" value="agree" id="inputAgree" name="inputAgree">  I agree to the <a href="#">Terms and Conditions</a>.
			</label>
		</div>
	</div>
	<br>
	<div class="form-group">
		<div class="col-xs-offset-3 col-xs-9">
			<input type="submit" class="btn btn-primary" value="Submit" id="btn_register">
			<input type="reset" class="btn btn-default" value="Reset" id="btn_reset">
			<!--<button type="submit" id="btn_addLink" class="btn btn-info"><span class="glyphicon glyphicon-star"></span><span id="btn_addLinkCaption"> Add link</span></button>-->
			<div class="clearfix"></div>	
		</div>
	</div>
	<input type="hidden" name="action" value="register">
</form>
<button type="button" onClick="refreshValidations();">Refresh</button>
			</div>
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