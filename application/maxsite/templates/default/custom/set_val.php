<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

mso_set_val('head_section_html_add', 'lang="ru"');
mso_set_val('mso-page-content-add-class', 'lightgallery1'); // класс лайтгалери для блока записи

// можно указать css-класс для BODY
if (is_type('home'))
	mso_set_val('body_class', 'mso-body-home lightgallery1'); // класс лайтгалери для главной
else
	mso_set_val('body_class', 'mso-body-all mso-body-' . getinfo('type'));

# end of file
