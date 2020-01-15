<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$title_contact = mso_get_option('title_contact', 'templates', tf('Обратная связь'));

if ($fn = mso_page_foreach('contact-head-meta'))
	require $fn;
else
	mso_head_meta('title', $title_contact); //  meta title страницы

if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-contact"><div class="mso-page-only">';
echo '<h3>' . $title_contact . '</h3>';
echo '<div class="mso-page-content">';
echo mso_get_option('prew_contact', 'templates', '');

if ($fn = mso_page_foreach('contact-do')) require $fn; // подключаем кастомный вывод

$form_def = '[form]

[options]
email = ' . mso_get_option('admin_email', 'general', 'admin@site.com') . '
[/options]

[field] 
require = 1
type = select
description = Тема письма
values = Пожелания по сайту # Нашел ошибку на сайте # Подскажите, пожалуйста
default = Пожелания по сайту 
subject = 1
[/field]

[field]
require = 1   
type = text
description = Ваше имя
placeholder = Ваше имя
[/field]

[field]
require = 1   
type = text
clean = email
description = Ваш email
placeholder = Ваш email
from = 1
[/field]

[field] 
require = 0   
type = url
description = Сайт
tip = Вы можете указать адрес своего сайта (если есть)
placeholder = Адрес сайта
[/field]

[field] 
require = 1 
type = textarea 
description = Ваш вопрос
placeholder = О чем вы хотите написать?
[/field]

[/form]';

$form_def = str_replace("\r", "", $form_def);
$form_def = str_replace("\n", "_NR_", $form_def);

$form = mso_get_option('form_contact', 'templates', $form_def);

if (!$form) $form = $form_def;

// используем плагин Forms
if (!function_exists('forms_content')) {
	require_once getinfo('plugins_dir') . 'forms/index.php';
}

echo forms_content(str_replace("_NR_", "\n", $form));
echo mso_get_option('post_contact', 'templates', '');

if ($fn = mso_page_foreach('contact-posle')) require $fn; // подключаем кастомный вывод

echo '</div></div></div><!-- class="mso-type-contact" -->';

if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

# end of file
