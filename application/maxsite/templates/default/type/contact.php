<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


	$title_contact = mso_get_option('title_contact', 'templates', tf('Обратная связь'));
	
	mso_head_meta('title', $title_contact); //  meta title страницы

	require(getinfo('template_dir') . 'main-start.php');
	echo NR . '<div class="type type_contact"><div class="page_only"><div class="wrap">' . NR;
?>

<h1><?= $title_contact ?></h1>

<?php
	echo '<div class="page_content">';
	
	echo mso_get_option('prew_contact', 'templates', '');
	
	if ($f = mso_page_foreach('contact-do')) require($f); // подключаем кастомный вывод

	$form_def = '[form]
[subject=Пожелания по сайту # Нашел ошибку на сайте # Подскажите, пожалуйста]

[field]
require = 0
type = text
type_text = url
description = Сайт
tip = Вы можете указать адрес своего сайта (если есть)
placeholder = Адрес сайта
[/field]

[field]
require = 0
type = text
description = Телефон
tip = Телефон лучше указывать с кодом города/страны
placeholder = Введите свой телефонный номер
[/field]

[field]
require = 1
type = textarea
description = Ваш вопрос
placeholder = О чем вы хотите написать?
attr = style="height: 200px"
[/field]

[/form]';
	
	$form_def = str_replace("\r", "", $form_def);
	$form_def = str_replace("\n", "_NR_", $form_def);
	
	
	$form = mso_get_option('form_contact', 'templates', $form_def);
	
	//pr($form);

	// используем плагин Forms
	if (!function_exists('forms_content'))
	{
		require_once(getinfo('plugins_dir') . 'forms/index.php');
	}
	
	
	
	echo forms_content(str_replace("_NR_", "\n", $form));
	
	echo mso_get_option('post_contact', 'templates', '');
	
	if ($f = mso_page_foreach('contact-posle')) require($f); // подключаем кастомный вывод
	
	echo '</div>'; //  class="page_content"

echo NR . '</div></div><!-- class="page_only" --></div><!-- class="type type_contact" -->' . NR;

require(getinfo('template_dir') . 'main-end.php');

# end file