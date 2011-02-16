<!doctype html>
	<html lang="en">
		<head>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8">
			<title>Create user</title>
			<link rel="stylesheet" href="http://labs.mozhunt.com/community-wizard/assets/css/reset.css" type="text/css">
			<link rel="stylesheet" href="http://labs.mozhunt.com/community-wizard/assets/css/generic.css" media="all" type="text/css">
			<link rel="stylesheet" href="http://labs.mozhunt.com/community-wizard/assets/css/dblog.css" media="all" type="text/css">
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
					<h2>Create user</h2>
					<?php if($this->input->get('invalid') == 'passwords'): ?>
					<p class="error">passwords do not match</p>
					<?php elseif($this->input->get('invalid') == 'username'): ?>
					<p class="error">username taken</p>
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
				<p>copyleft <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">cba</a> 2010 - <a href="http://fuzzyfox.mozhunt.com/">William D</a></p>
			</div>
		</body>
	</html>