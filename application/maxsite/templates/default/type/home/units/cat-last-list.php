<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

  /*
* одна рубрика: последняя запись + список записей

[unit]
file = cat-last-list.php
cat = 1
limit = 5
thumb_width = 100
thumb_height = 100
content_words = 65
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
	$thumb_width = isset($UNIT['thumb_width']) ? (int) $UNIT['thumb_width'] : 100;
	$thumb_height = isset($UNIT['thumb_height']) ? (int) $UNIT['thumb_height'] : 100;
	$content_words = isset($UNIT['content_words']) ? (int) $UNIT['content_words'] : 65;
		
	echo '<div class="onerow clearfix">';
	
		// данные рубрики
		$cat = mso_get_cat_from_id($cat_id);
		
		// вначале получаем одну последнюю апись
		$b = new Block_pages( array (
				'limit' => 1,
				'pagination' => false,
				'cat_id' => $cat_id,
			));
			
		if ($b->go)	
		{	
			$b->output(	array (
				'block_start' => '<h2>' . $cat['category_name'] . '</h2>',
				'block_end' => '',
				'content_words' => $content_words,
				'thumb_width' => $thumb_width,
				'thumb_height' => $thumb_height,
				'thumb_class' => 'right',
				'line1' => '[title]',
				'line2' => '[thumb]',
				'line3' => '',
				'title_start' => '<h3>',
				'title_end' => '</h3>',
			));
		}
		
		// теперь остальные
		$b = new Block_pages( array (
				'limit' => $limit,
				'pagination' => false,
				'cat_id' => $cat_id,
			));
			
		if ($b->go)	
		{	
			$b->output(	array (
				'block_start' => '<p></p><ul>',
				'block_end' => '</ul>',
				'thumb' => false,
				'line1' => '[title]',
				'line1_start' => '<li>',
				'line1_end' => '</li>',
				'content' => false,
				'line2' => '',
				'line3' => '',
				'title_start' => '',
				'title_end' => '',
			));
		}
		
	echo '</div>';
		
	mso_add_cache($home_cache_key, ob_get_flush(), $home_cache_time * 60);

}

# end file