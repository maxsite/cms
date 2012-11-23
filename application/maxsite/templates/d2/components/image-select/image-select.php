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

echo '<div class="image-select">'
	. '<img src="' . $img . '" alt="" title="">';
	. '</div>';

# end file