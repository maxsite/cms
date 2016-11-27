<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('footer-start');

if ($fn = mso_fe('custom/footer-start.php')) require($fn);

if ($fn = mso_fe('custom/footer_components.php')) require($fn);
else
{
	if ($fn = my_get_component_fn('footer_component1', mso_get_val('default_footer_component1'))) require($fn);
	if ($fn = my_get_component_fn('footer_component2', mso_get_val('default_footer_component2'))) require($fn);
	if ($fn = my_get_component_fn('footer_component3', mso_get_val('default_footer_component3'))) require($fn);
	if ($fn = my_get_component_fn('footer_component4', mso_get_val('default_footer_component4'))) require($fn);
	if ($fn = my_get_component_fn('footer_component5', mso_get_val('default_footer_component5'))) require($fn); 
}

if (function_exists('ushka')) echo ushka('footer-end');
if ($fn = mso_fe('custom/footer-end.php')) require($fn);

# end of file