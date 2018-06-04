<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл для full-страниц (рубрики, метки, главная и т.п.)
*/

$_width = 400;
$_height = 300;

if ($thumb = thumb_generate($p->meta_val('image_for_page'), $_width, $_height))
{
	$p->thumb = '<img class="w100 w400px-max" src="' . $thumb . '" alt="' . htmlspecialchars($p->val('page_title')) . '">';
	
	$p->thumb = '<a class="my-hover-img" href="' . $p->page_url() . '">' . $p->thumb . '<div></div></a>';
}

$p->format('title', '<h1 class="t140 t-gray900 links-no-color mar0 small-caps">', '</h1>', true);

$p->format('cat', ' / ', '<span class="i-folder-open-o t-color2 t90" title="' . tf('Рубрика записи') . '">', '</span>');

$p->format('tag', ' ', '<div class="mar5-t"><span class="my-tags">', '</span></div>');

$p->format('date', 'j F Y г.', '<time datetime="[page_date_publish_iso]" class="b-inline b-right  t90">', '</time>');

$p->format('edit', '<i class="i-pencil-square" title="Edit page"></i>', '<div class="">', '</div>');


$p->div_start('flex flex-wrap-phone bor1 bor-dotted-b bor-gray400 pad30-b mar10-t');
	
	$p->div_start('flex-grow1 w400px pad5-t t-center-phone mar10-b');
		$p->line('[thumb]');
	$p->div_end('');
	
	$p->div_start('flex-grow5 pad30-l pad0-phone');
		$p->line('[title]');
		$p->content_chars(180, '', '<p class="mar10-tb">', '...</p>');
		$p->line('<div class="mar10-t clearfix">[cat][date]</div>');
		
		// $p->line('[tag]');
		// $p->line('[edit]');
	$p->div_end('');	
	
$p->div_end('');

mso_set_val('my-page-content-full', false); // отключить вывод дальнейшего текста

# end of file