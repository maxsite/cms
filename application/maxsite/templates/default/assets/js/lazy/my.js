$(function(){
	$("body").append("<div id='to_top' title='Вверх' class='hide-print hide-phone pos-fixed pos10-r pos10-b bg-gray200 hover-bg-blue t-blue hover-t-white cursor-pointer t25px fas fa-angle-double-up icon-circle trans05-all'></div>");
	$("#to_top").hide();
	$(window).scroll(function () {if ($(this).scrollTop() > 30) { $("#to_top").fadeIn();} else {$("#to_top").fadeOut(); } });
	$("#to_top").click(function() {$("body,html").animate({scrollTop: 0}, 800); return false; });
	
	// меню
	var is_touch = Modernizr.touch;
	var menu = $('ul.menu');
	
	if (is_touch) {
		menu.removeClass('menu-hover');
		menu.addClass('menu-click');
	}
	else
	{
		if ($('ul.menu-tablet > li').css('float') != 'left') {
			menu.removeClass('menu-hover'); 
			menu.addClass('menu-click');
		}
		
		$(window).resize(function() {
			if ($('ul.menu-tablet > li').css('float') != 'left') {
				menu.removeClass('menu-hover');
				menu.addClass('menu-click');
			}
			else {
				menu.addClass('menu-hover');
				menu.removeClass('menu-click');
				$('ul.menu li ul').css('display', 'none');
				$('ul.menu li').removeClass('group-open');
			}
		});
	}
	
	$('nav').on('click', 'ul.menu-click li > a', function(e) {
		var href = $(this).attr("href");
		var ul = $(this).next();
		var li = $(this).parent('li');
		
		if (href === "#") {
			e.preventDefault();
			
			$('ul.menu li.group ul:visible').slideUp(200);
			$('ul.menu li.group').removeClass('group-open');
			
			if ( ul.is(':visible') ) {
				ul.slideUp(200);
				li.removeClass('group-open');
			}
			else {
				ul.stop().slideDown(200);
				li.addClass('group-open');
			}
		}
	});
	
	$('nav').on('mouseenter', 'ul.menu-hover li.group', function(e) {
		/* $(this).children('ul').slideDown(200); */
		$(this).children('ul').fadeIn(200);
	});
	
	$('nav').on('mouseleave', 'ul.menu-hover li.group', function(e) {
		/* $(this).children('ul').hide().slideUp(200); */
		$(this).children('ul').hide().fadeOut(200);
	});
	
	menu.removeClass('menu-no-load');
	
	if (is_touch) 
	{
		menu.removeClass('menu-hover');
		menu.addClass('menu-click');
	}
	
	$('.scrollToFixed').scrollToFixed({
		marginTop: 0
	});
	
});