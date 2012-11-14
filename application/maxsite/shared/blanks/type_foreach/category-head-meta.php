<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	mso_head_meta('title', $pages, '%category_name%|%title%', ' » '); //  meta title страницы
	mso_head_meta('description', $pages, '%category_desc%'); // meta description страницы
	mso_head_meta('keywords', $pages, '%category_name%'); // meta keywords страницы
	