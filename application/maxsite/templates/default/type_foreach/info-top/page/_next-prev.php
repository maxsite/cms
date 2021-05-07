<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

/*
	предыдущая - следующая запись
	использовать в других info-top-файлах через подключение
	if ($fn = mso_fe('type_foreach/my-template/_next-prev.php')) require($fn);
*/

$np_out = '';

if (is_type('page') and $p->val('page_type_name') == 'blog') {
	$np = mso_next_prev_page(
		[
			'page_id' => $p->val('page_id'),
			'page_categories' => $p->val('page_categories'),
			'page_date_publish' => $p->val('page_date_publish'),
			'use_category' => false, // не учитывать рубрики 
			// 'reverse' => true, // поменять местами пункты
		]
	);

	if ($np['next']) {
		$np_out .= '<div class="b-left mar5-b">' . $p->link(mso_page_url($np['next']['page_slug']), '<i class="im-long-arrow-alt-left icon0 mar7-r"></i> ' . $np['next']['page_title'], tf('Следующая запись'), '') . '</div>';
	}

	if ($np['prev']) {
		$np_out .= '<div class="b-right mar5-b">' . $p->link(mso_page_url($np['prev']['page_slug']), $np['prev']['page_title'] . ' <i class="im-long-arrow-alt-right icon0 mar7-l"></i>', tf('Предыдущая запись'), '') . '</div>';
	}

	$p->block($np_out, '<div class="next-prev-page mso-clearfix t90 mar30-t hover-no-underline">', '</div>');
}

# end of file
