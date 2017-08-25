<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (mso_get_option('use_html_compress', 'templates', 0) and function_exists('my_compress_text'))
{
	$OUTPUT = ob_get_contents(); // буферизация вывода
	ob_end_clean();
	$OUTPUT = my_compress_text($OUTPUT); // сжатие текст
	
	// заменить http-протокол
	if (mso_get_option('remove_protocol', 'templates', 0) and function_exists('my_remove_protocol'))
	{
		$OUTPUT = my_remove_protocol($OUTPUT); // убрали протокол
	}
	
	echo $OUTPUT;
}

# end of file