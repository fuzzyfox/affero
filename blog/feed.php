<? require_once('dblog.php') ?>
<? header("Content-type: text/xml") ?><?='<?xml version="1.0" encoding="UTF-8"?>'?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	>
	
	<channel>
		<title><?=$config->blogName?></title>
		<link>http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?></link>
		<description>Feed for <?=$config->blogName?></description>
		<pubDate><?=date('D, d M Y G:i:s')?> +0000</pubDate>
		<? foreach(dblog_loop() as $post): ?>
		<item>
			<title><?=$post->title?></title>
			<link>http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/../#'.$post->edit?></link>
			<description><![CDATA[<?=$post->html?>]]></description>
			<pubDate><?=date('D, d M Y G:i:s', $post->timestamp)?> +0000</pubDate>
		</item>
		<? endforeach ?>
	</channel>
</rss>