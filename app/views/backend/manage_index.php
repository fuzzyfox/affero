<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Affero Settings'); ?>
			<?php $this->stylesheet('ui'); ?>
			<?php $this->script('ui'); ?>
			
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div id="main-tabs" class="tabs">
					<ul>
						<li rel="areas" class="active">Areas Of Contribution</li>
						<li rel="skills">Skills</li>
						<li rel="times">Time Requirements</li>
					</ul>
				</div>
				<!-- Area Of Contribution Management -->
				<div class="section" id="areas">
					<?php include('manage/area.php'); ?>
				</div>
				
				<!-- Skill Management -->
				<div class="section" id="skills" style="display:none;">
					<?php include('manage/skill.php'); ?>
				</div>
				
				<!-- Time Requirement Management -->
				<div class="section" id="times" style="display:none;">
					<?php include('manage/time.php'); ?>
				</div>
			</div>
			
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			<script type="text/javascript">
				//quick access to the key form elements
				var form = {
					skill : {
						add : {
							name : document.getElementById('skill-add-name'),
							slug : document.getElementById('skill-add-slug')
						},
						edit : {
							name : document.getElementById('skill-edit-name'),
							slug : document.getElementById('skill-edit-slug'),
							submit : document.getElementById('skill-edit')
						},
						del : {
							name : document.getElementById('skill-delete-name'),
							submit : document.getElementById('skill-delete')
						}
					}
				};
				/*
				 get the skills to add events to them for UI enhancements
				*/
				//the skills themselves
				var skills = document.getElementById('skills');
				skills = skills.getElementsByTagName('select');
				skills = skills[0];
				//add skill events to each skill
				$c.addevent(skills, 'change', function(){
					form.skill.edit.submit.value = this.options[this.selectedIndex].value;
					form.skill.del.submit.value = this.options[this.selectedIndex].value;
					form.skill.edit.slug.value = this.options[this.selectedIndex].value;
					form.skill.edit.name.value = $c.trim(this.options[this.selectedIndex].innerHTML);
					form.skill.del.name.innerHTML = $c.trim(this.options[this.selectedIndex].innerHTML);
				});
				
				/*
				 auto create skill slugs
				*/
				$ui.autoSlug(form.skill.add.name, form.skill.add.slug);
				$ui.autoSlug(form.skill.edit.name, form.skill.edit.slug);
				
				/*
				 tab switching
				*/
				//get tabs
				var maintabs = document.getElementById('main-tabs');
				maintabs = maintabs.getElementsByTagName('li');
				var areaformtabs = document.getElementById('area-form-tabs');
				areaformtabs = areaformtabs.getElementsByTagName('li');
				var skillformtabs = document.getElementById('skill-form-tabs');
				skillformtabs = skillformtabs.getElementsByTagName('li');
				var timeformtabs = document.getElementById('time-form-tabs');
				timeformtabs = timeformtabs.getElementsByTagName('li');
				//enable tabs
				$ui.tabs(maintabs);
				$ui.tabs(areaformtabs);
				$ui.tabs(skillformtabs);
				$ui.tabs(timeformtabs);
				
				/*
				 enable area edit
				*/
				//the areas themselves
				var areas = document.getElementById('areas');
				areas = areas.getElementsByTagName('select');
				areas = areas[0];
				//add area to populate form with
				$c.addevent(areas, 'change', function(){
					$c.ajax('GET', '<?php echo $this->site_url('api/area?slug='); ?>'+this.value, function(data){
						data = JSON.parse(data);
						
						/*
						 edit area
						*/
						document.getElementById('area-edit-name').value = data[0].name;
						document.getElementById('area-edit-slug').value = data[0].slug;
						document.getElementById('area-edit').value = data[0].slug;
						document.getElementById('area-edit-url').value = data[0].url;
						
						document.getElementById('area-edit-description').innerHTML = data[0].description;
						
						//set parent
						var parent = document.getElementById('area-edit-parent');
						parent = parent.getElementsByTagName('option');
						$c.each(parent, function(key, option){
							if(option.value == data[0].parent)
							{
								option.setAttribute('selected', 'selected');
							}
							else
							{
								option.removeAttribute('selected');
							}
						});
						
						//if has no parent but has children then hide that option
						$c.ajax('GET', '<?php echo $this->site_url('api/area?parent='); ?>'+data[0].slug, function(d){
							d = JSON.parse(d);
							if(d.length > 0)
							{
								//hide select
								document.getElementById('area-edit-parent').style.display = 'none';
								//hide label
								document.getElementById('area-edit-parent').previousSibling.previousSibling.previousSibling.previousSibling.style.display = 'none';
							}
							else
							{
								//show select
								document.getElementById('area-edit-parent').style.display = 'block';
								//show label
								document.getElementById('area-edit-parent').previousSibling.previousSibling.previousSibling.previousSibling.style.display = 'block';
							}
						});
						
						//set tags
						if(typeof(data[0].tag) != 'undefined')
						{
							var tags = [];
							$c.each(data[0].tag, function(key, value){
								tags.push(value.name);
							});
							document.getElementById('area-edit-tags').value = tags.join(', ');
						}
						else
						{
							document.getElementById('area-edit-tags').value = '';
						}
						
						//set time requirement
						var time = document.getElementById('area-edit-time');
						time = time.getElementsByTagName('option');
						$c.each(time, function(key, option){
							if(option.value == data[0].timeID)
							{
								option.setAttribute('selected', 'selected');
							}
							else
							{
								option.removeAttribute('selected');
							}
						});
						
						/*
						 delete area
						*/
						document.getElementById('area-delete-name').innerHTML = data[0].name;
						document.getElementById('area-delete').value = data[0].slug;
					});
				});
				
				/*
				 time edit
				*/
				var times = document.getElementById('times');
				times = times.getElementsByTagName('select');
				times = times[0];
				//add area to populate form with
				$c.addevent(times, 'change', function(){
					$c.ajax('GET', '<?php echo $this->site_url('api/time?timeID='); ?>'+this.value, function(data){
						data = JSON.parse(data);
						document.getElementById('time-edit-short').value = $c.trim(times.options[times.selectedIndex].innerHTML);
						document.getElementById('time-edit-long').innerHTML = data[0].timeLong;
						document.getElementById('time-edit').value = times.value;
						document.getElementById('time-delete-short').innerHTML = $c.trim(times.options[times.selectedIndex].innerHTML);
						document.getElementById('time-delete').value = times.value;
					});
				});
			</script>
		</body>
	</html>