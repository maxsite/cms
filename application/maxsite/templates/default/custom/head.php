<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// меню
$menu = mso_get_option('menu_template', getinfo('template'), 'menu1.css');
mso_add_file('assets/css/menu/' . $menu);

// шрифты общие в assets/css/fonts
if ($fonts_css = mso_get_option('fonts_template', getinfo('template'), [])) {
	foreach ($fonts_css as $font) {
		mso_add_file('assets/css/fonts/' . $font);
	}
}

# end of file
