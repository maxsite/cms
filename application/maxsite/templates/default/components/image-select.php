<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	/*
		вывод заданного изображения из заданного каталога
	*/
	
	
	$subdir = mso_get_option('default_header_image', 'templates', '-template-');
	if ($subdir == '-template-')  // каталог шаблона
		$subdir = getinfo('template_url') . 'images/headers/';
	else
		$subdir = getinfo('uploads_url') . $subdir . '/'; // каталог в uploads


	$img = $subdir . mso_get_option('component_image_select', 'templates', '');
	
	// вывод блока изображения
	echo '<div class="component_image_select">';
	echo NR . '<img src="' . $img . '" alt="" title="">';
	echo '</div>';


# end file