<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ($fn = mso_fe('custom/body-end.php')) require($fn);

if (function_exists('ushka')) {
	echo ushka('google_analytics');
	echo ushka('body_end');
}

mso_hook('body_end');

// одиночный assets/css/-lazy.css
if (mso_fe('assets/css/-lazy.css'))
	echo mso_load_style(getinfo('template_url') . 'assets/css/-lazy.css');

// lazy-загрузка css-файлов
if ($lazy_css = mso_get_path_files(getinfo('template_dir') . 'assets/css/lazy/', getinfo('template_url') . 'assets/css/lazy/', true, array('css'))) {

	foreach ($lazy_css as $fn_css) {
		echo '<link rel="stylesheet" href="' . $fn_css . '">';
	}
}

// lazy-загрузка js-файлов
if ($lazy_js = mso_get_path_files(getinfo('template_dir') . 'assets/js/lazy/', getinfo('template_url') . 'assets/js/lazy/', true, array('js'))) {

	foreach ($lazy_js as $fn_js) {
		echo '<script src="' . $fn_js . '"></script>';
	}
}

# end of file
