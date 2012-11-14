$(document).ready(function() {
	$('ul.admin-menu:has(li.admin-menu-selected)').find('li:has(li)').addClass('admin-menu-selected');
	$('table:has(input[name=f_header])').find('td').css('background', 'none');
});