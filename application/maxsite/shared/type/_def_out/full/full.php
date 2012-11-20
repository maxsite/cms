<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if (!$pages) return;

$p = new Page_out();

$p->format('title', mso_get_val('full_format_title_start', '<h2 class="page_title">'), mso_get_val('full_format_title_end', '</h2>'), true);
$p->format('date', 'D, j F Y г.', '<span><time datetime="[page_date_publish]">', '</time></span>');
$p->format('cat', ' -&gt; ', '<br><span>' . tf('Рубрика') . ': ', '</span>');
$p->format('tag', ' | ', '<br><span>' . tf('Метки') . ': ', '</span>');
$p->format('feed', tf('Комментарии по RSS'), ' | <span>', '</span>');
$p->format('edit', 'Edit', ' | <span>', '</span>');
$p->format('view_count', '<br><span>' . tf('Просмотров') . ': ', '</span>');
$p->format('comments', tf('Обсудить'), tf('Читать комментарии'), '<div class="comments-link"><span>',  '</span></div>');


// исключенные записи
$exclude_page_id = mso_get_val('exclude_page_id');

$p->div_start(mso_get_val('container_class'));

foreach ($pages as $page)
{
	if ($f = mso_page_foreach(getinfo('type'))) 
	{
		require($f); // подключаем кастомный вывод
		continue; // следующая итерация
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
		
		if ($f = mso_page_foreach('page-content-' . getinfo('type'))) 
		{
			require($f);
		}
		else
		{
			$p->div_start('page_content type_' . getinfo('type'));
	
				$p->content('', '');
				
				if ($f = mso_page_foreach('info-bottom')) require($f);
				
				$p->clearfix();
				
				$p->html('<aside>');
					mso_page_content_end();
					$p->line('[comments]');
				$p->html('</aside>');
				
			$p->div_end('page_content type_' . getinfo('type'));
		}
	
	$p->div_end('page_only', 'wrap', '</article>');
	
	if ($f = mso_page_foreach(getinfo('type') . '-page-only-end')) require($f);
	
	$exclude_page_id[] = $p->val('page_id');
	
} // end foreach

$p->div_end(mso_get_val('container_class'));

mso_set_val('exclude_page_id', $exclude_page_id);

# end file