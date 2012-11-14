<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


global $MAIN_OUT;

$MAIN_OUT = ob_get_contents();
ob_end_clean();

require('main.php');