<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# библиотека для вывода записей в цикле и вывод колонок
require_once(getinfo('shared_dir') . 'stock/page-out/page-out.php');

# библиотека для работы с изображениями
require_once(getinfo('shared_dir') . 'stock/thumb/thumb.php');

ob_start();

# end file