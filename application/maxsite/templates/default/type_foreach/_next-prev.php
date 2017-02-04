<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	предыдущая - следующая запись
	использовать в других info-top-файлах через подключение
	if ($fn = mso_fe('type_foreach/_next-prev.php')) require($fn);
*/

$np_out = ''; // может использоваться в mso-info-bottom-page.php

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
		$np_out .= '<div class="b-right pad20-l">' . $p->link( mso_page_url($np['prev']['page_slug']), $np['prev']['page_title'] ) . ' <i class="i-long-arrow-right"></i></div>';
	}
	
	$p->block($np_out, '<div class="next-prev-page clearfix t90 mar10-t">', '</div>');
}

# end of file