<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
* две колонки/рубрики

[unit]
file = 2col-cats.php
cats = 1,3
limit = 5
[/unit]

*/

# используем кэширование
$home_cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
$home_cache_key = getinfo('template') . '-' .  __FILE__ . '-' . mso_current_paged();

if ($home_cache_time > 0 and $k = mso_get_cache($home_cache_key) ) echo $k; // да есть в кэше
else
{
	ob_start();
	
	// pr($UNIT); // все данные юнита
	
	// дефолтные рубрики (ID)
	$cat1 = 1;
	$cat2 = 2;
	
	if (isset($UNIT['cats']))
	{
		$cats = mso_explode($UNIT['cats']);
		if(isset($cats[0])) $cat1 = $cats[0];
		if(isset($cats[1])) $cat2 = $cats[1];
	}
	
	// кол-во записей в одной колонке
	$limit = 3;
	
	if (isset($UNIT['limit']))
	{
		$limit = $UNIT['limit'];
	}
	
	
	
	echo '<div class="onerow">';
	
		// первая колонка
		
		// данные рубрики
		$cat = mso_get_cat_from_id($cat1);
		
		$b = new block_pages( array (
				'limit' => $limit,
				'pagination' => false,
				'cat_id' => $cat1,
			));
			
		$b->output(	array (
				'block_start' => '<div class="col w1-2"><h2>' . $cat['category_name'] . '</h2>',
				'block_end' => '<p class="all-cat"><a href="' . getinfo('siteurl') . 'category/' . $cat['category_slug'] . '">Посмотреть все записи</a></p></div>',
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
		
		// вторая колонка
		$cat = mso_get_cat_from_id($cat2);
		
		$b = new block_pages( array (
				'limit' => $limit,
				'pagination' => false,
				'cat_id' => $cat2,
			));
			
		$b->output(	array (
				'block_start' => '<div class="col w1-2"><h2>' . $cat['category_name'] . '</h2>',
				'block_end' => '<p class="all-cat"><a href="' . getinfo('siteurl') . 'category/' . $cat['category_slug'] . '">Посмотреть все записи</a></p></div>',
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
			
	echo '</div>';
	
	
	echo '<div class="clearfix"></div>';
		
	mso_add_cache($home_cache_key, ob_get_flush(), $home_cache_time * 60);

}

# end file