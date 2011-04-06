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
						<form action="#" id="options">
							<label for="type">graph</label>
							<select name="type" id="type">
								<option value="metric">global data</option>
								<option value="locale">locale based data</option>
							</select>
							<label for="filter">choose specific area</label>
							<select name="filter" id="filter">
								<option value="---">show all</option>
								<?php foreach($areas as $option): ?>
								<?php $option = $option->areaSlug; ?>
								<option value="<?php echo $option; ?>"><?php echo $option; ?></option>
								<?php endforeach; ?>
							</select>
						</form>
					</div>
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			
			<script type="text/javascript">
				/* Deal with graph */
				var data = [], xaxis = [], graphdata = [], legenddata = [], idx = 0;
				
				/**
				 * function graph total metrics
				 */
				function totalMetric()
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
				
				function specificMetric(slug)
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
						
						$g.bar('graph', graphdata, [slug], 'legend', legenddata);
					});
				};
				
				function totalLocale()
				{
					$c.ajax('GET', 'http://localhost/affero/api/locale', function(d){
						data = JSON.parse(d);
						
						console.log(data);
						
						locales = [], areas = [];
						for(i = 0; i < data.length; i++)
						{
							//count locales
							if(!locales.inArray(data[i]['locale']))
							{
								locales.push(data[i]['locale']);
							}
							
							//count areas
							if(!areas.inArray(data[i]['slug']))
							{
								areas.push(data[i]['slug']);
							}
						}
						
						//setup data sets
						graphdata = [];
						for(i = 0; i < areas.length; i++)
						{
							graphdata[i] = [];
							for(j = 0; j < locales.length; j++)
							{
								graphdata[i].push(0);
							}
						}
						
						//construct data sets
						for(i = 0; i < data.length; i++)
						{
							y = locales.indexOf(data[i]['locale']);
							x = areas.indexOf(data[i]['slug']);
							graphdata[x][y] = data[i]['localeQty'];
						}
						
						console.log(graphdata);
						
						$g.bar('graph', graphdata, locales, 'legend', areas);
					});
				};
				
				function specificLocale(slug)
				{
					$c.ajax('GET', 'http://localhost/affero/api/locale?slug='+slug, function(d){
						data = JSON.parse(d);
						
						console.log(data);
						
						locales = [], dates = [];
						for(i = 0; i < data.length; i++)
						{
							//count locales
							if(!locales.inArray(data[i]['locale']))
							{
								locales.push(data[i]['locale']);
							}
							
							//count areas
							if(!dates.inArray(data[i]['date']))
							{
								dates.push(data[i]['date']);
							}
						}
						
						//setup data sets
						graphdata = [];
						for(i = 0; i < dates.length; i++)
						{
							graphdata[i] = [];
							for(j = 0; j < locales.length; j++)
							{
								graphdata[i].push(0);
							}
						}
						
						//construct data sets
						for(i = 0; i < data.length; i++)
						{
							y = locales.indexOf(data[i]['locale']);
							x = dates.indexOf(data[i]['date']);
							graphdata[x][y] = data[i]['localeQty'];
						}
						
						console.log(graphdata);
						
						$g.bar('graph', graphdata, locales, 'legend', dates);
					});
				};
				
				//set initial graph
				totalMetric();
				
				/* Deal with getting options */
				$c.addevent(document.getElementById('options'), 'change', function(){
					//clear graph and legend
					document.getElementById('graph').innerHTML = '';
					document.getElementById('legend').innerHTML = '';
					
					//reset variables
					data = [];
					xaxis = [];
					graphdata = [];
					legenddata = [];
					idx = 0;
					
					switch(document.getElementById('type').value)
					{
						case 'metric':
							if(document.getElementById('filter').value == '---')
							{
								totalMetric();
							}
							else
							{
								console.log(document.getElementById('filter').value);
								specificMetric(document.getElementById('filter').value);
							}
						break;
						case 'locale':
							if(document.getElementById('filter').value == '---')
							{
								totalLocale();
							}
							else
							{
								specificLocale(document.getElementById('filter').value);
							}
						break;
					}
				});
				
			</script>
		</body>
	</html>