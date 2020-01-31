<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('main-end');
if ($fn = mso_fe('custom/main-end.php')) require $fn;

# end of file