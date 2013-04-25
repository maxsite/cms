<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	вывод заданного изображения из заданного каталога
	
*/
	

// где выводить
$component_output = mso_get_option('component_image_select_output', 'templates', array());

if (!in_array('all', $component_output)) // не отмечено выводить везде
{
	if (!in_array(getinfo('type'), $component_output)) return;
}

$subdir = mso_get_option('default_header_image', 'templates', '-template-');

if ($subdir == '-template-')  // каталог шаблона
	$subdir = getinfo('template_url') . 'images/headers/';
else
	$subdir = getinfo('uploads_url') . $subdir . '/'; // каталог в uploads

$img = '<img src="' . $subdir . mso_get_option('component_image_select', 'templates', '') . '" alt="" title="">';

// ссылка на главную
$component_image_select_link_home = mso_get_option('component_image_select_link_home', 'templates', true);

if ($component_image_select_link_home and !is_type('home'))
{
	$img = '<a href="' . getinfo('site_url') . '">' . $img . '</a>';
}

echo '<div class="image-select"><div class="wrap">'
	. $img
	. '</div></div>';

# end file