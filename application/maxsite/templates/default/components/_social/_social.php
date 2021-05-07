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

$icons = trim(mso_get_option('social', getinfo('template'), '')); // вначале опция шаблона

if (!$icons) $icons = mso_get_option('social', 'templates', ''); // дефолтная опция

$icons = '[icons]' . NR . $icons . '[/icons]';
$icons = mso_section_to_array($icons, 'icons', [], true);

if ($icons and isset($icons[0])) {
	$icons = $icons[0];

	// через эту переменную можно передавать дополнительные классы для ссылок	
	$my_social_class = mso_get_val('my_social_class', 'my-social');
	
	foreach ($icons as $icon => $data) {
		$data = explode('|', $data);
		$data = array_map('trim', $data);

		$url = (isset($data[0])) ? $data[0] : '';
		$title = (isset($data[1])) ? htmlspecialchars($data[1]) : '';
		$add_class = (isset($data[2])) ? ' ' . $data[2] : '';

		echo '<a class="' . strtolower($icon) . $add_class . ' ' . $my_social_class . '" rel="nofollow" title="' . $title . '" href="' . $url . '"></a>';
	}
	
	mso_unset_val('my_social_class'); // удаляем значение, чтобы оно не влияло на повторный вызов
}

# end of file
