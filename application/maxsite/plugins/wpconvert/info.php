<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('WordPress convert'),
	'description' => t('Конвертирование записей, страниц, рубрик и комментариев из WordPress в MaxSite CMS'),
	'version' => '1.4',
	'author' => 'Максим',
	'plugin_url' => '',
	'author_url' => 'https://maxsite.org/',
	'group' => 'template',
	'options_url' => getinfo('site_admin_url') . 'plugin_wpconvert',
);

# end of file