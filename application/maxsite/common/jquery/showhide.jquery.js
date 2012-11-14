/**
* jQuery Showhide plugin
* Показывает/скрывает элементы по клику, записывает состояние в куку.
* Для использования необходим фреймворк jQuery и плагин jQuery.cookie (https://github.com/carhartl/jquery-cookie).
* (с) Cuprum, http://cuprum.name
*/

(function($) {
	$.showHide = function(options) {
		var settings = $.extend({
				initVisible: false,
				cookieName: 'cookie',
				cookieExpires: 30,
				cookiePath: '/',
				blockElem: '.block',
				clickElem: '.link',
				blockinElem: '.block-in'
			}, options || {}),
			uniqueArr = function(array) { // функиция удаляет дубликаты из массива, на выходе - массив с обратным порядком элементов, но в данном случае порядок не важен
				var newArr = [], i = array.length, j;
				label:
					while (i--) {
						for (j = 0; j < newArr.length; j++) {
							if (newArr[j] == array[i])
							continue label;
						}
						newArr[newArr.length] = array[i];
					}
				return newArr;
			},
			updateCookie = function(el) {
				var id = el.attr("id"),
				tmp = uniqueArr(data);
				if (settings.initVisible ? el.is(":hidden") : el.is(":visible")) {
					tmp.push(id);
				} else {
					tmp.splice($.inArray(id, tmp), 1);
				}
				data = uniqueArr(tmp); 
				$.cookie(settings.cookieName, data.join("|"), {expires: settings.cookieExpires, path: settings.cookiePath});
			},
			cookie = $.cookie(settings.cookieName),
			data = cookie ? uniqueArr(cookie.split("|")) : [];

		$.each(data, function() {
			settings.initVisible ? $("#" + this).hide() : $("#" + this).show();
		});

		$(settings.blockElem).click(function(e) {
			if ($(e.target).is(settings.clickElem)) {
				var el = $(settings.blockinElem, this); 
				el.toggle();
				updateCookie(el);
			}
			return false;
		});
	}
})(jQuery);