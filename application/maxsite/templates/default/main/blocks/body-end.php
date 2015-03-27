<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ($fn = mso_fe('custom/body-end.php')) require($fn); 

if (function_exists('ushka')) 
{
	echo ushka('google_analytics'); 
	echo ushka('body_end');
} 

mso_hook('body_end'); 

// lazy-загрузка js-файлов
if ($lazy_js = mso_get_path_files(getinfo('template_dir') . 'assets/js/lazy/', getinfo('template_url') . 'assets/js/lazy/', true, array('js')))
{
	foreach($lazy_js as $fn_js)
	{
		echo '<script src="' . $fn_js . '"></script>' . NR;
	}
}

# end of file