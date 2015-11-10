<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	# поскольку в хуках может быть вывод данных через echo, следует 
	# включить буферизацию вывода на каждый хук
	
	global $admin_menu_select_option;
		
	// в теле контента можно определить хуки на остальные части 
	ob_start();
		$admin_content_hook = mso_hook('mso_admin_content', mso_admin_content());
		$admin_content = ob_get_contents() . $admin_content_hook;
	ob_end_clean();
	
	ob_start();
		$admin_menu_hook = mso_hook('mso_admin_menu', mso_admin_menu());
		$admin_menu = ob_get_contents() . $admin_menu_hook;
	ob_end_clean();
	
	ob_start();
		$admin_footer_hook = mso_hook('mso_admin_footer', mso_admin_footer());
		$admin_footer = ob_get_contents() . $admin_footer_hook;
	ob_end_clean();
	
	// url каталог текущего шаблона
	$admin_template_url = getinfo('admin_url') . 'template/' . mso_get_option('admin_template', 'general', 'default') . '/';
	
	$admin_css = $admin_template_url . 'assets/css/style.css';
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

?><!DOCTYPE HTML>
<html><head>
<meta charset="UTF-8">
<title><?= $admin_title ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="generator" content="MaxSite CMS">
<link rel="shortcut icon" href="<?= $admin_template_url . 'assets/images/favicons/favicon1.png' ?>" type="image/x-icon">
<link rel="stylesheet" href="<?= $admin_css ?>">
<?= $admin_css_profile ?>
<?= mso_load_jquery() ?>
<?php mso_hook('admin_head') ?>
<?php mso_hook('admin_head_css') ?>
</head>
<body class="admin-<?= mso_segment(2) ?>"><?php mso_hook('admin_body_start'); ?>

<div class="visible-tablet-phone">
	<div class="flex flex-wrap-phone flex-vcenter pad10-rl my-nav-panel-tablet">
		<div class="flex-grow0 pad5-t">
			<a href="http://max-3000.com/"><img src="<?= $admin_template_url . 'assets/images/maxsitelogo.fw.png' ?>" alt="" title="MaxSite CMS" class="mar10-b mar10-r"></a>
			<a href="<?= getinfo('site_url') ?>" class="my-q-site" title="<?= t('Переход к сайту') ?>"></a>
			<a href="<?= getinfo('site_admin_url') ?>home" class="my-q-dashboard" title="<?= t('Консоль') ?>"></a>
			<a href="<?= getinfo('site_admin_url') ?>users_my_profile" class="my-q-my_profile" title="<?= t('Мой профиль') ?>"></a>
			<a href="<?= getinfo('site_url') ?>logout" class="my-q-logout mar20-r" title="<?= t('Выход') ?>"></a>
		</div>
		<div class="flex-grow2 pad5-b"><select id="my-nav-panel" class="w100"><?= $admin_menu_select_option ?></select></div>
	</div>
</div>


<div id="sh-my-nav-panel" class="h32px bg-color1 t-white t18px i-ellipsis-v hover-bg-blue600 cursor-pointer pos-absolute hide-tablet" style="width: 12px; border-radius: 0 5px 5px 0; line-height: 32px; padding-left: 3px; left: 200px;"></div>

<div class="flex">
	<div class="flex-grow1 w30 w200px-max my-nav-panel hide-tablet">
		<div class="mar10-rl mar10-t mar15-b">
			<div class="flex flex-vcenter mar10-b">
				<div class="flex-grow0">
					<a href="http://max-3000.com/" class="my-q-maxsite"><img src="<?= $admin_template_url . 'assets/images/maxsitelogo.fw.png' ?>" alt="" title="MaxSite CMS"></a>
				</div>
				
				<div class="flex-grow3 pad10-l">
					<div class="t110 normal t-gray200">MaxSite CMS</div>
					<div class="t80 t-gray500">Version <?= $MSO->version ?></div>
				</div>
			</div>
			
			<div class="flex">
				<a href="<?= getinfo('site_url') ?>" class="my-q-site" title="<?= t('Переход к сайту') ?>"></a>
				<a href="<?= getinfo('site_admin_url') ?>home" class="my-q-dashboard" title="<?= t('Консоль') ?>"></a>
				<a href="<?= getinfo('site_admin_url') ?>users_my_profile" class="my-q-my_profile" title="<?= t('Мой профиль') ?>"></a>
				<a href="<?= getinfo('site_url') ?>logout" class="my-q-logout mar5-r" title="<?= t('Выход') ?>"></a>
			</div>
		</div>
		
		<div class="mainmenu">
			<?= $admin_menu ?>
		</div>
	</div>
	
	<div class="flex-grow2 pad20-rl pad20-b w100-tablet">
		<?= $admin_content ?>
	</div>
</div>
<?= mso_load_jquery('jquery.cookie.js') ?>
<?= mso_load_jquery('jquery.showhide.js') ?>
<script src="<?= $admin_template_url ?>assets/js/jquery.tablesorter.js"></script>
<script src="<?= $admin_template_url ?>assets/js/jquery.fullscreen.js"></script>
<script src="<?= $admin_template_url ?>assets/js/my.js"></script>
</body></html>