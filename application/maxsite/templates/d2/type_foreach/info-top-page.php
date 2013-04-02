<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл

	предыдущая - следующая запись

	вывод рубрик перед заголовком записи

*/

// следующая-предыдущая запись
if (is_type('page') and $p->val('page_type_name') == 'blog')
{
	$np = mso_next_prev_page(
				array(
					'page_id' => $p->val('page_id'),
					'page_categories' => $p->val('page_categories'),
					'page_date_publish' => $p->val('page_date_publish')
				));
	
	$np_out = '';
	
	if ($np['next'])
	{
		$np_out .= '<div class="d-inline-block left">← ' . $p->link( mso_page_url($np['next']['page_slug']), $np['next']['page_title'] ) . '</div>';
	}

	if ($np['prev'])
	{
		$np_out .= '<div class="d-inline-block right">' . $p->link( mso_page_url($np['prev']['page_slug']), $np['prev']['page_title'] ) . ' →</div>';
	}
	
	$p->block($np_out, '<div class="next-prev-page">', '</div><div class="clearfix"></div>');
}


$p->format('edit', 'Edit', '<div class="right bg-yellow padding5 d-inline-block">', '</div>');
$p->format('cat', ' / ', '<div>', '</div>');
$p->format('date', 'D, j F Y г.', '<div><time datetime="[page_date_publish_iso]">', '</time></div>');

$p->html(NR . '<header>');

	$p->line('[edit][title]');
	
	$p->div_start('info info-top');
		$p->line('[cat]');
		$p->line('[date]');
	$p->div_end('info info-top');

$p->html('</header>');

# end file