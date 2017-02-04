<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// шорткод lightslider, если нужен в шаблоне
if ($fn = mso_fe('components/lightslider/lightslider-shortcode.php')) require_once($fn);

// Инициализация опций шаблона. 
// Для принудительной инициализации нужно раскоментировать строчку
// достаточно один раз обновить любую страницу сайта, после вновь закоментировать 
# mso_delete_option('template_set_component_options', getinfo('template'));

if (mso_get_option('template_set_component_options', getinfo('template'), false) === false)
{
	// определим выбранные компоненты
	my_set_opt('header_component1', 'top1');
	my_set_opt('header_component2', '');
	my_set_opt('header_component3', '');
	my_set_opt('header_component4', '');
	my_set_opt('header_component5', '');
	
	my_set_opt('footer_component1', 'footer-copy-stat');
	my_set_opt('footer_component2', '');
	my_set_opt('footer_component3', '');
	my_set_opt('footer_component4', '');
	my_set_opt('footer_component5', '');
	
	// top1 - прописываем опции
	my_set_opt('top1_header_logo', getinfo('template_url') . 'assets/images/logos/logo01.png');
	my_set_opt('top1_header_logo_width', 0);
	my_set_opt('top1_header_logo_height', 0);
	my_set_opt('top1_header_logo_type_resize', 'resize_full_crop_center');
	my_set_opt('top1_header_logo_attr', '');
	my_set_opt('top1_block', '');
	my_set_opt('top1_rules_output', '');
	
	my_set_opt('info-top_page', 'next-prev.php');
	
	// опция-флаг, указывающая, что компоненты были установлены 
	// и больше не требуют обновлений
	my_set_opt('template_set_component_options', true);
}


// присваивает опции (для текущего шаблона) значение
// если опция не содержит заданного значение
function my_set_opt($key, $val = '')
{
	if (mso_get_option($key, getinfo('template'), false) != $val)
		mso_add_option($key, $val, getinfo('template'));
}

# end of file