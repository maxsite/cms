<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


	# поскольку в хуках может быть вывод данных через echo, следует 
	# включить буферизацию вывода на каждый хук
	
	// в теле контента можно определить хуки на остальные части 
	ob_start();
		$admin_content_hook = mso_hook('mso_admin_content', mso_admin_content());
		$admin_content = ob_get_contents() . $admin_content_hook;
	ob_end_clean();
	
	ob_start();
		$admin_header_hook = mso_hook('mso_admin_header', mso_admin_header());
		$admin_header = ob_get_contents() . $admin_header_hook;
	ob_end_clean();
	
	ob_start();
		$admin_menu_hook = mso_hook('mso_admin_menu', mso_admin_menu());
		$admin_menu = ob_get_contents() . $admin_menu_hook;
	ob_end_clean();
	
	ob_start();
		$admin_footer_hook = mso_hook('mso_admin_footer', mso_admin_footer());
		$admin_footer = ob_get_contents() . $admin_footer_hook;
	ob_end_clean();
	
	if (!$admin_header) $admin_header = t('Админ-панель');
	
	// url каталог текущего шаблона
	$admin_template_url = getinfo('admin_url') . 'template/' . mso_get_option('admin_template', 'general', 'default') . '/';
	
	$admin_css = $admin_template_url . 'style.css';
	$admin_css = mso_hook('admin_css', $admin_css);
	
	$admin_css_profile = ''; // дополнительные css-файлы
	
	if ($admin_css_profile_s = mso_get_option('admin_template_profile', 'general', '')) 
	{
			$admin_css_profile_s = mso_explode($admin_css_profile_s, false);
			
			foreach ($admin_css_profile_s as $css)
			{
				$admin_css_profile .= '<link rel="stylesheet" href="' . $admin_template_url . 'profiles/' . $css . '">';
			}
	}
	
	$admin_title = t('Админ-панель') . ' - ' . mso_hook('admin_title', mso_head_meta('title'));
	
	global $admin_menu_bread;
	
	if ($admin_menu_bread) 
	{
		$admin_menu_bread = '<div class="admin-menu-bread">' . implode(' » ', $admin_menu_bread) . '</div>';
	}
	elseif (mso_segment(2) == 'plugin_options') // отдельно для опций плагинов
	{
		$admin_menu_bread = '<div class="admin-menu-bread">' . t('Опции плагинов') . '</div>';
	}
	else
	{
		$admin_menu_bread = '';
	}
	
	$avatar_url = $MSO->data['session']['users_avatar_url'];
	
	if (!$avatar_url)
	{
		$avatar_url = $admin_template_url . 'images/avatar_user.png';
	}
	
	// панель уведомлений — задел на будущее
	$notification = '';
	
	
?><!DOCTYPE HTML>
<html><head>
<meta charset="UTF-8">
<title><?= $admin_title ?></title>
<link rel="shortcut icon" href="<?= $admin_template_url . 'images/favicon1.png' ?>" type="image/x-icon">
<link rel="stylesheet" href="<?= $admin_css ?>">
<?= $admin_css_profile ?>
<?= mso_load_jquery() ?>
<!-- <?= mso_load_jquery('jquery.scripts.js', $admin_template_url . 'js/') ?> -->
<?php mso_hook('admin_head') ?>
</head>
<body class="admin-<?= mso_segment(2) ?>"><?php mso_hook('admin_body_start'); ?>
<div class="all">
	<div class="all-wrap">
	
		<div class="header"><div class="header-wrap">
			
			<a href="http://max-3000.com/" class="logo-cms" title="MaxSite CMS"></a>
			
			<div class="name-site-descr">
				<a href="<?= getinfo('siteurl') ?>"><?= mso_get_option('name_site', 'general') ?></a>
				<span class="descr"><?= mso_get_option('description_site', 'general') ?></span>
			</div>
			
			<div class="user"> 
				<span class="avatar"><img src="<?= $avatar_url ?>"></span>
				<span class="users-nik"><?= t('Привет,') ?> <?= getinfo('users_nik') ?>!</span>
				<span class="users-action"><a href="<?= getinfo('siteurl') ?>admin/users_my_profile"><?= t('Профиль') ?></a> / <a href="<?= getinfo('siteurl') ?>logout"><?= t('Выход') ?></a></span>
			</div>
			
			<div class="notification"><?= $notification ?></div>
			
			<div class="clearfix"></div>
		</div></div>
		
		<div class="main"><div class="main-wrap">
		
			<div class="sidebar"><div class="sidebar-wrap">
				<div class="mainmenu">
					<?= $admin_menu ?>
					<div class="clearfix"></div>
					<!-- ?= $admin_menu_bread ? -->
				</div>
			</div></div>
			
			<div class="content"><div class="content-wrap">
				<?= $admin_menu_bread ?>
				<!-- <div class="info"><?= $admin_header ?></div> -->
			
				<?= $admin_content ?>
			
			</div></div><!-- div.content div.content-wrap -->
		
		</div></div>
		

	</div>
</div>
<div class="footer"><div class="footer-wrap">
	<?= $admin_footer ?>
</div></div>

</body></html>