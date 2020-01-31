<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('footer-start');
if ($fn = mso_fe('custom/footer-start.php')) require $fn;

if ($fn = mso_fe('custom/footer_components.php')) {
	require $fn;
} else {
	$my_component = [
		'footer_component1' => 'default_footer_component1',
		'footer_component2' => 'default_footer_component2',
		'footer_component3' => 'default_footer_component3',
		'footer_component4' => 'default_footer_component4',
		'footer_component5' => 'default_footer_component5',
	];

	foreach ($my_component as $option => $def_component) {
		if ($fn = my_get_component_fn($option, mso_get_val($def_component)))
			require $fn;
	}
}

if (function_exists('ushka')) echo ushka('footer-end');
if ($fn = mso_fe('custom/footer-end.php')) require $fn;

# end of file
