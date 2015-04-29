<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

	echo NR . '<div class="mso-type-sitemap">' . NR;
	
	echo NR . '<div class="mso-page-only">' . NR;
	
	echo  '<h1>' . tf('Карта сайта (архив)') . '</h1>';
	
	if ( function_exists('sitemap') ) echo sitemap();
		else echo mso_hook('sitemap');
	
	echo NR . '</div><!-- class="mso-page-only" -->' . NR;
	echo NR . '</div><!-- class="mso-type-sitemap" -->' . NR;
	
	if ($f = mso_page_foreach('sitemap-posle')) require($f);
	
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

# end file