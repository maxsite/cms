<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

			extract($page);
			mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);
			echo '<div class="page_content">';
				echo '<div class="info">';
					mso_page_date($page_date_publish, 
						array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
								'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
								'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
								'<span>', '</span><br>');
					mso_page_cat_link($page_categories, ' -&gt; ', '<span>' . tf('Рубрика') . ':</span> ', '<br>');
					mso_page_tag_link($page_tags, ' | ', '<span>' . tf('Метки') . ':</span> ', '');                  
					mso_page_edit_link($page_id, 'Edit page', ' [', ']');
				echo '</div>';
				mso_page_content($page_content);
				mso_page_content_end();
				echo '<div class="break"></div>';
			echo '</div>';
			
			
?>