<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# Файл служит для работы с type_foreach-файлами. 
# Основное предназначение - изменение «на лету» входящего type_foreach-файла. 
# Входящий type_foreach-файл доступен как переменная $type_foreach_file
# или getinfo('type_foreach_file')
# Ниже приведённый пример демонстрирует, как можно перевести вывод некоторых
# type_foreach-файлов на один общий.
# Результат отдаётся в переменной $type_foreach_file
# getinfo('type_foreach_file') при этом не меняется

/*
Пример. Делаем единый type_foreach-файл для home и page

if (
	$type_foreach_file == 'home' // если это главная
	or $type_foreach_file == 'page' // или запись
	)
{
	return $type_foreach_file = 'home-page'; // меняем на свой type_foreach-файл
}

*/

/*
Пример type_foreach-файл/home-page.php
В нём меняется формат вывода записей

		extract($page);
		# pr($page);
		echo NR . '<div class="page_only">' . NR;

		echo '<div class="info">';
			mso_page_title($page_slug, $page_title, '<h1>', '</h1>', is_type('home'));
			mso_page_date($page_date_publish, 
							array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
									'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
									'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
							'<span>', '</span>');
		echo '</div>';

		
		echo '<div class="page_content type_page">';
			mso_page_content($page_content);

			
			echo '<div class="info">';
					mso_page_cat_link($page_categories, ' -&gt; ', '<span>' . tf('Рубрика') . ':</span> ', '');
					mso_page_tag_link($page_tags, ' | ', '<br><span>' . tf('Метки') . ':</span> ', '');			
					
					if (is_type('page'))
					{
						mso_page_view_count($page_view_count, '<br><span>' . tf('Просмотров') . ':</span> ', '');
						mso_page_meta('nastr', $page_meta, '<br><span>' . tf('Настроение') . ':</span> ', '');
						mso_page_meta('music', $page_meta, '<br><span>' . tf('В колонках звучит') . ':</span> ', '');
						if ($page_comment_allow) mso_page_feed($page_slug, tf('комментарии по RSS'), '<br><span>' . tf('Подписаться на').'</span> ', '', true);
						mso_page_edit_link($page_id, 'Edit page', ' [', ']');
					}
			echo '</div>';
			
			
			mso_page_content_end();
			echo '<div class="break"></div>';			
			
			// связанные страницы по родителям
			if (is_type('page') and $page_nav = mso_page_nav($page_id, $page_id_parent))
			{
				echo '<div class="page_nav">' . $page_nav . '</div>';
			}
			
			// выводить ли блок "Еще записи этой рубрики"
			if (is_type('page') and $bl_title = mso_get_option('page_other_pages', 'templates', tf('Еще записи по теме', '')))
			{
				$bl_pages = mso_get_pages(
									array(  'type'=> false, 'content'=> false, 'pagination'=>false, 
											'custom_type'=> 'category', 'categories'=>$page_categories, 
											'exclude_page_id'=>array($page_id), 
											'content'=>false,
											'limit'=> mso_get_option('page_other_pages_limit', 'templates', 7), 
											'order'=>mso_get_option('page_other_pages_order', 'templates', 'page_date_publish'),
											'order_asc'=>mso_get_option('page_other_pages_order_asc', 'templates', 'random')
											),
											$_temp);
				if ($bl_pages)
				{
					echo '<div class="page_other_pages"><h3>' . $bl_title . '</h3><ul>';
					foreach ($bl_pages as $bl_page)
						mso_page_title($bl_page['page_slug'], $bl_page['page_title'], '<li>', '</li>', true);
					echo '</ul></div>';
				}
			}
			
		echo '</div>';
		
		echo NR . '</div><!--div class="page_only"-->' . NR;
		


*/