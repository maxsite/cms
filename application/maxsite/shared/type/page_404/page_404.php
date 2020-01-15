<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if (mso_get_option('page_404_http_not_found', 'templates', 1)) header('HTTP/1.0 404 Not Found');

if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-page_404">';

if ($fn = mso_page_foreach('page_404')) {
	require $fn; // подключаем кастомный вывод
} else // стандартный вывод
{
	if ($fn = mso_find_ts_file('type/page_404/units/page_404.php')) require $fn;
}

echo '</div><!-- class="mso-type-page_404" -->';

if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

# end of file
