<!doctype html>
	<html lang="en">
		<head>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8">
			<title>Confirm Account Deletion</title>
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
					<h2>Confirm Account Deletion</h2>
					<?php if($this->input->get('invalid') == true): ?>
					<p class="error">invalid password</p>
					<?php elseif($this->input->get('failed')): ?>
					<p class="error">an unknown error occured meaning your account was not deleted<br><small>please try again... if the problem persists contact us and we will remove your account for you</small></p>
					<?php endif; ?>
					<form method="post" action="<?php echo$this->site_url('user/delete'); ?>">
						<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
						<label for="password">Password</label>
						<input type="password" name="password" id="password">
						<div class="controls">
							<button type="button" onclick="history.go(-1);">cancel</button> <button type="submit">delete</button>
						</div>
					</form>
				</div>
			</div>
			
			<div id="footer">
				<p>copyleft <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">cba</a> 2010 - <a href="http://fuzzyfox.mozhunt.com/">William D</a></p>
			</div>
		</body>
	</html>