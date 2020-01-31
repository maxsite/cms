<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$p->format('edit', '<i class="fas fa-edit t-gray600 hover-t-black" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');
$p->format('title', '<h1 class="t-gray800 mar15-t t220">', '</h1>', false);
$p->format('cat', '', '<span class="my-cats t90" title="' . tf('Рубрика записи') . '">', '</span>');
$p->format('date', 'j F Y г.', '<time class="" datetime="[page_date_publish_iso]">', '</time>');
$p->format('view_count', '<span class="mar15-l">' . tf('Просмотров') . ': ', '</span>');

$p->html('<header class="mar30-t mar20-b">');
	$p->line('[cat]');
	$p->line('[edit][title]');

	$p->div_start('t-gray600 t90 mso-clearfix');
		$p->line('[date][view_count]');
	$p->div_end('');
$p->html('</header>');

echo <<<EOF
<style>
.my-cats a {
	background: #afb3b6;
	padding: 2px 5px;
	border-radius: 3px;
	color: #fff;
	margin-right: 7px;
}
.my-cats a:hover {
	background: #919396;
	text-decoration: none;
}
</style>
EOF;

# end of file
