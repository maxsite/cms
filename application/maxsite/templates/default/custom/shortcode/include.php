<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Подключение произвольного php-файла
	
	[include]файл[/include]

	Имя файла указывается относительно каталога шаблона, например:

	[include]myparts/include/form.php[/include]
*/

mso_shortcode_add('include', 'my_shortcode_include');

function my_shortcode_include($attr)
{
	if (!isset($attr[2]) or !$attr[2]) return; // не указан файл
	
	if ($fn= mso_fe($attr[2])) {
		ob_start();
		require $fn;
		$out = ob_get_contents();
		
		if (ob_get_length()) ob_end_clean();
		
		return $out;
	}
}

# end of file