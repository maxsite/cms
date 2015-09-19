<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	предыдущая - следующая запись
	вывод рубрик перед заголовком записи
*/

$np_out = '';

if (is_type('page') and $p->val('page_type_name') == 'blog')
{
	$np = mso_next_prev_page(
				array(
					'page_id' => $p->val('page_id'),
					'page_categories' => $p->val('page_categories'),
					'page_date_publish' => $p->val('page_date_publish'),
					'use_category' => false, // не учитывать рубрики 
					// 'reverse' => true, // поменять местами пункты
				));
	
	if ($np['next'])
	{
		$np_out .= '<div class="b-left"><i class="i-long-arrow-left"></i> ' . $p->link( mso_page_url($np['next']['page_slug']), $np['next']['page_title'] ) . '</div>';
	}

	if ($np['prev'])
	{
		$np_out .= '<div class="b-right">' . $p->link( mso_page_url($np['prev']['page_slug']), $np['prev']['page_title'] ) . ' <i class="i-long-arrow-right"></i></div>';
	}
	
	$p->block($np_out, '<div class="next-prev-page clearfix t90">', '</div>');
}

$p->format('edit', '<i class="i-edit t-gray600 hover-t-black" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');

$p->format('title', '<h1 class="t-gray700 bor-double-b bor3px bor-gray300 pad5-b">', '</h1>', false);

$p->format('date', 'j F Y г.', '<time datetime="[page_date_publish_iso]" class="i-calendar">', '</time>');

$p->format('view_count', '<span class="i-eye mar15-l">' . tf('Просмотров') . ': ', '</span>');

$p->format('comments_count', '<span class="i-comment mar15-l">Комментарии: ', '</span>');

$p->format('cat', '<i class="i-bookmark-o mar10-l"></i>', '<br><span class="i-bookmark" title="' . tf('Рубрика записи') . '">', '</span>');

$p->format('tag', '<i class="i-tag mar10-l"></i>', '<br><span class="i-tags links-no-color" title="' . tf('Метка записи') . '">', '</span>');


$p->html(NR . '<header class="mar20-b">');

	$p->line('[edit][title]');
	
	$p->div_start('info info-top t-gray600 t90');
		$p->line('[date][view_count][comments_count][cat][tag]');
	$p->div_end('info info-top');

$p->html('</header>');

# end file