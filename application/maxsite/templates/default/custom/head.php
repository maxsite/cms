<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// themes
// каталог в assets/css/themes/
$designDir = mso_get_option('theme_template', getinfo('template'), 'default');

if ($filesCSS = mso_get_path_files(getinfo('template_dir') . 'assets/css/themes/' . $designDir . '/', '', false, ['css'])) {
    
    foreach($filesCSS as $fn) {
        mso_add_file('assets/css/themes/' . $designDir . '/' . $fn);
    }
}

// lazy
if ($filesCSS = mso_get_path_files(getinfo('template_dir') . 'assets/css/themes/' . $designDir . '/lazy/', '', false, ['css'])) {
    
    foreach($filesCSS as $fn) {
        mso_add_file('assets/css/themes/' . $designDir . '/lazy/' . $fn, true);
    }
}

// css-файл меню тоже в assets/css/themes/
if ($menu_template = mso_get_val('menu_template', '')) {
	mso_add_file('assets/css/themes/' . $designDir . '/menu/' . $menu_template);
} else {
	if ($menu_css = mso_get_option('menu_template', getinfo('template'), 'menu1.css')) {
		mso_add_file('assets/css/themes/' . $designDir . '/menu/' . $menu_css);
	}
}

// шрифты общие в assets/css/fonts
if ($fonts_css = mso_get_option('fonts_template', getinfo('template'), [])) {
	foreach ($fonts_css as $font) {
		mso_add_file('assets/css/fonts/' . $font);
	}
}

# end of file
