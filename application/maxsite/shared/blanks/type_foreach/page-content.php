<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// только тип page

echo '<div class="page_content type_' . getinfo('type'). '">';
				
	mso_page_content($page_content);
	if ($f = mso_page_foreach('info-bottom')) require($f); // подключаем кастомный вывод
	mso_page_content_end();
	echo '<div class="break"></div>';
	
	// связанные страницы по родителям
	if ($page_nav = mso_page_nav($page_id, $page_id_parent))
		echo '<div class="page_nav">' . $page_nav . '</div>';
	
	// блок "Еще записи этой рубрики"
	mso_page_other_pages($page_id, $page_categories);
	
echo '</div>';