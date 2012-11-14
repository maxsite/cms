<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	// в титле следует указать формат вывода | заменяется на  » true - использовать только page_title
	mso_head_meta('title', $pages, '%page_title%|%title%', ' » ', true ); // meta title страницы
	mso_head_meta('description', $pages); // meta description страницы
	mso_head_meta('keywords', $pages); // meta keywords страницы
	
	