<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Admin IP'),
	'description' => t('Плагин разрешает доступ в админ-панель только с указанных IP'),
	'version' => '1.0',
	'author' => 'Максим',
	'plugin_url' => 'http://max-3000.com/',
	'author_url' => 'http://maxsite.org/',
	'group' => 'template',
	'options_url' => getinfo('site_admin_url') . 'plugin_admin_ip',
);

# end file