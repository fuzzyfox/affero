<?php
	
	//set the configuration
	$config = (object)array(
		'blogName' => 'affero',
		'author' => 'william d',
		'authorUrl' => 'http://www.wduyck.com/'
	);
	
	//get the needed libraries
	require_once('markdown.php');
	
	//create the loop to return objects of the posts
	function dblog_loop()
	{
		$posts = array();
		
		foreach(glob('posts/*.post.md') as $post)
		{
			$posts[filemtime($post)] = (object)array(
				'timestamp' => filemtime($post),
				'filename' => $post,
				'title' => str_replace('_', ' ', substr($post, 6, -8)),
				'markdown' => str_replace("\r\n", '<br>', file_get_contents($post)),
				'html' => Markdown(file_get_contents($post))
			);
		}
		
		krsort($posts);
		
		if(count($posts) == 0)
		{
			$post = 'readme.md';
			
			$posts[fileCreateTime($post)] = (object)array(
				'timestamp' => filemtime($post),
				'filename' => $post,
				'edit' => false,
				'title' => str_replace('_', ' ', substr($post, 6, -19)),
				'markdown' => str_replace("\r\n", '<br>', file_get_contents($post)),
				'html' => Markdown(file_get_contents($post))
			);
		}
		
		return $posts;
	}
	
	function emoticons($input)
	{
		$emoticons = array(
			'(:))' => '<img src="./assets/img/emoticon_smile.png" alt="(:))">',
			'(:P)' => '<img src="./assets/img/emoticon_tongue.png" alt="(:P)">',
			'(}:D)' => '<img src="./assets/img/emoticon_evilgrin.png" alt="(>:))">',
			'(:D)' => '<img src="./assets/img/emoticon_grin.png" alt="(:D)">',
			'(:o)' => '<img src="./assets/img/emoticon_surprised.png" alt="(:o)">',
			'(:()' => '<img src="./assets/img/emoticon_unhappy.png" alt="(:()">',
			'(:3)' => '<img src="./assets/img/emoticon_waii.png" alt="(:3)">',
			'(;D)' => '<img src="./assets/img/emoticon_wink.png" alt="(;D)">',
			'(XD)' => '<img src="./assets/img/emoticon_happy.png" alt="(XD)">'
		);
		
		foreach($emoticons as $search => $replace)
		{
			$input = str_replace($search, $replace, $input);
		}
		
		return $input;
	}
	
?>
