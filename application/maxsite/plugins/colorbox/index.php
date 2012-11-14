<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function colorbox_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'colorbox_admin_init');
	mso_hook_add( 'head', 'colorbox_head');
	mso_hook_add( 'admin_head', 'colorbox_head');
	mso_hook_add( 'content_out', 'colorbox_content');
}

# функция выполняется при активации (вкл) плагина
function colorbox_activate($args = array())
{	
	mso_create_allow('colorbox_edit', 'Админ-доступ к настройкам плагина ColorBox');
	return $args;
}

function colorbox_uninstall($args = array())
{	
	mso_delete_option('plugin_colorbox', 'plugins' );
	return $args;
}

function colorbox_admin_init($args = array()) 
{
	if ( !mso_check_allow('colorbox_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'plugin_colorbox';
	mso_admin_menu_add('plugins', $this_plugin_url, 'ColorBox');
	mso_admin_url_hook($this_plugin_url, 'colorbox_admin_page');
	return $args;
}

function colorbox_admin_page($args = array()) 
{
	if ( !mso_check_allow('colorbox_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic('mso_admin_header', ' return $args."ColorBox"; ' );
	mso_hook_add_dinamic('admin_title', ' return "ColorBox - ".$args; ' );
	require(getinfo('plugins_dir') . 'colorbox/admin.php');
}

function colorbox_head($args = array()) 
{
	$url = getinfo('plugins_url') . 'colorbox/';
	$options = mso_get_option('plugin_colorbox', 'plugins', array());

	if ( !isset($options['style']) ) $options['style'] = '1';
	if ( !isset($options['effect']) ) $options['effect'] = 'elastic';
	if ( !isset($options['size']) ) $options['size'] = '0';
	if ( !isset($options['width']) ) $options['width'] = '75%';
	if ( !isset($options['height']) ) $options['height'] = '75%';
	if ( !isset($options['slideshowspeed']) ) $options['slideshowspeed'] = '2500';
	
	echo '<link rel="stylesheet" href="'.$url.'style/'.$options['style'].'/colorbox.css" media="screen">';
	
	$size = '';
	if ($options['size'] == '1') $size = ',width:"'.$options['width'].'",height:"'.$options['height'].'"';
	echo '<script src="'.$url.'js/jquery.colorbox-min.js"></script>
<script>
$(document).ready(function(){
	$(".gallery,.slideshow").find("a[href$=\'.jpg\'],a[href$=\'.jpeg\'],a[href$=\'.png\'],a[href$=\'.gif\'],a[href$=\'.bmp\']").attr("rel","cb");
	$("div.gallery a[rel=cb]").colorbox({rel:"true",transition:"'.$options['effect'].'"'.$size.',photo:"true"});
	$("a.lightbox").colorbox({transition:"'.$options['effect'].'"'.$size.'});
	$("div.slideshow a[rel=cb]").colorbox({rel:"true",transition:"'.$options['effect'].'"'.$size.',slideshow:"true",slideshowSpeed:"'.$options['slideshowspeed'].'",photo:"true"});
});
</script>';

}

function colorbox_content($text = '')
{
	$preg = array(
		'~<p>\[gal=(.*?)\[\/gal\]</p>~si' => '[gal=$1[/gal]',
		'~<p>\[slide=(.*?)\[\/slide\]</p>~si' => '[slide=$1[/slide]',
		'~<p>\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
		'~<p>\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		'~<p>\[slideshow(.*?)\](\s)*</p>~si' => '[slideshow$1]',
		'~<p>\[\/slideshow\](\s)*</p>~si' => '[/slideshow]',
		
		'~<p>\[gallery(.*?)\](\s)*~si' => '[gallery$1]',
		'~\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		'~<p>\[slideshow(.*?)\](\s)*~si' => '[slideshow$1]',
		'~\[\/slideshow\](\s)*</p>~si' => '[/slideshow]',
		
		'~\[gallery\](.*?)\[\/gallery\]~si' => '<div class="gallery">$1</div>',
		'~\[slideshow\](.*?)\[\/slideshow\]~si' => '<div class="slideshow">$1</div>',
		
		'~\[gal=(.[^\s]*?) (.*?)\](.*?)\[\/gal\]~si' => '<a href="$3" title="$2"><img src="$1" alt="$2"></a>',
		'~\[slide=(.[^\s]*?) (.*?)\](.*?)\[\/slide\]~si' => '<a href="$3" title="$2"><img src="$1" alt="$2"></a>',
		
		'~\[gal=(.*?)\](.*?)\[\/gal\]~si' => '<a href="$2"><img src="$1" alt=""></a>',
		'~\[slide=(.*?)\](.*?)\[\/slide\]~si' => '<a href="$2"><img src="$1" alt=""></a>',
		
		'~\[image\](.*?)\[\/image\]~si' => '<a href="$1" class="lightbox"><img src="$1" alt=""></a>',
	
		'~\[image=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox" title="$2"><img src="$1" alt="$2"></a>',
		
		'~\[image=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$1" alt="" /></a>',
		
		'~\[image\((.[^\s]*?)\)=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$4" class="lightbox" title="$3"><img src="$2" alt="$3" class="$1"></a>',
		
		'~\[image\((.[^ ]*?)\)=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox"><img src="$2" alt="" class="$1"></a>',

		'~\[image\((.[^ ]*?)\)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$2" alt="" class="$1"></a>',
		
		'~\[galname\](.*?)\[\/galname\]~si' => '<div>$1</div>',
		'~\[slidename\](.*?)\[\/slidename\]~si' => '<div>$1</div>',
	);

	return preg_replace(array_keys($preg), array_values($preg), $text);
}

?>