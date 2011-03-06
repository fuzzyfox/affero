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
				$ui.autoSlug(form.skill.edit.name, form.skill.edit.submit);
				$ui.autoSlug(form.skill.edit.slug, form.skill.edit.submit);
				
				/*
				 tab switching
				*/
				//get tabs
				var maintabs = document.getElementById('main-tabs');
				maintabs = maintabs.getElementsByTagName('li');
				var skillformtabs = document.getElementById('skill-form-tabs');
				skillformtabs = skillformtabs.getElementsByTagName('li');
				//enable tabs
				$ui.tabs(maintabs);
				$ui.tabs(skillformtabs);
				
				/*
				 ajax function for editing areas of contrib
				*/
				var $a = $ajax = (function(){
					$ = {
						makeRequest : function(file, fn){
							var request, response;
							if(window.XMLHttpRequest)
							{
								//IE7+/Firefox/Chrome/Opera/Safari support
								request = new XMLHttpRequest();
							}
							else
							{
								//IE6/IE5
								request = new ActiveXObject("Microsoft.XMLHTTP");
							}
							request.onstatechange = function(){
								if((request.readyState == 4)&&(request.status == 200))
								{
									fn(request.responseText);
								}
							}
							request.open('GET', file, true);
							request.send();
						},
						getJSON : function(file, fn){
							var json;
							this.makeRequest(file, function(data){
								json = JSON.parse(data);
								fn(json);
							});
						}
					};
					return $;
				})();
				
				/*
				 enable area edit
				*/
				//the skills themselves
				var areas = document.getElementById('areas');
				areas = areas.getElementsByTagName('select');
				areas = areas[0];
				//add skill events to each skill
				$c.addevent(areas, 'change', function(){
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
							data = JSON.parse(xmlhttp.responseText);
							document.getElementById('area-edit-name').value = data.area[0].areaName;
							document.getElementById('area-edit-slug').value = data.area[0].areaSlug;
							document.getElementById('area-edit-url').value = data.area[0].areaURL;
							if(typeof(data.area[0].areaDescription) != 'undefined')
							{
								document.getElementById('area-edit-description').innerHTML = data.area[0].areaDescription;
							}
						}
					  }
					xmlhttp.open("GET",'<?php echo $this->site_url('api/area?slug='); ?>'+this.value,true);
					xmlhttp.send();
				});
			</script>
		</body>
	</html>