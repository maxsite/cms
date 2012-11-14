<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		extract($page);
		
		if (!$full_posts)
		{
			mso_page_title($page_slug, $page_title, '<li>', '', true);
			mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
			echo '</li>';
		}
		else
		{
			echo NR . '<div class="page_only">' . NR;
			echo '<div class="info">';
				mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);
				mso_page_date($page_date_publish, 
							array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
									'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
									'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
							'<span>', '</span>');
				mso_page_cat_link($page_categories, ' | ', '<br><span>'. tf('Рубрика'). ':</span> ', '');
				mso_page_tag_link($page_tags, ' | ', '<br><span>'. tf('Метки'). ':</span> ', '');
				mso_page_edit_link($page_id, 'Edit page', '<br>-', '-');
				// mso_page_feed($page_slug, tf('комментарии по RSS'), '<br><span>'. tf('Подписаться на'). '</span> ', '', true);
			echo '</div>';
			
			
			echo '<div class="page_content">';
				mso_page_content($page_content);
				mso_page_content_end();
				echo '<div class="break"></div>';
				mso_page_comments_link(array( 
					'page_comment_allow' => $page_comment_allow,
					'page_slug' => $page_slug,
					'title' => tf('Обсудить'). ' (' . $page_count_comments . ')',
					'title_no_link' => tf('Читать комментарии').' (' . $page_count_comments . ')',
					'do' => '<div class="comments-link"><span>',
					'posle' => '</span></div>',
					'page_count_comments' => $page_count_comments
				 ));
				
			echo '</div>';
			echo NR . '</div><!--div class="page_only"-->' . NR;
		}
	
?>