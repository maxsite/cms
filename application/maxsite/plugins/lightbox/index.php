<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://max-3000.com/
 */


# функция автоподключения плагина
function lightbox_autoload($args = array())
{
	mso_hook_add( 'head', 'lightbox_head');
	mso_hook_add( 'admin_head', 'lightbox_head');
	mso_hook_add( 'content_out', 'lightbox_content'); # хук на вывод контента после обработки всех тэгов
}

function lightbox_head($args = array()) 
{
	echo mso_load_jquery();
	
	$url = getinfo('plugins_url') . 'lightbox/';
	
	$t_izob = t('Изображение');
	$t_iz = t('из');
	
	// http://leandrovieira.com/projects/jquery/lightbox/
	echo <<<EOF
	
	<script src="{$url}js/jquery.lightbox.js"></script>
	<script>
		$(function(){
			lburl = '{$url}images/';
			$('div.gallery a').lightBox({
				imageLoading: lburl+'lightbox-ico-loading.gif',
				imageBtnClose: lburl+'lightbox-btn-close.gif',
				imageBtnPrev: lburl+'lightbox-btn-prev.gif',
				imageBtnNext: lburl+'lightbox-btn-next.gif',
				imageBlank: lburl+'lightbox-blank.gif',
				txtImage: '{$t_izob}',
				txtOf: '{$t_iz}',
			});
			
			$('a.lightbox').lightBox({
				imageLoading: lburl+'lightbox-ico-loading.gif',
				imageBtnClose: lburl+'lightbox-btn-close.gif',
				imageBtnPrev: lburl+'lightbox-btn-prev.gif',
				imageBtnNext: lburl+'lightbox-btn-next.gif',
				imageBlank: lburl+'lightbox-blank.gif',
				txtImage: '{$t_izob}',
				txtOf: '{$t_iz}',
			});
		});
	</script>
	<link rel="stylesheet" href="{$url}css/jquery.lightbox-0.5.css">
	
EOF;

}

function lightbox_content($text = '')
{
	$url = getinfo('plugins_url') . 'lightbox/images/';
	
	$preg = array(
	
		// удалим раставленные абзацы
		'~<p>\[gal=(.*?)\[\/gal\]</p>~si' => '[gal=$1[/gal]',
		'~<p>\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
		'~<p>\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		
		'~<p>\[gallery(.*?)\](\s)*~si' => '[gallery$1]',
		'~\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		
		'~\[gallery=(.*?)\](.*?)\[\/gallery\]~si' => '<div class="gallery$1">$2</div><script>\$(function() { lburl = \'' . $url . '\'; \$(\'div.gallery$1 a\').lightBox({imageLoading: lburl+\'lightbox-ico-loading.gif\', imageBtnClose: lburl+\'lightbox-btn-close.gif\', imageBtnPrev: lburl+\'lightbox-btn-prev.gif\', imageBtnNext: lburl+\'lightbox-btn-next.gif\'});});</script>
		',
		
		'~\[gallery\](.*?)\[\/gallery\]~si' => '<div class="gallery">$1</div>',
		
		'~\[gal=(.[^\s]*?) (.*?)\](.*?)\[\/gal\]~si' => '<a href="$3" title="$2"><img src="$1" alt="$2"></a>',
		
		'~\[gal=(.*?)\](.*?)\[\/gal\]~si' => '<a href="$2"><img src="$1" alt=""></a>',
		
		'~\[image\](.*?)\[\/image\]~si' => '<a href="$1" class="lightbox"><img src="$1" alt=""></a>',
	
		'~\[image=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox" title="$2"><img src="$1" alt="$2"></a>',
		
		'~\[image=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$1" alt=""></a>',
		
		# [image(left)=http://localhost/uploads/mini/2008-07-11-19-50-56.jpg Картинка]http://localhost/uploads/2008-07-11-19-50-56.jpg[/image]
		'~\[image\((.[^\s]*?)\)=(.[^\s]*?) (.*?)\](.*?)\[\/image\]~si' => '<a href="$4" class="lightbox" title="$3"><img src="$2" alt="$3" class="$1"></a>',
		
		# [image(left)=http://localhost/uploads/mini/2008-07-11-19-50-56.jpg]http://localhost/uploads/2008-07-11-19-50-56.jpg[/image]
		'~\[image\((.[^ ]*?)\)=(.[^ ]*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox"><img src="$2" alt="" class="$1"></a>',
		
		# [image(right)]http://localhost/uploads/2008-07-11-19-50-56.jpg[/image]
		'~\[image\((.[^ ]*?)\)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$2" alt="" class="$1"></a>',

		
	
	
		'~\[galname\](.*?)\[\/galname\]~si' => '<div>$1</div>',
	);

	return preg_replace(array_keys($preg), array_values($preg), $text);
}

# end file