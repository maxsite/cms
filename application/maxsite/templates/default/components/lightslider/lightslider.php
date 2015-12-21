<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/
	
	Слайдер lightslider
	
	(c) http://sachinchoolur.github.io/lightslider/
		http://sachinchoolur.github.io/lightslider/settings.html
*/


// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option('lightslider_rules_output', getinfo('template'), '')))
{
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

// опции слайдера дефолтные
$_def = '
[options]
block_start = <div class="layout-center-wrap"><div class="layout-wrap mar10-t pad20-rl">
block_end = </div></div>
[/options]

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
';

$slides0 = mso_get_option('lightslider', getinfo('template'), $_def);

if (!$slides0) return; // опции не определены - выходим

// ищем вхождение [slide] ... [slide]
$slides = mso_section_to_array($slides0, '!\[slide\](.*?)\[\/slide\]!is', array(), false, true);

if (!$slides) return; // нет секций - выходим

// замена в тексте
$slides0 = str_replace('TEMPLATE_URL/', getinfo('template_url'), $slides0);
$slides0 = str_replace('SITE_URL/', getinfo('siteurl'), $slides0);

// опции слайдера свои
$options = mso_section_to_array($slides0, '!\[options\](.*?)\[\/options\]!is', array());

if (isset($options[0])) $options = $options[0];

$options_def = array(
	'block_start' => '<div class="layout-center-wrap"><div class="layout-wrap mar30-tb pad20-rl">',
	'block_end' => '</div></div>',
	'element' => '.component-lightslider', // элемент для jQuery (с точкой)
	'ul_class' => 'component-lightslider', // class для ul
);

$options = mso_merge_array($options, $options_def);

// в секции [js] все параметры слайдера в родном js-формате 
$js = mso_section_to_array($slides0, '!\[js\](.*?)\[\/js\]!is', array(), false, true);

// данные в первом элементе
$js = (isset($js[0])) ? $js[0] : '';

echo $options['block_start'] . '<ul class="' . $options['ul_class'] . '">';

foreach ($slides as $slide) 
{
	if (!$slide) continue; // не указан текст
	echo '<li>' . trim($slide) . '</li>';
}

echo '</ul>' . $options['block_end'];

echo mso_load_script(getinfo('template_url') . 'components/lightslider/lightslider.min.js');

echo '<script>$(document).ready(function() { $("' . $options["element"] . '").lightSlider({' . $js . '}); });</script>';


# end of file