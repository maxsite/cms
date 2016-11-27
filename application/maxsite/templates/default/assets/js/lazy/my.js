// http://tinynav.viljamis.com адаптивное меню
(function ($, window, i) { $.fn.tinyNav = function (options) { var settings = $.extend({ 'active' : 'selected', 'header' : '', 'label'  : '' }, options); return this.each(function () { i++; var $nav = $(this), namespace = 'tinynav', namespace_i = namespace + i, l_namespace_i = '.l_' + namespace_i, $select = $('<select/>').attr("id", namespace_i).addClass(namespace + ' ' + namespace_i); if ($nav.is('ul,ol')) { var options = ''; $nav .addClass('l_' + namespace_i) .find('a') .each(function () { options += '<option value="' + $(this).attr('href') + '">'; var j; for (j = 0; j < $(this).parents('ul, ol').length - 1; j++) { options += '- '; } options += $(this).text() + '</option>'; }); $select.append(options); var indx = $select.find(':eq(' + $(l_namespace_i + ' li').index($(l_namespace_i + ' li.' + settings.active)) + ')').index(); if ( $(l_namespace_i + ' li').eq(indx).hasClass(settings.active) ) { $select .find(':eq(' + $(l_namespace_i + ' li') .index($(l_namespace_i + ' li.' + settings.active)) + ')') .attr('selected', true); } else { $select.prepend( $('<option/>').text(settings.header).attr('selected', true) ); } $select.change(function () { window.location.href = $(this).val(); }); $(l_namespace_i).after($select); if (settings.label) { $select.before( $("<label/>") .attr("for", namespace_i) .addClass(namespace + '_label ' + namespace_i + '_label') .append(settings.label) ); } } }); }; })(jQuery, this, 0);

$(function(){
	// анимация меню
	$('div.MainMenu li.group')	
		.mouseenter(function()
		{
			// $(this).children('ul').first().hide().stop().fadeIn(400);
			$(this).children('ul').first().hide().stop().slideDown(200);
		})
		
		.mouseleave(function()
		{
			// $(this).children('ul').first().stop(0).fadeOut(400);
			$(this).children('ul').first().stop(0).slideUp(200);
		});
	
	// адаптивное меню
	$('ul.menu').tinyNav({
        active: 'selected',
		header: '☰ Меню',
		// label: '☰',
      });
	
	$('select.tinynav').addClass('hide-desktop visible-tablet-phone w100-tablet');
	$('ul.menu').addClass('hide-tablet-phone');
	
	
	// скролл вверх
	$("body").append("<div id='to_top' title='Вверх' class='hide-print pos-fixed pos10-r pos10-b bg-gray600 t-gray100 cursor-pointer i-arrow-up icon-circle bg-op40'></div>");
	$("#to_top").hide();
	$(window).scroll(function () {if ($(this).scrollTop() > 100) { $("#to_top").fadeIn();} else {$("#to_top").stop(0).fadeOut(); } });
	$("#to_top").click(function() {$("body,html").animate({scrollTop: 0}, 800); return false; }); 


});

