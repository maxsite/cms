<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function auth_content_autoload() 
{
	mso_hook_add( 'content', 'auth_content_parse'); # хук на админку
}

function auth_content_check ($m) 
{
	if ( is_login() || is_login_comuser() ) 
	{
		return $m[1];
	} 
	else 
	{
		return 'Запись только для зарегистрированных';
	}
}

function auth_content_parse($text) 
{
	$preg = '~\[auth\](.*?)\[\/auth\]~si';
	$text = preg_replace_callback($preg, "auth_content_check" , $text);
	$text = str_ireplace('[auth]', '', $text);
	$text = str_ireplace('[/auth]', '', $text);
	return $text;
}

# end file
