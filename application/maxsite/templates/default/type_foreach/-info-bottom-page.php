<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-bottom файл

	предыдущая - следующая запись в конце записи
	
	для использования, переименуйте файл в info-bottom-page.php

*/

if (isset($np_out) and $np_out)
{
	$p->block($np_out, '<div class="next-prev-page">', '</div><div class="clearfix"></div>');
}

# end file