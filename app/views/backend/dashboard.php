<!doctype html>
	<html lang="en">
		<head>
			<?php $this->head('Dashboard'); ?>
			<?php $this->script('common'); ?>
			<?php $this->script('graph/raphael'); ?>
			<?php $this->script('graph/g.raphael'); ?>
			<?php $this->script('graph/g.bar'); ?>
			<?php $this->script('graph/g.line'); ?>
			<?php $this->script('graph/g.pie'); ?>
			<?php $this->script('graph/dashboard'); ?>
		</head>
		<body>
			<div id="header">
				<h1><a href="./">Affero</a></h1>
				
				<?php $this->navigation(); ?>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				<div class="article">
					<h2>Dashboard</h2>
					<div class="section left" id="graph" style="width:400px;height:340px">
						<p class='caution'>Loading Graph...</p>
					</div>
					<div class="section aside left" id="legend">
						<p class='caution'>Loading Legend...</p>
					</div>
					<div class="section aside left"></div>
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			
			<script type="text/javascript">
				
			</script>
		</body>
	</html>