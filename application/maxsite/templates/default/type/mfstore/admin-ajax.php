<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if (!is_login()) return;

if (mso_check_post(array('session', 'm'))) {

    if (getinfo('session_id') !== $_POST['session']) return;

    $bdir = @base64_decode($_POST['m']);

    if (!$bdir) return;

    $dirStore = getinfo('template_dir') . 'store/' . $bdir;

    if (file_exists($dirStore . '/index.php')) {

        $dirModules = getinfo('template_dir') . 'modules/' . $bdir;

        if (file_exists($dirModules . '/index.php')) {
            echo '<span class="t-red">Нельзя переписать существующий модуль</span>';
            return;
        }

        recurse_copy($dirStore, $dirModules);

        if (file_exists($dirModules)) {
            $adminUrl = getinfo('site_admin_url') . 'editor_files/' . base64_encode('type/home/units.php');

            echo 'Модуль скопирован. Для его использования в добавьте строчку <code>@module ' . $bdir . '</code>';
        } else
            echo 'Ошибка копирования';
    } else {
        return;
    }
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
