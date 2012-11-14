<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Пинги'),
	'description' => t('Пинг поисковиков и ping-сервисов через XMLRPC'),
	'version' => '1.03',
	'author' => 'Евгений Самборский',
	'plugin_url' => 'http://www.samborsky.com/samborsky_ping/',
	'author_url' => 'http://www.samborsky.com/',
	'group' => 'template',
	'options_url' => getinfo('site_admin_url') . 'samborsky_ping',
);

# end file