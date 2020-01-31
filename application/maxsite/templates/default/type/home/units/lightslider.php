<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * Слайдер lightslider
 * v. 17-01-2020
 * (c) http://sachinchoolur.github.io/lightslider/
 * http://sachinchoolur.github.io/lightslider/settings.html
 */

/*
Юнит подключает lightslider.min.js (опционно) путь относительно каталога шаблона
И прописывает js-код слайдера с нужными опциями

[unit]
file = lightslider.php
element = .lightslider
js_file = components/lightslider/lightslider.js
loop = 1
pager = 1
auto = 1
rtl = 0
mode = slide
item = 1
speed = 400
pause = 2000
controls = 1
vertical = 0
verticalHeight = 400
adaptiveHeight = 0
slideMargin = 10
[/unit]

*/

// здесь задаём true/false, числа и строки как положено 
$def = [
	'load_js' => true,
	'element' => '.lightslider',
	'js_file' => 'components/lightslider/lightslider.js',
	'loop' => true,
	'pager' => true,
	'auto' => false,
	'rtl' => false,
	'mode' => 'slide',
	'item' => 3,
	'speed' => 400,
	'pause' => 2000,
	'slideMargin' => 10,
	'controls' => true,
	'vertical' => false,
	'verticalHeight' => 300,
	'adaptiveHeight' => false,	
	'addClass' => '',	
];

$options = mso_merge_array($UNIT, $def);

if ($options['js_file']) echo mso_load_script(getinfo('template_url') . $options['js_file']);

?>
<script>
$(document).ready(function() { $("<?= $options['element'] ?>").lightSlider({
loop: <?= ($options['loop']) ? 'true' : 'false' ?>,
pager: <?= ($options['pager']) ? 'true' : 'false' ?>,
auto: <?= ($options['auto']) ? 'true' : 'false' ?>,
rtl: <?= ($options['rtl']) ? 'true' : 'false' ?>,
mode: "<?= $options['mode'] ?>",
item: <?= $options['item'] ?>,
speed: <?= $options['speed'] ?>,
pause: <?= $options['pause'] ?>,
controls: <?= ($options['controls']) ? 'true' : 'false' ?>,
vertical: <?= ($options['vertical']) ? 'true' : 'false' ?>,
verticalHeight: <?= $options['verticalHeight'] ?>,
adaptiveHeight: <?= ($options['adaptiveHeight']) ? 'true' : 'false' ?>,
slideMargin: <?= $options['slideMargin'] ?>,
responsive : [{ breakpoint:760, settings: { item:2 } }, { breakpoint:480, settings: { item:1 }}],
addClass : "<?= $options['addClass'] ?>",
}); });
</script>
