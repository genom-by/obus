var chart;
var cData   = getCarData(cars);
var carData = cData.carData;
var catsTop = cData.catsTop;
var catsBot = cData.catsBot;
$(function() {
    $('#container').highcharts({
        chart       : { type    : 'line', alignTicks: false },
        title       : { text: 'Parallel Coordindates' },
        subtitle    : { text: 'Proof of Concept Using the classic \'cars\' data set' },
        legend      : { enabled : false },
        tooltip     : { enabled : true },
    plotOptions : { 
			series : {
				color : 'rgba(20,20,20,.25)',
                events: {
                    mouseOver: function() {                      
                        this.graph.attr('stroke', 'rgba(0,156,255,1)');
                        this.group.toFront();
                    },
                    mouseOut: function() {
                        this.graph.attr('stroke', 'rgba(20,20,20,0.25)');
                    }
                }
			}
		},
		xAxis  : [{
			opposite: true,
			tickInterval:1,
			lineWidth:0,
			tickWidth:0,
			gridLineWidth:1,
			gridLineColor:'rgba(0,0,0,0.5)',
			gridZIndex: 5,
			labels: {
				y:-17,	
				formatter: function() {
					return catsTop[this.value];
				},
				style: {
					fontWeight:'bold'
				}
			}
		},{
			linkedTo:0,
			lineWidth:0,
			tickWidth:0,
			gridLineWidth:0,
			labels: {
                y:10,	
				formatter: function() {
					return catsBot[this.value];
				},
				style: {
					fontWeight:'bold'
				}
			}
		}],
		yAxis  : {
			min:0,
			max:100,
			gridLineWidth:0,
            tickWidth:0,
			lineWidth:0,
			labels: {
				enabled: false
			}
		},
       	series : carData
	});	
    chart = $('#container').highcharts();
})

function getCarData(cars) {
    var mpgs 	= [];	var cyls 	= [];	var dsps 	= [];	var hps 	= [];
	var lbss 	= [];	var accs 	= [];	var years 	= [];	
	
	var mins 	= {};	var maxs 	= {};	var ranks 	= {};	var pData 	= {};
	
	var mpg;	var cyl;	var dsp;	var hp;	var lbs;	var acc;	var year;
	var paramNames = ['mpg', 'cyl', 'dsp', 'hp', 'lbs', 'acc', 'year'];
	        	
	$.each(cars, function(i, car) {

		//if(typeof car[paramNames[0]] 	!= 'undefined') { mpgs.push(car[paramNames[0]]		); }	
		if(typeof car.mpg 	!= 'undefined') { mpgs.push(car.mpg		); }	
		if(typeof car.cyl 	!= 'undefined') { cyls.push(car.cyl		); }	
		if(typeof car.dsp 	!= 'undefined') { dsps.push(car.dsp		); }	
		if(typeof car.hp  	!= 'undefined') { hps.push(car.hp		); }	
		if(typeof car.lbs 	!= 'undefined') { lbss.push(car.lbs		); }	
		if(typeof car.acc 	!= 'undefined') { accs.push(car.acc		); }	
		if(typeof car.year 	!= 'undefined') { years.push(car.year	); }	

		mpg 	= typeof car.mpg 	!= 'undefined' ? car.mpg 	: null;
		cyl 	= typeof car.cyl 	!= 'undefined' ? car.cyl 	: null;
		dsp 	= typeof car.dsp 	!= 'undefined' ? car.dsp 	: null;
		hp 		= typeof car.hp 	!= 'undefined' ? car.hp 	: null;
		lbs 	= typeof car.lbs 	!= 'undefined' ? car.lbs 	: null;
		acc 	= typeof car.acc 	!= 'undefined' ? car.acc 	: null;
		year 	= typeof car.year 	!= 'undefined' ? car.year 	: null;
		
		pData[car.name] = [];
		pData[car.name].push(
			{name : 'cyl',  value : cyl }, 
			{name : 'dsp',  value : dsp }, 
			{name : 'lbs',  value : lbs }, 
			{name : 'hp',   value : hp  }, 
			{name : 'acc',  value : acc }, 
			{name : 'mpg',  value : mpg }, 
			{name : 'year', value : year}
		);
		
	});

	ranks['mpg' ] = percentileRank(mpgs );
	ranks['cyl' ] = percentileRank(cyls );
	ranks['dsp' ] = percentileRank(dsps );
	ranks['hp'  ] = percentileRank(hps  );
	ranks['lbs' ] = percentileRank(lbss );
	ranks['acc' ] = percentileRank(accs, true );
	ranks['year'] = percentileRank(years);

	mins['mpg' ] = Math.min.apply(null, mpgs );
	mins['cyl' ] = Math.min.apply(null, cyls );
	mins['dsp' ] = Math.min.apply(null, dsps );
	mins['hp'  ] = Math.min.apply(null, hps  );
	mins['lbs' ] = Math.min.apply(null, lbss );
	mins['acc' ] = Math.min.apply(null, accs );
	mins['year'] = Math.min.apply(null, years);

	maxs['mpg' ] = Math.max.apply(null, mpgs );
	maxs['cyl' ] = Math.max.apply(null, cyls );
	maxs['dsp' ] = Math.max.apply(null, dsps );
	maxs['hp'  ] = Math.max.apply(null, hps  );
	maxs['lbs' ] = Math.max.apply(null, lbss );
	maxs['acc' ] = Math.max.apply(null, accs );
	maxs['year'] = Math.max.apply(null, years);
	
	var colNames = ['Кольцова','Немига','пл.Мясникова','акад.Управления','г-ца Спутник',
	'пл.Казинца','з-д Транзистор'];
	/*
	for (var i_=0; i_<7; i_++){
		catsTop[i_] = 
			colNames[0]+'<br/><span style="font-weight:normal;">'+maxs['cyl']+'</span>';
	}*/
	var catsTop = [
		colNames[0]+'<br/><span style="font-weight:normal;">'+maxs['cyl']+'</span>', 
    	colNames[1]+'<br/><span style="font-weight:normal;">'+maxs['dsp']+'</span>', 
    	colNames[2]+'<br/><span style="font-weight:normal;">'+maxs['lbs']+'</span>', 
        colNames[3]+'<br/><span style="font-weight:normal;">'+maxs['hp']+'</span>', 
        colNames[4]+'<br/><span style="font-weight:normal;">'+mins['acc']+'</span>', 
        colNames[5]+'<br/><span style="font-weight:normal;">'+maxs['mpg']+'</span>', 
        colNames[6]+'<br/><span style="font-weight:normal;">'+maxs['year']+'</span>'
	]; 
	var catsBot = [
       	colNames[1]+'<br/><span style="font-weight:normal;">'+mins['cyl']+'</span>', 
        colNames[2]+'<br/><span style="font-weight:normal;">'+mins['dsp']+'</span>', 
        colNames[3]+'<br/><span style="font-weight:normal;">'+mins['lbs']+'</span>', 
        colNames[4]+'<br/><span style="font-weight:normal;">'+mins['hp']+'</span>', 
        colNames[5]+'<br/><span style="font-weight:normal;">'+maxs['acc']+'</span>', 
        colNames[6]+'<br/><span style="font-weight:normal;">'+mins['mpg']+'</span>', 
    	colNames[7]+'<br/><span style="font-weight:normal;">'+mins['year']+'</span>'
	]; 
	       	
	var carData = [];
	var i = 0;
	$.each(pData, function(car, measures) {
		carData[i] = {};
		carData[i].name = car;
		carData[i].data = [];
		var val; 
		$.each(measures, function() {
			var val = typeof ranks[this.name][this.value] != 'undefined' ? ranks[this.name][this.value] : null; 
			console.info("name[ %s ] and value [ %d ]",this.name, ranks[this.name][this.value]);
			carData[i].data.push(val);
		});
		i++;
	});    
    rData = {};
    rData.carData = carData;
    rData.catsTop = catsTop;
    rData.catsBot = catsBot;
    return rData;
}
//crude percentile ranking
function percentileRank(data, reverse=false) {
	data.sort(numSort);
	if(reverse === true) {
		data.reverse();
	}
	var len   = data.length;
	var sData = {};
	$.each(data, function(i, point) {
		sData[point] = (i / (len / 100));
	});
	return sData;
}
//because .sort() doesn't sort numbers correctly
function numSort(a,b) { 
    return a - b; 
}
