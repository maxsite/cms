! function($)
{
$.fn.pursuingNav = function(options)
{
var element, height, is_pursuing, offsetTop, preSTop, stick, userAgent;
return null == options && (options = {}), userAgent = '',
	is_pursuing = !/android|iphone|ipad/i.test(userAgent), element = this,
	height = options.height || element.outerHeight(), myclass = options.myclass || "myfixed", offsetTop = element.offset()
	.top, stick = height + offsetTop, preSTop = $(document).scrollTop(),
	is_pursuing ? (element.css(
	{
		position: "absolute",
		top: offsetTop
	}), $(window).on("scroll", function()
	{
		var invisibleHeight, sTop;
		return sTop = $(document).scrollTop(), options.height || (height =
				element.outerHeight()), offsetTop = element.offset().top, stick =
			height + offsetTop, sTop >= 0 && (sTop > preSTop ? sTop === offsetTop ?
				(invisibleHeight = stick - preSTop - height, invisibleHeight > height &&
					(invisibleHeight = height), element.css(
					{
						position: "absolute",
						top: sTop - invisibleHeight
					}).addClass(myclass)) : sTop > stick && element.css(
				{
					position: "absolute",
					top: sTop - height
				}).addClass(myclass) : preSTop > sTop && offsetTop >= sTop && element.css(
				{
					position: "fixed",
					top: 0
				}).removeClass("myfix")), preSTop = sTop
	})) : element.css(
	{
		position: "fixed",
		top: offsetTop
	})
}
}(jQuery);