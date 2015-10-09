<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 if ( mso_get_option('category_show_rss_text', 'templates', 1) )
{
	if ($f = mso_page_foreach('tag-show-rss-text'))
	{
		require($f);
	}
	else 
	{
		echo 
			mso_get_val('show_rss_text_start', '<p class="mso-show-rss-text">') 
			. '<a href="' . getinfo('siteurl') . mso_segment(1) . '/' 
			. mso_segment(2) . '/feed">'
			. tf('Подписаться на эту метку по RSS'). '</a>' 
			.  mso_get_val('show_rss_text_end', '</p>');
	}
}

# end of file