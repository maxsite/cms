<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-sitemap"><div class="mso-page-only">';
echo '<h3 class="mso-type-sitemap">' . tf('Карта сайта (архив)') . '</h3>';
echo '<div class="mso-page-content mso-type-sitemap-content">';

if ($fn = mso_page_foreach('sitemap')) {
	require $fn;
} else {
	if (function_exists('sitemap'))
		echo sitemap();
	else
		echo mso_hook('sitemap');
}

echo '</div></div></div><!-- mso-page-content mso-type-sitemap-content mso-page-only mso-type-sitemap -->';

if ($fn = mso_page_foreach('sitemap-posle')) require $fn;
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

# end of file
