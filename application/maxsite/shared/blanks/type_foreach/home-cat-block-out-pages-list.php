<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_page_title($page_slug, $page_title, '<li>', '', true);
mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
echo '</li>';