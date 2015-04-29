<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if (!$pages) return;

$p = new Page_out();

$p->format('title', mso_get_val('full_format_title_start', '<h2 class="mso-page-title">'), mso_get_val('full_format_title_end', '</h2>'), true);
$p->format('date', 'D, j F Y ' . tf('г.'), '<span><time datetime="[page_date_publish_iso]">', '</time></span>');
$p->format('cat', ' -&gt; ', '<br><span>' . tf('Рубрика') . ': ', '</span>');
$p->format('tag', ' | ', '<br><span>' . tf('Метки') . ': ', '</span>');
$p->format('feed', tf('Комментарии по RSS'), ' | <span>', '</span>');
$p->format('edit', 'Edit', ' | <span>', '</span>');
$p->format('view_count', '<br><span>' . tf('Просмотров') . ': ', '</span>');
$p->format('comments', tf('Обсудить'), tf('Читать комментарии'), '<div class="mso-comments-link"><span>',  '</span></div>');


// исключенные записи
$exclude_page_id = mso_get_val('exclude_page_id');


$p->div_start(mso_get_val('container_class', ''));

foreach ($pages as $page)
{
	if ($f = mso_page_foreach(getinfo('type'))) 
	{
		require($f); // подключаем кастомный вывод
		continue; // следующая итерация
	}

	$p->load($page);

	$p->div_start('mso-page-only', '<article>');
		
		// для типа может быть свой info-top
		if ($f = mso_page_foreach('info-top-' . getinfo('type'))) 
		{
			require($f);
		}
		else
		{
			if ($f = mso_page_foreach('info-top'))
			{
				require($f);
			}
			else
			{
				$p->html(NR . '<header>');
					$p->line('[title]');
					
					$p->div_start('mso-info mso-info-top');
						$p->line('[date][edit][cat][tag][view_count]');
					$p->div_end('info info-top');
					
				$p->html('</header>');
			}
		}
		
		if ($f = mso_page_foreach('page-content')) 
		{
			require($f);
		}
		else
		{
			if ($f = mso_page_foreach('page-content-' . getinfo('type'))) 
			{
				require($f);
			}
			else
			{
				$p->div_start('mso-page-content mso-type-' . getinfo('type') . '-content');
					
					if ($f = mso_page_foreach('content')) require($f);
					else
					{
						// если show_thumb_type_ТИП вернул false, то картинку не ставим
						// show_thumb - если нужно отключить для всех типов
						if ( mso_get_val('show_thumb', true)
							 and mso_get_val('show_thumb_type_' . getinfo('type'), true) )
						{
							// вывод миниатюры перед записью
							if ($image_for_page = thumb_generate(
									$p->meta_val('image_for_page'), 
									mso_get_option('image_for_page_width', 'templates', 280),
									mso_get_option('image_for_page_height', 'templates', 210)
								))
							{
								if (mso_get_option('image_for_page_link', 'templates', 1))
								{
									echo $p->page_url(true) . $p->img($image_for_page, mso_get_option('image_for_page_css_class', 'templates', 'image_for_page'), '', $p->val('page_title')) . '</a>';
								}
								else
								{
									echo $p->img($image_for_page, mso_get_option('image_for_page_css_class', 'templates', 'image_for_page'), '', $p->val('page_title'));
								}
							}
						}
						
						$p->content('', '');
						$p->clearfix();
					}

					// для page возможен свой info-bottom
					if ($f = mso_page_foreach('info-bottom-' . getinfo('type'))) 
					{
						require($f);
					}
					elseif ($f = mso_page_foreach('info-bottom')) require($f);
					
					
					$p->html('<aside>');
					
						mso_page_content_end();
						
						$p->clearfix();
						
						$p->line('[comments]');
						
					$p->html('</aside>');
					
				$p->div_end('mso-page-content mso-type-' . getinfo('type') . '-content');
			}
		}
		
	$p->div_end('mso-page-only', '</article>');
	
	if ($f = mso_page_foreach(getinfo('type') . '-page-only-end')) require($f);
	
	$exclude_page_id[] = $p->val('page_id');
	
} // end foreach

$p->div_end(mso_get_val('container_class'));

mso_set_val('exclude_page_id', $exclude_page_id);

# end file