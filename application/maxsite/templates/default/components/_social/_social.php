<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, https://max-3000.com/
	
	Вывод иконок
	Файл использовать в других компонентах
	Используется глобальная опция social.templates
	
	Иконки произвольные из https://fontawesome.com/icons?d=gallery&m=free
	Поскольку используется FontAwesome 5, то нужно указывать двойной класс иконки 

	иконка = адрес
	иконка = адрес | подсказка
	иконка = адрес | подсказка | дополнительный css-класс
	
	fab fa-facebook = https://facebook.com/my | Фейсбук
	fas fa-rss = https://site.com/feed | RSS
	
*/

$social = '[social]' . NR . mso_get_option('social', getinfo('template'), '') . '[/social]';
$social = mso_section_to_array($social, 'social', [], true);

if ($social and isset($social[0])) {
	$socials = $social[0];

	foreach ($socials as $icon => $data) {
		$data = explode('|', $data);
		$data = array_map('trim', $data);

		$url = (isset($data[0])) ? $data[0] : '';
		$title = (isset($data[1])) ? htmlspecialchars($data[1]) : '';
		$add_class = (isset($data[2])) ? ' ' . $data[2] : '';

		echo '<a class="my-social ' . strtolower($icon) . $add_class . '" rel="nofollow" title="' . $title . '" href="' . $url . '"></a>';
	}
}

# end of file
