<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
$par = array( 
		'page_id' => mso_get_option('home_page_id', 'templates', '0'), // явно указаны номера записей
		'cat_id' => mso_get_option('home_cat_id', 'templates', '0'), // явно указаны рубрики для главной
		'limit' => mso_get_option('home_limit_post', 'templates', '7'), 
		'cut' => mso_get_option('more', 'templates', tf('Читать полностью »')),
		'cat_order' => 'category_id_parent', 
		'cat_order_asc' => 'asc',
		'exclude_page_id' => mso_get_val('exclude_page_id'),
	);
		
// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($f = mso_page_foreach('home-mso-get-pages')) require($f);

$pages = mso_get_pages($par, $pagination);

mso_set_val('container_class', 'type_home home_list');

if (mso_get_option('default_description_home', 'templates', '0'))
{
	mso_set_val('list_line_format', '[title] - [date] [meta_description]');
	
	// заголовок перед списком
	if ($_t = mso_get_val('home_list_header', '')) echo $_t;
	
}
if ($fn = mso_find_ts_file('type/_def_out/list/list.php')) require($fn);

if ($f = mso_page_foreach('home-do-pagination')) require($f);

mso_hook('pagination', $pagination);
	
# end file