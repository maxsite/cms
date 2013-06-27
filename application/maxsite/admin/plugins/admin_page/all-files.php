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
	
	
	$all_files = '<a href="' . getinfo('site_admin_url') . 'files/' . $current_dir . '" target="_blank">' . t('Управление файлами') . '</a> | <a href="#" id="all-files-update">' . t('Обновить') . '</a> <div class="clearfix"></div>';
	
	// скрипт выполняет аякс
	// первый раз при загрузке страницы
	// после по клику на ссылке Обновить
	$all_files .= '
<script>
	$(function(){
		
		$.post(
			"' . getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-update-ajax.php') . '",
			{
				dir: "' . $current_dir . '"
			},
			function(data)
			{
				$("#all-files-result").html(data);
			}
		);
	
		$("#all-files-update").click(function()
		{
			$("#all-files-result").html("' . t('Обновление...') . '");
			
			$.post(
				"' . getinfo('ajax') . base64_encode('admin/plugins/admin_page/all-files-update-ajax.php') . '",
				{
					dir: "' . $current_dir . '"
				},
				function(data)
				{
					$("#all-files-result").html(data);
				}
			);
			return false;
		});
	});
</script>

<div id="all-files-result"></div>
';

}
else
{
	$all_files = t('Сохраните запись');
}



# end file