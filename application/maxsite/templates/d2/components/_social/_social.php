<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
	(c) http://maxsite.org/
	
	вывод соцсететй иконками
	стили задаются в social.less
	Файл использовать в других компонентах
	
	if ($fn = mso_fe('components/_social/_social.php')) require($fn);
	
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
		'blogger' => 'Blogger',
		'dropbox' => 'Dropbox',
		'evernote' => 'Evernote',
		'facebook' => 'Facebook',
		'gplus' => 'Google+',
		'last_fm' => 'Last FM',
		'linked_in' => 'Linked In',
		'mail' => 'Mail.ru',
		'odnoklassniki' => 'Одноклассники',
		'rss' => 'RSS',
		'skype' => 'Skype',
		'twitter' => 'Twitter',
		'vimeo' => 'Vimeo',
		'vkontakte' => 'В Контакте',
		'yahoo' => 'Yahoo',
		'youtube' => 'Youtube'
	);
	
	foreach ($socials as $s => $url)
	{
		if ($s == 'rss') // rss автоматом формируем адрес
		{
			echo '<a class="rss" title="RSS" href="' . getinfo('rss_url') . '"></a>';
		}
		else
		{
			if (isset($title[$s])) $t = tf($title[$s]);
				else $t = $s;
			echo '<a class="' . $s . '" rel="nofollow" title="' . $t . '" href="' . trim($url) .'"></a>';
		}
	}
	
	echo '</div>';
}
