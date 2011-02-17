<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Affero Settings'); ?>
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
						<li rel="areas">Areas Of Contribution</li>
						<li rel="skills">Skills</li>
					</ul>
				</div>
				<div class="section" id="areas">
					<?php if(count($areas) > 0): ?>
					<ul style="height:400px;overflow-y: scroll;">
						<?php foreach($areas as $parent): ?>
						<li>
							<?php echo $parent->areaName; ?> -
							<a href="<?php echo $this->site_url('manage/area/edit/'.$parent->areaName); ?>">edit</a> |
							<a href="<?php echo $this->site_url('manage/area/delete/'.$parent->areaName); ?>">delete</a>
							<?php if(count($parent->children) > 0): ?>
							<ul>
								<?php foreach($parent->children as $child): ?>
								<li>
									<?php echo $child->areaName; ?> -
									<a href="<?php echo $this->site_url('manage/area/edit/'.$child->areaName); ?>">edit</a> |
									<a href="<?php echo $this->site_url('manage/area/delete/'.$child->areaName); ?>">delete</a>
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
				<div class="section" id="skills">
					<select size="30" style="float:left;display:block;border:none;width:40%;height:390px;">
						<?php if(count($skills) > 0): ?>
							<?php foreach($skills as $skill): ?>
							<option>
								<?php echo $skill->skillName; ?>
							</option>
							<?php endforeach; ?>
						<?php else: ?>
							<option>No skills yet</option>
						<?php endif; ?>
					</select>
					<form style="display:block;width:50%;height:358px;float:right;" action="<?php echo $this->site_url('manage/tag/add'); ?>" method="post">
						<label for="name">Name</label>
						<input type="text" name="name" id="name">
						<label for="slug">Slug</label>
						<input type="text" name="slug" id="slug">
						<div class="controls">
							<button type="submit" value="true" name="add">add</button> <button type="submit">save</button>
						</div>
					</form>
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
		</body>
	</html>