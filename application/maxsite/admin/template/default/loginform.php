<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
?><!DOCTYPE HTML>
<html><head>
<meta charset="UTF-8">
<meta name="generator" content="MaxSite CMS">
<title><?= 'MaxSite CMS &ndash; ' . t('Вход в админ-панель') ?></title>
<link rel="shortcut icon" href="<?= getinfo('template_url') . 'images/favicons/' . mso_get_option('default_favicon', 'templates', 'favicon1.png') ?>" type="image/x-icon">
<link rel="stylesheet" href="<?=  getinfo('admin_url') . 'template/' . mso_get_option('admin_template', 'general', 'default') . '/loginform.css'; ?>">
</head>

<body>

<div id="login">
	<p id="site"><a href="<?= getinfo('siteurl') ?>" title="<?= t('Вернуться к сайту') ?>"><?= getinfo('name_site') ?></a></p>
	<p id="cms_name"><span>M</span>ax<span>S</span>ite CMS</p>
	<p id="entry"><?= t('Для входа в админ-панель введите логин и пароль') ?></p>

<?php 
	if (!is_login())
	{
		$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : getinfo('siteurl') . mso_current_url();
		
		mso_remove_hook('login_form_auth'); # удалим все хуки для авторизации
				
		mso_login_form(array( 
			'login'=>t('Логин'), 
			'password'=> t('Пароль'), 
			'submit'=>'', 
			'submit_value'=> t('Войти'),
			'form_end'=>'<br clear="all">',
			),
			$redirect_url);
	}
?>

	<p id="cms">&copy; <a href="http://max-3000.com/" target="_blank" title="<?= t('Система управления сайтом MaxSite CMS') ?>">MaxSite CMS</a>, 2008&ndash;<?= date('Y') ?></p>
</div>
</body>
</html>