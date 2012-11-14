<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Cron'),
	'description' => t('Выполнение периодических задач по крону. Для работы необходимо включить на сервере CRON: «GET http://сайт/cron». Для ISPManager указывайте так: «/usr/local/bin/wget -O /dev/null http://site.com/cron»'),
	'version' => '1.1',
	'author' => 'Максим',
	'plugin_url' => 'http://max-3000.com/',
	'author_url' => 'http://maxsite.org/',
	'group' => 'admin'
);

# end file