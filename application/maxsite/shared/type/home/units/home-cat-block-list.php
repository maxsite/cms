<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


echo NR . '<div class="type type_home_cat_block">' . NR;


if ($f = mso_page_foreach('home-cat-block-text-do')) require($f); 

// нужно считать каждую указанную рубрику и в цикле
// получить для неё все данные и сразу вывести

// параметры для получения страниц - общие для всех
$par = array( 
			'limit' => mso_get_option('home_limit_post', 'templates', '7'), 
			'cut' => mso_get_option('more', 'templates', tf('Читать полностью »')),
			'cat_order' => 'category_id_parent', 
			'cat_order_asc' => 'asc',
			'pagination' => false,
			'exclude_page_id' => mso_get_val('exclude_page_id'),
		); 

$home_cat_block = mso_explode(mso_get_option('home_cat_id', 'templates', '0')); // в массив

// все блоки можно закэшировать на 15 минут
$key_home_cache = serialize($home_cat_block);

if ($k = mso_get_cache($key_home_cache) ) 
	print($k); // да есть в кэше
else
{
	ob_start();

	if ($home_cat_block) // есть рубрики
	{
		
		// перебираем рубрики
		foreach($home_cat_block as $cat_id)
		{
			if ($f = mso_page_foreach('home-cat-block')) 
			{
				require($f); // подключаем кастомный вывод
				continue; // следующая итерация
			}
		
			$par['cat_id'] = $cat_id;
			
			// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
			if ($f = mso_page_foreach('home-cat-block-mso-get-pages')) require($f);
			
			$pages = mso_get_pages($par, $temp); 

			if ($pages)
			{ 	
				$cat_info = mso_get_cat_from_id($cat_id); // все данные рубрики
				
				// название рубрики и ссылка
				echo '<div class="header_home_cat">'
					. '<a href="' . getinfo('site_url') . 'category/' 
					. $cat_info['category_slug'] . '">' 
					. htmlspecialchars($cat_info['category_name']) . '</a>' 
					. '</div>';
				
				// выводить описание рубрики
				if (mso_get_option('default_description_home_cat', 'templates', '0') 
					and $cat_info['category_desc'])
				{
					echo '<div class="description-cat">'
						. $cat_info['category_desc']
						. '</div>';
				}
				
				if ($f = mso_page_foreach('home-cat-block-out-pages-do')) require($f); 	
				
				mso_set_val('container_class', 'type_home type_home_cat_block type_home_cat_block_list');
				
				if (mso_get_option('default_description_home', 'templates', '0'))
					mso_set_val('list_line_format', '[title] - [date] [meta_description]');
				
				if ($fn = mso_find_ts_file('type/_def_out/list/list.php')) require($fn);
	
			} // endif $pages
			
		} // end foreach $home_cat_block
	}
	
	mso_add_cache($key_home_cache, ob_get_flush(), 900);
	
} // if $k

if ($f = mso_page_foreach('home-cat-block-posle')) require($f);

echo NR . '</div><!-- class="type type_home_cat_block" -->' . NR;

# end file