<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

mso_page_view_count_first(); // для подсчета количества прочтений страницы

$par = array( 
	'cut' => false, 
	'cat_order' => 'category_id_parent', 
	'cat_order_asc' => 'asc', 
	'type' => false); 

if ($f = mso_page_foreach('page-mso-get-pages')) require($f); 

$pages = mso_get_pages($par, $pagination); // получим все

if ($f = mso_page_foreach('page-head-meta')) 
{
	require($f);
}
else
{ 
	mso_head_meta('title', $pages, '%page_title%'); // meta title страницы
	mso_head_meta('description', $pages); // meta description страницы
	mso_head_meta('keywords', $pages); // meta keywords страницы
}
	
	
if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) 
	header('HTTP/1.0 404 Not Found'); 

if ($f = mso_page_foreach('page-main-start')) 
{
	require($f);
	return;
}
else
{
	if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);
}

echo NR . '<div class="type type_page">' . NR;

if ($f = mso_page_foreach('page-do')) require($f);

if ($pages)
{ 	
	$p = new Page_out();

	$p->format('title', '<h1>', '</h1>', false);
	$p->format('date', 'D, j F Y г.', '<span><time datetime="[page_date_publish]">', '</time></span>');
	$p->format('cat', ' -&gt; ', '<br><span>' . tf('Рубрика') . ': ', '</span>');
	$p->format('tag', ' | ', '<br><span>' . tf('Метки') . ': ', '</span>');
	$p->format('feed', tf('Комментарии по RSS'), ' | <span>', '</span>');
	$p->format('edit', 'Edit', ' | <span>', '</span>');
	$p->format('view_count', '<br><span>' . tf('Просмотров') . ': ', '</span>');

	foreach ($pages as $page)
	{
		if ($f = mso_page_foreach('page')) 
		{
			require($f);
			
			// здесь комментарии
			if ($fn = mso_find_ts_file('type/page/units/page-comments.php')) require($fn);

			continue;
		}
		
		$p->load($page);
		
		$p->div_start('page_only', 'wrap', '<article>');
		
			if ($f = mso_page_foreach('info-top')) 
			{
				require($f);
			}
			else
			{
				$p->html(NR . '<header>');
					$p->line('[title]');
					
					$p->div_start('info info-top');
						$p->line('[date][feed][edit][cat][tag][view_count]');
					$p->div_end('info info-top');
				$p->html('</header>');
			}
			
			if ($f = mso_page_foreach('page-content')) 
			{
				require($f);
			}
			else
			{
				$p->div_start('page_content type_' . getinfo('type'));
				
					$p->content('', '');
					
					if ($f = mso_page_foreach('info-bottom')) require($f); // подключаем кастомный вывод
					
					$p->html('<aside>');
					
						mso_page_content_end();
						
						$p->clearfix();
						
						// связанные страницы по родителям
						if ($page_nav = mso_page_nav($p->val('page_id'), $p->val('page_id_parent')))
						{
							$p->div($page_nav, 'page_nav');
						}
						
						// блок "Еще записи по теме"
						if ($f = mso_page_foreach('page-other-pages')) require($f);
							else mso_page_other_pages($p->val('page_id'), $p->val('page_categories'));
							
					$p->html('</aside>');
					
				$p->div_end('page_content type_' . getinfo('type'));
			}

		$p->div_end('page_only', 'wrap', '</article>');
		
		if ($f = mso_page_foreach('page-only-end')) require($f);
		
		// здесь комментарии
		if ($fn = mso_find_ts_file('type/page/units/page-comments.php')) require($fn);
			
	} // end foreach

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

if ($f = mso_page_foreach('page-posle')) require($f);

echo NR . '</div><!-- /div.type type_page -->' . NR;

if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file