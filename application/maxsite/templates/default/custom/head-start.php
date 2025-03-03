<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Berry CSS загружается всегда
mso_add_file('assets/css/berry/berry.css');

// themes
// каталог в assets/css/themes/
$design = mso_get_option('theme_template', getinfo('template'), 'default.css');
mso_add_file('assets/css/themes/' . $design);

# end of file
