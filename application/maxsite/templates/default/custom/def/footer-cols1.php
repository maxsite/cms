<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	дефолтные опции компонента footer_cols1 
*/

// Контейнер (css-классы) (footer-cols1)
my_set_opt('footer_cols1_container_css', 'bg-color2 t-white pad20-rl pad20-b hide-print my-footer1');

// Блоки | css-классы
my_set_opt('footer_cols1_block1', "{% if (function_exists('last_pages_unit_widget_custom')) echo last_pages_unit_widget_custom(array(
'header' => '<div class=\"mso-widget-header\"><span>' . t('Последние записи') . '</span></div>',
'prefs' => '

limit = 4
line1 = [thumb]
line2 = [title]
line3 = 
line4 = 
line5 = 
page_start = <div class=\"mar15-tb flex\">
page_end = </div>
title_start = <div class=\"flex-grow1 links-no-color mar10-l\">
title_end = </div>
block_start= <div>
block_end = </div>
content = 0
thumb_class = 
thumb_link_class = w100px-min
thumb_width = 100
thumb_height = 56

',
'_footer'
)); %}
");

my_set_opt('footer_cols1_block1_css', 'w30 w45-tablet w100-phone mar10-tb links-no-color links-hover-t-gray100 t90');

my_set_opt('footer_cols1_block2', "{% if (function_exists('tagclouds_widget_custom')) echo tagclouds_widget_custom(array(
'header' => '<div class=\"mso-widget-header\"><span>' . t('Метки') . '</span></div>',
'format' => '<a class=\"t90 mar5-r mar10-b pad5-tb pad10-rl b-inline bg-gray700 t-white hover-no-underline hover-bg-red600 hover-t-white\" href=\"[URL]\" style=\"font-size: [SIZE]%\">[TAG] <sup>[COUNT]</sup></a>',
'min_size' => '90',
'max_size' => '90',
'max_num' => '20',
'min_count' => '1',
'sort' => '0',
), '_footer'); %}");

my_set_opt('footer_cols1_block2_css', 'w30 w45-tablet w100-phone mar10-tb links-no-color links-hover-t-gray100 t90');	

my_set_opt('footer_cols1_block3', "<div class=\"mso-widget-header\"><span>Информация</span></div>
<p>Произвольный текст</p>");

my_set_opt('footer_cols1_block3_css', 'w30 w45-tablet w100-phone mar10-tb links-no-color links-hover-t-gray100 t90');


# end of file