<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

global $CONTENT_OUT, $MAIN_FILE;

$CONTENT_OUT = ob_get_contents();

if (ob_get_length()) ob_end_clean();

require $MAIN_FILE;

# end of file