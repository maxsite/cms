<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

echo '<header>';

	if ($f = mso_page_foreach('category-header')) 
	{
		require($f);
	}
	else
	{
		echo '<h1 class="mso-category">'
			. htmlspecialchars(mso_get_cat_key('category_name')) 
			. '</h1>';
	}
	
	if ( mso_get_option('category_show_rss_text', 'templates', 1) )
	{
		if ($f = mso_page_foreach('category-show-rss-text'))
		{
			require($f);
		}
		else
		{
			echo 
				mso_get_val('show_rss_text_start', '<p class="mso-show-rss-text">') 
				. '<a href="' . getinfo('siteurl') . mso_segment(1) 
				. '/' . mso_segment(2) 
				. '/feed">'
				. tf('Подписаться на эту рубрику по RSS'). '</a>' 
				.  mso_get_val('show_rss_text_end', '</p>');
		}
	}
	
	if ($f = mso_page_foreach('category-show-desc'))
	{
		require($f); // подключаем кастомный вывод
	}
	else
	{
		// описание рубрики
		if ($category_desc = mso_get_cat_key('category_desc'))
			echo '<div class="mso-category-desc">' . $category_desc . '</div>';
		
		if (function_exists('ushka')) echo ushka('category_' . mso_get_cat_key('category_slug'));
	}

echo '</header>';
	
# end file