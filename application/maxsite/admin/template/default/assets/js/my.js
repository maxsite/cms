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
	clickElem: "a.admin-menu-section ", 
	foldElem: "ul.admin-submenu", 
	visible: true
	});
	
});
