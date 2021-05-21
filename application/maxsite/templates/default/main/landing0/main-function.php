<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

mso_set_val('show_thumb', false);
mso_set_val('body_class', mso_get_val('main_class', ''));
# mso_set_val('page_content_only', true);

mso_remove_hook('content_end');
mso_remove_hook('head_css');
mso_remove_hook('head');
mso_remove_hook('content');

#end of file
