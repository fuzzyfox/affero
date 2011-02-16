<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('User Settings'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
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
					<?php endif; ?>
					<button class="right" style="margin: -40px 30px 0 0;" type="button" onclick="location.href='<?php echo $this->site_url('manage/area/add'); ?>'">add</button>
					<div class="clear">&nbsp;</div>
				</div>
				<div class="section" id="skills">
					<?php if(count($skills) > 0): ?>
						<select size="30">
							<?php foreach($skills as $skill): ?>
							<option>
								<?php echo $skill->skillName; ?>
							</option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
					<form action="<?php echo $this->site_url('manage/tag/add'); ?>" method="post">
					
					</form>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
		</body>
	</html>