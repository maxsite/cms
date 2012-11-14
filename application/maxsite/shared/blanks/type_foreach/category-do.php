<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		# выводим только если есть найденные страницы
		if ($pages) 
		{
			echo '<h1 class="category">' . $title_page . '</h1>';
		}