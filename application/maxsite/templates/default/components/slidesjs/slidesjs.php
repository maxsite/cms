<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/

	Слайдер (c) http://slidesjs.com/
	Опции задаются в Slides JS

[slide]
header = заголовок
text = текст с html без переносов
link = ссылка
img = адрес картинки
[/slide]

*/

// где выводить записи
$slidesjs_output = mso_get_option('slidesjs_output', 'templates', array());

if (!$slidesjs_output)  return; // ничего не отмечено - нигде не показывать

if (!in_array('all', $slidesjs_output)) // не отмечено выводить везде
{
	if (!in_array(getinfo('type'), $slidesjs_output)) return;
		elseif (mso_current_paged() > 1) return; // на страницах пагинации не показывать (или показывать?..)
}

// опции слайдера
$slides_def = '
[slide]
header = заголовок1
text = текст с html без переносов
link = http://maxsite.org/
img = TEMPLATE_URL/images/placehold/1140x300.png
[/slide]

[slide]
header = заголовок2
text = текст с html без переносов
link = http://max-3000.com/
img = TEMPLATE_URL/images/placehold/1140x300.png
[/slide]
';

$slides0 = mso_get_option('slidesjs', 'templates', $slides_def);

if (!$slides0) return; // слайды не определены - выходим

$slides0 = str_replace('TEMPLATE_URL/', getinfo('template_url'), $slides0);

// ищем вхождение [slide] ... [slide]
// указываем дефолтные атрибуты полей слайдера
$slides = mso_section_to_array($slides0, '!\[slide\](.*?)\[\/slide\]!is', array('header'=>'', 'text'=>'', 'link'=>'', 'img'=>''));

if (!$slides) return; // нет секций - выходим

$slidesjs_output = mso_get_option('slidesjs_pagination', 'templates', 1) ? 'true' : 'false';
$slidesjs_play = (int) mso_get_option('slidesjs_play', 'templates', 4000);

$slidesjs_easing = '';
$slidesjs_animation_time = '';
$slidesjs_effect = '';
$slidesjs_hoverpause = '';
$slidesjs_randomize = '';

// тип перехода easing
if ($slidesjs_easing = mso_get_option('slidesjs_easing', 'templates', 'linear'))
{
	$slidesjs_easing = 'slideEasing: "' . $slidesjs_easing . '", fadeEasing: "' . $slidesjs_easing . '",';
}

// время перехода
if ($slidesjs_animation_time = mso_get_option('slidesjs_animation_time', 'templates', 1500))
{
	$slidesjs_animation_time = 'slideSpeed: ' . $slidesjs_animation_time . ', fadeSpeed: ' . $slidesjs_animation_time . ',';
}

// эффект перехода: slide или fade
if ($slidesjs_effect = mso_get_option('slidesjs_effect', 'templates', 'slide'))
{
	$slidesjs_effect = 'effect: "' . $slidesjs_effect . '",';
}

// останавливать смену при hover
if (mso_get_option('slidesjs_hoverpause', 'templates', ''))
{
	$slidesjs_hoverpause = 'hoverPause: true,';
}

// случайный порядок
if (mso_get_option('slidesjs_randomize', 'templates', ''))
{
	$slidesjs_randomize = 'randomize: true,';
}

// 
echo mso_load_jquery('slides.min.jquery.js');
echo mso_load_jquery('jquery.easing.js');

echo '
<script>
	$(document).ready(function(){
		$("div.slidesjs").slides({
			currentClass: "slides_current",
			' . $slidesjs_hoverpause . '
			play: ' . $slidesjs_play . ',
			' . $slidesjs_effect . '
			' . $slidesjs_animation_time . '
			' . $slidesjs_easing . '
			' . $slidesjs_randomize . '
			generatePagination: ' . $slidesjs_output . ',
			animationStart: function(current){
				$("div.slide div.r2").animate({
					bottom: -100
				}, 100);
			},
			animationComplete: function(current){
				$("div.slide div.r2").animate({
					bottom: 0
				}, 200);
			},
			slidesLoaded: function() {
				$("div.slide div.r2").animate({
					bottom:0
				}, 200);
			}
		});
	});
</script>
';

// если нет вообще пагинации, то ставим специальный css-класс
if (!mso_get_option('slidesjs_prev_next', 'templates', 1) and  !mso_get_option('slidesjs_pagination', 'templates', 1))
		$class_no_pag = ' no-pagination';
	else
		$class_no_pag = '';
		

// формируем html-код слайдера
?>

<div class="slidesjs<?= $class_no_pag ?>"><div class="wrap">
	<div class="slides_container">
	<?php foreach ($slides as $slide) { ?>
			<div class="slide"><div class="slide-wrap">
				<div class="r1">
				<?= '<a href="' . trim($slide['link']) . '"><img src="' . trim($slide['img']) . '" alt=""></a>' ?>	
				</div>
				
				<?php if ($slide['header'] and $slide['text']) { ?>
				
				<div class="r2">
					<h3><a href="<?= $slide['link'] ?>"><?= trim($slide['header']) ?></a></h3>
					<p><?= trim($slide['text']) ?></p>
				</div>
				<?php } ?>
				
			</div></div>
	<?php } ?>
	</div>
	
	<?php if (mso_get_option('slidesjs_prev_next', 'templates', 1)) { ?>
	<div class="prev-next">
		<a href="#" class="prev"></a>
		<a href="#" class="next"></a>
	</div>
	<?php } ?>
				
</div></div>
