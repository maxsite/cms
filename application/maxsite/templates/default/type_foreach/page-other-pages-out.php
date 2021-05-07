<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *  * MaxSite CMS
 * (c) https://max-3000.com/
 * вывод миниатюр Еще записи по теме
 */

echo '<div class="hide-print mso-page-other-pages clearfix"><div class="t130 im-info-circle mar20-b">' . mso_get_option('page_other_pages', 'templates', tf('Еще записи по теме')) . '</div>';

$w = mso_get_val('thumb-width1', 640); //ширина
$h = mso_get_val('thumb-height1', 480); //высота

echo '<div class="flex flex-wrap">';

foreach ($bl_pages as $pm) {
	$url = mso_page_meta('image_for_page', $pm['page_meta'], '', '', '', false);

	if ($img = thumb_generate($url, $w, $h, mso_holder($w, $h, false, '#CCCCCC'))) $url = $img;

	$href_page = getinfo('siteurl') . 'page/' . $pm['page_slug'];

	echo '<div class="w31 w49-tablet mar30-b links-no-color t-center lh120">';
	echo '<a class="my-hover-img" href="' . $href_page . '" title="' . htmlspecialchars($pm['page_title']) . '"><img class="w100" src="' . $url . '" alt="' . htmlspecialchars($pm['page_title']) . '"><div></div></a><div class="mar5-t t90"><a href="' . $href_page . '">' . $pm['page_title'] . '</a></div>';
	echo '</div>';
}

echo '</div></div>';

# end of file
