<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$content_file = '';
$file_path = '';
$curfile = '';

$AJAX1 = getinfo('ajax') . base64_encode('admin/plugins/editor_files/load-file-ajax.php');
$AJAX2 = getinfo('ajax') . base64_encode('admin/plugins/editor_files/save-file-ajax.php');
$A_LINK = getinfo('site_admin_url') .'editor_files/';

$t1 = t('Файл загружен!');
$t2 = t('Текущий файл: ');


$directory = getinfo('template_dir');
$directory = str_replace('\\', '/', $directory);

$r = new RecursiveDirectoryIterator($directory);

$files = _getFiles($r, 0, $directory);

// кнопка Сохранить должна быть только если загружен файл, в остальнйх случаях скрываем
$button_show = "$('#b-save').fadeOut(0);";

// в третьем сегменте можно указать адрес файла в base64
if (mso_segment(3)) 
{
	$f = htmlentities(base64_decode(mso_segment(3)));
	
	if ($f)
	{
		$f = str_replace('~', '-', $f);
		$f = str_replace('\\', '-', $f);
		
		// $ff = getinfo('template_dir') . $f;
		$ff = mso_check_dir_file(getinfo('template_dir'), $f);
        
		// есть такой файл
		if ($ff and file_exists($ff)) 
		{
			$curfile = $t2 . '<b>' . $f . '</b>';
			$content_file = file_get_contents($ff);
			$file_path = mso_segment(3);
			$button_show = '';
		}
	}
}


$select = '<option value="" selected>-</option>';

foreach ($files as $file)
{
	if (strpos($file, 'optgroup') === false)
	{
		$opt_selected = (mso_segment(3) and base64_encode($file) === mso_segment(3)) ? ' selected' : '';
		
		$select .= '<option value="' . base64_encode($file) . '"' . $opt_selected . '>' . $file . '</option>';
	}
	else
	{
		$select .= $file;
	}
}

?>

<h1><?= t('Файлы шаблона') . ' «' . getinfo('template') . '»' ?></h1>

<div class="flex flex-vcenter">
	<div class="flex-grow0 pad5-r"><?= t('Файл:') ?></div>
	<div class="flex-grow3"> <select id="select_file" class="w100"><?= $select ?></select></div>
	
	<?php
		// если есть custom/my-editor-files.php (в нём только <option>), то подключаем его
		if ($f1 = mso_fe('custom/my-editor-files.php')) 
		{
			echo '<div class="flex-grow0 pad5-rl">' . t('или') . '</div><div class="flex-grow3"><select id="select_file1" class="w100">';
			require($f1);
			echo '</select></div>';
		}
	?>
</div>

<div class="t90 mar10-t"><span id="curfile"><?= $curfile ?></span></div>
<div id="success"></div>

<?php

echo '<form class="mar10-t" method="post" id="edit_form"><textarea name="content" id="content" class="w100 h500px bg-gray50">' . $content_file . '</textarea><input type="hidden" id="file_path" name="file_path" value="' . $file_path . '"><p><button id="b-save" class="button i-save" type="submit">Сохранить</button></p></form>';

echo <<<EOF
<script>
$(document).ready(function() {
	
	{$button_show}
	
	$('#select_file, #select_file1').change(function(){
		
		var f = $("option:selected", this).val();
		
		if (f)
		{			
			$('#success').hide();
			
			$.post("{$AJAX1}", {file:f},  function(response) {
				$('#file_path').val(f);
				$('#content').val(response);
				$('#success').html('<div class="update pos-fixed w200px pad10 pos20-r pos0-t t-center">{$t1}</div>');
				$('#success').show();
				$('#success').fadeOut(5000);
			});
			
			$('#curfile').html('{$t2} <a class="bold" href="{$A_LINK}' + f + '">' + $("option:selected", this).text() + '</a>');
			$('#b-save').fadeIn(500);
		}
	})
	
	$('#edit_form').submit(function(){
		$.post("{$AJAX2}", $("#edit_form").serialize(),  function(response) {
			$('#success').html(response);
			$('#success').show();
			$('#success').fadeOut(5000);
			$('#b-save').fadeIn(500);
		});
		
		return false;
	})
});
</script>
EOF;



function _getFiles($rdi, $depth = 0, $dir = '') 
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

				// php', 'txt', 'css', 'less', 'js', 'html', 'htm', 'ini', 'sass', 'scss'
				if (in_array($file_ext, array('php', 'txt', 'css', 'js', 'html', 'htm', 'ini'))) 
				{
					$pn = $rdi->getPathname();
					if (is_writable($pn)) $out[] = $cur;
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

# end of file