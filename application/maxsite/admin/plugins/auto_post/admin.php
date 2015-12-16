<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// загрузчик для новой записи
// загружать только текстовые файлы .txt
// после сохранения подключается add-new-page.php и add_new_page($fn)
// в которой и происходит добавление новой записи 
// формат текста как в yaml см в lib/format.txt

// адрес загрузки относительно корня сайта
$upload_dir = 'uploads/_temp/';
$upload_ext = 'txt';

// создадим временный каталог если его нет
if (!is_dir($upload_dir)) @mkdir(getinfo('FCPATH') . $upload_dir, 0777);


// $upload_ext = mso_get_option('allowed_types', 'general', 'mp3|gif|jpg|jpeg|png|svg|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz');

?>


<h1><?= t('AutoPost') ?></h1>

<form action="" method="POST" enctype="multipart/form-data">
	<fieldset>
	<legend>File Upload</legend>
	
	<input type="hidden" id="upload_max_file_size" name="upload_max_file_size" value="20000000">
	<input type="hidden" id="upload_action" name ="upload_action" value="<?= getinfo('require-maxsite') . base64_encode('admin/plugins/auto_post/uploads-require-maxsite.php') ?>">
	<input type="hidden" id="upload_ext" name ="upload_ext" value="<?= $upload_ext ?>">
	<input type="hidden" id="upload_dir" name ="upload_dir" value="<?= $upload_dir ?>">
	
	<div>
		<div id="upload_filedrag">or drop files here</div>
		<input type="file" id="upload_fileselect" name="upload_fileselect[]" multiple="multiple">
	</div>

	<div id="upload_submitbutton"><button type="button">Upload Files</button></div>

	</fieldset>
</form>

<div id="upload_progress"></div>
<div class="pad20-tb mar10-tb" id="upload_messages"></div>

<?= mso_load_script(getinfo('admin_url') . 'plugins/auto_post/filedrag.js'); ?>
