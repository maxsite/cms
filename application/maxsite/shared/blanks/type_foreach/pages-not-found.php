<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
	echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
	echo mso_hook('page_404');
	
?>