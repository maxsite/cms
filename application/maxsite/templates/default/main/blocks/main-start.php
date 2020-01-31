<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('main-start');
if ($fn = mso_fe('custom/main-start.php')) require $fn;

# end of file
