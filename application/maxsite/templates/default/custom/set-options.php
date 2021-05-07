<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Инициализация опций шаблона
 */

// Для принудительной инициализации нужно раскоментировать строчку
// достаточно один раз обновить любую страницу сайта, после вновь закоментировать
// mso_delete_option('template_set_component_options', getinfo('template'));

if (mso_get_option('template_set_component_options', getinfo('template'), false) === false) {
	
	my_set_opt('default_profiles', ['fontawesome5-lazy.css']);
	my_set_opt('fonts_template', ['opensans.css', 'robotoslab.css']);
	my_set_opt('menu_template', 'menu1alt.css');

	// определим выбранные компоненты
	my_set_opt('header_component1', 'header11');
	my_set_opt('header_component2', '');
	my_set_opt('header_component3', '');
	my_set_opt('header_component4', '');
	my_set_opt('header_component5', '');

	my_set_opt('footer_component1', '');
	my_set_opt('footer_component2', 'footer-copy-stat');
	my_set_opt('footer_component3', '');
	my_set_opt('footer_component4', '');
	my_set_opt('footer_component5', '');

	// дефолтные опции компонентов
	// my_set_opt('main_template_home', '');

	my_set_opt('info-top_page', '');
	my_set_opt('info-top_home', '');
	my_set_opt('info-top_category', '');
	my_set_opt('info-top_tag', '');
	my_set_opt('info-top_archive', '');
	my_set_opt('info-top_author', '');

	// персональные опции компонентов

	// file1
	my_set_opt('file1_file', '');
	my_set_opt('file1_use_tmpl', 1);
	my_set_opt('file1_rules_output', '');

	// file2
	my_set_opt('file2_file', '');
	my_set_opt('file2_use_tmpl', 1);
	my_set_opt('file2_rules_output', '');

	// any1
	my_set_opt('any1_block', '');
	my_set_opt('any1_rules_output', '');

	// any2
	my_set_opt('any2_block', '');
	my_set_opt('any2_rules_output', '');

	// footer_any1
	// my_set_opt('footer_any1_block', '<div class="layout-center-wrap"><div class="layout-wrap">{{$copy_maxsite}} {{$login}}</div></div>');
	// my_set_opt('footer_any1_rules_output', '');
	
	// сброс всех опций компонентов
	mso_delete_option_mask('header6-', getinfo('template'));
	mso_delete_option_mask('header7-', getinfo('template'));
	mso_delete_option_mask('header11-', getinfo('template'));
	
	mso_delete_option_mask('module1-', getinfo('template'));
	mso_delete_option_mask('module2-', getinfo('template'));
	
	mso_delete_option_mask('footer-copy-stat-', getinfo('template'));

	// опция-флаг, указывающая, что компоненты были установлены и больше не требуют обновлений
	my_set_opt('template_set_component_options', true);
}

# end of file
