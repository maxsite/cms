<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_head_meta('title', '{title}');
mso_head_meta('description', '{description}');
mso_head_meta('keywords', '{keywords}');

if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

echo '<h1>{header}</h1>';
echo '{body}';

if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file