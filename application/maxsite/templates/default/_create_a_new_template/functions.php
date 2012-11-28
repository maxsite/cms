<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


	# файл functions.php подключается при инициализации сайта
	# в этом файле нельзя выводить данные в браузер!
	
	# регистрируем сайдбары - имя, заголовок.
	# если имя совпадает, то берется последний заголовок
	mso_register_sidebar('1', tf('Первый сайдбар'));
	
	
	# меняем форат вывода заголовков Hx
	// заготовок виджета
	mso_set_val('widget_header_start', '<div class="widget_header"><span>');
	mso_set_val('widget_header_end', '</span></div><!-- class="widget_header" -->');
	
	// оставьте комментарий
	mso_set_val('leave_a_comment_start', '<div class="leave_a_comment">');
	mso_set_val('leave_a_comment_end', '</div>');
	
	//Комментариев
	mso_set_val('page_comments_count_start', '<div class="page_comments_count">');
	mso_set_val('page_comments_count_end', '</div>');
	
	//Подписаться на эту рубрику по RSS
	mso_set_val('show_rss_text_start', '<p class="show_rss_text">');
	mso_set_val('show_rss_text_end', '</p>');
	
	// Рубрика-заголовок в home-cat-block
	mso_set_val('home_full_text_cat_start', '<div class="header_home_cat">');
	mso_set_val('home_full_text_cat_end', '</div>');
	
	// Еще записи по теме
	mso_set_val('page_other_pages_start', '<div class="page_other_pages_header">');
	mso_set_val('page_other_pages_end', '</div>');

	// можно указать css-класс для BODY
	// mso_set_val('body_class', '');
	
	
	# подключим функции default-шаблона
	if (file_exists(getinfo('templates_dir') . 'default/functions-template.php')) require(getinfo('templates_dir') . 'default/functions-template.php');

	# дополнительный файл my_functions.php
	if (file_exists(getinfo('template_dir') . 'custom/my_functions.php')) require(getinfo('template_dir') . 'custom/my_functions.php');

# end file