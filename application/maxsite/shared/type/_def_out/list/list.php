<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

$p = new Page_out();

$p->format('title', '', '', true);
$p->format('date', 'j F Y', '<span><time datetime="[page_date_publish]">', '</time></span>');

$p->html(NR2 . '<article><ul class="list">');

foreach ($pages as $page) 
{
	if ($f = mso_page_foreach(getinfo('type') . '-list')) 
	{
		require($f); // подключаем кастомный вывод
		continue; // следующая итерация
	}

	$p->load($page);
	$p->line('[title] - [date]', NR2 . '<li>', '</li>');

} // end foreach

echo NR2 . '</ul></article>' . NR;
	
# end file