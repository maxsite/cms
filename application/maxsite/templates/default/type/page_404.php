<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
	if ( mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found');
	
	require(getinfo('template_dir') . 'main-start.php');
	
	echo NR . '<div class="type type_page_404">' . NR;


	if ($f = mso_page_foreach('page_404')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<div class="page_only"><div class="wrap">';
	
		echo '<h1>' . tf('404 - несуществующая страница') . '</h1>';
		
		echo '<div class="page_content">';
		echo '<p>' . tf('Извините по вашему запросу ничего не найдено!') . '</p>';
		echo mso_hook('page_404');
		
		echo '</div></div></div>';
	}
	
	echo NR . '</div><!-- class="type type_page_404" -->' . NR;
	
	require(getinfo('template_dir') . 'main-end.php'); 

?>