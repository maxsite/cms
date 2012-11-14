<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
		echo '<h1>' . tf('404 - несуществующая страница') . '</h1>';
		echo '<p>' . tf('Извините по вашему запросу ничего не найдено!') . '</p>';
		echo mso_hook('page_404');

?>