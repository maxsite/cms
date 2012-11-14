<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# подготовка данных

$full_posts = mso_get_option('category_full_text', 'templates', '1'); // полные или короткие записи
	
// параметры для получения страниц
$par = array( 'limit' => mso_get_option('limit_post', 'templates', '7'), 
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			'cat_order'=>'category_id_parent', 'cat_order_asc'=>'asc', 'type'=> false, 'content'=> $full_posts ); 

// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($f = mso_page_foreach('category-mso-get-pages')) require($f); 

$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации

$title_page = mso_head_meta('title', $pages, '%category_name%'); // заголовок для записи на основе титла

if ($f = mso_page_foreach('category-head-meta')) require($f);
else
{ 
	mso_head_meta('title', $pages, '%category_name%'); //  meta title страницы
	mso_head_meta('description', $pages, '%category_desc%'); // meta description страницы
	mso_head_meta('keywords', $pages, '%category_name%'); // meta keywords страницы
}

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_category">' . NR;

if ($f = mso_page_foreach('category-do')) require($f); // подключаем кастомный вывод
	else 
	{
		# выводим только если есть найденные страницы
		if ($pages) 
		{
			echo '<h1 class="category">' . $title_page . '</h1>';
		}
	}

if ($pages) // есть страницы
{ 	
	
	if ( mso_get_option('category_show_rss_text', 'templates', 1) )
	{
		if ($f = mso_page_foreach('category-show-rss-text')) 
			require($f); // подключаем кастомный вывод
		else
			echo mso_get_val('show_rss_text_start', '<h3 class="category">') . '<a href="' . getinfo('siteurl') . mso_segment(1) . '/' . mso_segment(2) . '/feed">'. tf('Подписаться на эту рубрику по RSS'). '</a>' .  mso_get_val('show_rss_text_end', '</h3>');
			
	}
	
	if ($f = mso_page_foreach('category-show-desc')) 
			require($f); // подключаем кастомный вывод
	else
	{
		if (isset($pages[0]['page_categories_detail']))
		{
			// описание рубрики
			foreach ($pages[0]['page_categories_detail'] as $_cat)
			{
				if ($_cat['category_slug'] == mso_segment(2))
				{
					if ($_cat['category_desc']) echo '<div class="category_desc">' . $_cat['category_desc'] . '</div>';
					break;
				}
			}
		}
	}
	
	
	if (!$full_posts) echo '<ul class="category">';
	
	foreach ($pages as $page) : // выводим в цикле
		
		if ($f = mso_page_foreach('category')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}

		
		extract($page);
	
		if (!$full_posts)
		{
			echo '<li>';
			
			if ($f = mso_page_foreach('category-list')) 
			{
				require($f); // подключаем кастомный вывод
			}
			else
			{
				mso_page_title($page_slug, $page_title, '', '', true);
				mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
			}
			
			echo '</li>';
		}
		else
		{
			echo NR . '<div class="page_only"><div class="wrap">' . NR;
				if ($f = mso_page_foreach('info-top')) 
				{
					require($f); // подключаем кастомный вывод
				}
				else
				{
					echo '<div class="info info-top">';
						mso_page_title($page_slug, $page_title, '<h1>', '</h1>');
						
						mso_page_date($page_date_publish, 
										array(	'format' => tf('D, j F Y г.'), // 'd/m/Y H:i:s'
												'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
												'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
										'<span>', '</span>');
						mso_page_cat_link($page_categories, ' -&gt; ', '<br><span>' . tf('Рубрика') . ':</span> ', '');
						mso_page_tag_link($page_tags, ' | ', '<br><span>' . tf('Метки') . ':</span> ', '');
						mso_page_view_count($page_view_count, '<br><span>' . tf('Просмотров') . ':</span> ', '');
						mso_page_meta('nastr', $page_meta, '<br><span>' . tf('Настроение') . ':</span> ', '');
						mso_page_meta('music', $page_meta, '<br><span>' . tf('В колонках звучит') . ':</span> ', '');
						if ($page_comment_allow) mso_page_feed($page_slug, tf('комментарии по RSS'), '<br><span>' . tf('Подписаться на').'</span> ', '', true);
						mso_page_edit_link($page_id, tf('Edit page'), '<br>[', ']');
					echo '</div>';
				}
				
				if ($f = mso_page_foreach('page-content-category')) 
				{
					require($f); // подключаем кастомный вывод
				}
				else
				{
					echo '<div class="page_content type_' . getinfo('type'). '">';
						mso_page_content($page_content);
						if ($f = mso_page_foreach('info-bottom')) require($f); // подключаем кастомный вывод
						mso_page_content_end();
						echo '<div class="break"></div>';
						
						mso_page_comments_link(array( 
							'page_comment_allow' => $page_comment_allow,
							'page_slug' => $page_slug,
							'title' => tf('Обсудить'). ' (' . $page_count_comments . ')',
							'title_no_link' => tf('Читать комментарии').' (' . $page_count_comments . ')',
							'do' => '<div class="comments-link"><span>',
							'posle' => '</span></div>',
							'page_count_comments' => $page_count_comments
						 ));
						 
					echo '</div>';
				}
			
			echo NR . '</div></div><!--div class="page_only"-->' . NR;
		}
		
		if ($f = mso_page_foreach('category-page-only-end')) require($f);
		
	endforeach;
	
	if (!$full_posts) echo '</ul>';
	
	if ($f = mso_page_foreach('category-posle-pages')) require($f); // подключаем кастомный вывод
	
	mso_hook('pagination', $pagination);

}
else 
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
	}
} // endif $pages

if ($f = mso_page_foreach('category-posle')) require($f); // подключаем кастомный вывод
	
echo NR . '</div><!-- class="type type_category" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>