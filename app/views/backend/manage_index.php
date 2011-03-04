<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Affero Settings'); ?>
			<style type="text/css">
				#tabs {
					margin : 0 -10px -10px 0;
				}
				#tabs li {
					margin : 0;
					display : inline;
					border : 1px solid #ccc;
					border-radius : 3px;
					-moz-border-radius : 3px;
					-webkit-border-radius : 3px;
					background : #ccc;
					background : -moz-linear-gradient(top, #eee, #ccc);
					background : -webkit-gradient(linear, left top, left bottom, from(#eee), to(#ccc));
					color : #82847f;
					padding : 5px 10px 5px 10px;
					font : 13px/18px "Helvetica Neue",Helvetica,Arial,Verdana,sans-serif;
					font-size : 14px;
					font-weight : bolder;
					text-shadow : #eee 1px 1px 0px;
					height : 30px !important;
					position : relative;
				}
				#tabs li.active {
					background : #fff;
					background : -moz-linear-gradient(top, #fff, #eee);
					background : -webkit-gradient(linear, left top, left bottom, from(#fff), to(#eee));
					text-shadow : #fff 1px 1px 0px;
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
				<div id="tabs">
					<ul>
						<li rel="areas" class="active">Areas Of Contribution</li>
						<li rel="skills">Skills</li>
					</ul>
				</div>
				<div class="section" id="areas">
					<?php if(count($areas) > 0): ?>
					<ul style="height:400px;overflow-y: scroll;">
						<?php foreach($areas as $parent): ?>
						<li>
							<?php echo $parent->areaName; ?> -
							<a href="<?php echo $parent->areaURL; ?>">view</a> |
							<!--<a href="<?php echo $this->site_url('manage/area/edit/'.$parent->areaSlug); ?>">edit</a> |-->
							<a href="<?php echo $this->site_url('manage/area/delete/'.$parent->areaSlug); ?>">delete</a>
							<?php if(isset($parent->children)): ?>
							<ul>
								<?php foreach($parent->children as $child): ?>
								<li>
									<?php echo $child->areaName; ?> -
									<a href="<?php echo $child->areaURL; ?>">view</a> |
									<!--<a href="<?php echo $this->site_url('manage/area/edit/'.$child->areaSlug); ?>">edit</a> |-->
									<a href="<?php echo $this->site_url('manage/area/delete/'.$child->areaSlug); ?>">delete</a>
								</li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else: ?>
					<p>No areas of contribution yet</p>
					<?php endif; ?>
					<button class="right" style="margin: -40px 30px 0 0;" type="button" onclick="location.href='<?php echo $this->site_url('manage/area/add'); ?>'">add</button>
					<div class="clear">&nbsp;</div>
				</div>
				<div class="section" id="skills" style="display:none;">
					<select size="30" style="float:left;display:block;border:none;width:40%;height:390px;">
						<?php if(count($skills) > 0): ?>
							<?php foreach($skills as $skill): ?>
							<option rel="<?php echo $skill->skillTag; ?>">
								<?php echo $skill->skillName; ?>
							</option>
							<?php endforeach; ?>
						<?php else: ?>
							<option>No skills yet</option>
						<?php endif; ?>
					</select>
					<form style="display:block;width:50%;height:358px;float:right;" action="<?php echo $this->site_url('manage/tag'); ?>" method="post" id="skill-form">
						<input type="hidden" name="existing" id="existing" value="false">
						<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['user']['token']; ?>">
						<label for="name">Name</label>
						<input type="text" name="name" id="name">
						<label for="slug">Slug</label>
						<input type="text" name="slug" id="slug">
						<div class="controls">
							<input style="display:inline" type="radio" name="action" value="add"> add
							<input style="display:inline" type="radio" name="action" value="edit"> edit
							<input style="display:inline" type="radio" name="action" value="delete"> delete
							<button type="submit">submit</button>
						</div>
					</form>
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			<script type="text/javascript">
				var $common = (function(){
					
					var $ = {
						addevent : function(elem, type, fn, capture){
							if(typeof capture !== 'boolean')
							{ capture = false; }
							if(elem.addEventListener)
							{ elem.addEventListener(type, fn, capture); }
							else if(elem.attachEvent)
							{ elem.attachEvent('on'+type, fn); }
							else
							{ elem['on'+type]; }
							return this;
						},
						each : function(obj, fn){
							for(var key in obj)
							{
								fn(key, obj[key]);
							}
						},
						trim : function(str){
							return str.replace(/^\s*/, '').replace(/\s*$/, '');
						}
					}
					
					return $;
				})();
				
				/*
				 get the skills to add events to them for UI enhancements
				*/
				//the skills themselves
				var tags = document.getElementById('skills');
				tags = tags.getElementsByTagName('select');
				tags = tags[0];
				tags = tags.getElementsByTagName('option');
				//quick access to the key form elements
				var form = {
					name : document.getElementById('name'),
					slug : document.getElementById('slug')
				};
				//add skill events to each skill
				$common.each(tags, function(key, value){
					//add the event on click
					$common.addevent(value, 'click', function(){
						document.getElementById('name').setAttribute('value', $common.trim(this.innerHTML));
						document.getElementById('slug').setAttribute('value', this.getAttribute('rel'));
						document.getElementById('existing').setAttribute('value', this.getAttribute('rel'));
					});
				});
				
				/*
				 auto create skill slugs
				*/
				$common.addevent(form.name, 'keyup', function(){
					value = this.value;
					value = value.toLowerCase();
					value = value.replace(/\s/g, '_').replace(/[^a-z0-9_\-]/g, '');
					form.slug.setAttribute('value', value);
				});
				
				/*
				 tab switching
				*/
				var tabs = document.getElementById('tabs');
				tabs = tabs.getElementsByTagName('li');
				//toggle based on url
				var tab = window.location.href.split('#');
				if(tab[1] == 'skills')
				{
					document.getElementById('skills').setAttribute('style', 'display:block');
					document.getElementById('areas').setAttribute('style', 'display:none');
					tab = document.getElementById('tabs');
					tab = tab.getElementsByTagName('li');
					tab[1].setAttribute('class', 'active');
					tab[0].setAttribute('class', '');
				}
				//switch to areas
				$common.addevent(tabs[0], 'click', function(){
					document.getElementById('areas').setAttribute('style', 'display:block');
					this.setAttribute('class', 'active');
					document.getElementById('skills').setAttribute('style', 'display:none');
					tab = document.getElementById('tabs');
					tab = tab.getElementsByTagName('li');
					tab[1].setAttribute('class', '');
				});
				//switch to skills
				$common.addevent(tabs[1], 'click', function(){
					document.getElementById('skills').setAttribute('style', 'display:block');
					this.setAttribute('class', 'active');
					document.getElementById('areas').setAttribute('style', 'display:none');
					tab = document.getElementById('tabs');
					tab = tab.getElementsByTagName('li');	
					tab[0].setAttribute('class', '');
				});
			</script>
		</body>
	</html>