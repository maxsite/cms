<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
	вывод изображений слайдером/каруселью из заданного каталога
*/

// где выводить записи
$nivo_slider_output = mso_get_option('nivo_slider_output', 'templates', array());

if (!$nivo_slider_output) return; // ничего не отмечено - нигде не показывать


if (!in_array('all', $nivo_slider_output)) // не отмечено выводить везде
{
	if (!in_array(getinfo('type'), $nivo_slider_output)) return;
		elseif (mso_current_paged() > 1) return; // на страницах пагинации не показывать (или показывать?..)
}

$subdir = mso_get_option('nivo_slider_dir', 'templates', false);

if ($subdir === false) return; // не определены опции

if ($subdir == '-template-')  // каталог шаблона
	$imgs = mso_get_path_files(getinfo('template_dir') . 'images/nivo-slider/', getinfo('template_url') . 'images/nivo-slider/');
else
	$imgs = mso_get_path_files(getinfo('uploads_dir') . $subdir . '/', getinfo('uploads_url') . $subdir . '/'); // каталог в uploads

shuffle($imgs); // случайный порядок

$nivo_slider_play = (int) mso_get_option('nivo_slider_play', 'templates', 4000);

echo mso_load_jquery('jquery.nivo.slider.js') . '
<script>
	$(window).load(function() {
		$("div.nivoSlider").nivoSlider({
			controlNav:false, 
			pauseTime:' . $nivo_slider_play . ', 
			prevText: "&lt;", 
			nextText: "&gt;"
		});
	});
</script>
';

// вывод блока слайдера
echo '<div class="image-nivo-slider"><div class="nivoSlider">';
	foreach ($imgs as $img) echo NR . '<img src="' . $img . '" alt="" title="">';
echo '</div></div>';
	

# end file