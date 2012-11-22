<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if (!$pages) return;
 
$p = new Page_out();

$p->format('title', '', '', true);
$p->format('date', 'j F Y', '<span><time datetime="[page_date_publish]">', '</time></span>');

// исключенные записи
$exclude_page_id = mso_get_val('exclude_page_id');

$line_format = mso_get_val('list_line_format', '[title] - [date]');

$p->div_start(mso_get_val('container_class'));

$p->html(NR2 . '<ul class="list">');

foreach ($pages as $page) 
{
	if ($f = mso_page_foreach(getinfo('type') . '-list')) 
	{
		require($f); // подключаем кастомный вывод
		continue; // следующая итерация
	}

	$p->load($page);
	
	$p->line($line_format, NR2 . '<li>', '</li>');
	
	$exclude_page_id[] = $p->val('page_id');
	
} // end foreach
echo NR2 . '</ul>' . NR;

$p->div_end(mso_get_val('container_class'));

mso_set_val('exclude_page_id', $exclude_page_id);

	
# end file