$(function(){
	$('#my-nav-panel').change(function () {
		window.location.href = $(this).val();
	});
	
	$("table.tablesorter").tablesorter();
	
	$.cookie.json = true;
	
	$("li.admin-menu-top").showHide({
		cookieName: "admin-menu",
		time: 400, 
		useID: false, 
		clickElem: "a.admin-menu-section", 
		foldElem: "ul.admin-submenu", 
		visible: true
	});
	
	$("div.update").fadeOut(15000);
	$("div.error").fadeOut(15000);
	
	$("#sh-my-nav-panel").click(function() 
	{
		var panel = $(".my-nav-panel");
		var sh = $("#sh-my-nav-panel");
		
		if (panel.hasClass("js-nav-panel-hide"))
		{
			panel.animate( {
					width: "show",
					opacity: "show",
			}, 600);
			
			panel.css("min-width", "200px");
			
			sh.animate( {
					left: panel.outerWidth() + "px",
			}, 200);
		
			panel.removeClass("js-nav-panel-hide");
		}
		else
		{
			sh.animate( {
					left: "0",
			}, 200);
			
			panel.css("min-width", 0);
			panel.animate( {
					width: "hide",
					opacity: "hide",
			}, 400);
			
			panel.addClass("js-nav-panel-hide");
		}

		return false;
	});

});
