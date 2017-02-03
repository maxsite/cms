<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
	mso-info-bottom-page
	вывод next/prev в конце записи (дубль верхней части, если есть, конечно)
	для использования перименовать файл в mso-info-bottom-page.php
*/

if (isset($np_out) and $np_out)
{
	$p->block($np_out, '<div class="clearfix"></div><div class="next-prev-page t90 mar30-tb clearfix">', '</div>');
}

# end of file