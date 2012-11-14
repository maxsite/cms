<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<script>
<?php require('bb.js.php') ?>
</script>

<?php echo '<script src="'. getinfo('plugins_url') . 'editor_markitup/jquery.markitup.js"></script>'; ?>
<?php echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'editor_markitup/style.css">'; ?>
<?php echo '<link rel="stylesheet" href="'. getinfo('plugins_url') . 'editor_markitup/bb.style.css">'; ?>

<?php
	$auto_id = mso_segment(3); // номер страницы по сегменту url
	// проверим, чтобы это было число
	if (!is_numeric($auto_id)) $auto_id = 0; // ошибочный id
?>
	
<script language="javascript">
	autosaveurl = '<?= getinfo('ajax') . base64_encode('plugins/editor_markitup/autosave-post-ajax.php') ?>';
	autosaveid = '<?= $auto_id ?>';

	$(document).ready(function() 
	{
		$('#f_content').markItUp(myBbcodeSettings);
	});
</script>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
