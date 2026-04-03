<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Email-рассылка'),
	'description' => t('Отправка email-сообщений по списку рассылки'),
	'version' => '1.1',
	'author' => 'Максим',
	'plugin_url' => '',
	'author_url' => 'https://maxsite.org/',
	'group' => 'template',
	'options_url' => getinfo('site_admin_url') . 'mail_send',
);

# end of file