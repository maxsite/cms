<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# Диспетчер опций шаблона
# Сами опции вынесены в ini-файлы
# поддерживаются custom/my_options.ini и custom/my_options.php
 
// если выставить true, то подключаются все опции из ini-файлов  из shared/options/default/ 
// если false - то только те, которые размещены в каталоге шаблона
mso_set_val('get_options_default', true);

require_once( getinfo('shared_dir') . 'options/options.php' );

# end file