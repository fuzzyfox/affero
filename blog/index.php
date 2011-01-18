<? require_once('dblog.php') ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html lang="en">
		<head>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8">
			<title><?=$config->blogName?></title>
			<link rel="stylesheet" href="assets/css/reset.css" type="text/css">
			<link rel="stylesheet" href="assets/css/generic.css" media="all" type="text/css">
			<link rel="stylesheet" href="assets/css/dblog.css" media="all" type="text/css">
			<link rel="alternate" type="application/rss+xml" title="dBlog Feed" href="feed.php">
			<link href="http://fuzzyfox.github.com/Mozilla-Universe-Widget/assets/css/mozilla-universe.css" rel="stylesheet" type="text/css">
		</head>
		<body>
			
			<a href="http://www.mozilla.org/community/" class="mozillaBG" rel="mozilla-universe">&nbsp;</a>
			
			<div id="header">
				<h1><a href="./"><?=$config->blogName?></a></h1>
				
				<div id="nav" class="right">
					<ul>
						<li>Created by William Duyck</li>
					</ul>
				</div>
				
				<div class="clear">&nbsp;</div>
			</div>
			
			<div class="section">
				
				<? foreach(dblog_loop() as $post): ?>
				<div class="article">
					<h1><?=$post->title?></h1>
					<?=emoticons($post->html)?>
					<small class="right">Last Modified: <?=date("F dS, Y", $post->timestamp)?></small>
					<div class="clear">&nbsp;</div>
				</div>
				<? endforeach ?>
				
			</div>
			
			<div id="footer">
				<p>copyleft <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">cba</a> 2010 - <a href="<?=$config->authorUrl?>"><?=$config->author?></a></p>
			</div>
			
			<script src="http://fuzzyfox.github.com/Mozilla-Universe-Widget/assets/js/mozilla-universe.js" type="text/javascript"></script>
			<script type="text/javascript">
				mozillaUniverse({
					map : {
						maxWidth : 500,
						maxHeight : 500,
						defaultNode : 'mozilla'
					}
				});
			</script>
		</body>
	</html>