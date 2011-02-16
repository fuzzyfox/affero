<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Create User'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div class="article">
					<h2>Create user</h2>
					<?php if($this->input->get('invalid') == 'passwords'): ?>
					<p class="error">passwords do not match</p>
					<?php elseif($this->input->get('invalid') == 'username'): ?>
					<p class="error">username taken or not permitted</p>
					<?php elseif($this->input->get('invalid') == 'email'): ?>
					<p class="error">email is invalid</p>
					<?php endif; ?>
					<form method="post" action="<?php echo$this->site_url('user/create'); ?>">
						<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
						<input type="hidden" name="inviteToken" value="<?php echo $token ?>">
						<label for="Email">Email</label>
						<input type="text" name="email" id="email" value="<?php echo $email?>">
						<label for="username">Username</label>
						<input type="text" name="username" id="username">
						<label for="password">Password</label>
						<input type="password" name="password" id="password">
						<label for="confPassword">Confirm Password</label>
						<input type="password" name="confPassword" id="confPassword">
						<div class="controls">
							<button type="submit">create</button>
						</div>
					</form>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
		</body>
	</html>