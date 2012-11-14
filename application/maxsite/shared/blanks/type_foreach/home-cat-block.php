<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
		
			$par['cat_id'] = $cat_id;
			
			if ($f = mso_page_foreach('home-cat-block-mso-get-pages')) require($f); 
			
			$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
			
			// и выводим как обычно на главной, только добавляем в начало блока заголовок из рубрик
			if ($pages) // есть страницы
			{ 	
				
				echo '<h1 class="home-cat-block">' . $all_cats[$cat_id]['category_name'] . '</h1>';
				
				
				// выводим полнные тексты или списком
				if ( !mso_get_option('home_full_text', 'templates', '1') ) echo '<ul class="home-cat-block">';
					
				foreach ($pages as $page) : // выводим в цикле

					extract($page);
					// pr($page);
					
					// выводим полные тексты или списком
					if ( mso_get_option('home_full_text', 'templates', '1') )
					{ 
						mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

						echo '<div class="info">';
							mso_page_date($page_date_publish, 
										array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
												'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
												'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
										'<span>', '</span><br>');
							
							mso_page_cat_link($page_categories, ' -&gt; ', '<span>' . tf('Рубрика') .':</span> ', '<br>');
							mso_page_tag_link($page_tags, ' | ', '<span>' . tf('Метки') . ':</span> ', '');                  
							mso_page_edit_link($page_id, 'Edit page', ' [', ']');
							# mso_page_feed($page_slug, 'комментарии по RSS', '<br><span>Подписаться</span> на ', '', true);
						echo '</div>';
						
						echo '<div class="page_content type_home">';
						
							mso_page_content($page_content);
							mso_page_content_end();
							echo '<div class="break"></div>';
							
							mso_page_comments_link( array( 
								'page_comment_allow' => $page_comment_allow,
								'page_slug' => $page_slug,
								'title' => 'Обсудить (' . $page_count_comments . ')',
								'title_no_link' => tf('Читать комментарии').' (' . $page_count_comments . ')',
								'do' => '<div class="comments-link"><span>',
								'posle' => '</span></div>',
								'page_count_comments' => $page_count_comments
							 ));
							
							// mso_page_comments_link($page_comment_allow, $page_slug, 'Обсудить (' . $page_count_comments . ')', '<div class="comments-link">', '</div>');
							
						echo '</div>';
					}
					else // списком
					{
						mso_page_title($page_slug, $page_title, '<li>', '', true);
						mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
						echo '</li>';
					}
					
				endforeach;
				
				if ( !mso_get_option('home_full_text', 'templates', '1') ) echo '</ul><!--ul class="home-cat-block"-->';
				
				// if (function_exists('pagination_go')) echo pagination_go($pagination); // вывод навигации
					
			}// endif $pages
			
	
?>