<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
    (c) MaxSite CMS, http://max-3000.com/
*/

// префикс опций компонента
$prefix = 'footer_cols1_';

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option($prefix . 'rules_output', getinfo('template'), '')))
{
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

$CI = & get_instance();	

$copy_maxsite = sprintf( tf('Работает на <a href="http://max-3000.com/">MaxSite CMS</a> | Время: {elapsed_time} | SQL: %s | Память: {memory_usage}'), $CI->db->query_count) . '<!--global_cache_footer-->';

if (is_login())
	$login = ' | <a href="' . getinfo('siteurl') . 'admin">' . tf('Управление') . '</a> | '
		. '<a href="' . getinfo('siteurl') . 'logout">' . tf('Выйти') . '</a>';
else
	$login = ' | <a href="' . getinfo('siteurl') . 'login">' . tf('Вход') . '</a>';

// используем php-шаблонизатор
// eval(mso_tmpl_prepare($opt));

$container_css = mso_get_option($prefix . 'container_css', getinfo('template'), '');

$block1 = mso_get_option($prefix . 'block1', getinfo('template'), '');
$block1_css = mso_get_option($prefix . 'block1_css', getinfo('template'), '');

$block2 = mso_get_option($prefix . 'block2', getinfo('template'), '');
$block2_css = mso_get_option($prefix . 'block2_css', getinfo('template'), '');

$block3 = mso_get_option($prefix . 'block3', getinfo('template'), '');
$block3_css = mso_get_option($prefix . 'block3_css', getinfo('template'), '');

$block4 = mso_get_option($prefix . 'block4', getinfo('template'), '');
$block4_css = mso_get_option($prefix . 'block4_css', getinfo('template'), '');

$block5 = mso_get_option($prefix . 'block5', getinfo('template'), '');
$block5_css = mso_get_option($prefix . 'block5_css', getinfo('template'), '');


?>
<div class="layout-center-wrap <?= $container_css ?>"><div class="layout-wrap flex flex-wrap">
<?php 
	if ($block1) 
	{	
		echo '<div class="' . $block1_css . '">';
		eval(mso_tmpl_prepare($block1));
		echo '</div>';
	}
	
	if ($block2) 
	{	
		echo '<div class="' . $block2_css . '">';
		eval(mso_tmpl_prepare($block2));
		echo '</div>';
	}
	
	if ($block3) 
	{	
		echo '<div class="' . $block3_css . '">';
		eval(mso_tmpl_prepare($block3));
		echo '</div>';
	}
	
	if ($block4) 
	{	
		echo '<div class="' . $block4_css . '">';
		eval(mso_tmpl_prepare($block4));
		echo '</div>';
	}
	
	if ($block5) 
	{	
		echo '<div class="' . $block5_css . '">';
		eval(mso_tmpl_prepare($block5));
		echo '</div>';
	}
?>
</div></div> 
