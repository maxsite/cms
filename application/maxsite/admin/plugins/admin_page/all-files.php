<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

/*
	результат отдаём в $all_files
*/

$upload_div = '';
$upload_btn = '';
$current_dir = 'tempfiles';
if ($n = mso_segment(3)) // указан N номер записи
{
	if (is_numeric($n))
		$current_dir = '_pages/' . $n;
}

if ( mso_check_allow('admin_files') ) # Если не разрешено загружать файлы, то и нечего показывать панель загрузки и ссылку на загрузки.
{
	// размер
	$resize_images = (int) mso_get_option('resize_images', 'general', 600);
	// миниатюра
	$size_image_mini = (int) mso_get_option('size_image_mini', 'general', 150);
	// тип миниатюры
	$image_mini_type = mso_get_option('image_mini_type', 'general', 1);
	// водяной знак
	$use_watermark = mso_get_option('use_watermark', 'general', 0);
	$watermark_type = mso_get_option('watermark_type', 'general', 1);

	if ($resize_images < 1) $resize_images = 600;
	if ($size_image_mini < 1) $size_image_mini = 150;

	$upload_div = '
		<div id="all-files-upload-panel">
			<div class="upload_file">

				<h2>' . t('Загрузка файлов') . '</h2>
				<p>' . t('Для загрузки файлов выставьте необходимые опции, нажмите кнопку «Обзор» и выберите один или несколько файлов.') . '.</p>
				<p><label><input type="checkbox" name="f_userfile_resize" checked="checked" value=""> ' . t('Для изображений изменить размер до') . '</label>
					<input type="text" name="f_userfile_resize_size" style="width: 50px" maxlength="4" value="' . $resize_images . '"> ' . t('px (по максимальной стороне).') . '</p>

				<p><label><input type="checkbox" name="f_userfile_mini" checked="checked" value=""> ' . t('Для изображений сделать миниатюру размером') . '</label>
					<input type="text" name="f_userfile_mini_size" style="width: 50px" maxlength="4" value="' . $size_image_mini . '"> ' . t('px (по максимальной стороне).') . ' <br><em>' . t('Примечание: миниатюра будет создана в каталоге') . ' <strong>uploads/' . $current_dir . 'mini</strong></em></p>

				<p>' . t('Миниатюру делать путем:') . ' <select name="f_mini_type">
				<option value="1"'.(($image_mini_type == 1)?(' selected="selected"'):('')).'>' . t('Пропорционального уменьшения') . '</option>
				<option value="2"'.(($image_mini_type == 2)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) по центру') . '</option>
				<option value="3"'.(($image_mini_type == 3)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с левого верхнего края') . '</option>
				<option value="4"'.(($image_mini_type == 4)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с левого нижнего края') . '</option>
				<option value="5"'.(($image_mini_type == 5)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с правого верхнего края') . '</option>
				<option value="6"'.(($image_mini_type == 6)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с правого нижнего края') . '</option>
				<option value="7"'.(($image_mini_type == 7)?(' selected="selected"'):('')).'>' . t('Уменьшения и обрезки (crop) в квадрат') . '</option>
				</select>

				<p><label><input type="checkbox" name="f_userfile_water" value="" '
					. ((file_exists(getinfo('uploads_dir') . 'watermark.png')) ? '' : ' disabled="disabled"') 
					. ($use_watermark ? (' checked="checked"') : (''))
					. '> ' . t('Для изображений установить водяной знак') . '</label>
					<br><em>' . t('Примечание: водяной знак должен быть файлом <strong>watermark.png</strong> и находиться в каталоге') . ' <strong>uploads</strong></em></p>

				<p>' . t('Водяной знак устанавливается:') . ' <select name="f_water_type">
				<option value="1"'.(($watermark_type == 1)?(' selected="selected"'):('')).'>' . t('По центру') . '</option>
				<option value="2"'.(($watermark_type == 2)?(' selected="selected"'):('')).'>' . t('В левом верхнем углу') . '</option>
				<option value="3"'.(($watermark_type == 3)?(' selected="selected"'):('')).'>' . t('В правом верхнем углу') . '</option>
				<option value="4"'.(($watermark_type == 4)?(' selected="selected"'):('')).'>' . t('В левом нижнем углу') . '</option>
				<option value="5"'.(($watermark_type == 5)?(' selected="selected"'):('')).'>' . t('В правом нижнем углу') . '</option>
				</select></p>

				<div class="attach unit">
					<span>
						<input id="attach_img" type="file" name="attach" data-url="" multiple>
						<div class="loader"><img src="'.getinfo('admin_url').'plugins/admin_page/images/loader.gif" width="16" height="11"></div>
						<div class="uploaded"></div>
						<div class="inserted"></div>
					</span>
				</div>
			</div>
		</div>
		<script src="'.getinfo('admin_url').'plugins/admin_page/upload/upload.js"></script>' . NR;

	$upload_btn = '<a href="' . getinfo('site_admin_url') . 'files/' . $current_dir . '" target="_blank" class="goto-files">' . t('Управление файлами') . '</a> <a href="#" id="all-files-upload" class="all-files-upload">' . t('Загрузить файлы') . '</a> ';

	$upload_scr = '
		$("#all-files-upload").click(function(event){
			$("#all-files-upload-panel").slideToggle();
			$(".attach .loader").hide();
			return false;
		});';

echo NR . '<script src="'.getinfo('admin_url').'plugins/admin_page/upload/jquery.ui.widget.js"></script>' .
     NR . '<script src="'.getinfo('admin_url').'plugins/admin_page/upload/jquery.iframe-transport.js"></script>' .
     NR . '<script src="'.getinfo('admin_url').'plugins/admin_page/upload/jquery.fileupload.js"></script>' . NR;

$ajax_path = getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-upload-ajax.php');
$ajax_update_path = getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-update-ajax.php');

echo "
	<script type=\"text/javascript\">
		var sess = '".$MSO->data['session']['session_id']."';
		var upload_path = '".$ajax_path."',
		    update_path = '".$ajax_update_path."',
			current_dir = '".$current_dir."';
	</script>
";

}


$all_files = '';

if ($n = mso_segment(3)) // указан N номер записи
{
	if (is_numeric($n))  //неверное число — выходим
	{
		$current_dir = '_pages/' . $n;
	}
}
else
{
	$current_dir = mso_get_option('uploads_temp_folder', 'general', 'tempfiles');
}

$path = getinfo('uploads_dir') . $current_dir;

if (!is_dir($path) ) // нет каталога
{
	if (!is_dir(getinfo('uploads_dir') . '_pages') ) // нет _pages
	{
		@mkdir(getinfo('uploads_dir') . '_pages', 0777); // пробуем создать
	}

	// нет каталога, пробуем создать
	@mkdir($path, 0777); 
	@mkdir($path . '/_mso_i', 0777); 
	@mkdir($path . '/mini', 0777);
}

if (!is_dir($path) ) // каталог не удалось создать
{
	$all_files = t('Не удалось создать каталог для файлов страницы');
	return;
}

	$all_files = '<div class="all-files-nav">' . $upload_btn . '</div>';
	
	// скрипт выполняет аякс
	// первый раз при загрузке страницы
	// после по клику на ссылке Обновить
	
	// для лайтбокса проверяем наличие функции из плагина lightbox_head
	
	if (!function_exists('lightbox_head')) $lightbox = '';
	else
	{
		$url = getinfo('plugins_url') . 'lightbox/';
		$t_izob = t('Изображение');
		$t_iz = t('из');
		
		$lightbox = <<<EOF
var lburl = "{$url}images/";
$("a.lightbox").lightBox({
	imageLoading: lburl+"lightbox-ico-loading.gif",
	imageBtnClose: lburl+"lightbox-btn-close.gif",
	imageBtnPrev: lburl+"lightbox-btn-prev.gif",
	imageBtnNext: lburl+"lightbox-btn-next.gif",
	imageBlank: lburl+"lightbox-blank.gif",
	txtImage: "{$t_izob}",
	txtOf: "{$t_iz}",
});
EOF;
	}
	
	
	$all_files .= '<script>
$(function(){
	$.post(
		"' . getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-update-ajax.php') . '",
		{
			dir: "' . $current_dir . '"
		},
		function(data)
		{
			$("#all-files-result").html(data);
			' . $lightbox . '
		}
	);

	$(window).on("storage", function(e) {
		var pageId = window.location.pathname.match(/\d+$/)[0],
			event = e.originalEvent;

		if (event.newValue === pageId) {
			$("#all-files-result").html("' . t('Обновление...') . '");

			$.post(
				"' . getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-update-ajax.php') . '",
				{
					dir: "' . $current_dir . '"
				},
				function(data)
				{
					$("#all-files-result").html(data);
					' . $lightbox . '
					localStorage.clear();
				}
			);
		}
		return false;
	});
' . $upload_scr . '
});

function addImgPage(img, t) {
	var e = $("input[name=\'f_options[image_for_page]\']");
	if ( e.length > 0 )
	{
		e.val(img);
		alert("' . t('Установлено:') . ' " + img);
	}
}
</script>

<script src="'. getinfo('plugins_url') . 'comment_smiles/comment_smiles.js"></script>

'.$upload_div.'

<div id="all-files-result" class="all-files-result">' . t('Загрузка...') . '</div>

';

# end file