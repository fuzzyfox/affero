<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Add Area Of Contribution'); ?>
			<?php $this->script('ui'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div class="article">
					<h2>Add an area of contribution</h2>
					<?php if($this->input->get('invalid') == 'missing'): ?>
					<p class="error">not all required fields were submitted</p>
					<?php elseif($this->input->get('invalid') == 'slug'): ?>
					<p class="error">slug already in use</p>
					<?php endif; ?>
					<form method="post" action="<?php echo$this->site_url('manage/area/add'); ?>">
						<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
						<label for="name">Name</label>
						<input type="text" name="name" id="name">
						<label for="slug">Slug</label>
						<input type="text" name="slug" id="slug">
						<label for="url">URL</label>
						<input type="text" name="url" id="url">
						<label for="description">Description</label>
						<textarea rows="10" cols="40" name="description" id="desciption"></textarea>
						
						<label for="parent">Parent</label>
						<!-- select parent -->
						<select name="parent" id="parent">
							<option value="root">No Parent</option>
							<?php if($parents->num_rows > 0): foreach($parents->results as $parent): ?>
							<option value="<?php echo $parent->areaSlug; ?>"><?php echo $parent->areaName; ?></option>
							<?php endforeach; endif;?>
						</select>
						
						<label for="tags">Tags</label>
						<input type="text" name="tags" id="tags">
						
						<label for="time">Minimum Time Requirement</label>
						<!-- select min time requirement -->
						<select name="time" id="time">
							<option value="null">---</option>
							<?php if($timeRequirements->num_rows > 0): foreach($timeRequirements->results as $timeRequirement): ?>
							<option value="<?php echo $timeRequirement->timeRequirementID; ?>"><?php echo $timeRequirement->timeRequirementShortDescription; ?></option>
							<?php endforeach; endif;?>
						</select>
						
						<div class="controls">
							<button type="button" onclick="history.go(-1)">cancel</button> <button type="submit" name="submit" value="true">save</button>
						</div>
					</form>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			<script type="text/javascript">
				$ui.autoSlug(document.getElementById('name'), document.getElementById('slug'))
			</script>
		</body>
	</html>