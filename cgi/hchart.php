<?php
namespace obus;
include_once 'utils.inc.php';
include_once 'dbObjects.class.php';
include_once 'HTMLroutines.class.php';

?>
<html>
<head>
<title>Chart scheduling</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/highcharts.js"></script>
<script type="text/javascript" src="../js/highcharts-more.js"></script>
<script type="text/javascript" src="../js/exporting.js"></script>
<script>
var d          = new Date();
var pointStart = d.getTime();
Highcharts.setOptions({
    global: {
        useUTC:false
    },
    colors: [
        'rgba( 0,   154, 253, 0.9 )', //bright blue
        'rgba( 253, 99,  0,   0.9 )', //bright orange
        'rgba( 40,  40,  56,  0.9 )', //dark
        'rgba( 253, 0,   154, 0.9 )', //bright pink
        'rgba( 154, 253, 0,   0.9 )', //bright green
        'rgba( 145, 44,  138, 0.9 )', //mid purple
        'rgba( 45,  47,  238, 0.9 )', //mid blue
        'rgba( 177, 69,  0,   0.9 )', //dark orange
        'rgba( 140, 140, 156, 0.9 )', //mid
        'rgba( 238, 46,  47,  0.9 )', //mid red
        'rgba( 44,  145, 51,  0.9 )', //mid green
        'rgba( 103, 16,  192, 0.9 )'  //dark purple
    ],
    chart: {
        alignTicks:false,
        type:'',
        margin:[80,25,50,25],
        //borderRadius:10,
        //borderWidth:1,
        //borderColor:'rgba(156,156,156,.25)',
        //backgroundColor:'rgba(204,204,204,.25)',
        //plotBackgroundColor:'rgba(255,255,255,1)',
        style: {
            fontFamily: 'Abel,serif'
        },        
    events:{
            load: function() {
                this.credits.element.onclick = function() {
                    window.open(
                      'http://stackoverflow.com/users/1011544/jlbriggs?tab=profile'
                    );
                 }
            }
        }           
    },
    credits: {
        text : 'http://stackoverflow.com/users/1011544/jlbriggs',
        href : 'http://stackoverflow.com/users/1011544/jlbriggs?tab=profile'
    },
    title: {
        text:'Test Chart Title',
        align:'left',
        margin:10,
        x: 10,
        style: {
            fontWeight:'bold',
            color:'rgba(0,0,0,.9)'
        }
    },
    subtitle: {
        text:'Test Chart Subtitle',   
        align:'left',
        x: 12,
    },
    legend: { enabled: false },
    plotOptions: {
        area: {
            lineWidth:1,
            marker: {
                enabled:false,
                symbol:'circle',
                radius:4
            }
        },
        arearange: {
            lineWidth:1
        },
        areaspline: {
            lineWidth:1,
            marker: {
                enabled:false,
                symbol:'circle',
                radius:4
            }
        },
        areasplinerange: {
            lineWidth:1
        },
        boxplot: {
            groupPadding:0.05,
            pointPadding:0.05,
            fillColor:'rgba(255,255,255,.75)'
        },
		bubble: {
			minSize:'0.25%',
			maxSize:'17%'
		},
        column: {
            //stacking:'normal',
            groupPadding:0.05,
            pointPadding:0.05
        },
        columnrange: {
            groupPadding:0.05,
            pointPadding:0.05
        },
        errorbar: {
            groupPadding:0.05,
            pointPadding:0.05,
        	showInLegend:true        
        },
        line: {
            lineWidth:1,
            marker: {
                enabled:false,
                symbol:'circle',
                radius:4
            }
        },
        scatter: {
            marker: {
                symbol: 'circle',
                radius:5
            }
        },
        spline: {
            lineWidth:1,
            marker: {
                enabled:false,
                symbol:'circle',
                radius:4
            }
        },
        series: {
            shadow: false,
            borderWidth:0,
            states: {
                hover: {
                    lineWidthPlus:0,
                }
            }
        }
    },
    xAxis: {
        title: {
            text: null,
            rotation:0,
            textAlign:'center',
            style:{ 
                color:'rgba(0,0,0,.9)'
            }
        },
        labels: { 
            style: {
                color: 'rgba(0,0,0,.9)',
                fontSize:'9px'
            }
        },
        lineWidth:.5,
        lineColor:'rgba(0,0,0,.5)',
        tickWidth:.5,
        tickLength:3,
        tickColor:'rgba(0,0,0,.75)'
    },
    yAxis: {
        minPadding:0,
        maxPadding:0,
        gridLineColor:'rgba(20,20,20,.25)',
        gridLineWidth:0.5,
        title: { 
            text: null,
            rotation:0,
            textAlign:'right',
            style:{ 
                color:'rgba(0,0,0,.9)',
            }
        },
        labels: { 
            style: {
                color: 'rgba(0,0,0,.9)',
                fontSize:'9px'
            }
        },
        lineWidth:.5,
        lineColor:'rgba(0,0,0,.5)',
        tickWidth:.5,
        tickLength:3,
        tickColor:'rgba(0,0,0,.75)'
    }
});	
    
function randomData(points, positive, multiplier) {
    points     = !points            ? 1     : points;
    positive   = positive !== true  ? false : true;
    multiplier = !multiplier        ? 1     : multiplier;
    
    function rnd() {
        return ((
            Math.random() + 
            Math.random() + 
            Math.random() + 
            Math.random() + 
            Math.random() + 
            Math.random()
        ) - 3) / 3;
    }
    var rData = [];
    for (var i = 0; i < points; i++) {
        val = rnd();
        val = positive   === true ? Math.abs(val)      : val;
        val = multiplier >   1    ? (val * multiplier) : val;
        rData.push(val);    
    }
    return rData;
}
<?php $pitstops = Way::getPitstopsByItinerary(); 
//\LinkBox\Logger::log(serialize($pitstops));
?>
var cars = [
{name:"chevrolet chevelle malibu", mpg:18, cyl:8, dsp:307, hp:130, lbs:3504, acc:12, year:70, origin:1},
{name:"buick skylark 320", mpg:15, cyl:8, dsp:350, hp:165, lbs:3693, acc:11.5, year:70, origin:1},
{name:"plymouth satellite", mpg:18, cyl:8, dsp:318, hp:150, lbs:3436, acc:11, year:70, origin:1},
{name:"amc rebel sst", mpg:16, cyl:8, dsp:304, hp:150, lbs:3433, acc:12, year:70, origin:1},
{name:"ford torino", mpg:17, cyl:8, dsp:302, hp:140, lbs:3449, acc:10.5, year:70, origin:1},
{name:"ford galaxie 500", mpg:15, cyl:8, dsp:429, hp:198, lbs:4341, acc:10, year:70, origin:1},
{name:"chevrolet impala", mpg:14, cyl:8, dsp:454, hp:220, lbs:4354, acc:9, year:70, origin:1},
{name:"plymouth fury iii", mpg:14, cyl:8, dsp:440, hp:215, lbs:4312, acc:8.5, year:70, origin:1},
{name:"pontiac catalina", mpg:14, cyl:8, dsp:455, hp:225, lbs:4425, acc:10, year:70, origin:1},
{name:"amc ambassador dpl", mpg:15, cyl:8, dsp:390, hp:190, lbs:3850, acc:8.5, year:70, origin:1},
{name:"citroen ds-21 pallas", mpg:undefined, cyl:4, dsp:133, hp:115, lbs:3090, acc:17.5, year:70, origin:2},
{name:"chevrolet chevelle concours (sw)", mpg:undefined, cyl:8, dsp:350, hp:165, lbs:4142, acc:11.5, year:70, origin:1},
{name:"ford torino (sw)", mpg:undefined, cyl:8, dsp:351, hp:153, lbs:4034, acc:11, year:70, origin:1},
{name:"plymouth satellite (sw)", mpg:undefined, cyl:8, dsp:383, hp:175, lbs:4166, acc:10.5, year:70, origin:1},
{name:"amc rebel sst (sw)", mpg:undefined, cyl:8, dsp:360, hp:175, lbs:3850, acc:11, year:70, origin:1},
{name:"dodge challenger se", mpg:15, cyl:8, dsp:383, hp:170, lbs:3563, acc:10, year:70, origin:1},
{name:"plymouth 'cuda 340", mpg:14, cyl:8, dsp:340, hp:160, lbs:3609, acc:8, year:70, origin:1},
{name:"ford mustang boss 302", mpg:undefined, cyl:8, dsp:302, hp:140, lbs:3353, acc:8, year:70, origin:1},
{name:"chevrolet monte carlo", mpg:15, cyl:8, dsp:400, hp:150, lbs:3761, acc:9.5, year:70, origin:1},
{name:"ford gran torino (sw)", mpg:14, cyl:8, dsp:302, hp:140, lbs:4638, acc:16, year:74, origin:1},
{name:"renault 18i", mpg:34.5, cyl:4, dsp:100, hp:undefined, lbs:2320, acc:15.8, year:81, origin:2},
{name:"saab 900s", mpg:undefined, cyl:4, dsp:121, hp:110, lbs:2800, acc:15.4, year:81, origin:2},
{name:"volvo diesel", mpg:30.7, cyl:6, dsp:145, hp:76, lbs:3160, acc:19.6, year:81, origin:2},
{name:"amc concord dl", mpg:23, cyl:4, dsp:151, hp:undefined, lbs:3035, acc:20.5, year:82, origin:1},
{name:"dodge rampage", mpg:32, cyl:4, dsp:135, hp:84, lbs:2295, acc:11.6, year:82, origin:1},
{name:"ford ranger", mpg:28, cyl:4, dsp:120, hp:79, lbs:2625, acc:18.6, year:82, origin:1},
{name:"chevy s-10", mpg:31, cyl:4, dsp:119, hp:82, lbs:2720, acc:19.4, year:82, origin:1}
];
</script>
<script type="text/javascript" src="../js/hchart.js"></script>
<link rel="stylesheet" type="text/css" href="../css/hchart.css">
</head>
<body>
<div id="container" style="width:1000px;height:600px;margin:.5em;"></div>
</body>
</html>