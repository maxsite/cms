<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

/**
 * Вывод юнитов
 * 
 * $text_units содержит полный текст с юнитами
 * пример использования см. shared/type/home/home.php
 * через $PAGES и $PAGINATION можно передать в юнит заранее подготовленные записи
 * 
 * спецкод @fromfile файл — позволяет подключать любой файл в виде текста. 
 * файл относительно шаблона. Не зависит от вложенности в [unit]
 * 		@fromfile blocks/home/lastp-1.php
 * 
 * спецкод @module модуль — позволяет подключать файл в виде текста из каталога шаблона modules.
 * 		@module ads/ad1 -> подключит файл modules/ads/ad1/index.php как и @fromfile
 * 
 * В @module можно использовать дополнительные параметры, которые добавляются в виде значения юнита {args}
 * 	    @module berry/block || block = ca/ca1.php ^ color = t-red
 *      В модуле berry/block вставка {args} бужет заменена на указанные параметры
 */
function mso_units_out($text_units, $PAGES = [], $PAGINATION = [], $path_file = 'type/home/units/')
{
    $text_units .= "\n";

    // подключаем файл как текст @fromfile и @module
    $text_units = preg_replace_callback('!@fromfile (.*?)\n!is', '_mso_units_out_fromfile', $text_units);

    // каталог для модулей можно переопределить через mso_set_val('mso_units_out_modulesDir', 'каталог/');
    $modulesDir = mso_get_val('mso_units_out_modulesDir', 'modules/');

    $text_units = preg_replace_callback('!@module (.*?)\n!is', '_mso_units_out_module', $text_units);

    // еще раз @fromfile, поскольку он может быть в подключенном @module
    $text_units = preg_replace_callback('!@fromfile (.*?)\n!is', '_mso_units_out_fromfile', $text_units);

    // если в тексте юнита есть вхождение @USE_PHP@
    // то разрешим в файле исполнять PHP, включая php-шаблонизатор
    if (strpos($text_units, '@USE_PHP@')) {
        $text_units = mso_tmpl_prepare($text_units, false);
        ob_start();
        eval($text_units);
        $text_units = ob_get_contents();
        ob_end_clean();
    }

    // замены по всему тексту юнитов
    $text_units = str_replace('[siteurl]', getinfo('siteurl'), $text_units);
    $text_units = str_replace('[site_url]', getinfo('siteurl'), $text_units);
    $text_units = str_replace('[templateurl]', getinfo('template_url'), $text_units);
    $text_units = str_replace('[template_url]', getinfo('template_url'), $text_units);

    // ищем вхождение [unit] ... [/unit]
    $units = mso_section_to_array($text_units, '!\[unit\](.*?)\[\/unit\]!is', ['file' => '']);

    // pr($units[0], 1);

    // подключаем каждый указанный unit 
    // _rules — php-условие, при котором юнит выводится
    // параметр file где указывается файл юнита в каталоге $path_file (type/home/units/) или относительно шаблона
    // если file нет, то проверяются другие параметры если есть:
    // html — выводится как есть текстом/ Можно использовать php-шаблонизатор {{ }} и {% %}
    // require — подключается файл в шаблоне (путь относительно каталога шаблона)
    //			у require также можно указать 
    //			парсер parser = autotag_simple — функция, которая через которую прогонится файл
    //			php-шаблонизатор: tmpl = 1
    //			каталог модуля в module = ads/ad1 — поиск файла будет относительно каталога модуля
    // ushka — ушка
    // component — компонент шаблона
    // option_key и option_type и option_default — опция

    if ($units) {
        $UNIT_NUM = 0; // порядковый номер юнита (можно использовать для кэширования)

        foreach ($units as $UNIT) {
            $UNIT_NUM++;
            
            // уникальный id-хэш юнита — можно использовать при кэшировании
            // с учтом адреса страницы
            $UNIT_UID = abs(crc32(json_encode($UNIT) . $UNIT_NUM . mso_current_url())); 

            // в юните в произвольном поле может быть вхождение [var@ПЕРЕМЕННАЯ]
            // нужно их обработать и заменить
            $UNIT1 = [];

            foreach ($UNIT as $k => $v) {
                if (strpos($v, '[var@') !== false) {
                    preg_match_all('!(\[var@)(.*?)(\])!is', $v, $matches, PREG_SET_ORDER);

                    if ($matches) {
                        foreach ($matches as $m) {
                            if (isset($UNIT['var@' . $m[2]])) {
                                $v = str_replace('[var@' . $m[2] . ']', trim($UNIT['var@' . $m[2]]), $v);
                            }
                        }
                    }
                }

                $UNIT1[$k] = $v;
            }

            $UNIT = $UNIT1;

            // храним текущий юнит в глобальном доступе
            mso_set_val('current_unit', $UNIT);

            // _rules — устаревший, вместо него нужно использовать rules
            if (isset($UNIT['_rules']) and trim($UNIT['_rules'])) {
                $rules = 'return ( ' . trim($UNIT['_rules']) . ' ) ? 1 : 0;';
                $rules_result = eval($rules); // выполяем
                if ($rules_result === false) $rules_result = 1; // возможно произошла ошибка
                if ($rules_result !== 1) continue;
            }

            // rules тоже самое что и _rules — код для обратной совместимости
            if (isset($UNIT['rules']) and trim($UNIT['rules'])) {
                $rules = 'return ( ' . trim($UNIT['rules']) . ' ) ? 1 : 0;';
                $rules_result = eval($rules); // выполяем
                if ($rules_result === false) $rules_result = 1; // возможно произошла ошибка
                if ($rules_result !== 1) continue;
            }

            if (trim($UNIT['file'])) {
                $file = trim($UNIT['file']);

                $module = (isset($UNIT['module']) and trim($UNIT['module'])) ? trim($UNIT['module']) : false;
                if ($module) $file = $modulesDir . $module . '/' . $file;

                // в подключаемом файле доступна переменная $UNIT — массив параметров
                if ($fn = mso_find_ts_file($path_file . $file)) {
                    require $fn;
                } else {
                    // аналогично, только файл относительно каталога шаблона
                    if ($fn = mso_fe($file)) require $fn;
                }
            } elseif (isset($UNIT['html']) and trim($UNIT['html'])) {
                eval(mso_tmpl_prepare(trim($UNIT['html']), false));
            } elseif (isset($UNIT['require']) and trim($UNIT['require'])) {
                $parser = (isset($UNIT['parser']) and trim($UNIT['parser'])) ? trim($UNIT['parser']) : false;

                $tmpl = (isset($UNIT['tmpl']) and trim($UNIT['tmpl'])) ? trim($UNIT['tmpl']) : false;

                $file = trim($UNIT['require']);

                // если указан модуль, то файл указан относительно каталога модуля
                $module = (isset($UNIT['module']) and trim($UNIT['module'])) ? trim($UNIT['module']) : false;

                if ($module) $file = $modulesDir . $module . '/' . $file;

                // pr($UNIT);
                mso_parse_file($file, $parser, $tmpl, true);
            } elseif (isset($UNIT['ushka']) and trim($UNIT['ushka']) and function_exists('ushka')) {
                echo ushka(trim($UNIT['ushka']));
            } elseif (isset($UNIT['component']) and trim($UNIT['component'])) {
                if ($_fn = mso_fe('components/' . trim($UNIT['component']) . '/index.php'))
                    require $_fn;
                elseif ($_fn = mso_fe('components/' . trim($UNIT['component']) . '/' . trim($UNIT['component']) . '.php'))
                    require $_fn;
            } elseif (isset($UNIT['option_key']) and trim($UNIT['option_key'])) {
                // если option_type не указан, то это текущий шаблон
                if (!isset($UNIT['option_type']))
                    $ot = getinfo('template');
                else
                    $ot = trim($UNIT['option_type']);

                // если option_type = %TEMPLATE% — меняем на текущий шаблон
                if ($ot === '%TEMPLATE%') $ot = getinfo('template');

                $od = isset($UNIT['option_default']) ? trim($UNIT['option_default']) : '';

                echo mso_get_option(trim($UNIT['option_key']), $ot, $od);
            } elseif (isset($UNIT['sidebar']) and trim($UNIT['sidebar'])) {
                mso_show_sidebar($UNIT['sidebar']);
            }

            mso_unset_val('current_unit'); // удалим текущий юнит, чтобы он больше не влиял
        }
    }
}

// callback к mso_units_out()
function _mso_units_out_fromfile($matches)
{
    $fn = trim(str_replace('=', '', $matches[1]));

    if ($fn = mso_fe($fn)) {
        $data = file_get_contents($fn);
        // pr($data, 1);
        return $data;
    } else {
        return '';
    }
}

// callback к mso_units_out()
function _mso_units_out_module($matches)
{
    $modulesDir = mso_get_val('mso_units_out_modulesDir', 'modules/');

    // параметры модуля
    $par = explode('||', $matches[1]);
    $f = trim($par[0]);
    $args = $par[1] ?? '';

    if ($args) {
        // внутри может использовать перенос строки в виде символа ^
        $args = explode("^", $args);
        $args = array_map('trim', $args);
        $args = implode("\n", $args);
    }

    $fn = $modulesDir . $f . '/index.php';

    // $fn = $modulesDir . trim(str_replace('=', '', $matches[1])) . '/index.php'; // старый вариант

    if ($fn = mso_fe($fn)) {
        $data = file_get_contents($fn);
        $data = str_replace('{args}', $args, $data);
        // pr($data);
        return $data;
    } else {
        return '';
    }
}

# end of file
