<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
/*
	вывод случайного изображения из заданного каталога
*/

// где выводить
$component_output = mso_get_option('component_image_rand_output', 'templates', array());

if (!in_array('all', $component_output)) // не отмечено выводить везде
{
	if (!in_array(getinfo('type'), $component_output)) return;
}

$subdir = mso_get_option('default_header_image', 'templates', '-template-');

if ($subdir == '-template-')  // каталог шаблона
	$imgs = mso_get_path_files(getinfo('template_dir') . 'images/headers/', getinfo('template_url') . 'images/headers/');
else
	$imgs = mso_get_path_files(getinfo('uploads_dir') . $subdir . '/', getinfo('uploads_url') . $subdir . '/'); // каталог в uploads

if ($imgs)
{
	$img = $imgs[array_rand($imgs, 1)]; // случайный выбор
	
	echo '<div class="image-rand"><div class="wrap">'
		. '<img src="' . $img . '" alt="" title="">'
		. '</div></div>';
}

# end file