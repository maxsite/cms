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
<link rel="stylesheet" href="<?=  getinfo('admin_url') . 'template/' . mso_get_option('admin_template', 'general', 'default') . '/loginform.css'; ?>">
</head>
<body>
<div class="all">
	<div class="all-wrap">
		<div class="logo">
			<a href="http://max-3000.com/" class="logo-cms" title="<?= t('MaxSite CMS — cистема управления сайтом') ?>"></a>
			
			<div class="name-site-descr">
				<span class="site"><?= mso_get_option('name_site', 'general') ?></span>
				<span class="descr"><?= mso_get_option('description_site', 'general') ?></span>
			</div>
			
		</div>
		
		
		<?php 
		if (!is_login())
		{
			$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : getinfo('siteurl') . mso_current_url();
			
			mso_remove_hook('login_form_auth'); # удалим все хуки для авторизации
			

?>
<form method="post" action="<?= $MSO->config['site_url'] . 'login' ?>" name="flogin">
	<input type="hidden" value="<?= $redirect_url ?>" name="flogin_redirect">
	<input type="hidden" value="<?= $MSO->data['session']['session_id'] ?>" name="flogin_session_id">
	
	<p><label><?= t('Логин') ?><br><input type="text" value="" name="flogin_user" placeholder="<?= t('ваш логин') ?>"></label></p>
	
	<p><label><?= t('Пароль') ?><br><input type="password" value="" name="flogin_password" placeholder="<?= t('ваш пароль') ?>"></label></p>
	
	<p><button type="submit" name="flogin_submit"><?= t('Войти') ?></button></p>
</form>
<?php } ?>
		
		<div class="goto-site"><a href="<?= getinfo('siteurl') ?>"><?= t('Вернуться к сайту') ?></a></div>
	</div>
</div>

</body>
</html>