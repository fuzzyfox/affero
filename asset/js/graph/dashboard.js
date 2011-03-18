//init namespace
var $g = (function($c){
	var $ = {
		/**
		 * function to handle bar graphs
		 *
		 * @param canvas {string} the id for the container element of the graph
		 * @param graphdata {array} the data to plot on the graph
		 * @param xaxis {array} the values for the x-axis
		 * @param legend {string} the container id for the legend
		 * @param legenddata {array} the names of each data set
		 */
		bar : function(canvas, graphdata, xaxis, legend, legenddata){
			/*
			 declare variables
			*/
			var maxHeight = 0,
			tmp = [' '],
			r = {},
			i = 0,
			paper = {},
			bars = {};
			/*
			 deal with plotting the graph
			*/
			//clear canvas
			document.getElementById(canvas).innerHTML = '';
			//init g.raphael.js
			r = window.Raphael(canvas, 400, 340);
			
			//get max height for bars
			$c.each(graphdata, function(key, value){
				if(typeof value == 'object')
				{
					$c.each(value, function(k, v){
						//check if the height of the current bar is greater than all the others previous
						if(v > maxHeight)
						{
							maxHeight = v;
						}
					});
				}
				else if(value > maxHeight)
				{
					maxHeight = value;
				}
			});
			
			//create the y-axis
			r.g.axis(25, 320, 280, 0, maxHeight, 10, 1);
			
			//create the x-axis
			for(i = 0; i < xaxis.length; i++)
			{
				tmp.push(xaxis[i], ' ');
			}
			r.g.axis(25, 320, 339, null, null, 6, 0, tmp, '.', 0);
			
			//plot graph
			r.g.barchart(5, 20, 380, 320, graphdata).hover(function(){
				this.flag = r.g.popup(this.bar.x, this.bar.y, this.bar.value || '0').insertBefore(this);
			}, function(){
				this.flag.animate({opacity: 0}, 300, function(){this.remove();});
			});
			
			/*
			 deal with the legend
			*/
			if(typeof legenddata != 'undefined')
			{
				//empty the legend
				document.getElementById(legend).innerHTML = '';
				//get colors for legend
				bars = document.getElementById(canvas).getElementsByTagName('svg')[0].getElementsByTagName('path');
				//for each of the first set of bars lets grab the colours
				for(i = 0; i < legenddata.length; i++)
				{
					//create a container
					document.getElementById(legend).innerHTML += '<div id="bar-'+i+'"></div>';
					//start drawing the bar graph icon
					paper = Raphael('bar-'+i, 30, 30);
					//bars[i+2] to avoid using the axis for colours
					paper.path('M21.25,8.375V28h6.5V8.375H21.25zM12.25,28h6.5V4.125h-6.5V28zM3.25,28h6.5V12.625h-6.5V28z').attr({fill:bars[i+2].getAttribute('fill'), stroke:'none'});
					//append the legend data
					document.getElementById('bar-'+i).innerHTML += '<p>'+legenddata[i]+'</p>';
					//float the icon right
					document.getElementById('bar-'+i).getElementsByTagName('svg')[0].setAttribute('class', 'right');
				}
			}
		},
		
		pie : function(canvas, graphdata, legend, legenddata){
			/*
			 declare variables
			*/
			var r = {},
			i = 0,
			paper = {},
			pie = {};
			/*
			 deal with plotting the graph
			*/
			//clear canvas
			document.getElementById(canvas).innerHTML = '';
			//init g.raphael.js
			r = window.Raphael(canvas, 400, 340);
			
			if(typeof legenddata != 'undefined')
			{
				for(i = 0; i < legenddata.length; i++)
				{
					legenddata[i] += ' %%'
				}
			}
			
			//plot graph
			pie = r.g.piechart(150, 170, 100, graphdata, ((typeof legenddata != 'undefined')?{legend: legenddata, legendpos: "east"}:null));
			
			pie.hover(function () {
				this.sector.stop();
				this.sector.scale(1.1, 1.1, this.cx, this.cy);
				if (this.label) {
					this.label[0].stop();
					this.label[0].scale(1.5);
					this.label[1].attr({"font-weight": 800});
				}
			}, function () {
				this.sector.animate({scale: [1, 1, this.cx, this.cy]}, 500, "bounce");
				if (this.label) {
					this.label[0].animate({scale: 1}, 500, "bounce");
					this.label[1].attr({"font-weight": 400});
				}
			});
			
			//enpty the default legend
			document.getElementById(legend).innerHTML = '<p>See graph.</p>'
		}
	};
	
	return $;
})($c);