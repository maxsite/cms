<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	/*
		вывод изображений слайдером/каруселью из заданного каталога
	*/
	
		
	$subdir = mso_get_option('default_header_image', 'templates', false);
	
	if ($subdir === false) return; // не определены опции

	if ($subdir == '-template-')  // каталог шаблона
		$imgs = get_path_files(getinfo('template_dir') . 'images/headers/', getinfo('template_url') . 'images/headers/');
	else
		$imgs = get_path_files(getinfo('uploads_dir') . $subdir . '/', getinfo('uploads_url') . $subdir . '/'); // каталог в uploads

	shuffle($imgs); // случайный порядок
	
	// высота блока: height 250px
	// <script type="text/javascript" src="' . getinfo('template_url') . 'components/js/jquery.nivo.slider.pack.js"></script>
	
	echo mso_load_jquery('jquery.nivo.slider.js') . '
	<script type="text/javascript">
		$(window).load(function() {
			$("#slider-header").nivoSlider({controlNav:false, pauseTime:4000, prevText: "&lt;", nextText: "&gt;"});
		});
	</script>
	';
	
	// вывод блока слайдера
	echo '<div id="slider-header" class="nivoSlider">';
	foreach ($imgs as $img) echo NR . '<img src="' . $img . '" alt="" title="">';
	echo '</div>';


	/*
	все настройки слайдера
	
    $('#slider').nivoSlider({
        effect:'random', // Specify sets like: 'fold,fade,sliceDown'
        slices:15, // For slice animations
        boxCols: 8, // For box animations
        boxRows: 4, // For box animations
        animSpeed:500, // Slide transition speed
        pauseTime:3000, // How long each slide will show
        startSlide:0, // Set starting Slide (0 index)
        directionNav:true, // Next & Prev navigation
        directionNavHide:true, // Only show on hover
        controlNav:true, // 1,2,3... navigation
        controlNavThumbs:false, // Use thumbnails for Control Nav
        controlNavThumbsFromRel:false, // Use image rel for thumbs
        controlNavThumbsSearch: '.jpg', // Replace this with...
        controlNavThumbsReplace: '_thumb.jpg', // ...this in thumb Image src
        keyboardNav:true, // Use left & right arrows
        pauseOnHover:true, // Stop animation while hovering
        manualAdvance:false, // Force manual transitions
        captionOpacity:0.8, // Universal caption opacity
        prevText: 'Prev', // Prev directionNav text
        nextText: 'Next', // Next directionNav text
        beforeChange: function(){}, // Triggers before a slide transition
        afterChange: function(){}, // Triggers after a slide transition
        slideshowEnd: function(){}, // Triggers after all slides have been shown
        lastSlide: function(){}, // Triggers when last slide is shown
        afterLoad: function(){} // Triggers when slider has loaded
    });

	*/
	

# end file