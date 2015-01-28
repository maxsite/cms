<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# файл functions.php подключается при инициализации сайта
# в этом файле нельзя выводить данные в браузер!

# Для своих функций используйте custom/my_functions.php

# регистрируем сайдбары - имя, заголовок.
# если имя совпадает, то берется последний заголовок
mso_register_sidebar('1', tf('Первый сайдбар'));

# основные функции шаблона
if ($fn = mso_fe('custom/template.php')) require_once($fn);

# набор из mso_set_val
if ($fn = mso_fe('custom/set_val.php')) require_once($fn);

# библиотека для вывода записей в цикле и вывод колонок
require_once(getinfo('shared_dir') . 'stock/page-out/page-out.php');

# библиотека для работы с изображениями
require_once(getinfo('shared_dir') . 'stock/thumb/thumb.php');

# дополнительный файл my_functions.php
if ($fn = mso_fe('custom/my_functions.php')) require_once($fn);

# end file