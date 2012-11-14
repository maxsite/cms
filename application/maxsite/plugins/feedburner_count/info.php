<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Количество подписчиков RSS'),
	'description' => t('Вывод текстового значения кол-ва подписчиков на ваш фид в FeedBurner.com'),
	'version' => '1.2',
	'author' => 'Евгений Самборский',
	'plugin_url' => 'http://www.samborsky.com/max-3000/223/',
	'author_url' => 'http://www.samborsky.com/',
	'group' => 'template',
	'options_url' => getinfo('site_admin_url') . 'plugin_feedburner_count',
);

# end file