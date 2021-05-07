<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

mso_head_meta('title', 'Дизайн шаблона'); //  meta title страницы

// класс для фона меню некоторых компонентов header
mso_set_val('comp_header_menu_bgtm1', 'bg-primary700');
mso_set_val('comp_header_menu_bgtm1_1', 'bg-primary800');
mso_set_val('comp_header_menu_bgtm2', 'bg-primary700');

mso_set_val('comp_breadcrumbs_add', ['Дизайн шаблона' => '']); // добавка к «хлебным крошкам»

// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-page-only"><div class="mso-page-content">';

if (!is_login()) {
    echo '<div class="bg-red300 t-white pad30 mar50-tb">Страница доступна только админам</div>';
    echo '</div></div>';
    // конечная часть шаблона
    if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

    return;
}


$f = str_replace(str_replace('\\', '/', getinfo('template_dir')), '', str_replace('\\', '/', __DIR__)) . '/elements.php';

if ($fn = mso_fe($f)) {
	ob_start();
	require($fn);
	$t1 = ob_get_contents();
	ob_end_clean();

	// в файле могут быть свои замены
	$t1 = str_replace('[siteurl]', getinfo('siteurl'), $t1);
	$t1 = str_replace('[templateurl]', getinfo('template_url'), $t1);

	if (function_exists('autotag_simple')) $t1 = autotag_simple($t1);

	// eval(mso_tmpl_prepare($t1, false));

	echo $t1;
}

echo '</div></div>';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end of file
