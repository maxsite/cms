<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (function_exists('ushka')) echo ushka('content-start');
if ($fn = mso_fe('custom/content-start.php')) require $fn;

if ($fn = mso_fe('custom/content-out.php')) {
	require $fn;
} else {
	global $CONTENT_OUT;
	echo $CONTENT_OUT;
}

if (function_exists('ushka')) echo ushka('content-end');
if ($fn = mso_fe('custom/content-end.php')) require $fn;

# end of file
