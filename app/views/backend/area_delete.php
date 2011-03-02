<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Confirm Area Deletion'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div class="article">
					<h2>Confirm Deletion Of <small><?php echo $area->areaName; ?></small></h2>
					<?php if($this->input->get('invalid') == true): ?>
					<p class="error">invalid password</p>
					<?php elseif($this->input->get('failed')): ?>
					<p class="error">an unknown error occured meaning your account was not deleted<br><small>please try again... if the problem persists contact us and we will remove your account for you</small></p>
					<?php endif; ?>
					
					<?php foreach($area as $key => $value): ?>
					<tr><td><?php echo $key; ?></td><td><?php echo $value; ?></td></tr>
					<?php endforeach; ?>
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
				<?php echo $this->config->site->footer; ?>
			</div>
		</body>
	</html>