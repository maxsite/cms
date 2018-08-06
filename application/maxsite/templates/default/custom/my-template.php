<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// шорткод lightslider, если нужен в шаблоне
if ($fn = mso_fe('components/lightslider/lightslider-shortcode.php')) require_once($fn);

// корзина
if ($fn = mso_fe('custom/cart/cart.php')) require_once($fn);

$this_version_template = '20180615';

// иммитация старого default
// mso_delete_option('template_set_component_options', getinfo('template'));
// my_set_opt('template_set_component_options', true);
// return;


// Реинициализация опций шаблона
// Для принудительной инициализации нужно указать 1
// достаточно один раз обновить любую страницу сайта, после вернуть 0 
my_delete_opt(0);

$current_version_template = mso_get_option('template_set_component_options', getinfo('template'), false);

if ($current_version_template == 1) // обновление старой версии default-шаблона
{
	// обновляем файлы и опции
	my_update_file();
	my_update_opt($this_version_template);
	// pr('Update old Default template');
}
elseif ($current_version_template == false) // нет опций — это новый шаблон
{
	// только опции
	my_update_opt($this_version_template);
	// pr('Set New options');
}

// обновление файлов от старой default версии шаблона
function my_update_file() 
{
	// удаляем старые файлы
	if ($fn = mso_fe('type_foreach/info-top.php')) @unlink($fn);
	if ($fn = mso_fe('type_foreach/_next-prev.php')) @unlink($fn);
	if ($fn = mso_fe('type_foreach/info-top/next-prev.php')) @unlink($fn);
	if ($fn = mso_fe('type_foreach/info-top/header-only-next-prev.php')) @unlink($fn);
	if ($fn = mso_fe('assets/css/profiles/theme-blue.css')) @unlink($fn);
	if ($fn = mso_fe('type/home/-units.php')) @unlink($fn);
}

// обновление опций
function my_update_opt($this_version_template) 
{
	// определим выбранные компоненты
	my_set_opt('header_component1', 'top1');
	my_set_opt('header_component2', 'lightslider');
	my_set_opt('header_component3', '');
	my_set_opt('header_component4', '');
	my_set_opt('header_component5', '');
	
	my_set_opt('footer_component1', 'footer-cols1');
	my_set_opt('footer_component2', 'footer-menu');
	my_set_opt('footer_component3', 'footer-copy-stat');
	my_set_opt('footer_component4', '');
	my_set_opt('footer_component5', '');
	
	// дефолтные компоненты отдельными файлами 
	if ($fn = mso_fe('custom/def/footer-cols1.php')) require($fn);
	if ($fn = mso_fe('custom/def/lightslider.php')) require_once($fn);
	
	// top1 - прописываем опции
	my_set_opt('top1_header_logo', getinfo('template_url') . 'assets/images/logos/logo01.png');
	my_set_opt('top1_header_logo_width', 0);
	my_set_opt('top1_header_logo_height', 0);
	my_set_opt('top1_header_logo_type_resize', 'resize_full_crop_center');
	my_set_opt('top1_header_logo_attr', '');
	my_set_opt('top1_block', '<div class="pad30 bg-gray300 t-center">Free Block</div>');
	my_set_opt('top1_rules_output', '');
	
	
	my_set_opt('info-top_page', ''); // page-default.php
	my_set_opt('info-top_home', 'full-default.php');
	my_set_opt('info-top_category', 'full-default.php');
	my_set_opt('info-top_tag', 'full-default.php');
	my_set_opt('info-top_archive', 'full-default.php');
	my_set_opt('info-top_author', 'full-default.php');
	
	// опция-флаг, указывающая, что компоненты были установлены 
	// и больше не требуют обновлений
	my_set_opt('template_set_component_options', $this_version_template);
}


// удаление флага-опции
// если $a = 1 или true
function my_delete_opt($a = 0)
{
	if ($a) mso_delete_option('template_set_component_options', getinfo('template'));
}

// присваивает опции (для текущего шаблона) значение
// если опция не содержит заданного значение
function my_set_opt($key, $val = '')
{
	if (mso_get_option($key, getinfo('template'), false) != $val)
		mso_add_option($key, $val, getinfo('template'));
}

# end of file
