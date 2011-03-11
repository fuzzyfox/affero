<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Affero'); ?>
			
			<style type="text/css">
				input[type=checkbox] {
					display : none;
				}
				form td {
					width : 100px;
					height : 100px;
					margin : 10px;
					vertical-align : middle;
					text-align : center;
					border : 1px solid #ccc;
					border-radius : 3px;
					-moz-border-radius : 3px;
					-webkit-border-radius : 3px;
					background : #eee;
					background : -moz-linear-gradient(top, #fff, #eee);
					background : -webkit-gradient(linear, left top, left bottom, from(#fff), to(#eee));
					color : #82847f;
					padding : 5px 10px 5px 10px;
					font : 13px/18px "Helvetica Neue",Helvetica,Arial,Verdana,sans-serif;
					font-size : 14px;
					font-weight : bolder;
					text-shadow : #fff 1px 1px 0px;
					cursor : default;
				}
				form td:hover, form td:focus {
					background : #fff;
					background : -moz-linear-gradient(top, #eee, #fff);
					background : -webkit-gradient(linear, left top, left bottom, from(#eee), to(#fff));
				}
				form td.checked {
					background : #ccc;
					background : -moz-linear-gradient(top, #eee, #ccc);
					background : -webkit-gradient(linear, left top, left bottom, from(#eee), to(#ccc));
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
					<form action="#" method="post">
						<input type="hidden" name="token" value="#">
						<label for="time">Time Available</label>
						<table id="time">
							<tr>
								<td>
									<input type="checkbox" name="time[]" value="1">
									A Few Minutes
								</td>
								<td>
									<input type="checkbox" name="time[]" value="2">
									A Few Hours
								</td>
								<td>
									<input type="checkbox" name="time[]" value="3">
									A Few Days
								</td>
								<td>
									<input type="checkbox" name="time[]" value="4">
									A Few Weeks Or More
								</td>
							</tr>
						</table>
					</form>
					<div id="recomendations"></div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			
			<script type="text/javascript">
				var checks = document.getElementById('time').getElementsByTagName('tr')[0].getElementsByTagName('td'),
				times = {};
				$c.each(checks, function(key, value){
					$c.addevent(value, 'click', function(){
						var input = value.getElementsByTagName('input')[0];
						if(input.getAttribute('checked') != 'checked')
						{
							input.setAttribute('checked', 'checked');
							value.setAttribute('class', 'checked');
							times[] = input.value;
						}
						else
						{
							input.removeAttribute('checked');
							value.removeAttribute('class');
							for(var i = 0; i < times.length; i++)
							{
								if(times[i] = input.value)
								{
									delete times[i];
								}
							}
						}
						console.log(times);
					});
				});
			</script>
		</body>
	</html>