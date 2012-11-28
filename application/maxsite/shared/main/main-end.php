<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


global $CONTENT_OUT;

$CONTENT_OUT = ob_get_contents();
ob_end_clean();

require(getinfo('template_dir') . 'main.php');

# end file