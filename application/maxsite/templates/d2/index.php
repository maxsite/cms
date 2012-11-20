<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Диспетчер подключаемых type-файлов
 * 
 */
  
# глобальное кэширование выполняется на уровне хука при наличии соответствующего плагина
# если хук вернул true, значит данные выведены из кэша, то есть выходим
if (mso_hook('global_cache_start', false)) return;

# $MSO->language = 'en'; // можно изменить язык шаблона

# подключаем нужные библиотеки
require_once(getinfo('common_dir') . 'page.php'); // функции страниц 
require_once(getinfo('common_dir') . 'category.php'); // функции рубрик

# подключаем нужный type-файл
if ($fn = mso_dispatcher()) require($fn);

# хук глобального кэша
mso_hook('global_cache_end');

# end file