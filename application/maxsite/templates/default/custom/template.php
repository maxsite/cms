<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * Функции для работы с шаблоном
 */

/**
 * Вывод секции HEAD
 *
 * @global $page нужна для всех подключаемых type_foreach-файлов (может ими использоваться)
 */
function my_default_head_section()
{
	global $page;

	// атирибуты <HTML>
	$html_attr = mso_get_val('head_section_html_add');
	$html_attr = mso_hook('html_attr', $html_attr);
	$html_attr = $html_attr ? ' ' . $html_attr : '';

	echo
		'<!DOCTYPE html>
<html' . $html_attr . '><head>' . mso_hook('head_start') . '
<meta charset="UTF-8">
<title>' . mso_head_meta('title') . '</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="generator" content="MaxSite CMS">
<meta name="description" content="' . mso_head_meta('description') . '">
<link rel="shortcut icon" href="' . getinfo('uploads_url') . 'favicons/' . mso_get_option('default_favicon', 'templates', 'favicon1.png') . '" type="image/x-icon">
';

	if (mso_get_option('default_canonical', 'templates', 0)) echo mso_link_rel('canonical');

	echo mso_rss();

	if ($fn = mso_fe('custom/head-start.php')) require $fn;

	// autoload файлов
	if ($autoload_css = mso_get_path_files(getinfo('template_dir') . 'assets/css/', getinfo('template_url') . 'assets/css/', true, ['css'])) {
		foreach ($autoload_css as $fn_css) {
			echo mso_load_style($fn_css);
		}
	}

	my_out_component_css();
	mso_hook('head_css');
	my_default_out_profiles();

	echo mso_load_jquery();

	mso_hook('head');

	// autoload js-файлов
	if ($autoload_js = mso_get_path_files(getinfo('template_dir') . 'assets/js/autoload/', getinfo('template_url') . 'assets/js/autoload/', true, ['js'])) {
		foreach ($autoload_js as $fn_js) {
			echo '<script src="' . $fn_js . '"></script>' . NR;
		}
	}

	// компоненты и файл _head.php
	my_out_component_js();

	if ($fn = mso_fe('custom/head.php')) require $fn;
	if ($fn = mso_page_foreach('head')) require $fn;
	if (function_exists('ushka')) echo ushka('head');

	if (mso_fe('assets/js/my.js')) mso_add_file('assets/js/my.js');

	if ($my_style = mso_get_option('my_style', getinfo('template'), ''))
		echo NR . '<!-- custom style --><style>' . $my_style . '</style>';

	mso_hook('head_end');

	if (function_exists('ushka')) echo ushka('google_analytics_top');

	echo '</head>';
}

/**
 * вывод подключенных css-профилей
 * @global type $page
 * @param type $path
 */
function my_default_out_profiles($path = 'assets/css/profiles/')
{
	global $page;

	if ($default_profiles = mso_get_option('default_profiles', getinfo('template'), [])) {

		$css_out = '';
		
		// theme и lazy профили подключаются как link rel="stylesheet
		foreach ($default_profiles as $css_file) {
			$fn = $path . $css_file;

			$theme = (strpos($css_file, 'theme-') === 0);
			$lazy = (strpos($css_file, '-lazy') !== false);

			if ($theme)
				mso_add_file($fn); // подключаем внешими стилями в HEAD
			elseif ($lazy)
				mso_add_file($fn, true); // подключаем внешими стилями в BODY
			else
				$css_out .= mso_out_css_file($fn, false, false); // получение и обработка CSS из файла
		}

		if ($css_out)
			echo '<style>' . $css_out . '</style>';
	}

	// здесь же выводим css-профиль записи
	// он задан через метаполе
	if (is_type('page') and isset($page) and $page) {
		if ($page_css_profiles = mso_page_meta_value('page_css_profiles', $page['page_meta'])) {

			$fn = $path . $page_css_profiles;

			$theme = (strpos($page_css_profiles, 'theme-') === 0);
			$lazy = (strpos($page_css_profiles, '-lazy') !== false);

			if ($theme)
				mso_add_file($fn); // подключаем внешими стилями в HEAD
			elseif ($lazy)
				mso_add_file($fn, true); // подключаем внешими стилями в BODY
			else {
				if ($css_out = mso_out_css_file($fn, false, false))
					echo '<style>' . $css_out . '</style>';
			}
		}
	}
}

/**
 * функция возвращает полный путь к файлу компонента для указанной опции
 * 
 * @example if ($fn = my_get_component_fn('header_component2', 'menu')) require $fn;
 * @param type $option опция
 * @param type $def_component Компонент по умолчанию
 * @return boolean|string
 */
function my_get_component_fn($option = '', $def_component = '')
{
	// получение опции
	if ($dir = mso_get_option($option, getinfo('template'), $def_component)) {
		if ($fn = mso_fe('components/' . $dir . '/' . $dir . '.php')) return $fn;
		elseif ($fn = mso_fe('components/' . $dir . '/index.php')) return $fn;
	}

	return false; // ничего нет
}

/**
 * функция подключает файлы style.css установленных компонентов
 * 
 * @param array $component_options - названия опций, которыми определяются компоненты в шаблоне
 */
function my_out_component_css($component_options = ['header_component1', 'header_component2', 'header_component3', 'header_component4', 'header_component5', 'footer_component1', 'footer_component2', 'footer_component3', 'footer_component4', 'footer_component5'])
{
	// проходимся по всем заданным опциям
	foreach ($component_options as $option) {
		// и если они определены
		if ($dir = mso_get_option($option, getinfo('template'), false)) {
			mso_add_file('components/' . $dir . '/style.css'); // подключаем внешими стилями
		}
	}
}

/**
 * функция подключает файлы js-файлы компонентов
 * Имя js-файла совпадает и каталогом компонента
 * Также подключается файл _head.php, если есть
 * 
 * @see my_out_component_css()
 * @param type $component_options - названия опций, которыми определяются компоненты в шаблоне 
 */
function my_out_component_js($component_options = ['header_component1', 'header_component2', 'header_component3', 'header_component4', 'header_component5', 'footer_component1', 'footer_component2', 'footer_component3', 'footer_component4', 'footer_component5'])
{
	// проходимся по всем заданным опциям
	foreach ($component_options as $option) {
		if ($dir = mso_get_option($option, getinfo('template'), false)) {
			mso_add_file('components/' . $dir . '/' . $dir . '.js');

			// подключаем файл _head.php если есть
			if ($fn = mso_fe('components/' . $dir . '/_head.php')) require $fn;
		}
	}
}

/**
 * Раняя инициализация компонентов в момент загрузки шаблона
 * Если в подключенном компоненте есть файл _init.php, то он подключается
 */
function my_init_components($component_options = ['header_component1', 'header_component2', 'header_component3', 'header_component4', 'header_component5', 'footer_component1', 'footer_component2', 'footer_component3', 'footer_component4', 'footer_component5'])
{
	foreach ($component_options as $option) {
		if ($dir = mso_get_option($option, getinfo('template'), false)) {
			// подключаем файл _init.php если есть
			if ($fn = mso_fe('components/' . $dir . '/_init.php')) require $fn;
		}
	}
}

/**
 * присваивает опции (для текущего шаблона) значение
 * если опция не содержит заданного значение
 * если $imp = true то принудительно ставим опцию
 */
function my_set_opt($key, $val = '', $imp = false)
{
	if ($imp) {
		mso_add_option($key, $val, getinfo('template'));
	} else {
		if (mso_get_option($key, getinfo('template'), false) != $val)
			mso_add_option($key, $val, getinfo('template'));
	}
}

# end of file
