<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *  * MaxSite CMS
 * (c) https://max-3000.com/
 */


if ($p->val('page_type_name') !== 'blog') return;

/*
// метки внизу
$p->format('tag', ' | ', '<span class="im-tag" title="' . tf('Метка записи') . '">', '</span>');
$p->line('<div class="t-gray600 t90 mar20-tb">[tag]</div>');


// вывод автора записи
$p->format('author', '<div class="" title="' . tf('Автор') . '">', '</div>');

echo '<div class="bg-gray200 bor1 bor-solid-tb bor-gray300 t-gray800 t90 mar20-tb pad20 flex flex-wrap-phone">';

	if ($users_avatar_url = $p->val('users_avatar_url'))
	{
		echo '<div class="w100px-min mar20-r flex flex-vcenter"><div><img class="w100" height="" src="' . $users_avatar_url . '" alt="' . htmlspecialchars($p->val('author')) . '"></div></div>';
	}
	
	$p->line('<div class="flex-grow1">[author]<div>' . $p->val('users_description') . '</div></div>');

echo '</div>';

*/

// навигация 

if (isset($np_out)) {
	if ($np_out) $p->block($np_out, '<div class="mso-clearfix"></div><div class="next-prev-page mso-clearfix t90 mar30-tb hover-no-underline">', '</div>');
} else {
	if ($fn = mso_fe('type_foreach/info-top/page/_next-prev.php')) require($fn);
}

# end of file
