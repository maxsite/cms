<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$p->format('edit', '<i class="im-edit t-gray600 hover-t-gray950" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');
$p->format('title', '<h1 class="t-gray800 mar15-t t220">', '</h1>', false);
$p->format('cat', ', ', '<span class="im-bookmark mar15-r" title="' . tf('Рубрика записи') . '">', '</span>');
$p->format('date', 'j F Y г.', '<time class="im-calendar" datetime="[page_date_publish_iso]">', '</time>');
$p->format('view_count', '<span class="mar15-l im-eye">' . tf('Просмотров') . ': ', '</span>');

$p->html('<header class="mar30-t mar20-b">');
	$p->line('[edit][title]');

	$p->div_start('t-gray600 t90 mso-clearfix');
		$p->line('[cat][date][view_count]');
	$p->div_end('');
$p->html('</header>');

# end of file
