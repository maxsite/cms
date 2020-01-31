<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('header-start');
if ($fn = mso_fe('custom/header-start.php')) require $fn;

if ($fn = mso_fe('custom/header_components.php')) {
	require $fn;
} else {
	$my_component = [
		'header_component1' => 'default_header_component1',
		'header_component2' => 'default_header_component2',
		'header_component3' => 'default_header_component3',
		'header_component4' => 'default_header_component4',
		'header_component5' => 'default_header_component5',
	];

	foreach ($my_component as $option => $def_component) {
		if ($fn = my_get_component_fn($option, mso_get_val($def_component)))
			require $fn;
	}
}

if (function_exists('ushka')) echo ushka('header-end');
if ($fn = mso_fe('custom/header-end.php')) require $fn;

# end of file
