<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_page_title($page_slug, $page_title, '<li>', '', true);
mso_page_date($page_date_publish, 'd/m/Y', ' (<small>', '</small>)');

if (mso_get_option('default_description_home', 'templates', 0))
	mso_page_meta('description', $page_meta, '<div class="description">', '</div>');

echo '</li>';