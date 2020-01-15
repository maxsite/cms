<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// добавляем куку ко всему сайту с помощью сессии и редиректа 
// на главную или другую указанную страницу (после главной)
function mso_add_to_cookie($name_cookies, $value, $expire, $redirect = false)
{
	$CI = &get_instance();

	if (isset($CI->session->userdata['_add_to_cookie']))
		$add_to_cookie = $CI->session->userdata['_add_to_cookie'];
	else
		$add_to_cookie = [];

	$add_to_cookie[$name_cookies] = ['value' => $value, 'expire' => $expire];

	$CI->session->set_userdata(array('_add_to_cookie' => $add_to_cookie));
	$CI->session->set_userdata(array('_add_to_cookie_redirect' => $redirect)); // куда редиректимся

	if ($redirect) {
		mso_redirect(getinfo('siteurl'), true);
		exit;
	}
}

// получаем куку. Если нет вообще или нет в $allow_vals, то возвращает $def_value
function mso_get_cookie($name_cookies, $def_value = '', $allow_vals = [])
{
	if (!isset($_COOKIE[$name_cookies])) return $def_value; // нет вообще

	$value = $_COOKIE[$name_cookies]; // значение куки

	if ($allow_vals) {
		if (in_array($value, $allow_vals))
			return $value; // нет в разрешенных
		else
			return $def_value;
	} else {
		return $value;
	}
}

# end of file
