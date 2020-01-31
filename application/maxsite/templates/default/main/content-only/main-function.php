<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

mso_set_val('show_thumb', false);
mso_set_val('body_class', mso_get_val('main_class', ''));

mso_remove_hook('content_end');

# end of file
