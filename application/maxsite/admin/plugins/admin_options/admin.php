<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	
	// основной url этого плагина - жестко задается
	$plugin_url = $MSO->config['site_admin_url'] . 'options';
	
	// само меню
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки сайта'), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'templates', t('Шаблон сайта'), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'editor', t('Настройка редактора'), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'page_type', t('Типы страниц'), 'select'); // . ' | ';
	// $a .= mso_admin_link_segment_build($plugin_url, 'other', 'Прочее', 'select');
	
	$a = mso_hook('plugin_admin_options_menu', $a);
	
	echo $a;
?>
</div>

<?php
// Определим текущую страницу (на основе сегмента url)
$seg = mso_segment(3);

// подключаем соответственно нужный файл
if ($seg == '') require($MSO->config['admin_plugins_dir'] . 'admin_options/general.php');
	elseif ($seg == 'templates') require($MSO->config['admin_plugins_dir'] . 'admin_options/templates.php');
	elseif ($seg == 'other') require($MSO->config['admin_plugins_dir'] . 'admin_options/other.php');
	elseif ($seg == 'editor') require($MSO->config['admin_plugins_dir'] . 'admin_options/editor.php');
	elseif ($seg == 'page_type') require($MSO->config['admin_plugins_dir'] . 'admin_options/page-type.php');

?>