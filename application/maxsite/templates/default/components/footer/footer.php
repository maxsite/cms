<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/
*/

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option('footer_rules_output', getinfo('template'), '')))
{
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	if ($rules_result === false) $rules_result = 1; 
	if ($rules_result !== 1) return; // выход
}

$CI = & get_instance();	

$copy_maxsite = sprintf( tf('Работает на <a href="http://max-3000.com/">MaxSite CMS</a> | Время: {elapsed_time} | SQL: %s | Память: {memory_usage}'), $CI->db->query_count) . '<!--global_cache_footer--> | ';

if (is_login())
	$login = '<a href="' . getinfo('siteurl') . 'admin">' . tf('Управление') . '</a> | '
		. '<a href="' . getinfo('siteurl') . 'logout">' . tf('Выйти') . '</a>';
else
	$login = '<a href="' . getinfo('siteurl') . 'login">' . tf('Вход') . '</a>';
	

$footer_block1 = mso_get_option('footer_block1', getinfo('template'), '<div class="hide-print flex flex-wrap">
<div class="w30">Блок 1</div>
<div class="w30">Блок 2</div>
<div class="w30">Блок 3</div>
</div>'); 

$footer_block2 = mso_get_option('footer_block2', getinfo('template'), 'Блок 4');

eval(mso_tmpl_prepare($footer_block1)); 

?>
<div class="hide-print flex flex-wrap">
	<div class="t-white t90 hover-no-color links-no-color pad20-t">
		<div class="">&copy; <?php echo getinfo('name_site') . ', ' . date('Y'); ?></div>
		<div class=""><?= $copy_maxsite ?> <?= $login ?></div>
	</div>
	
	<div class="pad10-t">
		<?php eval(mso_tmpl_prepare($footer_block2)) ?>
	</div>
</div>
