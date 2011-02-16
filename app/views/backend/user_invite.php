<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Invite User'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div class="article">
					<h2>Invite user</h2>
					<?php if($this->input->get('invalid') == 'email'): ?>
					<p class="error">you must enter a valid email address</p>
					<?php elseif($this->input->get('invalid') == 'sender'): ?>
					<p class="error">you must enter your name</p>
					<?php elseif($this->input->get('success') == 'true'): ?>
					<p class="success">invite sent</p>
					<?php elseif($this->input->get('success') == 'false'): ?>
					<p class="error">failed to send invite</p>
					<?php endif; ?>
					<form method="post" action="<?php echo$this->site_url('user/invite'); ?>">
						<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
						<label for="sender">Your Name</label>
						<input type="text" name="sender" id="sender">
						<label for="receipient">Their Email</label>
						<input type="text" name="receipient" id="receipient">
						<div class="controls">
							<button type="button" onclick="history.go(-1);">cancel</button> <button type="submit">send</button>
						</div>
					</form>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
		</body>
	</html>