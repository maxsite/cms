<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$bl_title = mso_get_option('page_other_pages', 'templates', tf('Еще записи по теме'));

echo '<div class="page_other_pages"><div class="page_other_pages_header">' . $bl_title . '</div>';

$pb = new Page_out();

$pb->format('title', '', '');

foreach ($bl_pages as $bl_page)
{
	$pb->load($bl_page);
	
	if ($image_for_page = thumb_generate(
			$pb->meta_val('image_for_page'), 
			100,
			100,
			getinfo('template_url') . 'images/placehold/100x100.png'
		))
	{
		$pb->thumb = $pb->page_url(true) . $pb->img($image_for_page, 'margin5', $pb->val('page_title'), $pb->val('page_title')) . '</a> ';
	}
	
	$pb->line('[thumb]');
	
}

echo '</div>';

$pb->clearfix();

# end file