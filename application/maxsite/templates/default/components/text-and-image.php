<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// выводим только на главной
if (!is_type('home')) return;

// получим опции компонента
$text = mso_get_option('component_text_and_image_text', 'templates', '');
$img = mso_get_option('component_text_and_image_image', 'templates', '');
$align = mso_get_option('component_text_and_image_align', 'templates', 'left');

// выводим
echo '
<div class="component-text-and-image">
	<div class="component-text-and-image-wrap">'
	. '<img class="' . $align . '" src="' . $img . '" alt="" title="">'
	. $text 
	. '<div class="clearfix"></div></div><!-- div class=component-text-and-image-wrap -->
</div><!-- div class=component-text-and-image -->';