<?php
namespace obus;

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

//\LinkBox\Logger::log('Start logging');
var_dump($_POST);
$retval = '';
if(!empty($_POST['action'])){
		
		//TODO
		/*
		https://github.com/delight-im/PHP-Auth
		http://phppot.com/php/php-login-script-with-remember-me/
		https://github.com/PHPAuth/PHPAuth/blob/master/Auth.php
		*/
		
switch ($_POST['action']){
	case 'login':
		//echo 'obus';		
		if(!empty($_POST['userName'])){
			$user = new User($_POST['userName']);
			$retval = $obus->save();
			if(!$retval)
			print_r("result: ".obus::$errormsg);
		}
		break;
	case 'register':
		//echo 'staaation';
		if(!empty($_POST['inputName'])){
			$newuser= new User($_POST['inputName'], $_POST['inputEmail'], $_POST['inputPWD']);
			$retval = $newuser->save();
			if(!$retval)
			print_r("result: ".User::$errormsg);
		}		
		break;
	case 'logout':
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
	case 'remember':
				//print_r($_POST);
			if( !empty($_POST['itinerarySelect']) ){
				$way = new Way($_POST);
				$retval = $way->save($_POST);
				if(!$retval)
					print_r("result: ".way::$errormsg);				
			}
	break;
	default:
		$retval = 'not dispatched user action';
	}
}
//echo( 'action:'.$_POST['action'] );
//parse_str($_POST["lbx_form_addlink"], $ajax);
//print_r($ajax);
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
			<div class="obus_userform">
			<div class="centered"><h3>Provide information about himself:</h3></div>
<form class="form-horizontal" name="obus_registerForm" id="obus_registerForm" method="post" action="authpage.php">
	<div class="form-group has-feedback">
		<label class="control-label col-xs-2" for="inputName">User Name</label><div class="col-xs-6">
			<input type="text" placeholder="User Name" class="form-control" id="inputName" name="inputName" autocomplete="off" autocorrect="off"><span class="val_msg glyphicon form-control-feedback"></span>
		</div><div class="col-xs-2"></div>	
	</div>
	<div class="form-group has-feedback">
		<label class="control-label col-xs-2" for="inputEmail">Email</label><div class="col-xs-6">
			<input type="text" placeholder="Email" class="form-control" id="inputEmail" name="inputEmail"autocomplete="off" autocorrect="off"><span class="val_msg glyphicon form-control-feedback"></span>
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