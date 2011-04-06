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
			<?php $this->script('graph/dashboard'); ?>
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
					<div class="section left">
						<h3>Graph</h3>
						<div id="graph" style="width:400px;height:340px">
							<p class='caution'>Loading Graph...</p>
						</div>
					</div>
					<div class="section aside left" style="max-height : 200px;overflow : auto;">
						<h3>Legend</h3>
						<div id="legend">
							<p class='caution'>Loading Legend...</p>
						</div>
					</div>
					<div class="section aside left">
						<h3>Options</h3>
						
					</div>
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			
			<script type="text/javascript">
				var data = [], xaxis = [], graphdata = [], legenddata = [], idx = 0;
				
				/**
				 * function graph total metrics
				 */
				function totalMetrics()
				{
					$c.ajax('GET', 'http://localhost/affero/api/metric', function(d){
						data = JSON.parse(d);
						
						console.log(data);
						
						for(i = 0; i < data.length; i++)
						{
							if(!legenddata.inArray(data[i]['slug']))
							{
								legenddata.push(data[i]['slug']);
								graphdata.push(parseInt(data[i]['qty']));
							}
							else
							{
								idx = legenddata.indexOf(data[i]['slug']);
								graphdata[idx] += parseInt(data[i]['qty']);
							}
						}
						
						$g.bar('graph', graphdata, null, 'legend', legenddata);
					});
				};
				
				function specificMetrics(slug)
				{
					$c.ajax('GET', 'http://localhost/affero/api/metric?slug='+slug, function(d){
						data = JSON.parse(d);
						
						console.log(data);
						
						for(i = 0; i < data.length; i++)
						{
							if(!legenddata.inArray(data[i]['date']))
							{
								legenddata.push(data[i]['date']);
								graphdata.push(parseInt(data[i]['qty']));
							}
							else
							{
								idx = legenddata.indexOf(data[i]['date']);
								graphdata[idx] += parseInt(data[i]['qty']);
							}
						}
						
						$g.bar('graph', graphdata, xaxis, 'legend', legenddata);
					});
				};
			</script>
		</body>
	</html>