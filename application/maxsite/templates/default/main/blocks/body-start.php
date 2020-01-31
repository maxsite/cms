<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// секция HEAD

if ($fn = mso_fe('custom/head-section.php'))
	require($fn); // подключение HEAD из файла
else
	my_default_head_section(); // подключение через функцию

echo '<body' . ((mso_get_val('body_class')) ? ' class="' . mso_get_val('body_class') . '"' : '') . '>';

mso_hook('body_start');

if (function_exists('ushka')) echo ushka('body_start');
if ($fn = mso_fe('custom/body-start.php')) require $fn;
if (function_exists('ushka')) echo ushka('header-pre');
if ($fn = mso_fe('custom/header-pre.php')) require $fn;

# end of file
