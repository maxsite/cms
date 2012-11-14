<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

# http://forum.max-3000.com/viewtopic.php?f=6&t=930
# Автор: Delete (http://www.finiks.ru/)

echo '<script src="'. getinfo('plugins_url') . 'editor_dumb/editor_zero.js"></script>'; 

?>

<form method="post" <?= $editor_config['action'] ?> enctype="multipart/form-data" id="form_editor">
<?= $editor_config['do'] ?>
<p class="editor_button">
	<!-- ВНИМАНИЕ: добавляем кнопки по следующему принципу:
	<input type="button" value="название кнопки" title="<?= t('всплывающая подсказка') ?>" onClick="addText('текст до курсора', 'текст после курсора') "> 
	Вместо знака " ставим &nbsp; перенос строки \n -->
	основные
	<input type="button" value="b" title="<?= t('полужирный') ?>" onClick="addText('[b]', '[/b]') ">
	<input type="button" value="i" title="<?= t('курсив') ?>" onClick="addText('[i]', '[/i]') "/>
	<input type="button" value="u" title="<?= t('подчеркнутый') ?>" onClick="addText('[u]', '[/u]') "/>
	<input type="button" value="s" title="<?= t('зачеркнутый') ?>" onClick="addText('[s]', '[/s]') "/> &nbsp;
	<input type="button" value="a" title="<?= t('ссылка, формат вывода - [url=http://ссылка/]текст[/url]') ?>" onClick="addText('[url=http://]', '[/url]') "/>
	<input type="button" value="img" title="<?= t('картинка выравнивание по левому краю, формат вывода - [imgleft]адрес[/imgleft]') ?>" onClick="addText('[imgleft]', '[/imgleft]') "/>
	<input type="button" value="<?= t('цитата') ?>" title="<?= t('цитата') ?>" onClick="addText('[quote]', '[/quote]') "/>
	<input type="button" value="<?= t('код') ?>" title="<?= t('код или преформатированный текст') ?>" onClick="addText('[code]', '[/code]') "/>
	<input type="button" value="cut" title="<?= t('обрезать текст') ?>" onClick="addText('[cut]\n', '') "/> &nbsp;
	списки
	<input type="button" value="ul" title="<?= t('список булечкой') ?>" onClick="addText('[ul]\n', '\n[/ul]') ">
	<input type="button" value="ol" title="<?= t('список номерами') ?>" onClick="addText('[ol]\n', '\n[/ol]') ">
	<input type="button" value="элемент" title="<?= t('элемент списка') ?>" onClick="addText('[*]', '') ">&nbsp;
	таблицы
	<input type="button" value="table" title="<?= t('вставить таблицу') ?>" onClick="addText('[table]\n', '\n[/table]') ">
	<input type="button" value="tr" title="<?= t('вставить строку таблицы') ?>" onClick="addText('[tr]\n', '\n[/tr]') ">
	<input type="button" value="td" title="<?= t('вставить ячейку таблицы') ?>" onClick="addText('[td]', '[/td]') ">&nbsp;
	дополнительно
	<input type="button" value="audio" title="<?= t('вставить музыкальную композицию формат вывода [audio=http://site.com/my.mp3]') ?>" onClick="addText('[audio=http://', '.mp3]') ">
	<input type="button" value="faq" title="<?= t('организация FAQ на странице') ?>" onClick="addText('[faqs]\n[faq=', 'вопрос]\n ответ\n[/faq]\n[faq=вопрос2]\n ответ2\n[/faq]\n[/faqs]') ">
	<input type="button" value="spoiler" title="<?= t('скрыть текст под спойлер') ?>" onClick="addText('[spoiler]', '[/spoiler]') ">
	<input type="button" value="auth" title="<?= t('текст только для авторизованных') ?>" onClick="addText('[auth]', '[/auth]') ">
	<input type="button" value="dc" title="<?= t('подсчет количества переходов по ссылке, обрамите нужную ссылку в [dc]...[/dc]') ?>" onClick="addText('[dc]', '[/dc]') ">
</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>
