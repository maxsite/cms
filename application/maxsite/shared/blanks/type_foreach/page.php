<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		extract($page);
		# pr($page);
		echo NR . '<div class="page_only">' . NR;
		
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', false);

		echo '<div class="info">';
			mso_page_cat_link($page_categories, ' -&gt; ', '<span>' . tf('Рубрика') . ':</span> ', '<br>');
			mso_page_tag_link($page_tags, ' | ', '<span>' . tf('Метки') . ':</span> ', '<br>');
			mso_page_date($page_date_publish, 
							array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
									'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
									'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
							'<span>', '</span>');
			mso_page_view_count($page_view_count, '<br><span>' . tf('Просмотров') . ':</span> ', '');
			mso_page_meta('nastr', $page_meta, '<br><span>' . tf('Настроение') . ':</span> ', '');
			mso_page_meta('music', $page_meta, '<br><span>' . tf('В колонках звучит') . ':</span> ', '');
			if ($page_comment_allow) mso_page_feed($page_slug, tf('комментарии по RSS'), '<br><span>' . tf('Подписаться на').'</span> ', '', true);
			mso_page_edit_link($page_id, 'Edit page', '<br>[', ']');
		echo '</div>';
		
		echo '<div class="page_content type_page">';
			mso_page_content($page_content);
			mso_page_content_end();
			echo '<div class="break"></div>';
			
			// связанные страницы по родителям
			if ($page_nav = mso_page_nav($page_id, $page_id_parent))
			{
				echo '<div class="page_nav">' . $page_nav . '</div>';
			}
			
			// блок "Еще записи этой рубрики"
			mso_page_other_pages($page_id, $page_categories);
			
		echo '</div>';
		
		echo NR . '</div><!--div class="page_only"-->' . NR;
		
?>