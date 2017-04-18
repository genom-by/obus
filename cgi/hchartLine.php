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
Highcharts.chart('container', {
    chart: {
        type: 'line'
    },
    xAxis: {
        categories: ['zel0', 'kol1', 'nem2', 'mas3', 'akd4', 'spu5', 'kaz6', 'tra7']
    },
    title: {
        text: 'Transport timeline'
    },
    subtitle: {
        text: 'Source: minsktrans'
    },

    yAxis: {
        title: {
            text: 'Time (normalized)'
        }
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
	tooltip: {
        formatter: function () {
            return 'Time at <b>' + this.x +
                '</b> is <b>' + time2HHMM(this.y) + '</b><br/> of '+this.series.name;
        }
    },
    series: <?php $pitstops = Way::getPitstopsByItinerary(); echo HTML::arrayLineChart($pitstops);?>
});
})
function time2HHMM(time){
		var m = time % 60;
		var mstr = '';
		var h = Math.floor(time / 60);
		if(m < 10){ mstr = '0'+m}else{mstr = m}
		return h+':'+mstr;
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
<a href="obus-test.php" >settings</a>
</body>
</html>