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
					<div class="section left" id="graph"></div>
					<div class="section aside left"></div>
					<div class="section aside left"></div>
					<div class="clear">&nbsp;</div>
				</div>
			</div>
			
			<div id="footer">
				<?php echo $this->config->site->footer; ?>
			</div>
			
			<script type="text/javascript">
				$c.onload(function(){
					var $r = Raphael('graph'),
					data = [[12, 30, 21], [20, 14, 26], [12, 35, 24]];
					
					$r.g.text(100, 10, 'example graph');
					
					$r.g.barchart(10, 10, 380, 320, data);
				});
			</script>
		</body>
	</html>