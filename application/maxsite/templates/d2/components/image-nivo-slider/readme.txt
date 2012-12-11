Вывод изображений слайдером/каруселью из заданного каталога

Опции слайдера

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

