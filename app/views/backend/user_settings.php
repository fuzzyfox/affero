<!doctype html>
	<html lang="en">
		<head>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8">
			<title>User Settings</title>
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
					<h2>User Settings</h2>
					<?php if($this->input->get('invalid') == 'old'): ?>
					<p class="error">incorrect old password<br><small>required to save changes</small></p>
					<?php elseif($this->input->get('invalid') == 'new'): ?>
					<p class="error">your new password does not match your confirmation password</p>
					<?php elseif($this->input->get('invalid') == 'email'): ?>
					<p class="error">you must enter a valid email address</p>
					<?php endif; ?>
					<form method="post" action="<?php echo$this->site_url('backend/user/settings'); ?>">
						<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
						<label for="username">Username</label>
						<input type="text" name="username" id="username" value="<?php echo $_SESSION['user']['username']; ?>" disabled="disabled">
						<label for="email">Email</label>
						<input type="text" name="email" id="email" value="<?php echo $userEmail ?>">
						<label for="newPassword">New Password</label>
						<input type="password" name="newPassword" id="newPassword">
						<label for="newPassword">Confirm New Password</label>
						<input type="password" name="confirmPassword" id="confirmPassword">
						<label for="oldPassword">Confirm Old Password</label>
						<input type="password" name="oldPassword" id="oldPassword">
						<div class="controls">
							<button type="submit">save</button>
						</div>
						<a href="<?php echo $this->site_url('backend/user/delete')?>">delete account</a>
					</form>
				</div>
			</div>
			
			<div id="footer">
				<p>copyleft <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">cba</a> 2010 - <a href="http://fuzzyfox.mozhunt.com/">William D</a></p>
			</div>
		</body>
	</html>