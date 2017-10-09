<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
$par = array( 
		'page_id' => mso_get_option('home_page_id', 'templates', '0'), 
		'cat_id' => mso_get_option('home_cat_id', 'templates', '0'),
		'exclude_cat_id' => mso_get_option('home_exclude_cat_id', 'templates', ''),
		'limit' => mso_get_option('home_limit_post', 'templates', '7'), 
		'cut' => mso_get_option('more', 'templates', tf('Читать полностью »')),
		'cat_order' => 'category_id_parent', 
		'cat_order_asc' => 'asc',
		'exclude_page_id' => mso_get_val('exclude_page_id'),
		'order' => mso_get_option('home_order', 'templates', 'page_date_publish'),
		'order_asc' => mso_get_option('home_order_asc', 'templates', 'desc'),
	);
		
// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($f = mso_page_foreach('def-mso-get-pages')) require($f);
if ($f = mso_page_foreach('home-mso-get-pages')) require($f);

$pages = mso_get_pages($par, $pagination);

mso_set_val('container_class', 'mso-type_home home_full');
mso_set_val('full_format_title_start', '<h1>');
mso_set_val('full_format_title_end', '</h1>');

if ($fn = mso_find_ts_file('type/_def_out/full/full.php')) require($fn);

if ($f = mso_page_foreach('home-do-pagination')) require($f);

mso_hook('pagination', $pagination);
	
# end of file