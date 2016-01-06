<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


function _getFiles($rdi, $depth=0, $dir) 
{
	$out = array();
	
	if (!is_object($rdi)) return $out;

	for ($rdi->rewind(); $rdi->valid(); $rdi->next()) 
	{
		if ($rdi->isDot()) continue;

		if ($rdi->isDir() || $rdi->isFile()) 
		{
			$cur = $rdi->current();
			$cur = str_replace('\\', '/', $cur);
			if (_is_exclude($cur)) continue;
			
			$cur = str_replace($dir, '', $cur);
			
			if ($rdi->isDir()) 
			{
				if ($depth == 0) 
				{
					$out[] = '<optgroup class="bg-gray100" label="' . $cur . '"></optgroup>';
				}
			}
			
			if ($rdi->isFile())
			{
				$file_ext = strtolower(str_replace('.', '', strrchr($cur, '.')));
				
				if (in_array($file_ext, array('php', 'txt', 'css', 'less', 'js', 'html', 'htm', 'ini', 'sass', 'scss'))) 
				{
					if (is_writable($rdi->getPathname())) $out[] = $cur;
				}
			}
			
			if ($rdi->hasChildren())
			{
				$out1 = _getFiles($rdi->getChildren(), 1 + $depth, $dir);
				$out = array_merge($out, $out1); 
			}
		}
	}
	 
	return $out;
}

// проверка части вхождения каждого элемента массива $a в строку $str
// если вхождение есть, то отдаем true если нет, то false 
function _is_exclude($str)
{
	// исключаемые из списка элементы задаются в custom/set_val_admin.php
	// mso_set_val('editor_files_exclude', array('/node_modules/'));
	
	$a = mso_get_val('editor_files_exclude', array());
	
	foreach ($a as $find)
	{
		if (stripos($str, $find) !== false) return true; // найдено вхождение
	}
	
	return false;
}

$directory = getinfo('template_dir');
$directory = str_replace('\\', '/', $directory);

$r = new RecursiveDirectoryIterator($directory);

$files = _getFiles($r, 0, $directory);

// в третьем сегменте можно указать адрес файла в base64
$content_file = '';
$file_path = '';

if (mso_segment(3)) 
{
	$f = base64_decode(mso_segment(3));
	$f = str_replace('~', '-', $f);
	$f = str_replace('\\', '-', $f);
	$f = getinfo('template_dir') . $f;
	
	// есть такой файл
	if (file_exists($f)) 
	{
		$content_file = file_get_contents($f);
		$file_path = mso_segment(3);
	}
}


// pr(getinfo('template'));
// pr($directory);
// pr($files);

$select = '<option value="" selected>-</option>';

foreach ($files as $file)
{
	if (strpos($file, 'optgroup') === false)
	{
		$opt_selected = (mso_segment(3) and base64_encode($file) === mso_segment(3)) ? ' selected' : '';
		
		$select .= '<option value="' . base64_encode($file) . '"' . $opt_selected . '>' . $file . '</option>';
	}
	else
		$select .= $file;
}

?>

<h1><?= t('Файлы для редактирования шаблона') . ' «' . getinfo('template') . '»' ?></h1>

<p class="mar30-t"><?= t('Выберите файл:') ?> <select id="select_file" class="w-auto"><?= $select ?></select> <span id="success"></span></p>


<?php

echo '<form method="post" id="edit_form" action=""><textarea name="content" id="content" class="w100 h500px bg-gray50">' . $content_file . '</textarea><input type="hidden" id="file_path" name="file_path" value="' . $file_path . '"><p><button id="b-save" class="button i-save" type="submit">Сохранить</button></p></form>';
		
$AJAX1 = getinfo('ajax') . base64_encode('admin/plugins/editor_files/load-file-ajax.php');
$AJAX2 = getinfo('ajax') . base64_encode('admin/plugins/editor_files/save-file-ajax.php');

echo <<<EOF
<script>
jQuery(function($) {
	
	$('#b-save').fadeOut(0);
	
	$('#select_file').change(function(){
		
		var f = $("#select_file :selected").val();
		
		if (f)
		{
			$.post("{$AJAX1}", {file:f},  function(response) {
				$('#file_path').val(f);
				
				// $('#content').html(response);
				$('#content').val(response);
				
				// это для отладки
				$('#success').html('<span class="i-check mar10-l t-green t130"></span>Файл загружен');
				$('#success').show();
				$('#success').fadeOut(5000);
				
				$('#b-save').fadeOut(1000);
			});
		}
	})
	
	$('#edit_form').submit(function(){
		$.post("{$AJAX2}", $("#edit_form").serialize(),  function(response) {
			$('#success').html(response);
			$('#success').show();
			$('#success').fadeOut(5000);
			
			$('#b-save').fadeOut(1000);
		});
		
		return false;
	})
	
	$('#content').keypress(function(){
		$('#b-save').fadeIn(1000);
	})
	
});
</script>

EOF;

# end of file