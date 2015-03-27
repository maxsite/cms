<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/
	
	вывод соцсететй иконками
	стили задаются в _social.less
	Файл использовать в других компонентах
*/

$social = '[social]' . NR . mso_get_option('social', 'templates', '') . '[/social]';
$social = mso_section_to_array($social, 'social', array(), true);

if ($social and isset($social[0]))
{
	echo '<div class="social">';
	
	$socials = $social[0];
	
	// подсказки в title
	$title = array(
		'behance' => 'Behance',
		'dropbox' => 'Dropbox',
		'facebook' => 'Facebook',
		'gplus' => 'Google+',
		'github' => 'Github',
		'last_fm' => 'Last FM',
		'linked_in' => 'Linked In',
		'email' => 'Контакты',
		'odnoklassniki' => 'Одноклассники',
		'rss' => 'RSS',
		'skype' => 'Skype',
		'twitter' => 'Twitter',
		'vimeo' => 'Vimeo',
		'vkontakte' => 'В Контакте',
		'yahoo' => 'Yahoo',
		'youtube' => 'Youtube'
		// 'blogger' => 'Blogger',
		// 'evernote' => 'Evernote',
		// 'mail' => 'Mail.ru',
	);
	
	// классы для каждой иконки
	$class = array(
		'behance' => 'i-behance',
		'dropbox' => 'i-dropbox ',
		'facebook' => 'i-facebook',
		'gplus' => 'i-google-plus',
		'github' => 'i-github',
		'last_fm' => 'i-lastfm',
		'linked_in' => 'i-linkedin',
		'email' => 'i-envelope',
		'odnoklassniki' => 'i-male',
		'rss' => 'i-rss',
		'skype' => 'i-skype',
		'twitter' => 'i-twitter',
		'vimeo' => 'i-vimeo-square',
		'vkontakte' => 'i-vk',
		'yahoo' => 'i-yahoo',
		'youtube' => 'i-youtube'
		// 'blogger' => 'Blogger',
		// 'mail' => 'Mail.ru',
		// 'evernote' => 'Evernote',
	);	
	
	foreach ($socials as $s => $url)
	{
		if (isset($title[$s])) $t = tf($title[$s]);
			else $t = $s;
		
		if (isset($class[$s])) $cls = $class[$s];
			else $cls = $s;
		
		if ($s == 'rss') // rss автоматом формируем адрес
		{
			echo '<a class="' . $cls . '" title="RSS" href="' . getinfo('rss_url') . '"></a> ';
		}
		else
		{
			echo '<a class="' . $cls . '" rel="nofollow" title="' . $t . '" href="' . trim($url) .'"></a> ';
		}
	}
	
	echo '</div>';
}

# end file