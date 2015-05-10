<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

global $CONTENT_OUT, $MAIN_FILE;

$CONTENT_OUT = ob_get_contents();
ob_end_clean();

require($MAIN_FILE);

# end file