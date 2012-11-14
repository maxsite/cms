<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	
	// основной url этого плагина - жестко задается
	$plugin_url = $MSO->config['site_admin_url'] . 'sidebars';
	
	// само меню
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки сайдбаров'), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'widgets', t('Настройка виджетов'), 'select');
	
	echo $a;
?>
</div>

<?php
// Определим текущую страницу (на основе сегмента url)
$seg = mso_segment(3);

// подключаем соответственно нужный файл
if ($seg == '') require($MSO->config['admin_plugins_dir'] . 'admin_sidebars/sidebars.php');
	elseif ($seg == 'widgets') require($MSO->config['admin_plugins_dir'] . 'admin_sidebars/widgets.php');

?>