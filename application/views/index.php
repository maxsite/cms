<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
global $MSO;

$template_file = $MSO->config['templates_dir'] . $MSO->config['template'] . '/index.php';

if ( file_exists($template_file) ) require($template_file);
	else show_error('Ошибка - отсутствует файл шаблона index.php');

?>