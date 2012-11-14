$(function() {

	$('table:has(input[name=f_header])').addClass('addPost');

	$('div.admin-h-menu a').wrapInner('<span></span>');

	$('p.info:empty').removeClass('info');
	$('input[type=text], input[type=file]').addClass('inputText');
	$('input[type=submit], input[type=button]').addClass('inputSubmit');

	$('li:has(table.page)').addClass('noli');

	if (window.location.href.indexOf('/admin/plugins') !=-1) {
		//$('span[style*="green"]').parents('tr').addClass('activated');
		$('#pagetable td span:not(".gray")').parents('tr').addClass('activated');
	}

	//$('p.info').next('p.info').css("color", "red");
	//alert($('p.info').next('p.info').html());

	pInfoCount = $('p.info').next('p.info').length;
	var pInfoText = '';

	if ( pInfoCount >= 1 ) {
		$('p.info').each(function() {
			pInfoText += '<p>' + $(this).text() + '</p>';
		})
		$('p.info:first').before('<div id="infos">'+pInfoText+'</div>');
		$('#infos').addClass('info');
		$('p.info').hide();
	};

});