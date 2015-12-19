<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

	echo '<div class="mso-type-sitemap"><div class="mso-page-only">';
	
	echo '<header><h1 class="mso-type-sitemap">' . tf('Карта сайта (архив)') . '</h1></header>';
	echo '<div class="mso-page-content mso-type-sitemap-content">';
	
	if ($f = mso_page_foreach('sitemap')) 
		require($f);
	else
	{
		if (function_exists('sitemap')) 
			echo sitemap();
		else 
			echo mso_hook('sitemap');
	}
	
	echo '</div></div></div><!-- mso-page-content mso-type-sitemap-content mso-page-only mso-type-sitemap -->';
	
	if ($f = mso_page_foreach('sitemap-posle')) require($f);
	
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

# end file