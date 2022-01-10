<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Autoload Composer
 * Располагается в корне сайта в каталоге vendor
 */
if (file_exists(BASEPATH . 'vendor/autoload.php')) require_once BASEPATH . 'vendor/autoload.php';

/**
 * Register autoload classes PSR-4
 * https://www.php-fig.org/psr/psr-4/
 * https://maxsite.org/page/php-autoload
 *
 * Файлы PSR4-классов располагаются в application/maxsite/common/psr4/
 * Каталог указывает на namespace
 *
 * Пример:
 * Файл: application/maxsite/common/psr4/Pdo/PdoConnect.php
 *
 *   namespace Pdo;
 *   class PdoConnect {...}
 *
 *   $dbPdo = new Pdo\PdoConnect;
 *
 */
spl_autoload_register(function ($class) {
    // разбиваем класс на элементы
    $namespace = explode('\\', $class);

    // получаем имя файла из имени класса - в $namespace окажется только namespace
    $file = array_pop($namespace) . '.php';

    // получаем путь на основе namespace
    $path = implode(DIRECTORY_SEPARATOR, $namespace);

    // формируем имя файла
    $fn = getinfo('common_dir') . 'psr4' . DIRECTORY_SEPARATOR .  $path . DIRECTORY_SEPARATOR . $file; 
    
    // pr($fn);

    // проверка на существование файла и его подключение
    if (file_exists($fn)) require $fn;
});

# end of file
