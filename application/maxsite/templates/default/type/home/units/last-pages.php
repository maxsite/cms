<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * 25-11-2015
 */
/*

Универсальный юнит для вывода записей по множеству критериев

[unit]
file = last-pages.php
limit = 3
[/unit]

Значение ключей по-умолчанию см. переменную $def

*/

# используем кэширование
$home_cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
$home_cache_key = getinfo('template') . '-' .  __FILE__ . '-' . mso_current_paged() . '-' . $UNIT_NUM;

if ($home_cache_time > 0 and $k = mso_get_cache($home_cache_key) ) echo $k; // да есть в кэше
else
{
	$def = array(
		'limit' => 3,
		'cat_id' => "0",
		'page_id' => "0",
		'pagination' => false,
		'type' => 'blog',
		'order' => 'page_date_publish',
		'order_asc' => 'desc',
		'cut' => '»»»',
		'show_cut' => false,
		'date_now' => true,
		'page_id_autor' => 0,
		
		'thumb' => true,
		'thumb_width' => 100,
		'thumb_height' => 100,
		'class_thumb' => 'b-left mar15-r rounded',
		
		'content' => true,
		'content_words' => 0,
		'content_chars' => 0,
		'content_cut' => '...',
		'content_start' => '<div class="mso-page-content clearfix">',
		'content_end' => '</div>',
		
		'line1' => '[title][date]',
		'line2' => '[thumb]',
		'line3' => '',
		'line4' => '[cat]',
		'line5' => '<p class="t-right"><a href="[page_url]">Читать дальше</a></p>',
		
		'title_start' => '<h4>',
		'title_end' => '</h4>',
		
		'block_start' => '<div class="layout-center flex flex-wrap pad5-rl">',
		'block_end' => '</div>',
		
		'page_start' => '<div class="w32 w48-tablet w100-phone pad20 mar15-tb bor1px bor-solid bor-gray400 rounded">',
		'page_end' => '</div>',
		
		'date' => 'j F Y, H:i',
		'date_start' => '<p class="italic t90 i-calendar"><time datetime="[page_date_publish_iso]">',
		'date_end' => '</time></p>',
		
		'cat_start' => '<p class="i-folder t90">',
		'cat_end' => '',
		'cat_sep' => ',&NBSP;',
		
		'tag_start' => '<p class="i-tag t90">',
		'tag_end' => '</p>',
		'tag_sep' => ',&NBSP;',
		
		'author_start' => '',
		'author_end' => '',

		'read' => '»»»',
		'read_start' => '<p>',
		'read_end' => '</p>',
		
		'comments_count_start' => '',
		'comments_count_end' => '',
		
		'placehold' => true,
		'placehold_path' => 'http://placehold.it/',
		'placehold_pattern' => '[W]x[H].png',
		'placehold_file' => 'data',
		'placehold_data_bg' => '#EEEEEE',

		'pagination_start' => '',
		'pagination_end' => '',
		'exclude_page_allow' => true,
		'exclude_page_add' => true,
	);
	
	$UNIT = mso_merge_array($UNIT, $def);
	
	ob_start();
	
	$b = new Block_pages( array (
				'limit' 		=> $UNIT['limit'],
				'cat_id' 		=> $UNIT['cat_id'],
				'page_id' 		=> $UNIT['page_id'],
				'pagination'	=> $UNIT['pagination'],
				'type' 			=> $UNIT['type'],
				'order' 		=> $UNIT['order'],
				'order_asc' 	=> $UNIT['order_asc'],
				'cut' 			=> $UNIT['cut'],
				'show_cut' 		=> $UNIT['show_cut'],
				'date_now' 		=> $UNIT['date_now'],
				'page_id_autor'	=> $UNIT['page_id_autor'],
				'exclude_page_allow'	=> $UNIT['exclude_page_allow'],
			));
		
		if ($b->go)
		{
			$b->output(	array (
				'block_start' 			=> $UNIT['block_start'],
				'block_end' 			=> $UNIT['block_end'],

				'content' 				=> $UNIT['content'],
				'content_words' 		=> $UNIT['content_words'],
				'content_chars' 		=> $UNIT['content_chars'],
				'content_cut' 			=> $UNIT['content_cut'],
				'content_start' 		=> $UNIT['content_start'],
				'content_end' 			=> $UNIT['content_end'],

				'thumb' 				=> $UNIT['thumb'],
				'thumb_width' 			=> $UNIT['thumb_width'],
				'thumb_height' 			=> $UNIT['thumb_height'],
				'thumb_class' 			=> $UNIT['class_thumb'],

				'line1' 				=> $UNIT['line1'],
				'line2' 				=> $UNIT['line2'],
				'line3' 				=> $UNIT['line3'],
				'line4' 				=> $UNIT['line4'],
				'line5' 				=> $UNIT['line5'],

				'title_start' 			=> $UNIT['title_start'],
				'title_end' 			=> $UNIT['title_end'],

				'page_start' 			=> $UNIT['page_start'],
				'page_end' 				=> $UNIT['page_end'], 
				
				'date' 					=> $UNIT['date'],
				'date_start' 			=> $UNIT['date_start'],
				'date_end' 				=> $UNIT['date_end'],

				'cat_start' 			=> $UNIT['cat_start'],
				'cat_end' 				=> $UNIT['cat_end'],
				'cat_sep' 				=> $UNIT['cat_sep'],

				'tag_start' 			=> $UNIT['tag_start'],
				'tag_end' 				=> $UNIT['tag_end'],
				'tag_sep' 				=> $UNIT['tag_sep'],
				
				'author_start' 			=> $UNIT['author_start'],
				'author_end' 			=> $UNIT['author_end'],

				'read' 					=> $UNIT['read'],
				'read_start' 			=> $UNIT['read_start'],
				'read_end' 				=> $UNIT['read_end'],

				'comments_count_start' 	=> $UNIT['comments_count_start'],
				'comments_count_end' 	=> $UNIT['comments_count_start'],

				'placehold'				=> $UNIT['placehold'],
				'placehold_path' 		=> $UNIT['placehold_path'],
				'placehold_pattern' 	=> $UNIT['placehold_pattern'],
				'placehold_file' 		=> $UNIT['placehold_file'],
				'placehold_data_bg' 	=> $UNIT['placehold_data_bg'],
					
				'pagination_start' 		=> $UNIT['pagination_start'],
				'pagination_end' 		=> $UNIT['pagination_end'], 
				
				'exclude_page_add'		=> $UNIT['exclude_page_add'],
			));
		}

	mso_add_cache($home_cache_key, ob_get_flush(), $home_cache_time * 60);

}

# end of file