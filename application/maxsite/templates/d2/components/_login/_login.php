<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*

	Файл: _login.php

	Описание: Форма логина. Подкомпонет.
		Если есть залогиненность, то выводим приветствие.
	
	PHP-связи:
		if ($fn = mso_fe('components/_login/_login.php')) require($fn);

*/

echo mso_load_jquery('jquery.dropdown.js');

if (is_login()) // юзер
{
	$out = '
		
	<a href="#" data-dropdown="#dropdown-1" class="dropdown">' . t('Привет,') . ' ' . getinfo('users_nik') . '!</a>

	<div id="dropdown-1" class="dropdown-menu has-tip anchor-right">
	<ul>
		<li><a href="' . getinfo('siteurl') . 'admin">' . t('Админ-панель') . '</a></li>
		<li><a href="' . getinfo('site_admin_url') . 'page_new">' . t('Создать запись') . '</a></li>
		<li><a href="' . getinfo('site_admin_url') . 'page">' . t('Список записей') . '</a></li>
		<li><a href="' . getinfo('site_admin_url') . 'cat">' . t('Рубрики') . '</a></li>
		<li><a href="' . getinfo('site_admin_url') . 'plugins">' . t('Плагины') . '</a></li>
		<li><a href="' . getinfo('site_admin_url') . 'files">' . t('Загрузки') . '</a></li>
		<li><a href="' . getinfo('site_admin_url') . 'sidebars">' . t('Сайдбары') . '</a></li>
		<li><a href="' . getinfo('site_admin_url') . 'options">' . t('Основные настройки') . '</a></li>
		<li><a href="' . getinfo('site_admin_url') . 'template_options">' . t('Настройка шаблона') . '</a></li>
		<li class="divider"></li>
		<li><a href="' . getinfo('siteurl') . 'logout">' . t('Выйти') . '</a></li>
	</ul>
	</div>
	';
			
}
elseif ($comuser = is_login_comuser()) // комюзер
{

	if (!$comuser['comusers_nik']) $cun = t('Привет!');
		else $cun = t('Привет,') . ' ' . $comuser['comusers_nik'] . '!';
	
	$out = '
	<a href="#" data-dropdown="#dropdown-1" class="dropdown">' . $cun . '</a>

	<div id="dropdown-1" class="dropdown-menu has-tip anchor-right">
	<ul>
		<li><a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' . t('Своя страница') . '</a></li>
		<li><a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '/edit">' . t('Редактировать данные') . '</a></li>
		<li class="divider"></li>
		<li><a href="' . getinfo('siteurl') . 'logout">' . t('Выйти') . '</a></li>
	</ul>
	</div>
	';

}
else // нет залогирования, выводим форму
{
	global $MSO;
	
	// если разрешены регистрации, то выводим ссылку
	if (mso_get_option('allow_comment_comusers', 'general', '1'))
	{
		$registration = ' <span class="registration"><a href="' . getinfo('siteurl') . 'registration">' . tf('Регистрация') . '</a></span>';
		$reg_text = t('Вход / Регистрация');
	}
	else 
	{
		$registration = '';
		$reg_text = t('Вход');
	}
	
	
	// возможен вход через соцсеть
	$hook_login_form_auth = mso_hook_present('login_form_auth') ? '<span class="login-form-auth-title">' . tf('Вход через:') . ' </span>' . mso_hook('login_form_auth') : '';
	
	if ($hook_login_form_auth)
	{
		$hook_login_form_auth = trim(str_replace('[end]', '     ', $hook_login_form_auth));
		$hook_login_form_auth = '<p class="login-form-auth">' . str_replace('     ', ', ', $hook_login_form_auth) . '</p>';
	}
	else
	{
		$hook_login_form_auth = '';
	}
	
	$out = '
			
	<a href="#" data-dropdown="#dropdown-1" class="dropdown">' . $reg_text . '</a>

	<div id="dropdown-1" class="dropdown-menu has-tip anchor-right">
	<ul><li>
		<form method="post" action="' . $MSO->config['site_url'] . 'login" name="flogin">
			<input type="hidden" value="' . $MSO->config['site_url'] . mso_current_url() . '" name="flogin_redirect">
			<input type="hidden" value="' . $MSO->data['session']['session_id'] . '" name="flogin_session_id">
			
			<p class="user"><input type="text" value="" name="flogin_user" placeholder="' . t('email/логин') .'"></p>
			<p class="password"><input type="password" value="" name="flogin_password" placeholder="' . t('пароль') .'"></p>
			<p class="submit"><button type="submit" name="flogin_submit">Вход</button>' . $registration . '</p>'
			. $hook_login_form_auth . '
		</form>
	</li></ul>
	</div>';

}


echo $out;

# end file