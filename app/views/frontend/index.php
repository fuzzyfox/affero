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
						<label for="time">Time Available</label>
						<div id="time">
							<p><input type="checkbox" name="time[]" value="1"> A Few Minutes</p>
							<p><input type="checkbox" name="time[]" value="2"> A Few Hours</p>
							<p><input type="checkbox" name="time[]" value="3"> A Few Days</p>
							<p><input type="checkbox" name="time[]" value="4"> A Few Weeks Or More</p>
						</div>
						<label for="tags">Keywords</label>
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
										document.getElementById('recomendations').innerHTML += '<div id="'+value.slug+'" class="area"><h2><a href="<?php echo $this->site_url('manage/out/'); ?>'+value.url+'">'+value.name+'</a></h2><p class="time"><strong>Time Required:</strong> '+value.timeShort+'</p><div class="description">'+value.description+'</div><div class="tags"><strong>Tags: </strong>'+tags.join(', ')+'</div></div>';
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
									document.getElementById('recomendations').innerHTML += '<div id="'+value.slug+'" class="area"><h2><a href="<?php echo $this->site_url('manage/out/'); ?>'+value.url+'">'+value.name+'</a></h2><p class="time"><strong>Time Required:</strong> '+value.timeShort+'</p><div class="description">'+value.description+'</div><div class="tags"><strong>Tags: </strong>'+tags.join(', ')+'</div></div>';
								});
							}
						});
					}
				});
			</script>
		</body>
	</html>