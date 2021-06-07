<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// Функция возвращает полный путь к файлу
// если файла нет, то возвращается false
// если второй параметр == false, используется каталог текущего шаблона
// if ($fn = mso_fe('stock/page_out/page-out.php')) require $fn;
function mso_fe($file, $dir = false)
{
	if ($dir === false) $dir = getinfo('template_dir');

	$file = $dir . $file;

	if (file_exists($file) and is_file($file))
		return $file;
	else
		return false;
}

// поиск файла либо в каталоге шаблона, либо в shared-каталоге
// файл указывается относительно каталога шаблона/shared-каталога
// приоритет имеет файл в шаблоне, после в shared
// если файл не найден, то возвращается $default
// иначе полный путь, годный для require
// if ($fn = mso_find_ts_file('type/page_404/page_404.php')) require $fn;
function mso_find_ts_file($fn, $default = false)
{
	$fn1 = getinfo('template_dir') . $fn; // путь в шаблоне
	$fn2 = getinfo('shared_dir') . $fn; // путь в shared

	// если указан хук custom_ts_file, то вначале его обрабатываем
	// хук должен вернуть полное имя файла 
	if (mso_hook_present('custom_ts_file') and $fn3 = mso_hook('custom_ts_file', $fn) and file_exists($fn3)) return $fn3;
	elseif (file_exists($fn1)) return $fn1; // если шаблонный
	elseif (file_exists($fn2)) return $fn2; // нет, значит shared
	else return $default;
}

// Функция возвращает полный путь к файлу, который следует подключить в index.php шаблона
// использовать вместо старого варианта выбора type-файла
// 	if ($fn = mso_dispatcher()) require $fn;
function mso_dispatcher()
{
	global $MSO;

	// тип данных
	$type = getinfo('type');

	// для rss используются отдельное подключение
	if (is_feed()) {
		// ищем файл в шаблоне или shared
		if ($f = mso_find_ts_file('type/feed/' . $type . '.php'))
			return $f;
		else
			return mso_find_ts_file('type/feed/home.php');
	}

	// в зависимости от типа данных подключаем нужный файл

	// на page_404 может быть свой хук. Тогда ничего не подключаем
	if (
		$type == 'page_404'
		and mso_hook_present('custom_page_404')
		and mso_hook('custom_page_404')
	) {
		return false;
	} elseif ($type == 'page_404') {
		// страница не найдена, формируем по сегменту
		$seg = mso_strip(mso_segment(1));
		$fn = 'type/' . $seg . '/' . $seg . '.php';

		// !!! пробный вариант, который непонятно как сработает !!!
		// создаем новый тип по сегменту — это позволит использовать is_type() для этих типов
		// если не будетвыявлено ошибок, то оставить (внесено: 2018-11-20 ver. 100.6)
		if (mso_find_ts_file($fn)) $MSO->data['type'] = $seg;
	} else {
		$fn = 'type/' . $type . '/' . $type . '.php';
	}

	if ($f = mso_find_ts_file($fn))
		return $f;
	else
		return mso_find_ts_file('type/page_404/page_404.php');
}

// возвращает jquery-script
// в $path можно указать http-путь к файлу
function mso_load_jquery($plugin = '', $path = '')
{
	global $MSO;

	if (!isset($MSO->js['jquery'][$plugin])) {
		// еще нет включения этого файла
		$MSO->js['jquery'][$plugin] = '1';

		if ($plugin) {
			// это какой-то плагин
			if ($path)
				return '<script src="' . $path . $plugin . '"></script>';
			else
				return '<script src="' . getinfo('common_url') . 'jquery/' . $plugin . '"></script>';
		} else {
			// это основная библиотека, которая вызывается в секции HEAD
			// если есть в шаблоне assets/js/jquery.min.js то подключаем только его

			if (mso_fe('assets/js/jquery.min.js'))
				$fj = getinfo('template_url') . 'assets/js/jquery.min.js';
			else
				$fj = getinfo('common_url') . 'jquery/jquery.min.js';

			// режим загрузки jQuery
			// если head, то грузим как есть
			// если это BODY, то прописываем только preload
			$mode = mso_get_option('jquery_load', 'general', 'head');

			if ($mode == 'head') {
				return '<script src="' . $fj . '"></script>';
			} elseif ($mode == 'body') {
				return '<link rel="preload" href="' . $fj . '" as="script">';
			}
		}
	}
}

// подключаемый type_foreach-файл
// должен находиться в каталоге шаблона /type_foreach/
// либо в /take/ — это второй альтернативный каталог (они работают одновременно)
// каталог take можно переопределить через mso_set_val('type_foreach_alt_dir', 'my_take');
function mso_page_foreach($type_foreach_file = false)
{
	global $MSO;

	// при первом обращении занесем сюда все файлы из шаблонного type_foreach
	// чтобы потом результат считывать из масива, а не по file_exists
	static $files = false;
	static $files_alt = false;

	$MSO->data['type_foreach_file'] = $type_foreach_file; // помещаем в $MSO вызываемый тип

	// альтернативный каталог
	$alt = mso_get_val('type_foreach_alt_dir', 'take') . '/';

	// можно переопределить файлы через type_foreach/_general.php
	if (file_exists(getinfo('template_dir') . 'type_foreach/general.php'))
		include(getinfo('template_dir') . 'type_foreach/general.php');

	// можно поменять type_foreach-файл через хук
	$type_foreach_file = mso_hook('type-foreach-file-general', $type_foreach_file);

	if ($type_foreach_file) {
		if ($files === false) {
			// старый вариант с helper('directory')
			// $CI = & get_instance();
			// $CI->load->helper('directory');
			// $files = directory_map(getinfo('template_dir') . 'type_foreach/', true); // только в type_foreach
			// $files_alt = directory_map(getinfo('template_dir') . $alt, true);

			$files = glob(getinfo('template_dir') . 'type_foreach/' . '*.php');
			$files = array_map(function ($a) {
				return pathinfo($a, PATHINFO_BASENAME);
			}, $files);

			$files_alt = glob(getinfo('template_dir') .  $alt . '*.php');
			$files_alt = array_map(function ($a) {
				return pathinfo($a, PATHINFO_BASENAME);
			}, $files_alt);

			if (!$files) $files = [];
			if (!$files_alt) $files_alt = [];
		}

		$find_file = $type_foreach_file . '.php'; // какой файл ищем

		if (in_array($find_file, $files)) {
			// есть в type_foreach/
			return getinfo('template_dir') . 'type_foreach/' . $find_file;
		} elseif (in_array($find_file, $files_alt)) {
			// есть в альтернативном каталоге
			return getinfo('template_dir') . $alt . $find_file;
		} else {
			// нет файла
			// если есть хук type-foreach-file
			if (mso_hook_present('type-foreach-file')) {
				// получим его значение
				// он должен возвращать либо полный путь к файлу, либо false
				if ($out = mso_hook('type-foreach-file', $type_foreach_file))
					return $out; // указан путь
				else
					return false; // вернул false
			} else {
				// нет хука type-foreach-file
				return false;
			}
		}
	}

	return false;
}

/**
 * Вывод содержимого опции из текстового файла
 *
 * @param  string $component - компонент
 * @param  string $file - текстовый файл 
 * @param  boolean $quot - выполнять замены для ini-файла
 *
 * @return string
 */
function mso_get_component_option($component, $file = '_default.txt', $quot = true)
{
	$fn = getinfo('template_dir') . 'components/' . $component . '/' . $file;

	if (file_exists($fn)) {
		$text = file_get_contents($fn);

		if ($quot) $text = str_replace('"', '_QUOT_', $text);

		return $text;
	}

	return '';
}

/**
 * Получить их ini-файла все секции и преобразовать их в массив [type][key]=default
 * подходящий для mso_get_option(... $default_values)
 * 
 * используется для того, чтобы передать в mso_get_option дефолтные значения ключей прямо из ini-файла
 * 
 * $optionsINI = mso_get_defoptions_from_ini(dirname(__DIR__) . '/options.ini');
 * $b1 = mso_get_option($component . '-home_block1', getinfo('template'), '', $optionsINI);
 */
function mso_get_defoptions_from_ini($file)
{
	if (!file_exists($file)) return false;

	$arrayIni = file_get_contents($file);

	if ($arrayIni) {
		ob_start();
		eval('?>' . $arrayIni . '<?php ');
		$arrayIni = ob_get_contents();
		ob_end_clean();

		$arrayIni = parse_ini_string($arrayIni, true);
	}

	if (!$arrayIni) return false;

	$arrayOut = [];
	
	// для замены %CURRENTDIR% в options_key
	$CURRENTDIR = basename(dirname($file));
	
	// формируем массив из options_type[ options_key ] = default
	foreach ($arrayIni as $val) {

		$type = $val['options_type'] ?? 'general';

		if ($type == '%TEMPLATE%') $type = getinfo('template');

		$default = $val['default'] ?? '';
		$default = str_replace('_QUOT_', '"', $default);
		$default = str_replace('`', '"', $default);
		$default = str_replace('_NBSP_', ' ', $default);
		$default = str_replace('_NR_', "\n", $default);

		$val['options_key'] = str_replace('%CURRENTDIR%', $CURRENTDIR, $val['options_key']);
		
		$arrayOut[$type][$val['options_key']] = $default;
	}

	return $arrayOut;
}

# end of file
