<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$OUTPUT = ob_get_contents(); // буферизация вывода
ob_end_clean();

// сжатие текста
if (mso_get_option('use_html_compress', 'templates', 0) and function_exists('my_compress_text'))
	$OUTPUT = my_compress_text($OUTPUT); 

// заменить http-протокол
if (mso_get_option('remove_protocol', 'templates', 0) and function_exists('my_remove_protocol'))
	$OUTPUT = my_remove_protocol($OUTPUT);

echo mso_hook('main_all_output_text', $OUTPUT);

# end of file