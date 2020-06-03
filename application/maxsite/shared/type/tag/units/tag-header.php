<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

echo '<header>';

if ($fn = mso_page_foreach('tag-header'))
	require $fn;
else
	echo '<h1 class="mso-tag">' . htmlspecialchars(html_entity_decode(mso_segment(2))) . '</h1>';

if (mso_get_option('category_show_rss_text', 'templates', 1)) {
	if ($fn = mso_page_foreach('tag-show-rss-text')) {
		require $fn;
	} else {
		echo
			mso_get_val('show_rss_text_start', '<p class="mso-show-rss-text">')
				. '<a href="' . getinfo('siteurl') . mso_segment(1) . '/'
				. mso_segment(2) . '/feed">'
				. tf('Подписаться на эту метку по RSS') . '</a>'
				.  mso_get_val('show_rss_text_end', '</p>');
	}
}

if ($fn = mso_page_foreach('tag-show-desc')) require $fn;

// ушка с описанием метки
// имя метки должно быть полностью в нижнем регистре
// например: tag/солнечная система   tag/земля
if (function_exists('ushka')) echo ushka('tag/' . mb_strtolower(htmlspecialchars(mso_segment(2))));

if ($fn = mso_find_ts_file('type/tag/units/tag-info.php')) require $fn;

echo '</header>';

# end of file
