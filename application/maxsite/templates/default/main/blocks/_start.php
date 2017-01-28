<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (mso_get_option('use_html_compress', 'templates', 0) and function_exists('my_compress_text'))
{
	ob_start(); // буферизация вывода
}

# end of file