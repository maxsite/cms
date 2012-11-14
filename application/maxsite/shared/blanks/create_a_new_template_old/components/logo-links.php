<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	
echo '<div class="logo-links"><div class="wrap">';

	echo '<div class="left w75">';
	
		$logo = getinfo('stylesheet_url') . 'images/logos/' . mso_get_option('default_header_logo', 'templates', 'logo01.png');
		
		if (!is_type('home')) echo '<a href="' . getinfo('siteurl') . '">';
			
		echo '<img class="left" src="' . $logo . '" alt="' . getinfo('name_site') . '" title="' . getinfo('name_site') . '">';
		
		if (!is_type('home')) echo '</a>';


		echo '
			<div class="name_site">' . getinfo('name_site') . '</div>
			<div class="description_site">' . getinfo('description_site') . '</div>';

	echo '</div>';

	echo '<div class="right text-right w25 social">';
	
		echo '<a class="header-social rss" href="' . getinfo('rss_url') . '"><img src="' . getinfo('stylesheet_url') . 'images/social/rss.png" width="16" height="16" alt="RSS" title="RSS"></a>';
		
		if ($u = mso_get_option('default_twitter_url', 'templates', ''))
			echo '<a class="header-social twitter" rel="nofollow" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/twitter.png" width="16" height="16" alt="Twitter" title="Twitter"></a>';
		
		if ($u = mso_get_option('default_facebook_url', 'templates', ''))
			echo '<a class="header-social facebook" rel="nofollow" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/facebook.png" width="16" height="16" alt="Facebook" title="Facebook"></a>';
			
		if ($u = mso_get_option('default_skype_url', 'templates', ''))
			echo '<a class="header-social skype" rel="nofollow" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/skype.png" width="16" height="16" alt="Skype" title="Skype"></a>';
			
		if ($u = mso_get_option('default_vkontakte_url', 'templates', ''))
			echo '<a class="header-social vkontakte" rel="nofollow" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/vkontakte.png" width="16" height="16" alt="В контакте" title="В контакте"></a>';
		
		if ($u = mso_get_option('default_jabber_url', 'templates', ''))
			echo '<a class="header-social jabber" rel="nofollow" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/jabber.png" width="16" height="16" alt="Jabber" title="Jabber"></a>';
		
		if ($u = mso_get_option('default_gplus_url', 'templates', ''))
			echo '<a class="header-social gplus" rel="nofollow" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/gplus.png" width="16" height="16" alt="Google plus" title="Google plus"></a>';	
	
	echo '</div>';
	
	echo '<div class="clearfix"></div>';
	
echo '</div><!-- div class=wrap --></div><!-- class="logo-links" -->';