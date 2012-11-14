<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

echo '<div class="page_content type_' . getinfo('type'). '">';
					
	mso_page_content($page_content);
	if ($f = mso_page_foreach('info-bottom')) require($f); // подключаем кастомный вывод
	mso_page_content_end();
	echo '<div class="break"></div>';
	
	mso_page_comments_link( array( 
		'page_comment_allow' => $page_comment_allow,
		'page_slug' => $page_slug,
		'title' => tf('Обсудить'). ' (' . $page_count_comments . ')',
		'title_no_link' => tf('Читать комментарии').' (' . $page_count_comments . ')',
		'do' => '<div class="comments-link"><span>',
		'posle' => '</span></div>',
		'page_count_comments' => $page_count_comments
	 ));
	
echo '</div>';