<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

/*
* миниатюры с заголовками указанной рубрики

[unit]
file = cat-title-mini.php
cat = 1
cols = 4
limit = 4
width = 100
height = 100
[/unit]

*/

# используем кэширование
$home_cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
$home_cache_key = getinfo('template') . '-' .  __FILE__ . '-' . mso_current_paged();

if ($home_cache_time > 0 and $k = mso_get_cache($home_cache_key) ) echo $k; // да есть в кэше
else
{
	ob_start();
	
	$cat_id = isset($UNIT['cat']) ? (int) $UNIT['cat'] : 1;
	$cols = isset($UNIT['cols']) ? (int) $UNIT['cols'] : 4; // 4 колонки
	$limit = isset($UNIT['limit']) ? (int) $UNIT['limit'] : 4; // всего записей
	$width = isset($UNIT['width']) ? (int) $UNIT['width'] : 100; // ширина миниатюры
	$height = isset($UNIT['height']) ? (int) $UNIT['height'] : 100; // высота миниатюры
	
	// данные рубрики
	$cat = mso_get_cat_from_id($cat_id);
		
	$b = new Block_pages( array (
			'limit' => $limit,
			'cat_id' => $cat_id,
			'pagination' => false,
		));
	
	if ($b->go)	
	{
		$b->output(	array (
			'block_start' => '<div class="home-cat-title-mini"><h2><a href="' . mso_page_url($cat['category_slug'], 'category') . '">' . $cat['category_name'] . '</a></h2>',
			'block_end' => '</div>',
			'columns' => $cols,
			'columns_class_cell' => 'col w1-' . $cols,
			'content' => false,
			'thumb_width' => $width,
			'thumb_height' => $height,
			'thumb_class' => '',
			'placehold' => true,
			
			'line1' => '[thumb]',
			'line2' => '[title]',
			'line3' => '',
			
			'title_start' => '<p>',
			'title_end' => '</p>',
		));
	}	
	
	
	mso_add_cache($home_cache_key, ob_get_flush(), $home_cache_time * 60);
}

# end file