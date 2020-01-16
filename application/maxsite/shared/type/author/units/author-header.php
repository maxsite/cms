<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

echo '<header>';

$title_page = isset($pages[0]['users_nik']) ? $pages[0]['users_nik'] : '';

if ($f = mso_page_foreach('author-header'))
	require $f;
else
	echo '<h1 class="mso-author">' . $title_page . '</h1>';

// ушка с описанием автора например: author/1
if (function_exists('ushka')) echo ushka('author/' . mb_strtolower(htmlspecialchars(mso_segment(2))));

echo '</header>';

# end of file
