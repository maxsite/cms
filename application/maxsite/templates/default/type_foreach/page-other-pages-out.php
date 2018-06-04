<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	вывод миниатюр Еще записи по теме
*/

// библиотека для создания миниатюр
require_once(getinfo('shared_dir') . 'stock/thumb/thumb.php');

echo '<div class="hide-print mso-page-other-pages clearfix"><h4><span>' . mso_get_option('page_other_pages', 'templates', tf('Еще записи по теме')) . '</span></h4>';
echo '<div class="flex flex-wrap">';

$w = 220; //ширина
$h = 150; //высота

foreach($bl_pages as $pm)
{
	$url = mso_page_meta('image_for_page', $pm['page_meta'], '', '', '', false);
	
	if ($img = thumb_generate($url, $w, $h, mso_holder($w, $h, false, '#CCCCCC'))) $url = $img;
	
	$href_page = getinfo('siteurl') . 'page/' . $pm['page_slug'];
	
	echo '<div class="w31 w49-tablet w100-phone mar30-b links-no-color">';
		echo '<a class="my-hover-img" href="' . $href_page . '" title="' . htmlspecialchars($pm['page_title']) . '"><img class="w100" src="' . $url . '" alt="' . htmlspecialchars($pm['page_title']) . '"><div></div></a><a class="" href="' .$href_page . '">' . $pm['page_title'] . '</a>';
	echo '</div>';
}

echo '</div>';
echo '</div>';


# end of file