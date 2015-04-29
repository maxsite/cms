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
	mso_set_val('widget_header_end', '</span></div>');
	
	// mso_set_val('jquery_url', 'URL-адрес загрузки jQuery');
	
	// можно указать css-класс для BODY
	if (is_type('home')) mso_set_val('body_class', 'mso-body-home');
	else mso_set_val('body_class', 'mso-body-all mso-body-' . getinfo('type'));
	
	
# end file