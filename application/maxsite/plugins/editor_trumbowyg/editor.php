<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// стили
echo mso_load_style($editor_config['url'] . 'dist/ui/trumbowyg.min.css');

// скрипты
echo mso_load_script(getinfo('plugins_url') . 'tabs/tabs.js', false); // чтобы работали табы
echo mso_load_script($editor_config['url'] . 'dist/trumbowyg.min.js');

// автосохранение реализуется на уровне самого редактора, где нужно перехватить например Ctrl+S
// необходимо передать в ajax-адрес и id-записи. 
// Обработчик аякс запроса можно взять из admin\plugins\editor_markitup\autosave-post-ajax.php
// в этом плагине автосохранения нет, но код оставлен (закомментированным) для примера

// $auto_id = mso_segment(3); // номер страницы по сегменту url
// if (!is_numeric($auto_id)) $auto_id = 0; // ошибочный id
// js-код
// autosaveurl = '< ?= getinfo('ajax') . base64_encode('plugins/editor_trumbowyg/autosave-post-ajax.php') ? >';
// autosaveid = '< ?= $auto_id ? >';

?>
<script>
$(document).ready(function(){
	$('#f_content').trumbowyg();	
});
</script>
<?= $editor_config['do_script'] ?>
<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
