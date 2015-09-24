(function( $, window, document, undefined ) {
	'use strict';

	$.fn.fullScreen = function () {
		var $this = this;
		var $body = $("body");
		var $html = $("html");
		var flag = false;
		var $btn = $('<a href="#" class="i-expand" title="Полноэкранный режим"></a>').on("click", function (e) {
			e.preventDefault();

			if(!flag) {
				$(this).removeClass('i-expand').addClass('i-compress').attr("title", "Обычный режим (Esc)");
				flag = true;
			}
			else {
				$(this).removeClass('i-compress').addClass('i-expand').attr("title", "Полноэкранный режим");
				flag = false;
			}

			$(this).parent().toggleClass("fScreen__active");
			$(this).prev().focus();
			$html.toggleClass("my-html-no-scroll");
		});

		this.wrap('<div class="fScreen"></div>') .parent().append($btn) ;
		// $body.toggleClass("bodyShadow");

		$(document).keyup(function (e) {
			if (e.keyCode === 27) {
				$this.parent('.fScreen__active').find('> .i-compress').click();
			}
		});

		return this;
	};
})( jQuery, window, document );
/*---------------------------*/

$(function () {
	$("textarea:not(#f_content)").fullScreen();
});
