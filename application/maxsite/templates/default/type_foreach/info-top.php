<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$p->format('edit', 'Edit', '<div class="right bg-yellow">', '</div>');
$p->format('cat', ' / ', '<div>', '</div>');
$p->format('date', 'D, j F Y г.', '<div><time datetime="[page_date_publish_iso]">', '</time></div>');

$p->html(NR . '<header>');

	$p->line('[edit][title]');
	
	$p->div_start('info info-top');
		$p->line('[date][cat]');
	$p->div_end('info info-top');

$p->html('</header>');

# end file