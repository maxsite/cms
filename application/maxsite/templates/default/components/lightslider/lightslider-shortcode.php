<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

/*
Слайдер lightslider в виде шорткода

[html][lightslider 1]
[js]
item: 2,
auto: true,
loop: true,
speed: 400,
[/js]

[slide]
<h3 class="bg-blue pad20 t-center">1</h3>
[/slide]

[slide]
<h3 class="bg-yellow pad20 t-center">2</h3>
[/slide]

[slide]
<h3 class="bg-red t-white pad20 t-center">3</h3>
[/slide]

[slide]
<h3 class="bg-gray800 t-white pad20 t-center">4</h3>
[/slide]
[/lightslider][/html]


Подключать в custom/my-template.php

if ($fn = mso_fe('components/lightslider/lightslider-shortcode.php')) require_once($fn);

*/

function lightslider_shortcode($attr)
{
	$slides0 = $attr[2];

	if (!$slides0) return ''; // опции не определены - выходим

	// замена в тексте
	$slides0 = str_replace('TEMPLATE_URL/', getinfo('template_url'), $slides0);
	$slides0 = str_replace('SITE_URL/', getinfo('siteurl'), $slides0);

	// ищем вхождение [slide] ... [slide]
	$slides = mso_section_to_array($slides0, '!\[slide\](.*?)\[\/slide\]!is', [], false, true);

	if (!$slides) return ''; // нет секций - выходим

	// опции слайдера свои
	$options = mso_section_to_array($slides0, '!\[options\](.*?)\[\/options\]!is', []);

	if (isset($options[0])) $options = $options[0];

	$num = trim($attr[1]);

	$options_def = [
		'block_start' => '<div class="mar30-tb">',
		'block_end' => '</div>',
		'element' => '.lightslider' . $num, // элемент для jQuery (с точкой)
		'ul_class' => 'lightslider' . $num, // class для ul
	];

	$options = mso_merge_array($options, $options_def);

	// в секции [js] все параметры слайдера в родном js-формате 
	$js = mso_section_to_array($slides0, '!\[js\](.*?)\[\/js\]!is', array(), false, true);

	// данные в первом элементе
	$js = (isset($js[0])) ? $js[0] : '';

	$out = $options['block_start'] . '<ul class="inline ' . $options['ul_class'] . '">';

	foreach ($slides as $slide) {
		if (!$slide) continue; // не указан текст

		$out .=  '<li>' . trim($slide) . '</li>';
	}

	$out .=  '</ul>' . $options['block_end'];

	$out .=  mso_load_script(getinfo('template_url') . 'components/lightslider/lightslider.js');

	$out .= '<script>$(document).ready(function() { $("' . $options["element"] . '").lightSlider({' . $js . '}); });</script>';

	return $out;
}

function lightslider_shortcode_css($attr)
{
	mso_add_file('components/lightslider/style.css');

	return $attr;
}


// включение шорткода
mso_shortcode_add('lightslider', 'lightslider_shortcode');
mso_hook_add('head_css', 'lightslider_shortcode_css');

# end of file
