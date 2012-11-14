<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

echo '<div class="info info-top">';
	mso_page_title($page_slug, $page_title, '<h1>', '</h1>', !is_type('page'));
	mso_page_date($page_date_publish, 
					array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
							'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
							'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
					'<span>', '</span>');
	
	//mso_page_author_link($users_nik, $page_id_autor, '<br><span>Автор:</span> ', '');

echo '</div>';
	
