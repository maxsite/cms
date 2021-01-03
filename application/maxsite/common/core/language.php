<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// устанавливаем $MSO->current_lang_dir в которой хранится
// текущий каталог языка. Это второй параметр функции t()
// в связи с изменением алгоритма перевода, функция считается устаревшей
function mso_cur_dir_lang($dir = false)
{
    // global $MSO;
    // return $MSO->current_lang_dir = $dir;
}

// Языковой перевод MaxSite CMS
// Описание см. ниже для _t()
// перевод только для frontend - фраз сайта.
function tf($w = '', $file = false)
{
    return _t($w, $file, 'tf');
}

// перевод только для админки
function t($w = '', $file = false)
{
    return _t($w, $file, 't');
}

// функция трансляции (языковой перевод)
// не использовать эту функцию!
// первый параметр - переводимое слово - учитывается регистр полностью
// второй параметр:
//  __FILE__ (по нему вычисляется каталог перевода)
//  mytemplate - текущий каталог
// 
//  Всегда подключается  /common/language/ЯЗЫК-f.php
//  если это админка, то подключается еще и /common/language/ЯЗЫК.php
//  Если вторым параметром указан __FILE__ то подключается перевод из каталога language
//  откуда была вызвана функция t() или tf().
//  Если второй параметр это mytemplate, то подключается language текущего шаблона
//  Если второй параметр не указан, то он принимается равным mytemplate, что позволяет использовать перевод в шаблоне
//  $label нужна для файла MSO__PLEASE__ADD__FILE_LANG
function _t($w = '', $file = false, $label = '')
{
    global $MSO;

    static $langs = []; // общий массив перевода
    static $file_langs = []; // список уже подключенных файлов
    static $all_w = [];

    // только для получения переводимых фраз и отладки!
    // ОПИСАНИЕ см. в common/language/readme.txt
    if (defined('MSO__PLEASE__RETURN__LANGS')) {
        if ($w === '__please__return__langs__') return $langs;
        if ($w === '__please__return__w__') return array_unique($all_w);
        if ($w === '__please__return__file_langs__') return array_unique($file_langs);

        if ($w) $all_w[] = $w;
    }

    // отслеживание всех переводимых фраз — РЕСУРСОЁМКО, ТОЛЬКО ДЛЯ ОТЛАДКИ
    if (defined('MSO__PLEASE__ADD__FILE_LANG')) require MSO__PLEASE__ADD__FILE_LANG;

    if (!isset($MSO->language)) return $w; // язык вообще не существует, выходим
    if (!($current_language = $MSO->language)) return $w; // есть, но не указан язык, выходим

    // проверим перевод, возможно он уже есть
    if (isset($langs[$w]) and $langs[$w]) return $langs[$w];

    /*
        на примере en
        
        /common/language/en-f.php - перевод для frontend (функция tf )
        /common/language/en.php - перевод для админки (функция t )
        
        всегда загружается ($file_langs['common'])
            /common/language/en-f.php
            
        если это админка, ($file_langs['admin']) то 
            /common/language/en.php - перевод для админки (функция t )
            
        если __FILE__, то загружаем и его.
    */

    if (!isset($file_langs['common'])) {
        // common был не подключен
        $langs = _t_add_file_to_lang($langs, 'common/language/', $current_language . '-f'); // front
        $file_langs['common'] = 'common/language/' . $current_language . '-f';
    }

    // если не указан $file то даём приоритет текущему шаблону
    if ($file === false) $file = 'mytemplate';

    // в админке подключаем свой перевод автоматом
    if (mso_segment(1) == 'admin') {
        if (!isset($file_langs['admin'])) {
            // admin был не подключен
            $langs = _t_add_file_to_lang($langs, 'common/language/', $current_language);
            $file_langs['admin'] = 'common/language/' . $current_language;
        }
    }

    if ($file == 'mytemplate' and !isset($file_langs['mytemplate'])) {
        // mytemplate был не подключен
        $langs = _t_add_file_to_lang($langs, 'templates/' . $MSO->config['template'] . '/language/', $current_language);
        $file_langs['mytemplate'] = 'templates/' . $MSO->config['template'] . '/language/' . $current_language;
    }

    // возможно указан свой каталог в __FILE__
    // условия оставляю для совместимости со старым переводом
    if ($file and $file != 'admin' and $file != 'plugins' and $file != 'templates' and $file != 'mytemplate') {
        // ключ = $file так меньше вычислений
        if (!isset($file_langs[$file])) {
            // заменим windows \ на /
            $fn = str_replace('\\', '/', $file);
            $bd = str_replace('\\', '/', $MSO->config['base_dir']);

            // если в $file входит base_dir, значит это использован __FILE__
            // нужно вычленить base_dir
            $pos = strpos($fn, $bd);
            if ($pos !== false) {
                $fn = str_replace($bd, '', $fn);
                $fn = dirname($file) . '/language/';

                $langs = _t_add_file_to_lang($langs, $fn, $current_language, true);
            }

            $file_langs[$file] = true;
        }
    }

    if (isset($langs[$w]) and $langs[$w]) return $langs[$w]; // проверка перевода	

    // перевода нет :-(	
    return $w;
}

// служебная функция для _t()
// нигде не использовать!
function _t_add_file_to_lang($langs, $path, $current_language, $full_name = false)
{
    global $MSO;

    if ($full_name)
        $fn = $path . $current_language . '.php';
    else
        $fn = $MSO->config['base_dir'] . $path . $current_language . '.php';

    if (file_exists($fn)) {
        require_once $fn; // есть такой файл

        if (isset($lang)) $langs = array_merge($langs, $lang); // есть ли в нем $lang ?
    }

    return $langs;
}

# end of file
