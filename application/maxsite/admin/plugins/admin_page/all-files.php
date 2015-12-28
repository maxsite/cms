<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
/*
	результат отдаём в $all_files
*/


if ($n = mso_segment(3)) // указан N номер записи
{
	if (!is_numeric($n))  //неверное число — выходим
	{
		$all_files = '';
		return;
	}
	
	$current_dir = '_pages/' . $n;
	$path = getinfo('uploads_dir') . $current_dir;
	$update_path = getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-update-ajax.php');

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
	
	
	$all_files = '<div class="all-files-nav"><a href="' . getinfo('site_admin_url') . 'files/' . $current_dir . '" target="_blank" class="goto-files">' . t('Управление файлами') . '</a> <a href="#" id="all-files-upload" class="all-files-upload">' . t('Быстрая загрузка') . '</a></div>';
	
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
function lbox() {
' . $lightbox . '
}

$(function(){
	$.post(
		"' . getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-update-ajax.php') . '",
		{
			dir: "' . $current_dir . '"
		},
		function(data)
		{
			$("#all-files-result").html(data);
			lbox();
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
					lbox();
					localStorage.clear();
				}
			);
		}
	});

	$("#all-files-upload-panel").slideToggle();
	$("#all-files-upload").click(function(event){
		$("#all-files-upload-panel").slideToggle();
		$("#upload_messages").html("");
		$("#upload_progress").html("");
		return false;
	});
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

<script src="'. getinfo('plugins_url') . 'comment_smiles/comment_smiles.js"></script>';

$all_files .= '
<div id="all-files-upload-panel">

<input type = "hidden" id = "upload_max_file_size" name = "upload_max_file_size" value="20000000">
<input type = "hidden" id = "upload_action" name = "upload_action" value = "' . getinfo('require-maxsite') . base64_encode('admin/plugins/admin_page/uploads-require-maxsite.php') . '">
<input type = "hidden" id = "upload_ext" name = "upload_ext" value = "' . mso_get_option('allowed_types', 'general', 'mp3|gif|jpg|jpeg|png|svg|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz') . '">
<input type = "hidden" id = "upload_dir" name = "upload_dir" value = "' . $path . '">
<input type = "hidden" id = "update_path" name = "update_path" value = "' . $update_path . '">
<input type = "hidden" id = "page_id" name = "page_id" value = "' . mso_segment(3) . '">

<div>
	<div id="upload_filedrag">' . t('... перетащите файлы сюда ...') . '</div>
	<input type="file" id="upload_fileselect" name="upload_fileselect[]" multiple="multiple">
</div>

<div id="upload_submitbutton"><button type="button">Upload Files</button></div>
<div class="mar10-tb" id="upload_progress"></div>
<div id="upload_messages"></div>
</div>

<div id="all-files-result" class="all-files-result">' . t('Загрузка...') . '</div>
<script src="' . getinfo('admin_url') . 'plugins/admin_page/filedrag.js' . '"></script>';
}
else
{
	$all_files = t('Сохраните запись');
}


# end file