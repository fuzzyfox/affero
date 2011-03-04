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
					<?php if($this->input->get('msg') == 'invalid'): ?>
					<p class="error">invalid password</p>
					<?php elseif($this->input->get('msg') == 'failed'): ?>
					<p class="error">an unknown error occured meaning your account was not deleted<br><small>please try again... if the problem persists contact us and we will remove your account for you</small></p>
					<?php endif; ?>
					
					<?php if(!isset($area->areaParent)): ?><p class="caution">child areas will become root areas</p><?php endif; ?>
					
					<form method="post" action="<?php echo$this->site_url('manage/area/delete/'.$area->areaSlug); ?>">
						<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
						<label for="password">Password</label>
						<input type="password" name="password" id="password">
						<div class="controls">
							<button type="button" onclick="history.go(-1);">cancel</button> <button type="submit">delete</button>
						</div>
					</form>
					
					<h2>Area Details</h2>
					<table>
						<tr>
							<th>Field</th>
							<th>Value</th>
						</tr>
						<?php foreach($area as $key => $value): ?>
						<tr>
							<td><?php echo $key; ?></td>
							<td><?php echo (is_array($value))?(count($value) > 0)?implode(', ', $value):'no skills linked':$value; ?></td>
						</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
		</body>
	</html>