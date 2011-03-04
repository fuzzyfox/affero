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
				<div id="tabs">
					<ul>
						<li rel="areas" class="active">Areas Of Contribution</li>
						<li rel="skills">Skills</li>
						<li rel="times">Time Requirements</li>
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
				<div class="section" id="times" style="display:none;">
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			<script type="text/javascript">
				/*
				 get the skills to add events to them for UI enhancements
				*/
				//the skills themselves
				var skills = document.getElementById('skills');
				skills = skills.getElementsByTagName('select');
				skills = skills[0];
				skills = skills.getElementsByTagName('option');
				//quick access to the key form elements
				var form = {
					name : document.getElementById('name'),
					slug : document.getElementById('slug')
				};
				//add skill events to each skill
				$c.each(skills, function(key, value){
					//add the event on click
					$c.addevent(value, 'click', function(){
						document.getElementById('name').setAttribute('value', $c.trim(this.innerHTML));
						document.getElementById('slug').setAttribute('value', this.getAttribute('rel'));
						document.getElementById('existing').setAttribute('value', this.getAttribute('rel'));
					});
				});
				
				/*
				 auto create skill slugs
				*/
				$ui.autoSlug(form.name, form.slug);
				
				/*
				 tab switching
				*/
				//get tabs
				var tabs = document.getElementById('tabs');
				tabs = tabs.getElementsByTagName('li');
				//enable tabs
				$ui.tabs(tabs);
			</script>
		</body>
	</html>