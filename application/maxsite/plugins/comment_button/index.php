<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function comment_button_autoload($args = array())
{
	mso_hook_add( 'body_end', 'comment_button_head'); # хук на head шаблона - для JS
	mso_hook_add( 'admin_comment_edit', 'comment_button_head_admin_comment_edit'); # для JS админки
	mso_hook_add( 'comments_content_start', 'comment_button_custom'); # хук на форму
}

# функция выполняется при деинсталяции плагина
function comment_button_uninstall($args = array())
{	
	mso_delete_option('plugin_comment_button', 'plugins' ); // удалим созданные опции
	return $args;
}

# подключаем JS в head
function comment_button_head($arg = array())
{
	if (is_type('page')) echo mso_load_script(getinfo('plugins_url') . 'comment_button/comment_button.js');
	return $arg;
}

# подключаем JS в head
function comment_button_head_admin_comment_edit($arg = array())
{
	echo mso_load_script(getinfo('plugins_url') . 'comment_button/comment_button.js');
	// echo '<script src="'. getinfo('plugins_url') . 'comment_button/comment_button.js"></script>' . NR;
}

# опции
function comment_button_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_comment_button', 'plugins',
		array(
			'show_buttons' => array(
							'type' => 'text',
							'name' => t('Активные кнопки'),
							'description' => t('Перечислите через запятую, пробел или «|» кнопки, которые будут отображаться. Доступны следующие значения: <b>b</b>, <b>i</b>, <b>u</b>, <b>s</b>, <b>blockquote</b>, <b>pre</b>. Если оставить поле пустым, то будут выводится все стандартные кнопки.'),
							'default' => ''
						),
			),
		t('Настройки comment_button'),
		t('Задайте настройки отображения кнопок на форме комментирования.')
	);
}

# функции плагина
function comment_button_custom($arg = array())
{
	$options = mso_get_option('plugin_comment_button', 'plugins', array());
	
	if (!isset($options['show_buttons']) or !trim($options['show_buttons'])) 
		$options['show_buttons'] = 'b|i|u|s|blockquote|pre';
		
	$buttons = array_map('trim', preg_split("/[\s,\|]+/", trim($options['show_buttons'])));
		
	echo '<p class="comment_button">
'.( !in_array('b', $buttons) ? '' : '	<button type="button" class="comment_button_b" title="' . tf('Полужирный') . '" onClick="addText(\'<b>\', \'</b>\') ">B</button>' ).'
'.( !in_array('i', $buttons) ? '' : '	<button type="button" class="comment_button_i" title="' . tf('Курсив') . '" onClick="addText(\'<i>\', \'</i>\') ">I</button>' ).'
'.( !in_array('u', $buttons) ? '' : '	<button type="button" class="comment_button_u" title="' . tf('Подчеркнутый') . '" onClick="addText(\'<u>\', \'</u>\') ">U</button>' ).'
'.( !in_array('s', $buttons) ? '' : '	<button type="button" class="comment_button_s" title="' . tf('Зачеркнутый') . '" onClick="addText(\'<s>\', \'</s>\') ">S</button>' ).'
'.( !in_array('blockquote', $buttons) ? '' : '	<button type="button" class="comment_button_blockquote" title="' . tf('Цитата') . '" onClick="addText(\'<blockquote>\', \'</blockquote>\') ">' . t('Цитата') . '</button>' ).'
'.( !in_array('pre', $buttons) ? '' : '	<button type="button" class="comment_button_pre" title="' . tf('Код или преформатированный текст') . '" onclick="addText(\'<pre>\', \'</pre>\') ">' . t('Код') . '</button>' ).'
'.mso_hook('comment_button_more').'
	</p>';
}

# end file