<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

$admin_template_url = getinfo('admin_url') . 'template/' . mso_get_option('admin_template', 'general', 'default') . '/';

?><!DOCTYPE HTML>
<html class="loginform"><head>
<meta charset="UTF-8">
<meta name="generator" content="MaxSite CMS">
<title><?= 'MaxSite CMS &ndash; ' . t('Вход в админ-панель') ?></title>
<link rel="shortcut icon" href="<?= $admin_template_url . 'assets/images/favicons/favicon1.png' ?>" type="image/x-icon">
<link rel="stylesheet" href="<?= $admin_template_url . 'assets/css/style.css'; ?>">
</head><body>
<div class="container">
<div class="flex flex-vcenter mar20-b">
	<div class="flex-grow0">
		<a href="http://max-3000.com/" class="my-q-maxsite"><img src="<?= $admin_template_url . 'assets/images/maxsitelogo.fw.png' ?>" alt="MaxSite CMS" title="MaxSite CMS"></a>
	</div>
	<div class="flex-grow3 pad10-l">
		<div class="t250 normal t-gray200">MaxSite CMS</div>
	</div>
</div>
<?php if (!is_login()) :
		$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? mso_clean_str($_SERVER['HTTP_REFERER'], 'xss') : getinfo('siteurl') . mso_current_url();
		mso_remove_hook('login_form_auth'); # удалим все хуки для авторизации
?>
<form method="post" action="<?= $MSO->config['site_url'] . 'login' ?>" name="flogin">
	<input type="hidden" value="<?= $redirect_url ?>" name="flogin_redirect">
	<input type="hidden" value="<?= $MSO->data['session']['session_id'] ?>" name="flogin_session_id">
	<p><label><i class="i-user icon-square"></i><input type="text" value="" required name="flogin_user" placeholder="<?= t('ваш логин...') ?>"></label></p>
	<p><label><i class="i-key icon-square"></i><input type="password" value="" required name="flogin_password" placeholder="<?= t('ваш пароль...') ?>"></label></p>
	<p><button type="submit" name="flogin_submit" class="w100 button mar10-t"><?= t('Вход в админ-панель') ?></button></p>
</form>
<?php endif ?>
<div class="t-right t-gray100"><a class="t-gray500 t80 hover-no-color" href="<?= getinfo('siteurl') ?>"><?= t('Вернуться к сайту') ?></a></div>
</div>
</body></html>