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
	
	$("div.update").fadeOut(3000);
	
	if ($.cookie('sh-my-nav-panel-off') == 1)
	{
		$(".my-nav-panel").addClass("b-hide");
		$("#sh-my-nav-panel").css("left", 0);
	}
	
	$("#sh-my-nav-panel").click(function() 
	{
		$("#sh-my-nav-panel").hide();
		$(".my-nav-panel").toggleClass("b-hide");
		
		if ($(".my-nav-panel").outerWidth() < 200)
		{
			$("#sh-my-nav-panel").css("left", 0);
			$.cookie('sh-my-nav-panel-off', 1);
		}
		else
		{
			$("#sh-my-nav-panel").css("left", $(".my-nav-panel").outerWidth() );
			$.cookie('sh-my-nav-panel-off', 0);
		}
		
		$("#sh-my-nav-panel").show();
		
		return false;
	});

});
