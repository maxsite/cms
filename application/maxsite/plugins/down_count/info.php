<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Счетчик переходов'),
	'description' => t('Подсчет количества переходов по ссылке. Обрамите нужную ссылку в [dc]...[/dc]'),
	'version' => '1.3',
	'author' => 'Максим',
	'editors' => 'Wave',
	'plugin_url' => '',
	'author_url' => 'http://maxsite.org/',
	'group' => 'text',
	'options_url' => getinfo('site_admin_url') . 'plugin_down_count',
);

# end of file