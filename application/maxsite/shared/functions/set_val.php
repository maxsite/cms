<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) MaxSite CMS
 * http://max-3000.com/
 *
 * меняем формат вывода некоторых блоков
 *
 */

	// заголовок виджета
	mso_set_val('widget_header_start', '<div class="widget_header"><span>');
	mso_set_val('widget_header_end', '</span></div><!-- class="widget_header" -->');
	
	// Еще записи по теме
	mso_set_val('page_other_pages_start', '<div class="page_other_pages_header">');
	mso_set_val('page_other_pages_end', '</div>');

	// можно указать css-класс для BODY
	if (is_type('home')) mso_set_val('body_class', 'body-home');
	else mso_set_val('body_class', 'body-all body-' . getinfo('type'));
	
# end file