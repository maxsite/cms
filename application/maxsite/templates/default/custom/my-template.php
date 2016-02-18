<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
// простой демо-пример
// [class t-red bold]text page[/class]

mso_shortcode_add('class', 'my_class');

function my_class($attr)
{
	return  '<span class="' . $attr[1] . '">'. $attr[2] . '</span>';
}
*/

if ($fn = mso_fe('components/lightslider/lightslider-shortcode.php'))
{
	require_once($fn);
	
	mso_shortcode_add('lightslider', 'lightslider_shortcode');
	
	mso_hook_add('head_css', 'lightslider_shortcode_css');
}


# end of file