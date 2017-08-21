<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Admin IP'),
	'description' => t('Плагин разрешает доступ в админ-панель только с указанных IP'),
	'version' => '1.0',
	'author' => 'Максим',
	'plugin_url' => '',
	'author_url' => '//maxsite.org/',
	'group' => 'security',
	'options_url' => getinfo('site_admin_url') . 'plugin_admin_ip',
);

# end of file