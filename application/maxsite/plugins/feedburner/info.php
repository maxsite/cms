<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('RSS для FeedBurner.com'),
	'description' => t('Подключение RSS блога для FeedBurner.com'),
	'version' => '1.0',
	'author' => 'Максим',
	'plugin_url' => 'http://max-3000.com/',
	'author_url' => 'http://maxsite.org/',
	'group' => 'template',
	'options_url' => getinfo('site_admin_url') . 'plugin_feedburner',
);

# end file