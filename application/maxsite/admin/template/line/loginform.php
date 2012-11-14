<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * 19-01-2011 : http://forum.max-3000.com/viewtopic.php?p=12195#p12195
 *
 */
 
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?= 'MaxSite CMS &ndash; ' . t('Вход в админ-панель') ?></title>
<link rel="shortcut icon" href="<?=getinfo('siteurl')?>favicon.ico"type="image/x-icon">
<meta name="generator" content="MaxSite CMS">

<style type="text/css">
html, body, div, span, p, a, form {margin: 0; padding: 0; border: none;}

body {height: auto; font-size: 11pt; background-color: #f2f3f5; font-family: "Segoe UI", Arial, "Liberation Sans", sans-serif; line-height: 1.4em;}

a {color: #616264;}
a:hover {color: #c40;}

#login {position: relative; width: 340px; background: #fcfcff url("<?= getinfo('admin_url') ?>template/default/images/maxsitelogo-white.gif") no-repeat 96% 4%; padding: 10px 15px; border: #dce0e7 solid 4px; margin: 100px auto; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;} 

#site {font-size: 9pt;}

#cms_name { color: #919294; font: 20pt "Trebuchet MS", Verdana, FreeSans, sans-serif; margin: 5px 0; } 
#cms_name span {color: #E77844;}

#entry {color: #818284; font-size: 10pt; margin: 25px 0 10px 0;}

form.flogin {color: #808080; font-weight: bold;}
form.flogin input. span {display: block;}
form.flogin input.flogin_user, form.flogin input.flogin_password {width: 330px; padding: 3px; border: 1px solid #dce0e7;}
form.flogin input.flogin_user:focus, form.flogin input.flogin_password:focus {border: 1px solid #D6AE9C; background: #F9F2EF; }
form.flogin input.flogin_user {margin: 0 0 10px 0; }
form.flogin input.flogin_submit {float: right; margin: 20px 0 0 0; border: 1px solid #808080; padding: 3px;}
form.flogin input.flogin_submit:hover {border: 1px solid #D6AE9C; background: #F9F2EF; color: #E77844;}

#cms {color: #717274; font-size: 9pt; margin: 20px 0 0px 0;}

</style>
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