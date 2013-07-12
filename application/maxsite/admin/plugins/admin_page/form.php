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
				
				$("div.header").show();
				$("div.sidebar").show();
				$("div.footer").show();
				
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
				
				$("div.header").hide();
				$("div.footer").hide();
				$("div.sidebar").hide();
				
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
				<button type="submit" name="' . $name_submit . '" class="i save">' . t('Готово') . '</button> ' . $f_bsave . ' <span class="autosave-editor"></span>
			</div>
			
			<div class="page_meta_container">' 
				. mso_load_jquery('jquery.cookie.js')
				. mso_load_script(getinfo('plugins_url'). 'tabs/tabs.js')

				. mso_hook('admin_page_form_pre_all_meta')
				
				. '<div class="tabs_widget tabs_widget_000">
				
						<div class="tabs">
							<ul class="tabs-nav">
								<li class="elem tabs-current i i-cat"><span>' . t('Рубрики и метки') . '</span></li>
								<li class="elem i i-meta"><span>' . t('Дополнительные поля') . '</span></li>
								<li class="elem i i-other"><span>' . t('Прочее') . '</span></li>
								<li class="elem i i-files"><span>' . t('Файлы') . '</span></li>
							</ul>
							<div class="clearfix"></div>
							<div class="tabs-box tabs-visible all-cat">' 
								. '<div class="page_cat">'
									. $all_cat 
								. '</div>'
								. '<div class="page_tags">
										<h3>' . t('Метки (через запятую)') . '</h3>
										<textarea name="f_tags" id="f_tags">' . $f_tags . '</textarea>
										' . $f_all_tags . '
									</div>
									<div class="break"></div>
									'
							. '</div>
							
							
							<div class="tabs-box all-meta">' . $all_meta . mso_hook('admin_page_form_add_all_meta') . '</div>
							
							<div class="tabs-box other fform">
								' . mso_hook('admin_page_form_add_block_1') . '
								
								<p><label class="fwrap"><span class="ffirst ftitle">Короткая ссылка:</span><span><input type="text" value="' . $f_slug . '" name="f_slug" class="f_slug" title="' . t('Короткая ссылка') . '"></span></label></p>
								
								<p><span class="ffirst ftitle ftop">' . t('Тип страницы:') . '</span><span>' . $all_post_types . '</span></p>
								
								<p><span class="ffirst ftitle">Обсуждение:</span>
									<label><input name="f_comment_allow" type="checkbox" ' . $f_comment_allow . '> ' . t('Разрешить комментирование') . '</label>

									<label><input name="f_feed_allow" type="checkbox" ' . $f_feed_allow . '> ' . t('Публикация в RSS') . '</label>
								</p>

								<p><label class="fwrap"><span class="ffirst ftitle">' . t('Пароль для чтения:') . '</span><span><input type="text" value="' . $f_password . '" name="f_password"></span></label></p>
								
								
								<p><label class="fwrap"><span class="ffirst ftitle">' . t('Порядок:') . '</span><span><input type="number" value="' . $page_menu_order . '" name="f_menu_order"></span></label></p>
									
								<p><label class="fwrap"><span class="ffirst ftitle">' . t('Автор:') . '</span><span>' . $all_users . '</span></label></p>
								
								<p class="page_all_parent"><label class="fwrap"><span class="ffirst ftitle">' . t('Родительская страница:') . '</span><span>' . $all_pages . '</span></label></p>
								
								<p class="ends">
									<span class="ffirst ftitle ftop">' . t('Дата публикации:') . '</span>
								
									<span><input name="f_date_change" id="f_date_change" type="checkbox" ' . $f_date_change . '> ' . t('Изменить дату') . '
									
									<a href="#" style="font-size: 1.5em; text-decoration: none;" id="set_current_time" title="' . t('Установить текущее время компьютера') . '">&#9685;</a>
									
									<br>' . $date_y . ' ' . $date_m . ' ' . $date_d . '
										&nbsp;&nbsp; — &nbsp;&nbsp;' . $time_h . ' : ' . $time_m . ' : ' . $time_s . '
										<br><em>' . $date_time . '</em>
									</span>
								</p>
							
							</div><!-- /div.tabs-box.tabs-other -->
							
							<div class="tabs-box all-files">' . $all_files . '</div>
							
						</div>
					
					
				</div>
			</div>
			
			<button type="submit" name="' . $name_submit . '" class="i save">' . t('Сохранить') . '</button>
	
	</div><!-- /div.new_or_edit -->
	';
	
	// $posle = 'posle</div><!-- /div.new_or_edit -->';
	//$do = 'do';

?>