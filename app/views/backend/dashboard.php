<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Dashboard'); ?>
			<?php $this->script('common'); ?>
			<?php $this->script('graph/raphael'); ?>
			<?php $this->script('graph/g.raphael'); ?>
			<?php $this->script('graph/g.bar'); ?>
			<?php $this->script('graph/g.line'); ?>
			<?php $this->script('graph/g.pie'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div class="article">
					<h2>Dashboard</h2>
					<div class="section left" id="graph" style="width:400px;height:340px">
						<h2>Graph</h2>
					</div>
					<div class="section aside left" id="legend"></div>
					<div class="section aside left"></div>
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			
			<script type="text/javascript">
			$c.onload(function(){
					var data = [[10, 20, 25], [15, 25, 30], [20, 30, 35], [35, 35, 35]],
					chart = function(container, data)
					{
						//clear previous chart
						document.getElementById(container).innerHTML = '';
						document.getElementById('legend').innerHTML = '';
						//create new chart
						window.$r = Raphael(container, 380, 320);
						//plot chart
						$r.g.barchart(0, 0, 380, 320, data);
						//plot axis
						$r.g.axis(20, 300, 280, 0, 35, 7, 1);
						$r.g.axis(20, 300, 339, null, null, 6, 0, [' ', 'past', ' ', 'present', ' ', 'future', ' '], '.', 0);
						
						//get colors for legend
						var bars = document.getElementById(container).getElementsByTagName('svg')[0].getElementsByTagName('path');
						for(var i = 0; i < data.length; i++)
						{
							document.getElementById('legend').innerHTML += '<div id="bar-'+i+'"></div>';
							var paper = Raphael('bar-'+i, 30, 30);
							paper.path('M21.25,8.375V28h6.5V8.375H21.25zM12.25,28h6.5V4.125h-6.5V28zM3.25,28h6.5V12.625h-6.5V28z').attr({fill:bars[i].getAttribute('fill'), stroke:'none'});
							document.getElementById('bar-'+i).innerHTML += '<p>Data Set '+i+'</p>';
							document.getElementById('bar-'+i).getElementsByTagName('svg')[0].setAttribute('class', 'right');
						}
					};
					chart('graph', data);
			});
			</script>
		</body>
	</html>