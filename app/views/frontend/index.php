<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Affero'); ?>
			
			<style type="text/css">
				input[type=checkbox] {
					display : inline;
					width : auto;
				}
			</style>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div class="article">
					<form action="#" method="post" id="conditions">
						<label for="time">Time Available <small>Tick all that apply</small></label>
						<div id="time">
							<?php foreach($timeRequirements->results as $timeRequirement): ?>
							<p><input type="checkbox" name="time[]" value="<?php echo $timeRequirement->timeRequirementID?>"> <?php echo $timeRequirement->timeRequirementShortDescription; ?></p>
							<?php endforeach; ?>
						</div>
						<label for="tags">Keywords</label>
						<p>Any skills, interests, or specific products! Just enter them bellow (comma seperated)</p>
						<input type="" name="tags" id="tags">
						<div class="controls">
							<button type="submit">go</button>
						</div>
					</form>
					<div id="recomendations"></div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			
			<script type="text/javascript">
				var checks = document.getElementById('time').getElementsByTagName('input'),
				times = [],
				query = [];
				$c.each(checks, function(key, value){
					$c.addevent(value, 'click', function(){
						if(value.getAttribute('checked') != 'checked')
						{
							value.setAttribute('checked', 'checked');
							times.push(parseInt(value.value));
						}
						else
						{
							value.removeAttribute('checked');
							var idx = times.indexOf(parseInt(value.value));
							if(idx != -1)
							{
								times.splice(idx, 1);
							}
						}
					});
				});
				
				$c.addevent(document.getElementById('conditions'), 'submit', function(e){
					//prevent form submit
					$c.stopevent(e);
					
					//empty the results section
					document.getElementById('recomendations').innerHTML = '';
					
					if(document.getElementById('tags').value != null)
					{
						query['tag'] = document.getElementById('tags').value.split(/, */).join('|');
					}
					
					if(times.length > 0)
					{
						for(var i = 0; i < times.length; i++)
						{
							$c.ajax('GET', '<?php echo $this->site_url('api/json/area?timeID='); ?>'+times[i]+((query['tag'] != '')?'&tag='+query['tag']:''), function(data){
								data = JSON.parse(data);
								if(data.length > 0)
								{
									$c.each(data, function(key, value){
										var tags = [];
										$c.each(value.tag, function(k, v){
											tags.push(v.name);
										});
										document.getElementById('recomendations').innerHTML += '<div id="'+value.slug+'" class="area"><h2><a href="<?php echo $this->site_url('affero/out/'); ?>'+value.slug+'/'+value.url+'">'+value.name+'</a></h2><p class="time"><strong>Time Required:</strong> '+value.timeShort+'</p><div class="description">'+value.description+'</div><div class="tags"><strong>Tags: </strong>'+tags.join(', ')+'</div></div>';
									});
								}
							});
						};
					}
					else
					{
						$c.ajax('GET', '<?php echo $this->site_url('api/json/area'); ?>'+((query['tag'] != '')?'?tag='+query['tag']:''), function(data){
							data = JSON.parse(data);
							if(data.length > 0)
							{
								$c.each(data, function(key, value){
									var tags = [];
									$c.each(value.tag, function(k, v){
										tags.push(v.name);
									});
									document.getElementById('recomendations').innerHTML += '<div id="'+value.slug+'" class="area"><h2><a href="<?php echo $this->site_url('affero/out/'); ?>'+value.slug+'/'+value.url+'">'+value.name+'</a></h2><p class="time"><strong>Time Required:</strong> '+value.timeShort+'</p><div class="description">'+value.description+'</div><div class="tags"><strong>Tags: </strong>'+tags.join(', ')+'</div></div>';
								});
							}
						});
					}
				});
			</script>
		</body>
	</html>