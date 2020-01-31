<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// css-файл меню
if ($menu_template = mso_get_val('menu_template', '')) {
	mso_add_file('assets/css/menu/' . $menu_template);
} else {
	if ($menu_css = mso_get_option('menu_template', getinfo('template'), 'menu1.css')) {
		mso_add_file('assets/css/menu/' . $menu_css);
	}
}

// шрифты
if ($fonts_css = mso_get_option('fonts_template', getinfo('template'), [])) {
	foreach ($fonts_css as $font) {
		mso_add_file('assets/css/fonts/' . $font);
	}
}

# end of file
