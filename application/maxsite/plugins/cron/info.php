<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Cron'),
	'description' => t('Выполнение периодических задач по крону. Для работы необходимо включить на сервере CRON: «GET http://сайт/cron». Для ISPManager указывайте так: «/usr/local/bin/wget -O /dev/null http://site.com/cron»'),
	'version' => '1.1',
	'author' => 'Максим',
	'plugin_url' => '',
	'author_url' => '//maxsite.org/',
	'group' => 'system'
);

# end of file