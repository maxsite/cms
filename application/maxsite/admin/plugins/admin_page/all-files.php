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
	
	
	$all_files = ''; // '<div class="all-files-nav"><a href="#" id="all-files-upload1" class="all-files-upload">' . t('Быстрая загрузка') . '</a><!--  <a href="' . getinfo('site_admin_url') . 'files/' . $current_dir . '" target="_blank" class="goto-files">' . t('Управление файлами') . '</a> --></div>';
	
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


function del_file(f) {
	if(confirm("' . t('Удалить файл') . ' " + f + " ?")) 
	{
		$.post(
			"' . getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-update-ajax.php') . '",
			{
				dir: "' . $current_dir . '",
				deletefile : f
			},
			function(data)
			{
				$("#all-files-result").html(data);
				lbox();
			}
		);
	} 
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
		all_files_upload_toggle();
		return false;
	});
	
	
	function all_files_upload_toggle()
	{
		$("body,html").animate({scrollTop: 800}, 500);
		$("#all-files-upload-panel").slideToggle();
		$("#upload_messages").html("");
		$("#upload_progress").html("");
		$("#all-files-upload").toggleClass("selected");
	}
	
	var timerId;
	
	$("#all-files-upload").mouseenter( function(){ timerId = setTimeout(all_files_upload_toggle, 1000); } );
	
	$("#all-files-upload").mouseleave( function(){ clearTimeout(timerId); } );
	
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

$check_use_watermark = mso_get_option('use_watermark', 'general', '0') ? ' checked' : '';
$check_use_watermark_mini = mso_get_option('use_watermark_mini', 'general', '0') ? ' checked' : '';

$all_files .= '
<div id="all-files-upload-panel">

<input type="hidden" id="upload_max_file_size" name="upload_max_file_size" value="20000000">
<input type="hidden" id="upload_action" name="upload_action" value="' . getinfo('require-maxsite') . base64_encode('admin/plugins/admin_page/uploads-require-maxsite.php') . '">
<input type="hidden" id="upload_ext" name="upload_ext" value="' . mso_get_option('allowed_types', 'general', 'mp3|gif|jpg|jpeg|png|svg|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz') . '">
<input type="hidden" id="upload_dir" name="upload_dir" value="' . $path . '">
<input type="hidden" id="update_path" name="update_path" value="' . $update_path . '">
<input type="hidden" id="page_id" name="page_id" value="' . mso_segment(3) . '">

<div class="flex flex-wrap">
	
	<div class="flex-grow1 pad20-r">
		<div id="upload_filedrag">' . t('... перетащите файлы сюда ...') . '</div>
		<input class="w-auto mar10-b" type="file" id="upload_fileselect" name="upload_fileselect[]" multiple="multiple">
	</div>
	
	<div class="flex-grow1">
		
		<div class="mar10-t links-no-color">' . t('Размер') . ' <input class="w100px-max" type="number" min="1" id="upload_resize_images" name="upload_resize_images" value = "' . mso_get_option('resize_images',   'general', '600') . '" title="' . t('Размер конечного изображения') . '">
			
			<select class="w-auto" title="' . t('Метод изменения размера') . '" id="upload_resize_images_type" name="upload_resize_images_type">
			' . form_select_options(array(
					'width' => t('по ширине'),
					'height' => t('по высоте'),
					'max' => t('по максимальной стороне'),
					'crop_center_ratio_auto' => t('кроп по центру (авто-высота)'),
					'crop_center_ratio_4_3' => t('кроп по центру (пропорция 4:3)'), 
					'crop_center_ratio_3_2' => t('кроп по центру (пропорция 3:2)'),
					'crop_center_ratio_16_9' => t('кроп по центру (пропорция 16:9)'),
					'no' => t('не менять (исходный размер)')
					), mso_get_option('upload_resize_images_type', 'general', 'width')) 
			. '</select>
			
			<a class="i-picture-o t150 mar10-l icon0" href="' . getinfo('site_admin_url') . 'files/_pages/' . mso_segment(3) . '/mini" target="_blank" title="' . t('Управление миниатюрами') . '"></a>
			
			<label class="b-inline mar10-l pad10-b" title="' . t('Загрузить файлы без обработки') . '"><input type="checkbox" id="upload_asis" name="upload_asis"> </label>
			
		</div>
		
		<div class="mar10-tb">
			
			' . t('Миниатюра') . ' <input class="w70px" title="' . t('Ширина миниатюры') . '" type="number" min="1" id="upload_size_image_mini_w" name="upload_size_image_mini_w" value = "' . mso_get_option('size_image_mini', 'general', '150') . '">
			
			x <input class="w70px" title="' . t('Высота миниатюры') . '" type="number" min="1" id="upload_size_image_mini_h" name="upload_size_image_mini_h" value = "' . mso_get_option('size_image_mini_height', 'general', '150') . '">
					
			<select class="w250px-max" title="' . t('Способ создания миниатюры') . '" id="upload_type_resize" name="upload_type_resize">
				' . form_select_options(array(
						'none' => t('Не создавать миниатюру') . '||'. t('Не создавать миниатюру'),
						'resize_full_crop_center' => 'resize_full_crop_center||'. t('Обрезка по центру с соблюдением пропорций'),
						'resize_full_crop_top_left' => 'resize_full_crop_top_left||' . t('Обрезка от верхнего левого угла (пропорции)'),
						'resize_full_crop_top_center' => 'resize_full_crop_top_center||' . t('Обрезка от верхнего центра (пропорции)'),
						'resize_crop' => 'resize_crop||'. t('Обрезка пропорционально ширине'),
						'resize_crop_center' => 'resize_crop_center||'. t('Пропорциональная ширина и обрезка по центру'),
						'resize_h_crop_center' => 'resize_h_crop_center||'. t('Пропорциональная высота и обрезка по центру'),
						'crop' => 'crop||'. t('Обрезка по верхнему левому углу'),
						'crop_center' => 'crop_center||'. t('Обрезка по центру'),
						'resize' => 'resize||'. t('Непропорциональное изменение до указанных размеров'),
						'resize_w' => 'resize_w||'. t('Пропорциональное изменение до указанной ширины'),
						'resize_h' => 'resize_h||'. t('Пропорциональное изменение до указанной высоты'),
						'crop_center_ratio_auto' => 'crop_center_ratio_auto||'. t('Обрезка по центру с авто-высотой'),
						'crop_center_ratio_4_3' => 'crop_center_ratio_4_3||'. t('Обрезка по центру с пропорцией 4:3'),
						'crop_center_ratio_3_2' => 'crop_center_ratio_3_2||'. t('Обрезка по центру с пропорцией 3:2'),
						'crop_center_ratio_16_9' => 'crop_center_ratio_16_9||'. t('Обрезка по центру с пропорцией 16:9'),
						'zoom25' => 'zoom25||'. t('Масштаб 25% (от обработанного)'),
						'zoom50' => 'zoom50||'. t('Масштаб 50% (от обработанного)'),
						'zoom75' => 'zoom75||'. t('Масштаб 75% (от обработанного)'),
						'zoom25_crop_center_ratio_auto' => 'zoom25_crop_center_ratio_auto||'. t('Масштаб 25%, после обрезка по центру'),
						'zoom50_crop_center_ratio_auto' => 'zoom50_crop_center_ratio_auto||'. t('Масштаб 50%, после обрезка по центру'),
						'zoom75_crop_center_ratio_auto' => 'zoom75_crop_center_ratio_auto||'. t('Масштаб 75%, после обрезка по центру'),
						
						
						), mso_get_option('upload_type_resize', 'general', 'resize_full_crop_center')) 
			. '</select>
		</div>
		
		<label class="b-inline pad10-b mar20-r"><input type="checkbox" id="upload_replace_file" name="upload_replace_file" checked> ' . t('Заменять существующие файлы') .'</label>
		
		<label class="b-inline pad10-b" title="' . t('Водяной знак для миниатюры') . '"><input type="checkbox" id="upload_watermark_mini" name="upload_watermark_mini"' . $check_use_watermark_mini . '> </label>
		
		<label class="b-inline pad10-b" title="' . t('Водяной знак для основного изображения') . '"><input type="checkbox" id="upload_watermark" name="upload_watermark"' . $check_use_watermark . '> ' . t('Водяной знак') .'</label>
		
		
		
	</div>
</div>

<div id="upload_submitbutton"><button type="button">Upload Files</button></div>
<div class="mar10-tb" id="upload_progress"></div>
<div class="mar10-tb" id="upload_messages"></div>
</div>

<div id="all-files-result" class="all-files-result">' . t('Загрузка...') . '</div>
<script src="' . getinfo('admin_url') . 'plugins/admin_page/filedrag.js' . '"></script>';
}
else
{
	$all_files = ''; // t('Сохраните запись');
}


# end of file