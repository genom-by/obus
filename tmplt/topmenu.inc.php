<?php
namespace obus;

include_once 'auth.inc.php';

include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

?>

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

<nav role="navigation" class="navbar navbar-default">
<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
<button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<a href="hchartline.php" class="navbar-brand">Chart</a>
</div>
<!-- Collection of nav links and other content for toggling -->
<div id="navbarCollapse" class="collapse navbar-collapse">
	<ul class="nav navbar-nav">
		<li class="active"><a href="#">Scheduling</a></li>
		<li><a href="#">Profile</a></li>
		<li><a href="#">Messages</a></li>
	</ul>
	<ul class="nav navbar-nav navbar-right">
	<?if(!isset($_SESSION["user_id"])){?>
		<li><a href="#">Login</a></li>
	<?}else {?>
		<li class="dropdown">
    <a data-toggle="dropdown" class="dropdown-toggle" href="#">Logged as <? echo $_SESSION['user_name'];?> <b class="caret"></b></a>
			<ul role="menu" class="dropdown-menu">
				<li><a href="#">Inbox</a></li>
				<li><a href="#">Drafts</a></li>
				<li><a href="#">Sent Items</a></li>
				<li class="divider"></li>
				<li><a href="?action=logout">Logout</a></li>
			</ul>
		</li>
	<?}?>
	</ul>
</div>
</nav>