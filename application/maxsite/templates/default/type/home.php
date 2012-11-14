<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// нужно выводить рубрики блоками - сделаем отдельным файлом
if (mso_get_option('home_cat_block', 'templates', '0'))
{
	// home-cat-block.php может быть в type своего шаблона
	$fn1 = getinfo('template_dir') . 'type/home-cat-block.php'; 		 // путь в шаблоне
	$fn2 = getinfo('templates_dir') . 'default/type/home-cat-block.php'; // путь в default
	if ( file_exists($fn1) ) require($fn1); // если есть, подключаем шаблонный
	elseif (file_exists($fn2)) require($fn2); // нет, значит дефолтный

	return;
}

// возможно указана страница для отображения вверху перед всеми страницами
if (mso_get_option('home_page_id_top', 'templates', '0'))
{
	$par = array( 
			// колво записей на главной
			// 'limit' => 1, 
			// номер записи для главной
			'page_id' => mso_get_option('home_page_id_top', 'templates', '0'), 
			// текст для Далее
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			// сортировка рубрик
			'cat_order' => 'category_name', 
			// порядок сортировки
			'cat_order_asc' => 'asc',
			'pagination' => false,
			); 
	
	// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
	if ($f = mso_page_foreach('home-top-mso-get-pages')) require($f); 

	$page_top = mso_get_pages($par, $pag);
}
else $page_top = false;

// параметры для получения страниц
$par = array( 
			// колво записей на главной
			'limit' => mso_get_option('home_limit_post', 'templates', '7'), 

			// номер записи для главной
			'page_id' => mso_get_option('home_page_id', 'templates', '0'), 

			// рубрики для главной
			'cat_id' => mso_get_option('home_cat_id', 'templates', '0'), 
			
			// полные ли записи (1) или только заголовки (0)
			'content'=> mso_get_option('home_full_text', 'templates', '1'), 
			
			// текст для Далее
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			
			// сортировка рубрик
			'cat_order' => 'category_id_parent', 
			
			// порядок сортировки
			'cat_order_asc' => 'asc',
	
			); 


// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($f = mso_page_foreach('home-mso-get-pages')) require($f); 
	
$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации

if ($f = mso_page_foreach('home-head-meta')) require($f);

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

// теперь сам вывод

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_home">' . NR;

// если есть верхняя страница, то выводим
if ($page_top) // есть страницы
{ 	
	echo '<div class="home_top">';
	
	foreach ($page_top as $page)  // выводим в цикле
	{
		if ($f = mso_page_foreach('home-top')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}
		
		extract($page);
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);
	
		echo '<div class="page_content">';
			mso_page_content($page_content);
			mso_page_content_end();
			echo '<div class="break"></div>';
		echo '</div>';
	}
	echo '</div>';
}

// если указан текст перед всеми записями, то выводим и его
if ( $home_text_do = mso_get_option('home_text_do', 'templates', '') ) echo $home_text_do;


if ($pages) // есть страницы
{ 	
	// выводим полнные тексты или списком
	if ( !mso_get_option('home_full_text', 'templates', '1') ) echo '<ul class="category">';
		else echo NR . '<div class="page_all">' . NR;
		
	foreach ($pages as $page) : // выводим в цикле

		if ($f = mso_page_foreach('home')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}
		
		extract($page);
		// pr($page);
		
		// выводим полные тексты или списком
		if ( mso_get_option('home_full_text', 'templates', '1') )
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
				
				if ($f = mso_page_foreach('page-content-home')) 
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
						
						mso_page_comments_link( array( 
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
		else // списком
		{
			mso_page_title($page_slug, $page_title, '<li>', '', true);
			mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
			echo '</li>';
		}
		
	endforeach;
	
	if ( !mso_get_option('home_full_text', 'templates', '1') ) echo '</ul><!--ul class="category"-->';
		else echo '</div><!--div class="page_all"-->' . NR;

	if ($f = mso_page_foreach('home-do-pagination')) require($f);
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

if ($f = mso_page_foreach('home-posle')) require($f);

echo NR . '</div><!-- class="type type_home" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>