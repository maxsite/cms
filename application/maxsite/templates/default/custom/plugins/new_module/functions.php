<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// получить все модули
function getAllModules($files)
{
    $arModules = [];

    foreach ($files as $file) {
        $bdirBlock = basename(dirname(dirname($file)));
        $bdir = basename(dirname(dirname($file))) . '/' . basename(dirname($file));
        $arModules[$bdirBlock][] = $bdir;
    }

    return $arModules;
}

/**
 * https://www.php.net/manual/ru/function.copy.php#91010
 */
function recurse_copy($src, $dst)
{
    $dir = opendir($src);
    mkdir($dst, 0700, true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

# end of file
