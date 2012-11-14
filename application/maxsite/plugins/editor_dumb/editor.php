<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<?php echo '<script src="'. getinfo('plugins_url') . 'editor_dumb/editor_zero.js"></script>'; ?>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>
<p class="editor_button">
	<input type="button" value="B" title="<?= t('Полужирный') ?>" onClick="addText('<b>', '</b>') ">
	<input type="button" value="I" title="<?= t('Курсив') ?>" onClick="addText('<i>', '</i>') ">
	<input type="button" value="U" title="<?= t('Подчеркнутый') ?>" onClick="addText('<u>', '</u>') ">
	<input type="button" value="S" title="<?= t('Зачеркнутый') ?>" onClick="addText('<s>', '</s>') "> &nbsp;
	<input type="button" value="A" title="<?= t('Ссылка') ?>" onClick="addText('<a href=&quot;&quot;>', '</a>') ">
	<input type="button" value="IMG" title="<?= t('Картинка') ?>" onClick="addText('<img src=&quot;&quot; alt=&quot;&quot;>', '') ">
	<input type="button" value="<?= t('Цитата') ?>" title="<?= t('Цитата') ?>" onClick="addText('<blockquote>', '</blockquote>') ">
	<input type="button" value="<?= t('Код') ?>" title="<?= t('Код или преформатированный текст') ?>" onClick="addText('<code>', '</code>') ">
	<input type="button" value="cut" title="<?= t('Отрезать текст') ?>" onClick="addText('[cut]\n', '') ">
</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

