<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Login'); ?>
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
					<form method="post" action="<?php echo$this->site_url('user/login'); ?>">
						<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
						<label for="username">Username</label>
						<input type="text" name="username" id="username">
						<label for="password">Password</label>
						<input type="password" name="password" id="password">
						<div class="controls">
							<button type="submit">login</button>
						</div>
					</form>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
		</body>
	</html>