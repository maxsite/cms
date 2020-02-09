<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('footer-start');
if ($fn = mso_fe('custom/footer-start.php')) require $fn;

if ($fn = mso_fe('custom/footer_components.php')) {
	require $fn;
} else {
	$components = my_get_components('footer');
	
	foreach($components as $fn) {
		require $fn;
	}
}

if (function_exists('ushka')) echo ushka('footer-end');
if ($fn = mso_fe('custom/footer-end.php')) require $fn;

# end of file
