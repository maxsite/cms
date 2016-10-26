<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function auth_content_autoload() 
{
	mso_hook_add('head', 'auth_content_head'); # хук для подключения стилей на внешних страницах
	mso_hook_add('content', 'auth_content_parse'); # хук на админку

	$options = mso_get_option('plugin_auth_content', 'plugins', array());
	if( isset($options['comments']) && ( $options['comments'] == 1 ) )
	{
		mso_hook_add('comments_content_out', 'auth_content_parse'); # хук на обработку комментариев
	}
}

# функция выполняется при деинстяляции плагина
function auth_content_uninstall($args = array())
{	
	mso_delete_option('plugin_auth_content', 'plugins' ); # удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function auth_content_mso_options() 
{
	$options = mso_get_option('plugin_auth_content', 'plugins', array());
	if( !isset($options['message']) ) $options['message'] = 'Запись только для зарегистрированных';
	if( !isset($options['format']) ) $options['format'] = '%MESSAGE%';
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_auth_content', 'plugins', 
		array(
			'message' => array(
				'type' => 'text', 
				'name' => t('Общий текст сообщения'), 
				'description' => t('Укажите текст сообщения для незалогиненых посетителей.'), 
				'default' => 'Запись только для авторизованных - хотите <a href="/login">войти</a> или <a href="/registration">зарегистрироваться</a>?',
			),
			'format' => array(
				'type' => 'text', 
				'name' => t('Шаблон ввода сообщения'), 
				'description' => t('Укажите шаблон вывода текстового сообщения. Используйте код <b>%MESSAGE%</b> для подстановки общего текста сообщения. Можно использовать HTML чтобы потом иметь возможность задать стили оформления через css. Например: <br><code>&lt;div class="auth_content">%MESSAGE%&lt;/div></code>'), 
				'default' => '<div class="auth_content">%MESSAGE%</div>',
			),
			'comments' => array(
				'type' => 'checkbox', 
				'name' => 'Обрабатывать комментарии', 
				'description' => 'Если поставить галочку, то тэг [auth] можно будет использовать в комментариях. Снимите галочку в случае, если в комментариях точно не будет использоваться скрытый контент - это позволит экономить вычеслительные ресурсы не делая лишних обработок.',
				'default' => 1
			),
		),
		t('Настройки плагина «Скрытый текст»'), // титул
		'<p class="info">Задайте общий текст сообщения и шаблон вывода. Кастомный текст сообщения можно будет задать внутри бб-кода. Например, так: <code>[auth Ссылка только для авторизованых]...[/auth]</code></p>' // инфо
	);
}

# подключение своих стилей на внешних страницах
function auth_content_head( $args = array() )
{
	# стили пользователя
	if( file_exists(getinfo('plugins_dir').basename(dirname(__FILE__)).'/custom.css') )
	{
		echo '<link rel="stylesheet" href="'.getinfo('plugins_url').basename(dirname(__FILE__)).'/custom.css" type="text/css" media="screen">'.NR;
	}
		
	return $args;
}


function auth_content_check( $m )
{
	static $options; # статик, чтобы не получать каждый раз опции

	if( !isset($options) )
	{

		$options = mso_get_option('plugin_auth_content', 'plugins', array());
		if( !isset($options['message']) ) $options['message'] = 'Запись только для зарегистрированных';
		if( !isset($options['format']) ) $options['format'] = '%MESSAGE%';
	}
		
	if( is_login() || is_login_comuser() )
	{
		return $m[2];
	} 
	else 
	{
		return str_ireplace('%MESSAGE%', ( isset($m[1]) && $m[1] != '' ? $m[1] : $options['message'] ), $options['format']);
	}
}

# основная функция
function auth_content_parse( $text )
{
	$preg = '~\[auth(.*?)\](.*?)\[\/auth\]~si';
	$text = preg_replace_callback($preg, "auth_content_check" , $text);
	$text = str_ireplace('[auth]', '', $text);
	$text = str_ireplace('[/auth]', '', $text);
	return $text;
}

# end file