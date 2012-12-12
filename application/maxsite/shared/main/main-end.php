<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

global $CONTENT_OUT;

$CONTENT_OUT = ob_get_contents();
ob_end_clean();

if ($fn = mso_fe('custom/main-template.php')) require($fn);
$fn_main = mso_get_val('main_file', getinfo('template_dir') . 'main.php');

if (file_exists($fn_main)) require($fn_main);
	else require(getinfo('template_dir') . 'main.php');

# end file