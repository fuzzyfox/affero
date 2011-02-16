<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Login'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<div id="nav" class="right">
					<ul>
						<li><a href="#">navigation</a></li>
					</ul>
				</div>
				
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
				<p>copyleft <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">cba</a> 2010 - <a href="http://fuzzyfox.mozhunt.com/">William D</a></p>
			</div>
		</body>
	</html>