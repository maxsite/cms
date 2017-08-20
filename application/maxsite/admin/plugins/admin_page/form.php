<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
// Форма - работает совместно с edit и new и возвращает $do_script $do $posle
/* 
вывод с editor_markitup по такой схеме:

$do_script

<form method="post" enctype="multipart/form-data" id="form_editor">
	$do
	<textarea>
	$posle
</form>
*/

// script, который подключаем перед формой $do_script
// вынесен в отдельный файл ради удобства
require($MSO->config['admin_plugins_dir'] . 'admin_page/form-do_script.php');

# до 
// div.new_or_edit общий контейнер: открывается в $do, закрывается в $posle
$do = mso_form_session('f_session_id') . '<div class="new_or_edit">
<div class="page-header"><input value="' . $f_header . '" name="f_header" class="f_header" title="' . t('Заголовок записи (нажмите Enter, чтобы сохранить запись)') . '" placeholder="' . t('Укажите заголовок') . '"></div>'

. mso_hook('admin_page_form_pre_all_meta')

. '<div class="mso-tabs_widget mso-tabs_widget_000">
	<div class="mso-tabs">
		<ul class="mso-tabs-nav">
			<li class="mso-tabs-elem mso-tabs-current i-edit"><span>' . t('Текст') . '</span></li>
			<li class="mso-tabs-elem i-cat"><span>' . t('Рубрики и метки') . '</span></li>
			<li class="mso-tabs-elem i-meta"><span>' . t('Основные опции') . '</span></li>
			<li class="mso-tabs-elem i-other"><span>' . t('Дополнительные') . '</span></li>' 
			. $custom_meta_i . '
		</ul>

		<div class="mso-tabs-box mso-tabs-visible tabs_editor"><!-- Редактор -->';
	
# после
$posle = '
			<div class="page_status">
				<p class="page_status">
					<label><input name="f_status[]" type="radio" ' . $f_status_publish . ' value="publish" id="f_status_publish"> ' . t('Опубликовать') . '</label> 
					<label><input name="f_status[]" type="radio" ' . $f_status_draft . ' value="draft" id="f_status_draft"> ' . t('Черновик') . '</label> 
					<label><input name="f_status[]" type="radio" ' . $f_status_private . ' value="private" id="f_status_private"> ' . t('Личное') . '</label>
				</p>
				
			</div>
		</div>
		
		
		<div class="mso-tabs-box tabs_bordered"><!-- Рубрики и метки -->
			<div class="page_cat">' . $all_cat . '</div>
			<div class="page_tags">
				<h4>' . t('Метки (через запятую)') . '</h4>
				<textarea name="f_tags" id="f_tags">' . $f_tags . '</textarea>' 
				. $f_all_tags . '
			</div>
				
		</div>
		
		
		<div class="mso-tabs-box tabs_bordered"><!-- Основные опции -->
			<div class="all-meta">' . $all_meta . mso_hook('admin_page_form_add_all_meta') . '</div>
		</div>
		
		
		<div class="mso-tabs-box tabs_bordered"><!-- Дополнительные -->
			<div class="all-other">' 
			. mso_hook('admin_page_form_add_block_1') . '
	
				<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">
					<p class="w25 w100-tablet bold">' . t('Короткая ссылка') . '</p>
					<div class="w75 w100-tablet"><input type="text" value="' . $f_slug . '" name="f_slug" class="f_slug" title="' . t('Короткая ссылка') . '">
				</div></div></div>
				
				<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">
					<p class="w25 w100-tablet bold">' . t('Тип страницы') . '</p>
					<div class="w75 w100-tablet">' . $all_post_types . '
				</div></div></div>

				<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">
					<p class="w25 w100-tablet bold">' . t('Обсуждение') . '</p>
					<div class="w75 w100-tablet"><label><input name="f_comment_allow" type="checkbox" ' . $f_comment_allow . '> ' . t('Разрешить комментирование') . '</label> &nbsp;&nbsp;<label><input name="f_feed_allow" type="checkbox" ' . $f_feed_allow . '> ' . t('Публикация в RSS') . '</label>
				</div></div></div>
				
				<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">
					<p class="w25 w100-tablet bold">' . t('Пароль для чтения') . '</p>
					<div class="w75 w100-tablet"><input type="text" value="' . $f_password . '" name="f_password">
				</div></div></div>
				
				<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">
					<p class="w25 w100-tablet bold">' . t('Порядок') . '</p>
					<div class="w75 w100-tablet"><input type="number" value="' . $page_menu_order . '" name="f_menu_order">
				</div></div></div>
				
				<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">
					<p class="w25 w100-tablet bold">' . t('Автор') . '</p>
					<div class="w75 w100-tablet">' . $all_users . '
				</div></div></div>
				
				<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">
					<p class="w25 w100-tablet bold">' . t('Родительская страница') . '</p>
					<div class="w75 w100-tablet">' . $all_pages . '
				</div></div></div>
				
				<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">
					<p class="w25 w100-tablet bold">' . t('Дата публикации') . '</p>
					<div class="w75 w100-tablet"><input name="f_date_change" id="f_date_change" type="checkbox" ' . $f_date_change . '> ' . t('Изменить дату') . '
					
					<a href="#" style="font-size: 1.5em; text-decoration: none;" id="set_current_time" title="' . t('Установить текущее время компьютера') . '">&#9685;</a>
					
					<br>' . $date_y . ' ' . $date_m . ' ' . $date_d . '
						&nbsp;&nbsp; — &nbsp;&nbsp;' . $time_h . ' : ' . $time_m . ' : ' . $time_s . '
						<br><em>' . $date_time . '</em>
				</div></div></div>'
				
				. mso_hook('admin_page_form_add_block_2') . '
			</div>
		</div>
		
		' . $custom_meta . '
		
		
	</div><!-- /div.mso-tabs -->

		
</div><!-- div.mso-tabs_widget.mso-tabs_widget_000 -->

' . $f_return . '
<button type="submit" name="' . $name_submit . '" class="button i-save">' . t('Сохранить') . '</button> ' . $f_bsave . $f_bfiles_upload . ' <span class="autosave-editor mar10-l t90"></span><div class="bsave_result"></div>
				
<div class="all-files">' . $all_files . '</div>
	
</div><!-- /div.new_or_edit -->
	';
	
# end of file