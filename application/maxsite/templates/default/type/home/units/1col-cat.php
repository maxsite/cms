<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
 
* одна колонка для указанной рубрики

[unit]
file = 1col-cat.php
cat = 1
limit = 5
[/unit]

*/

# используем кэширование
$home_cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
$home_cache_key = getinfo('template') . '-' .  __FILE__ . '-' . mso_current_paged();

if ($home_cache_time > 0 and $k = mso_get_cache($home_cache_key) ) echo $k;
else
{
	ob_start();
	
	// pr($UNIT); // все данные юнита
	
	$cat_id = isset($UNIT['cat']) ? (int) $UNIT['cat'] : 1;
	$limit = isset($UNIT['limit']) ? (int) $UNIT['limit'] : 3;
		
	echo '<div class="onerow clearfix">';
	
		// данные рубрики
		$cat = mso_get_cat_from_id($cat_id);
		
		$b = new Block_pages( array (
				'limit' => $limit,
				'pagination' => false,
				'cat_id' => $cat_id,
			));
		
		if ($b->go)	
		{	
			$b->output(	array (
				'block_start' => '<div class="col w1-1"><h2>' . $cat['category_name'] . '</h2>',
				'block_end' => '<p class="all-cat"><a href="' . mso_page_url($cat['category_slug'], 'category') . '">Посмотреть все записи</a></p></div>',
				'content_words' => 20,
				'thumb_width' => 100,
				'thumb_height' => 100,
				'thumb_class' => 'left',
				'line1' => '[title]',
				'line2' => '[thumb]',
				'line3' => '',
				'title_start' => '<h4>',
				'title_end' => '</h4>',
			));
		}
			
	echo '</div>';
		
	mso_add_cache($home_cache_key, ob_get_flush(), $home_cache_time * 60);

}

# end file