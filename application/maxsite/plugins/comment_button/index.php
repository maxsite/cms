<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function comment_button_autoload($args = array())
{
	mso_hook_add( 'head', 'comment_button_head'); # хук на head шаблона - для JS
	mso_hook_add( 'admin_comment_edit', 'comment_button_head_admin_comment_edit'); # для JS админки
	mso_hook_add( 'comments_content_start', 'comment_button_custom'); # хук на форму
}

# подключаем JS в head
function comment_button_head($arg = array())
{
	if (is_type('page')) 
		echo '<script src="'. getinfo('plugins_url') . 'comment_button/comment_button.js"></script>' . NR;
}

# подключаем JS в head
function comment_button_head_admin_comment_edit($arg = array())
{
	echo '<script src="'. getinfo('plugins_url') . 'comment_button/comment_button.js"></script>' . NR;
}


# функции плагина
function comment_button_custom($arg = array())
{
	echo '<p class="comment_button">
	<button type="button" class="comment_button_b" title="' . tf('Полужирный') . '" onClick="addText(\'<b>\', \'</b>\') ">B</button>
	<button type="button" class="comment_button_i" title="' . tf('Курсив') . '" onClick="addText(\'<i>\', \'</i>\') ">I</button>
	<button type="button" class="comment_button_u" title="' . tf('Подчеркнутый') . '" onClick="addText(\'<u>\', \'</u>\') ">U</button>
	<button type="button" class="comment_button_s" title="' . tf('Зачеркнутый') . '" onClick="addText(\'<s>\', \'</s>\') ">S</button>
	<button type="button" class="comment_button_blockquote" title="' . tf('Цитата') . '" onClick="addText(\'<blockquote>\', \'</blockquote>\') ">' . t('Цитата') . '</button>
	<button type="button" class="comment_button_pre" title="' . tf('Код или преформатированный текст') . '" onclick="addText(\'<pre>\', \'</pre>\') ">' . t('Код') . '</button>
	</p>';
}

# end file