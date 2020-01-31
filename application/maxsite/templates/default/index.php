<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * Диспетчер подключаемых type-файлов
 */

// глобальное кэширование выполняется на уровне хука при наличии соответствующего плагина
// если хук вернул true, значит данные выведены из кэша, то есть выходим
if (mso_hook('global_cache_start', false)) return;

// $MSO->language = 'en'; // можно изменить язык шаблона

// подключаем нужный type-файл
if ($fn = mso_dispatcher()) require $fn;

// хук глобального кэша
mso_hook('global_cache_end');

# end of file
