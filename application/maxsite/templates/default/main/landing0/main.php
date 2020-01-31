<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

global $CONTENT_OUT;
 
if ($fn = mso_fe('main/blocks/_start.php')) require $fn;

echo $CONTENT_OUT; 

if ($fn = mso_fe('main/blocks/_end.php')) require $fn;

# end of file