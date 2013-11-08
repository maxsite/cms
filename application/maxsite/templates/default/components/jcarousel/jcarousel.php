<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
Слайдер

Опции задаются в JCarousel

[slide]
text = текст с html без переносов
link = ссылка
img = адрес картинки
[/slide]

*/

// где выводить записи
$jcarousel_output = mso_get_option('jcarousel_output', 'templates', array());

if (!$jcarousel_output)  return; // ничего не отмечено - нигде не показывать

if (!in_array('all', $jcarousel_output)) // не отмечено выводить везде
{
	if (!in_array(getinfo('type'), $jcarousel_output)) return;
		elseif (mso_current_paged() > 1) return; // на страницах пагинации не показывать (или показывать?..)
}


// опции слайдера
$slides_def = '
[slide]
text = текст с html без переносов
link = http://maxsite.org/
img = TEMPLATE_URL/images/placehold/220x300.png
[/slide]

[slide]
text = текст с html без переносов
link = http://max-3000.com/
img = TEMPLATE_URL/images/placehold/220x300.png
[/slide]
';


$slides0 = mso_get_option('jcarousel', 'templates', $slides_def);

if (!$slides0) return; // слайды не определены - выходим

$slides0 = str_replace('TEMPLATE_URL/', getinfo('template_url'), $slides0);

// ищем вхождение [slide] ... [slide]
// указываем дефолтные атрибуты полей слайдера
$slides = mso_section_to_array($slides0, '!\[slide\](.*?)\[\/slide\]!is', array('text'=>'', 'link'=>'', 'img'=>''));

if (!$slides) return; // нет секций - выходим

echo mso_load_jquery('jquery.jcarousel.js');
echo mso_load_jquery('jquery.easing.js');


if ($jcarousel_autoscroll = mso_get_option('jcarousel_autoscroll', 'templates', 2000))
{
	$jcarousel_autoscroll = '.jcarouselAutoscroll({"interval": ' . $jcarousel_autoscroll . '})';
}
else
{
	$jcarousel_autoscroll = '';
}

$jcarousel_wrap = mso_get_option('jcarousel_wrap', 'templates', 'last');

$jcarousel_animation = (int) mso_get_option('jcarousel_animation', 'templates', 600);

$jcarousel_easing = mso_get_option('jcarousel_easing', 'templates', 'linear');

if ($jcarousel_pagination = mso_get_option('jcarousel_pagination', 'templates', 0))
{
	$jcarousel_pagination = '$(".jcarousel-pagination").jcarouselPagination();';
}
else
{
	$jcarousel_pagination = '';
}

if ($jcarousel_stop_on_hover = mso_get_option('jcarousel_stop_on_hover', 'templates', 0))
{
	$jcarousel_stop_on_hover = 'jcarouselBlock.hover(function () {
		jcarouselBlock.jcarouselAutoscroll("stop");
	},function () {
		jcarouselBlock.jcarouselAutoscroll("start");
	});';
}
else
{
	$jcarousel_stop_on_hover = '';
}




// http://sorgalla.com/jcarousel/docs/

echo '
<script>
$(function() {
	var jcarouselBlock = $(".jcarousel");

	jcarouselBlock.jcarousel({
		"list": ".jcarousel-list",
		"animation": 
			{
			"duration": ' . $jcarousel_animation . ',
			"easing": "' . $jcarousel_easing . '",
			},
		"wrap": "' . $jcarousel_wrap . '", 
		})' . $jcarousel_autoscroll . ';
	
	$(".prev").jcarouselControl({
        target: "-=1"
    });

    $(".next").jcarouselControl({
        target: "+=1"
    });

	' . $jcarousel_stop_on_hover . '
	' . $jcarousel_pagination . '
});
</script>
';


// формируем html-код слайдера
?>
<div class="jcarousel-component"><div class="wrap">
	<div class="jcarousel">
		<ul class="jcarousel-list">
		<?php 
		
			foreach ($slides as $slide) 
			{
				if (!$slide['img']) continue; // не указана картинка
				
				$a1 = $a2 = '';
				
				if ($slide['link']) 
				{
					$a1 = '<a href="' . trim($slide['link']) . '">';
					$a2 = '</a>';
				}
				
				if ($slide['text']) 
				{
					$slide['text'] = '<p>' . trim($slide['text']) . '</p>';
				}
				
				echo '<li>' . $a1 . '<img src="' . $slide['img'] . '">' . $slide['text'] . $a1 . '</li>';
			}
		?>
		</ul>
	</div>
	
	<a href="#" class="prev">&lsaquo;</a><a href="#" class="next">&rsaquo;</a>
	
	<?php 
		if ($jcarousel_pagination) echo '<div class="jcarousel-pagination"></div>';
	?>
	
</div></div>
