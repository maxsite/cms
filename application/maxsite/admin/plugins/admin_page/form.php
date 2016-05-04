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
				
				$("div.my-nav-panel").show();
				
				/*
				$("div.header").show();
				$("div.sidebar").show();
				$("div.footer").show();
				*/
				
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
				/*
				$("div.header").hide();
				$("div.footer").hide();
				$("div.sidebar").hide();
				*/
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
	});

	$(function(){
		
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
					id: ' . ( mso_segment(3) ?  mso_segment(3) : 0 ) . '
				},
				function(data)
				{
					// Здесь мы получаем данные,
					$("div.bsave_result").html(data);
				}
			);
		});
	});

	$(function(){
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

	</script>
	
	<div class="new_or_edit">
		<div class="page-header">
		<input value="' . $f_header . '" name="f_header" class="f_header" title="' . t('Заголовок записи') . '" placeholder="' . t('Укажите заголовок') . '"></div>'
		. $fses;
	
	# после
	$posle = '
			<div class="page_status">
						
				<p class="page_status">
					<label><input name="f_status[]" type="radio" ' . $f_status_publish . ' value="publish" id="f_status_publish"> ' . t('Опубликовать') . '</label> 
					<label><input name="f_status[]" type="radio" ' . $f_status_draft . ' value="draft" id="f_status_draft"> ' . t('Черновик') . '</label> 
					<label><input name="f_status[]" type="radio" ' . $f_status_private . ' value="private" id="f_status_private"> ' . t('Личное') . '</label>
					
					<a class="page_files" href="'. $MSO->config['site_admin_url'] . 'files" target="_blank" >' . t('Загрузки') . '</a>
					
				</p>
									
				' . $f_return . '
				<button type="submit" name="' . $name_submit . '" class="button i-save">' . t('Сохранить') . '</button> ' . $f_bsave . ' <span class="autosave-editor"></span>
			</div>
			
			<div class="page_meta_container">' 
				. mso_load_script(getinfo('plugins_url'). 'tabs/tabs.js')

				. mso_hook('admin_page_form_pre_all_meta')
				
				. '<div class="mso-tabs_widget mso-tabs_widget_000">
				
						<div class="mso-tabs">
							<ul class="mso-tabs-nav">
								<li class="mso-tabs-elem mso-tabs-current i i-cat"><span>' . t('Рубрики и метки') . '</span></li>
								<li class="mso-tabs-elem i i-meta"><span>' . t('Основные опции') . '</span></li>
								<li class="mso-tabs-elem i i-other"><span>' . t('Дополнительные') . '</span></li>' 
								. $custom_meta_i . '
								<li class="mso-tabs-elem i i-files"><span>' . t('Файлы') . '</span></li>
							</ul>

							<div class="mso-tabs-box mso-tabs-visible all-cat">' 
								. '<div class="page_cat">'
									. $all_cat 
								. '</div>'
								. '<div class="page_tags">
										<h4>' . t('Метки (через запятую)') . '</h4>
										<textarea name="f_tags" id="f_tags">' . $f_tags . '</textarea>
										' . $f_all_tags . '
									</div>
									'
							. '</div>
							
							
							<div class="mso-tabs-box all-meta">' . $all_meta . mso_hook('admin_page_form_add_all_meta') . '</div>
							
							<div class="mso-tabs-box other fform">
								' . mso_hook('admin_page_form_add_block_1') . '
								
								
								<table class="page page-responsive">
								<colgroup style="width: 25%;"><tbody>
								<tr>
									<td><strong>' . t('Короткая ссылка') . '</strong></td>
									<td><input type="text" value="' . $f_slug . '" name="f_slug" class="f_slug" title="' . t('Короткая ссылка') . '"></td>
								</tr>
								<tr>
									<td><strong>' . t('Тип страницы') . '</strong></td>
									<td>' . $all_post_types . '</td>
								</tr>
								<tr>
									<td><strong>' . t('Обсуждение') . '</strong></td>
									<td><label><input name="f_comment_allow" type="checkbox" ' . $f_comment_allow . '> ' . t('Разрешить комментирование') . '</label> &nbsp;&nbsp;<label><input name="f_feed_allow" type="checkbox" ' . $f_feed_allow . '> ' . t('Публикация в RSS') . '</label>
									</td>
								</tr>
								<tr>
									<td><strong>' . t('Пароль для чтения') . '</strong></td>
									<td><input type="text" value="' . $f_password . '" name="f_password"></td>
								</tr>
								<tr>
									<td><strong>' . t('Порядок') . '</strong></td>
									<td><input type="number" value="' . $page_menu_order . '" name="f_menu_order"></td>
								</tr>
								<tr>
									<td><strong>' . t('Автор') . '</strong></td>
									<td>' . $all_users . '</td>
								</tr>
								<tr>
									<td><strong>' . t('Родительская страница') . '</strong></td>
									<td>' . $all_pages . '</td>
								</tr>
								<tr>
									<td><strong>' . t('Дата публикации') . '</strong></td>
									<td><input name="f_date_change" id="f_date_change" type="checkbox" ' . $f_date_change . '> ' . t('Изменить дату') . '
									
									<a href="#" style="font-size: 1.5em; text-decoration: none;" id="set_current_time" title="' . t('Установить текущее время компьютера') . '">&#9685;</a>
									
									<br>' . $date_y . ' ' . $date_m . ' ' . $date_d . '
										&nbsp;&nbsp; — &nbsp;&nbsp;' . $time_h . ' : ' . $time_m . ' : ' . $time_s . '
										<br><em>' . $date_time . '</em></td>
								</tr>
								</tbody>
								</table>
								' . mso_hook('admin_page_form_add_block_2') . '
							
							</div><!-- /div.mso-tabs-box.tabs-other -->
							
							' . $custom_meta . '
							
							<div class="mso-tabs-box all-files">' . $all_files . '</div>
							
							
						</div>
					
					
				</div>
			</div>
			
			<button type="submit" name="' . $name_submit . '" class="button i-save">' . t('Сохранить') . '</button>
	
	</div><!-- /div.new_or_edit -->
	';
	
	// $posle = 'posle</div><!-- /div.new_or_edit -->';
	//$do = 'do';

?>