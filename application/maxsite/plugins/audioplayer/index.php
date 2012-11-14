<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function audioplayer_autoload($args = array())
{
	mso_hook_add( 'head', 'audioplayer_head');
	mso_hook_add( 'content', 'audioplayer_content');
}

# функции плагина
function audioplayer_head($arg = array())
{
	static $audioplayer_js = false;
	
	if (!$audioplayer_js)
		echo '	<script src="' . getinfo('plugins_url') . 'audioplayer/audio-player.js"></script>';
	
	$audioplayer_js = true;
	
	return $arg;
}

# callback функция 
function audioplayer_content_callback($matches)
{	
	$url = $matches[1];
	$id = md5($url);
	
	$out = '<p id="' . $id 
			. '" class="audioplayer"></p><script>AudioPlayer.setup("' . getinfo('plugins_url') 
			. 'audioplayer/player.swf", {width: 350}); AudioPlayer.embed("' . $id 
			. '", {soundFile: "' . $url . '"}); </script>';

	return $out;
}


# функции плагина
function audioplayer_content($text = '')
{

	/*
	<p id="audioplayer_1">Alternative content</p>
 	<script>
	AudioPlayer.setup("http://localhost/player.swf", {width: 290});
	AudioPlayer.embed("audioplayer_1", { 
	soundFile: "http://localhost/22.mp3", 
	titles: "Title", 
	artists: "Мадонна", 
	autostart: "no"
	});
	</script>
	*/
	
	$text = preg_replace_callback('~\[audio=(.*?)\]~si', 'audioplayer_content_callback', $text);

	return $text;
}

?>