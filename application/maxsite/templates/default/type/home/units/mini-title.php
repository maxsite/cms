<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

/*
* миниатюры с заголовками

[unit]
file = mini-title.php
[/unit]

*/
 
 
 
# используем кэширование
$home_cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
$home_cache_key = getinfo('template') . '-' .  __FILE__ . '-' . mso_current_paged();

if ($home_cache_time > 0 and $k = mso_get_cache($home_cache_key) ) echo $k; // да есть в кэше
else
{
	ob_start();
	
	$b = new Block_pages( array (
			'limit' => 5,
			'pagination' => false,
		));
	
	if ($b->go)	
	{
		$b->output(	array (
			'block_start' => '<div class="home-mini-title">',
			'block_end' => '</div>',
			'columns' => 5,
			'columns_class_cell' => 'col w1-5',
			'content' => false,
			'thumb_width' => 100,
			'thumb_height' => 100,
			'thumb_class' => '',
			'placehold' => true,
			'placehold_path' => getinfo('template_url') . 'images/placehold/', // путь к плейсхолдеру
			'line3' => '',
			'title_start' => '<p>',
			'title_end' => '</p>',
		));
	}
	
	mso_add_cache($home_cache_key, ob_get_flush(), $home_cache_time * 60);
}

# end file