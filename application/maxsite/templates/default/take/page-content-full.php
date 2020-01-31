<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Отключения вывода контента для full
*/

if (mso_get_val('my-page-content-full', true)) {
	if ($fn = mso_find_ts_file('type/_def_out/full/units/full-default.php')) require $fn;
}

# end of file
