<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/
	
	вывод иконок
	Файл использовать в других компонентах
	используется глобальная опция social.templates
	
	Иконки произвольные из http://fortawesome.github.io/Font-Awesome/icons/

	иконка = адрес
	иконка = адрес | подсказка
	иконка = адрес | подсказка | дополнительный css-класс
	
	facebook = //facebook.com/my | Фейсбук
	
*/

$social = '[social]' . NR . mso_get_option('social', 'templates', '') . '[/social]';
$social = mso_section_to_array($social, 'social', array(), true);

if ($social and isset($social[0]))
{
	$socials = $social[0];
	
	foreach ($socials as $icon => $data)
	{
		$data = explode('|', $data);
		$data = array_map('trim', $data);

		$url = (isset($data[0])) ? $data[0] : '';
		$title = (isset($data[1])) ? htmlspecialchars($data[1]) : '';
		$add_class = (isset($data[2])) ? ' ' . $data[2] : '';
		
		echo '<a class="my-social i-' . strtolower($icon) . $add_class . '" rel="nofollow" title="' . $title . '" href="' . $url .'"></a>';
	}
}

# end of file