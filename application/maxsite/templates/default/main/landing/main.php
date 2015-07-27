<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * HTML-структура шаблона
 *
 */

if ($fn = mso_fe('main/blocks/_start.php')) require($fn);

if ($fn = mso_fe('custom/head-section.php')) 
	require($fn); // подключение HEAD из файла
else 
	my_default_head_section(); // подключение через функцию

echo '<body' . ((mso_get_val('body_class')) ? ' class="' . mso_get_val('body_class') . '"' : '') . '>';

global $CONTENT_OUT; 
echo $CONTENT_OUT; 

if (function_exists('ushka')) echo ushka('google_analytics'); 

// lazy-загрузка js-файлов
if ($lazy_js = mso_get_path_files(getinfo('template_dir') . 'assets/js/lazy/', getinfo('template_url') . 'assets/js/lazy/', true, array('js')))
{
	foreach($lazy_js as $fn_js)
	{
		echo '<script src="' . $fn_js . '"></script>' . NR;
	}
}

?></body></html><?php if ($fn = mso_fe('main/blocks/_end.php')) require($fn) ?>