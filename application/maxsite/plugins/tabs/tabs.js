	$(function() {
		var getCookiesFilter = function (cookieNameRegEx) { // фильтрует куки по регулярному выражению
				var allCookies = document.cookie,
					arrayCookies = allCookies.split(/\s*;\s*/),
					cookies = {},
					i,
					max,
					keyValue;
				if (allCookies) 
				{
					for (i = 0, max = arrayCookies.length; i < max; i += 1) 
					{
						keyValue = arrayCookies[i].split("=");
						if (keyValue[0].match(cookieNameRegEx)) 
						{
							cookies[decodeURIComponent(keyValue[0])] = decodeURIComponent(keyValue[1]);
						}
					}
				}
				return cookies;
			},
			objCookies = getCookiesFilter(/tabs_widget_[0-9a-z]+/);

		$.each(objCookies, function(key, value)
		{
			$("." + key + " .elem").eq(value).addClass("tabs-current").siblings().removeClass("tabs-current")
				.parents("div.tabs").find(".tabs-box").hide().eq(value).show();
		});
		
		$(".tabs-nav").on("click", ".elem:not(.tabs-current)", function() 
		{
			var cookieName = $(this).parents(".tabs_widget").attr("class").match(/tabs_widget_[0-9a-z]+/).join(),
				index = $(this).index();
			$(this).addClass("tabs-current").siblings().removeClass("tabs-current")
					.parents("div.tabs").find(".tabs-box").hide().eq(index).fadeIn(300);
			$.cookie(cookieName, index, {expires: 1, path: "/"});
		});
	});