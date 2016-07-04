$(document).ready(function(){

	//$.cookie.json = true;

	// разворачиваем/сворачиваем форму создания новой рубрики
	$('button.add').on('click', function(){
		var form = $('form.new-cat .form');

		// $('div.msg').hide(2000);

		if( $(form).is(':visible') )
		{
			$(form).slideUp(500);
		}
		else
		{
			$('div.form').slideUp(500);
			$('ul.rubrics a.edit.current').removeClass('current');

			$(form).slideDown(500);
		}

		return false;
	});

	// разворачиваем/сворачиваем форму редактирования рубрики
	$('ul.rubrics a.edit').on('click', function(){
		var form = $(this).parent('div.li').nextAll('div.form').eq(0);

		// $('div.msg').hide();

		if( $(this).hasClass('current') )
		{
			$(form).slideUp(500);
			$(this).removeClass('current');
		}
		else
		{
			$('div.form').slideUp(500);
			$('ul.rubrics a.edit.current').removeClass('current');

			$(form).slideDown(500);
			$(this).addClass('current');
		}

		return false;
	});
	

	// удаление рубрики
	$('button.do-remove').on('click', function(){
		$('div.msg').slideUp(2000);
		
		if (confirm( cat_msg.delete_confirm ))
		{
			
			var cat_id = $(this).attr('data-id');
			
			$.ajax({
				url: rubrics_ajax,
				data: 'session_id=' + $('[name="session_id"]').val() + '&category_id=' + cat_id + '&do=delete',
				type: 'POST', // тип запроса
				dataType: 'json',
				success: function( res ){
					// alert(cat_msg.delete_ok);
					window.location = current_url;
				}
			});
		}
		return false;
	});


	// сохранение исправлений настроек рубрики
	$('button.do-save').on('click', function(){
		var id = $(this).attr('data-id'),
				vals = $('[name^="cat['+id+']"]').serialize();

		$.ajax({
			url: rubrics_ajax,
			data: vals + '&session_id=' + $('[name="session_id"]').val() + '&category_id=' + id + '&do=update',
			type: 'POST', // тип запроса
			dataType: 'json',
			success:function( res ){
				if( res ){
					if( res.reload )
					{
						// location.reload();
						window.location = current_url;
					}
					else
					{
						$('[id="cat['+id+'][title]"]').removeClass('current');
						$('[id="cat['+id+'][msg]"]').html( res.msg ).show();

						if( res.ok )
						{
							$('[id="cat['+id+'][msg]"]').fadeOut(5000);

							$('[id="cat['+id+'][title]"]').text(res.name);
							$('[id="cat['+id+'][page]"]').attr('href', res.url).text(res.slug);
							$('[id="cat['+id+'][slug]"]').val(res.slug);
						}
					}
				}
				else
				{
					alert(cat_msg.save_error);
				}
			}
		});

		return false;
	});
});
