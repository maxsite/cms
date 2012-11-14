<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
	# Форма - работает совместно с edit и new
	
	
	# до 
	$do = '
	<script>
	
		function shsh()
		{
			if ($("input.f_header").attr("sh") == 0)
			{
				$("input.f_header").attr("sh", 1);
				$("input.f_header").attr("title", "' . t('Для перехода в полноэкранный режим нажмите F2') . '");
				
				$("td.page_info").show();
				$("div.block_page").show();
				$("div.admin-menu").show();
				$("div.admin-header").show();
				$("div.admin-footer").show();
				$("h1").show();
				$("p.ret-to-pages").show();
				$("div.update").show();
				$("div.error").show();
				
				$("div.admin-content").css("margin-left", $("div.admin-content").attr("sh-margin-left"));
				$("div.admin-content").css("background", $("div.admin-content").attr("sh-background"));
				$("div.admin-content").css("border-left", $("div.admin-content").attr("sh-border-left"));
				
			}
			else
			{
				$("input.f_header").attr("sh", 0);
				
				$("input.f_header").attr("title", "' . t('Для возврата в обычный режим нажмите F2') . '");
				
				$("td.page_info").hide();
				$("div.block_page").hide();
				$("div.admin-menu").hide();
				$("div.admin-header").hide();
				$("div.admin-footer").hide();
				$("h1").hide();
				$("p.ret-to-pages").hide();
				$("div.update").hide();
				$("div.error").hide();
				
				$("div.admin-content").attr("sh-margin-left", $("div.admin-content").css("margin-left"));
				$("div.admin-content").css("margin-left", "0");
				
				$("div.admin-content").attr("sh-background", $("div.admin-content").css("background"));
				$("div.admin-content").css("background", "white");
				
				$("div.admin-content").attr("sh-border-left", $("div.admin-content").css("border-left"));
				$("div.admin-content").css("border-left", "none");
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
	});

	$(function(){
		function select_page_type()
		{
			var page_type_id = $("div.page_post_type input:checked").val();
			var page_type_obj = ' . $page_type_js_obj . ';
			var page_type_name = "";

			$.each(page_type_obj, function(key, val)
			{
				if(page_type_id == val)
				{
					page_type_name = key;
				}
			});

			$("div.page_meta div.page_meta_block").hide();
			
			var d = "div.page_meta div." + page_type_name;

			$(d).show();
		}

		select_page_type();

		$("div.page_post_type input").click(function()
		{
			select_page_type();
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
		
	});
	
	
	// фоновое сохранение
	$(function(){	
		$("#bsave").click(function()
		{
			$("div.bsave_result").html("' . t('Сохранение...') . '");
			
			$.post(
				"' . getinfo('ajax') . base64_encode('admin/plugins/admin_page/bsave-post-ajax.php') . '",
				{
					params: $("#form_editor").serialize(),
					id: ' . mso_segment(3) . ',
				},
				function(data)
				{
					// Здесь мы получаем данные,
					$("div.bsave_result").html(data);
				}
			);
		});
	});
	
	
	</script>
	
	<table class="new_or_edit">
	<tr>
		<td class="editor_and_meta">
		<input type="text" value="' . $f_header . '" name="f_header" class="f_header" title="' . t('Для перехода в полноэкранный режим нажмите F2') . '">' . $fses;
	
	# после
	$posle = '
			<div class="page_status">
				
				<a style="display: block; float: right;" href="'. $MSO->config['site_admin_url'] 
						. 'files" target="_blank" class="page_files">' . t('Страница «Загрузки»') . '</a>
						
				<p class="page_status">
					<label><input name="f_status[]" type="radio" ' . $f_status_publish . ' value="publish" id="f_status_publish"> ' . t('Опубликовать') . '</label> 
					<label><input name="f_status[]" type="radio" ' . $f_status_draft . ' value="draft" id="f_status_draft"> ' . t('Черновик') . '</label> 
					<label><input name="f_status[]" type="radio" ' . $f_status_private . ' value="private" id="f_status_private"> ' . t('Личное') . '</label>
				</p>
									
				' . $f_return . '
				<input type="submit" name="' . $name_submit . '" value="' . t('Готово') . '" class="wymupdate"> ' . $f_bsave . ' <span class="autosave-editor"></span>
			</div>
			
			<div>' 
				. mso_hook('admin_page_form_q_files') 
				. mso_hook('admin_page_form_pre_all_meta') 
				. '<div class="block_page page_meta">
					<h3>' . t('Дополнительные поля') . '</h3>
					' . $all_meta . '
					' . mso_hook('admin_page_form_add_all_meta') . '
				</div>
			</div>
			<input type="submit" name="' . $name_submit . '" value="' . t('Готово') . '" class="wymupdate">
			
		</td>
		
		<td class="page_info">
			' . mso_hook('admin_page_form_add_block_1') . '
			<div class="block_page page_all_cat">
				<h3>' . t('Рубрика') . '</h3>
				<div class="cat_page">' . $all_cat . '</div>
			</div>
			
			<div class="block_page page_tags">
				<h3>' . t('Метки (через запятую)') . '</h3>
				<p><textarea name="f_tags" id="f_tags" style="width: 99%;">' . $f_tags . '</textarea></p>
				' . $f_all_tags . '
			</div>
			
			<div class="block_page page_slug">
				<h3>' . t('Короткая ссылка') . '</h3>
				<p><input type="text" value="' . $f_slug . '" name="f_slug" style="width: 99%;"></p>
			</div>

			<div class="block_page page_discus">
				<h3>' . t('Обсуждение') . '</h3>
				<p><label><input name="f_comment_allow" type="checkbox" ' . $f_comment_allow . '> ' . t('Разрешить комментирование') . '</label></p>
				<p><label><input name="f_feed_allow" type="checkbox" ' . $f_feed_allow . '> ' . t('Публикация в RSS') . '</label></p>
				<!--p><input name="f_ping_allow" type="checkbox" ' . $f_ping_allow . '> ' . t('Разрешить пинг') . '</p-->
			</div>

			<div class="block_page page_date">
				<h3>' . t('Дата публикации') . '</h3>
				<p><label><input name="f_date_change" id="f_date_change" type="checkbox" ' . $f_date_change . '> ' . t('Изменить дату') . '</label> 
				
				<a href="#" style="font-size: 1.2em; text-decoration: none;" id="set_current_time" title="' . t('Установить текущее время компьютера') . '">&#9685;</a>
				
				</p>
				<p>' . $date_y . ' - ' . $date_m . ' - ' . $date_d . '</p>
				<p>' . $time_h . ' : ' . $time_m . ' : ' . $time_s . '</p>
				<p><em>' . $date_time . '</em></p>
			</div>
			
			<div class="block_page page_post_type">
				<h3>' . t('Тип страницы') . '</h3>
				' . $all_post_types . '
			</div>			
			
			<div class="block_page page_password">
				<h3>' . t('Пароль для чтения') . '</h3>
				<p><input type="text" value="' . $f_password . '" name="f_password" style="width: 99%;"></p>
			</div>
			
			<div class="block_page page_menu_order">
				<h3>' . t('Порядок') . '</h3>
				<p><input type="text" value="' . $page_menu_order . '" name="f_menu_order" style="width: 99%;"></p>
			</div>
				
			<div class="block_page page_all_parent">
				<h3>' . t('Родительская страница') . '</h3>
				<p>' . $all_pages . '</p>
			</div>
			
			<div class="block_page page_all_users">
				<h3>' . t('Автор') . '</h3>
				<p>' . $all_users . '</p>
			</div>
		</td>
	</tr>
	</table>
	';

?>