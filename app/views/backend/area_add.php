<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Add Area Of Contribution'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div class="article">
					<h2>Login</h2>
					<?php if($this->input->get('invalid') == true): ?>
					<p class="error">invalid login credentials</p>
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
						<textarea rows="10" cols="40"></textarea>
						
						<label for="parent">Parent</label>
						<!-- select parent -->
						
						<label for="tags">Tags</label>
						<input type="text" name="tags" id="tags">
						
						<label for="parent">Minimum Time Requirement</label>
						<!-- select min time requirement -->
						
						<div class="controls">
							<button type="button" onclick="history.go(-1)">cancel</button> <button type="submit">save</button>
						</div>
					</form>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
		</body>
	</html>