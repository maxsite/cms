$(document).ready(function(){
	sessUp();

	setInterval(function() {
		sessUp();
	}, 1000 * 60 * 1);

	$("#attach_img").attr("data-url", upload_path);
	$("#attach_img").fileupload({
		sequentialUploads: true,
		dataType: "json",
		add: function (e, data)
		{
			var resize_img = ($("input[name=f_userfile_resize]").attr("checked") == 'checked') ? 1 : 0;
			var create_mini = ($("input[name=f_userfile_mini]").attr("checked") == 'checked') ? 1 : 0;
			var use_watermark = ($("input[name=f_userfile_water]").attr("checked") == 'checked') ? 1 : 0;

			data.formData = {
							current_dir: current_dir,
							resize_img: resize_img,
							resize_img_size: $("input[name=f_userfile_resize_size]").val(),
							create_mini: create_mini,
							create_mini_size: $("input[name=f_userfile_mini_size]").val(),
							image_mini_type: $("select[name=f_mini_type]").val(),
							use_watermark: use_watermark,
							watermark_type: $("select[name=f_water_type]").val()
							};
			data.submit();
		},
		fail: function (e, data)
		{
			alert("error - " + data.textStatus);
		},
		start: function (e)
		{
			$(".attach .loader").show();
		},
		stop: function (e)
		{
			$(".attach .loader").hide();
		},
		done: function (e, data)
		{
			$.each(data.result.attach, function (index, file)
			{
				if( file.error )
				{
					$(".attach .uploaded").append("<div class=\"pic err\" name=\""+file.name+"\"><span><b>Ошибка</b>: Файл «<i>"+file.name+"</i>» загружен не был. От сервера получено сообщение - «"+file.error+"»</span><button class=\"drop\" onclick=\"$( 'div[name="+addr+"] ).detach();return false;\">Удалить сообщение</button></div>");
				}
			});
			//$("#all-files-update").click();
			$.post(
				update_path,
				{
					dir: current_dir
				},
				function(data)
				{
					$("#all-files-result").html(data);
					' . $lightbox . '
					localStorage.clear();
				}
			);
		}
	});

});


function sessUp()
{
	$.ajax({
		type: "POST",
		url: upload_path,
		data: {"_session": sess, current_dir: current_dir},
		success: function(response)
		{
			if( response.error )
			{
				alert("Внимание! Имеются проблемы на сервере! «" + response.error + "»\n Будьте осторожны при попытке сохранить результаты заполнения формы!");
			}
		},
		error: function(xhr, str){
			alert("Внимание! По какой-то причине сервер не доступен!\n Будьте осторожны при попытке сохранить результаты заполнения формы!");
		}
	});
}
