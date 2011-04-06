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
			if((typeof xaxis != 'undefined')&&(xaxis !== null))
			{
				for(i = 0; i < xaxis.length; i++)
				{
					tmp.push(xaxis[i], ' ');
				}
			}
			else
			{
				for(i = 0; i < graphdata.length; i++)
				{
					tmp.push(' ');
				}
			}
			r.g.axis(25, 320, 350, null, null, ((xaxis !== null)?xaxis.length*2:1), 0, tmp, '.', 0);
			
			//plot graph
			r.g.barchart(25, 20, 350, 320, graphdata).hover(function(){
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
			
			//effects
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
			
			//empty the default legend
			document.getElementById(legend).innerHTML = '<p>See graph.</p>'
		},
		
		line : function(canvas, xdata, ydata, legend, legenddata){
			/*
			 declare variables
			*/
			var r = {},
			i = 0,
			paper = {},
			lines = {};
			
			/*
			 deal with drawing the graph
			*/
			//clear canvas
			document.getElementById(canvas).innerHTML = '';
			
			//init raphael.js
			r = window.Raphael(canvas, 400, 340);
			
			//plot lines
			lines = r.g.linechart(25, 10, 340, 320, xdata, ydata, {axis: '0 0 0 1', symbol: "o", smooth: true});
			
			//effects
			lines.hoverColumn(function(){
				this.tags = r.set();
				for (var i = 0, ii = this.y.length; i < ii; i++) {
					this.tags.push(r.g.tag(this.x, this.y[i], this.values[i], 160, 10).insertBefore(this).attr([{fill: "#444", stroke: false}, {fill: '#fff'}]));
				}
			}, function () {
				this.tags && this.tags.remove();
			});
			lines.symbols.attr({r: 3});
			
			/*
			 deal with legend
			*/
			if(typeof legenddata != 'undefined')
			{
				//empty the legend
				document.getElementById(legend).innerHTML = '';
				//get colors for legend
				lines = document.getElementById(canvas).getElementsByTagName('svg')[0].getElementsByTagName('path');
				//for each of the first set of bars lets grab the colours
				for(i = 0; i < legenddata.length; i++)
				{
					//create a container
					document.getElementById(legend).innerHTML += '<div id="bar-'+i+'"></div>';
					//start drawing the bar graph icon
					paper = Raphael('line-'+i, 30, 30);
					//bars[i+2] to avoid using the axis for colours
					paper.path('M3.625,25.062c-0.539-0.115-0.885-0.646-0.77-1.187l0,0L6.51,6.584l2.267,9.259l1.923-5.188l3.581,3.741l3.883-13.103l2.934,11.734l1.96-1.509l5.271,11.74c0.226,0.504,0,1.095-0.505,1.321l0,0c-0.505,0.227-1.096,0-1.322-0.504l0,0l-4.23-9.428l-2.374,1.826l-1.896-7.596l-2.783,9.393l-3.754-3.924L8.386,22.66l-1.731-7.083l-1.843,8.711c-0.101,0.472-0.515,0.794-0.979,0.794l0,0C3.765,25.083,3.695,25.076,3.625,25.062L3.625,25.062z').attr({fill:lines[i+1].getAttribute('stroke'), stroke:'none'});
					//append the legend data
					document.getElementById('line-'+i).innerHTML += '<p>'+legenddata[i]+'</p>';
					//float the icon right
					document.getElementById('line-'+i).getElementsByTagName('svg')[0].setAttribute('class', 'right');
				}
			}
		}
	};
	
	return $;
})($c);