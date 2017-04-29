<?php
namespace obus;
include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

?>
<html>
<head>
<title>Chart Line scheduling</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/highcharts.js"></script>
<script type="text/javascript" src="../js/highcharts-more.js"></script>
<script type="text/javascript" src="../js/exporting.js"></script>
<script>
		
	$(function() {	
var chart1 = new Highcharts.chart('container', {
    chart:		{	type: 'line'   },
    title:		{	text: 'Transport timeline'    },
    subtitle:	{	text: 'Source: minsktrans'    },	
    xAxis: {
        categories: ['zel0', 'kol1', 'nem2', 'mas3', 'akd4', 'spu5', 'kaz6', 'tra7']
    },
    yAxis: {
        title: {          text: 'Time (normalized)'        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true,
	point: {
		events: {
			click: function (e) {
				/*hs.htmlExpand(null, {
					pageOrigin: {
						x: e.pageX || e.clientX,
						y: e.pageY || e.clientY
					},
					headingText: this.series.name,
					maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) + ':<br/> ' +
						this.y + ' visits',
					width: 200
				});*/
			}
		}
	}
        }
    },
	tooltip: {  formatter: pit_formatter 
        /*formatter: function () {            return 'Time at <b>' + this.x + '</b> is <b>' + time2HHMM(this.y) + '</b><br/> of '+this.series.name;   }*/
    },
    series: <?php $pitstops = Way::getPitstopsByItinerary(); echo HTML::arrayLineChart($pitstops);?>
});
})
function pit_formatter(){
return 'Time at <b>' + this.x +
                '</b> is <b>' + time2HHMM(this.y) + '</b><br/> of '+this.series.name;
}
function time2HHMM(time){
		var m = time % 60;
		var mstr = '';
		var h = Math.floor(time / 60);
		if(m < 10){ mstr = '0'+m}else{mstr = m}
		return h+':'+mstr;
}
function redraw(){
/*var newData = [1,2,3,4,5,6,7];
var chart = $('#container').highcharts();
chart.series[0].setData(newData, true); */
	var id_sequence = $('#sequencesSelect').val();
	console.info('data to send:', {id:id_sequence, table:'chart_redraw_seq'});
	
	var newSequences = '';
	
	$.post(
		"post.routines.php",
		{id:id_sequence, table:'chart_redraw_seq'},
		function(data){
console.log("post returned: "+data.result);
console.log("post payload: "+data.payload);
		alert(data.result);
		if (data.result == 'ok' ){
	newSequences = data.payload ;
			//var domID = '#'+table_+'_id_'+id_entry;
			//$(domID).toggle( "highlight" );
		}else{
			console.log('error message: ',data.message);
		}
		}
		,"json"
	);
//TODO multithreading value
console.log("newSequences: "+newSequences);	

var chart1 = $('#container').highcharts();
chart1.destroy();
var chart1 = new Highcharts.chart('container', {
    chart:		{	type: 'line'   },
    title:		{	text: 'Transport timeline'    },
    subtitle:	{	text: 'Source: minsktrans'    },	
    xAxis: {
        //categories: ['zel0', 'kol1', 'nem2', 'mas3', 'akd4', 'spu5', 'kaz6', 'tra7']
		categories: <?php $seqstats = sequencesStations::getSequenceStationsBySequence(1); echo HTML::arrayLineChartCategories($seqstats);?>
    },
    yAxis: {
        title: {          text: 'Time (normalized)'        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true,
	point: {
		events: {
			click: function (e) {
				/*hs.htmlExpand(null, {
					pageOrigin: {
						x: e.pageX || e.clientX,
						y: e.pageY || e.clientY
					},
					headingText: this.series.name,
					maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) + ':<br/> ' +
						this.y + ' visits',
					width: 200
				});*/
			}
		}
	}
        }
    },
	tooltip: {  formatter: pit_formatter 
        /*formatter: function () {            return 'Time at <b>' + this.x + '</b> is <b>' + time2HHMM(this.y) + '</b><br/> of '+this.series.name;   }*/
    },
    series: <?php $pitstops = Way::getPitstopsByItinerary(); echo HTML::arrayLineChart($pitstops);?>
});
}
</script><pre>
<?php
//echo HTML::arrayLineChart($pitstops);
?></pre>
<!--<script type="text/javascript" src="../js/hchart.js"></script> -->
<link rel="stylesheet" type="text/css" href="../css/hchart.css">
</head>
<body>
<div id="container" style="width:1000px;height:600px;margin:.5em;"></div>
<fieldset>
<select name="sequencesSelect" id="sequencesSelect">
<?php echo HTML::getSelectItems('sequences');?>
</select>
<button onClick="redraw();">Redraw</button>
</fieldset>
<a href="obus-test.php" >settings</a>
<?php $seqstats = sequencesStations::getSequenceStationsBySequence(1); //echo HTML::arrayLineChartCategories($seqstats);?>
</body>
</html>