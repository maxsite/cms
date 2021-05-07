<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if ($post = mso_check_post(array('f_session_id', 'f_module', 'f_newmodule'))) {
    mso_checkreferer();

    $module = $post['f_module'];
    $newModule = $post['f_newmodule'];
    $errors = [];
    $ok = '';

    if ($newModule !== '') {
        // указан новый модуль - вначале проверки

        // новый модуль должен состоять из группа/каталог
        $newModuleCheck  = explode('/', $newModule);
        if (count($newModuleCheck) != 2) $errors[] = t('Имя нового модуля должно быть в формате «группа/каталог»');

        // только англ. символы, цифры, / _ -
        $newModuleCheck = preg_replace('/[^0-9a-zA-Z\-\_\/]/', '', $newModule);
        if ($newModuleCheck !== $newModule) $errors[] = t('Недопустимые символы в имени модуля');

        if (!$errors and file_exists(getinfo('template_dir') . 'modules/' . $newModule . '/index.php'))
            $errors[] = t('Модуль с таким именем уже существует');
    } else {
        $newModule = $module;
    }

    // проверки, которых в теории  не должно быть
    if (!$errors and !$module) $errors[] = t('Не указан модуль для копирования');

    // нет исходного модуля
    if (!$errors and !file_exists(getinfo('template_dir') . 'store/' . $module . '/index.php'))
        $errors[] = t('Исходный модуль недоступен');

    if (!$errors) {
        // работаем
        $dirStore = getinfo('template_dir') . 'store/' . $module;
        $dirModules = getinfo('template_dir') . 'modules/' . $newModule;

        recurse_copy($dirStore, $dirModules);

        if (file_exists($dirModules)) {
            $ok = '<br>Модуль создан. Для его использования в добавьте строчку <br><b>@module ' . $newModule . '</b>';

            // в index.php заменим имя модуля
            if ($module != $newModule) {
                $textIn = file_get_contents($dirModules . '/index.php');
                $textOut = str_replace($module, $newModule, $textIn);
                
                if ($textOut != $textIn)
                    file_put_contents($dirModules . '/index.php', $textOut);
            }
        } else {
            $errors[] = t('Ошибка копирования');
        }
    }

    if ($errors)
        echo '<div class="error">' . implode('<br>', $errors) . '</div>';
    else
        echo '<div class="update">' . t('Готово!') . $ok . '</div>';
}

# end of file
