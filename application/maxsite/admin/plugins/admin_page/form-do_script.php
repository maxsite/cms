<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// script, который подключаем перед формой 
$do_script = '
<script>
function shsh()
{
	if ($("input.f_header").attr("sh") == 0)
	{
		$("input.f_header").attr("sh", 1);
		$("input.f_header").attr("title", "' . t('Для перехода в полноэкранный режим нажмите F2') . '");
		
		$("div.my-nav-panel").show();
		$("#sh-my-nav-panel").show();
		$("div.update").show();
		$("div.error").show();
		$("h1").show();
		$("p.ret-to-pages").show();
		$("div.content").css("margin-left", $("div.content").attr("sh-margin-left"));
		$("div.content").css("background", $("div.content").attr("sh-background"));
		$("div.content").css("border-left", $("div.content").attr("sh-border-left"));
		$("div.all").css("background", $("div.all").attr("sh-background"));
		$("#f_content").css("height", $("#f_content").attr("sh-height"));
	}
	else
	{
		$("input.f_header").attr("sh", 0);
		$("input.f_header").attr("title", "' . t('Для возврата в обычный режим нажмите F2') . '");
		$("div.my-nav-panel").hide();
		$("#sh-my-nav-panel").hide();
		$("div.update").hide();
		$("div.error").hide();
		$("h1").hide();
		$("p.ret-to-pages").hide();
		$("div.content").attr("sh-margin-left", $("div.content").css("margin-left"));
		$("div.content").css("margin-left", "0");
		$("div.content").attr("sh-background", $("div.content").css("background"));
		$("div.content").css("background", "white");
		$("div.content").attr("sh-border-left", $("div.content").css("border-left"));
		$("div.content").css("border-left", "none");
		$("#f_content").attr("sh-height", $("#f_content").css("height"));
		$("#f_content").css("height", 600);
		$("div.all").attr("sh-background", $("div.all").css("background"));
		$("div.all").css("background", "white");
	}
}
	
$(function(){
	
	$("form textarea").keydown(function(eventObject)
	{
		if (eventObject.which == 113) // F2
		{
			shsh();
		}
	});
	
	$("input.f_header").keydown(function(eventObject)
	{
		if (eventObject.which == 113) // F2
		{
			shsh();
		}
	});
	
	$("#set_current_time").click(function()
	{
		var d = new Date();
		
		$("select[name=f_date_y]").val(d.getFullYear());
		$("select[name=f_date_m]").val(1 + d.getMonth());
		$("select[name=f_date_d]").val(d.getDate());
		$("select[name=f_time_h]").val(d.getHours());
		$("select[name=f_time_m]").val(d.getMinutes());
		$("select[name=f_time_s]").val(d.getSeconds());
		$("#f_date_change").attr("checked", "checked");

		return false;
	});	
	
	// фоновое сохранение
	$("#bsave").click(function()
	{
		$("div.bsave_result").html("' . t('Сохранение...') . '");
		
		$.post(
			"' . getinfo('ajax') . base64_encode('admin/plugins/admin_page/bsave-post-ajax.php') . '",
			{
				params: $("#form_editor").serialize(),
				id: ' . ( mso_segment(3) ?  mso_segment(3) : 0 ) . '
			},
			function(data)
			{
				// Здесь мы получаем данные,
				$("div.bsave_result").html(data);
				$("div.bsave_result").fadeIn(1000);
				$("div.bsave_result").fadeOut(5000);
				
			}
		);
	});
	
	function select_page_type() {
		var page_type_id = +$(".mso-tabs-box.other input:radio:checked").val(),
			page_type_obj = ' . $page_type_js_obj . ',
			page_meta_block = $(".page_meta_block"),
			checked_type;

		for (var key in page_type_obj) {
			if (page_type_obj[key] === page_type_id) {
				checked_type = key;
			}
		}

		page_meta_block.each(function() {
			var number_classes = $(this).attr("class").split(" ").length;

			if (number_classes == 1) {
				return;
			} else {
				if ($(this).hasClass(checked_type)) {
					$(this).show();
				} else {
					$(this).hide();
				}
			}
		});
	}

	select_page_type();

	$(".mso-tabs-box.other input:radio").click(function() {
		select_page_type();
	});
});
</script>' . mso_load_script(getinfo('plugins_url'). 'tabs/tabs.js');

# end of file