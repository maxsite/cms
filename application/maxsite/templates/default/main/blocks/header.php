<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('header-start');
if ($fn = mso_fe('custom/header-start.php')) require $fn;

if ($fn = mso_fe('custom/header_components.php')) {
	require $fn;
} else {
	$components = my_get_components('header');
	
	foreach($components as $fn) {
		require $fn;
	}
}

if (function_exists('ushka')) echo ushka('header-end');
if ($fn = mso_fe('custom/header-end.php')) require $fn;

# end of file
