<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
	вывод изображений слайдером/каруселью из заданного каталога
*/

	
$subdir = mso_get_option('default_header_image', 'templates', false);

if ($subdir === false) return; // не определены опции

if ($subdir == '-template-')  // каталог шаблона
	$imgs = mso_get_path_files(getinfo('template_dir') . 'images/headers/', getinfo('template_url') . 'images/headers/');
else
	$imgs = mso_get_path_files(getinfo('uploads_dir') . $subdir . '/', getinfo('uploads_url') . $subdir . '/'); // каталог в uploads

shuffle($imgs); // случайный порядок

echo mso_load_jquery('jquery.nivo.slider.js') . '
<script type="text/javascript">
	$(window).load(function() {
		$("div.nivoSlider").nivoSlider({controlNav:false, pauseTime:4000, prevText: "&lt;", nextText: "&gt;"});
	});
</script>
';

// вывод блока слайдера
echo '<div class="nivoSlider">';
	foreach ($imgs as $img) echo NR . '<img src="' . $img . '" alt="" title="">';
echo '</div>';
	

# end file