<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<?php
	// для автосохранения определим id
	$auto_id = mso_segment(3); // номер страницы по сегменту url
	// проверим, чтобы это было число
	$auto_id1 = (int) $auto_id;
	if ( (string) $auto_id != (string) $auto_id1 ) $auto_id = 0; // ошибочный id
?>

	<script src="<?= $editor_config['url'] ?>jw/jquery.wysiwyg.js"></script>
	<script src="<?= $editor_config['url'] ?>jw/jquery.timers.js"></script>
	<script>
		$(function()
		{
		  autosavetime = 60000; // = 60 sec
		  autosaveurl = '<?= getinfo('ajax') . base64_encode('admin/plugins/editor_jw/autosave-post-ajax.php') ?>';
		  autosaveold = '<?= getinfo('siteurl') . 'uploads/_mso_float/autosave-' . $auto_id . '.txt' ?>';
		  autosaveid = '<?= $auto_id ?>';
		  autosavetextold = '';
		  
		  $('#wysiwyg').wysiwyg({
				css: '<?= $editor_config['url'] ?>jw/styles.css',
				controls : {},
				controls_extra : 
				{
					separator1 : { separator : true }
					<?php mso_hook('editor_controls_extra') ?>
				}
			});
		});
	</script>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>
<textarea id="wysiwyg" name="f_content" style="height: <?= $editor_config['height'] ?>px;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

