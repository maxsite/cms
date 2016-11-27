<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('header-start');

if ($fn = mso_fe('custom/header-start.php')) require($fn);

if ($fn = mso_fe('custom/header_components.php')) require($fn);
else
{
	if ($fn = my_get_component_fn('header_component1', mso_get_val('default_header_component1'))) require($fn);
	if ($fn = my_get_component_fn('header_component2', mso_get_val('default_header_component2'))) require($fn);
	if ($fn = my_get_component_fn('header_component3', mso_get_val('default_header_component3'))) require($fn);
	if ($fn = my_get_component_fn('header_component4', mso_get_val('default_header_component4'))) require($fn);
	if ($fn = my_get_component_fn('header_component5', mso_get_val('default_header_component5'))) require($fn);
}

if (function_exists('ushka')) echo ushka('header-end');
if ($fn = mso_fe('custom/header-end.php')) require($fn);

# end of file