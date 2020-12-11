<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// получение из массива номера $num_key ключа
// array('2'=>'Изменить');
// возвратит 2
function mso_array_get_key($ar, $num_key = 0, $no = false)
{
    $ar = array_keys($ar);

    if (isset($ar[$num_key]))
        return $ar[$num_key];
    else
        return $no;
}

// получение из массива ключ значения
// array('2'=>'Изменить');
// mso_array_get_key_value($ar, 'Изменить' ) возвратит 2
function mso_array_get_key_value($ar, $value = false, $no = false)
{
    if (!$value) return $no;
    if (!in_array($value, $ar)) return $no;

    foreach ($ar as $key => $val) {
        if ($val == $value) return $key;
    }
}

// объединение входящего массива $a с $def по ключам
// в $def задаются все дефолтные значения
// происходит пребразование значения trim, а также приведение типов bool и int
// если во входящем массиве нет ключа, он берется из $def
// &NBSP; заменяется на обычный пробел
function mso_merge_array($a, $def)
{
    $out = [];

    $def = array_merge($def, $a);

    foreach ($def as $d_key => $d_val) {
        if (isset($a[$d_key])) {
            $a_val = trim($a[$d_key]);

            if (is_bool($d_val)) $a_val = (bool) $a_val;
            elseif (is_int($d_val)) $a_val = (int) $a_val;

            $a_val = str_replace('&NBSP;', ' ', $a_val);

            $out[$d_key] = $a_val;
        } else {
            $out[$d_key] = str_replace('&NBSP;', ' ', $d_val);
        }
    }

    return $out;
}

// функция преобразования MySql-даты (ГГГГ-ММ-ДД ЧЧ:ММ:СС) в указанный формат date
// идея - http://dimoning.ru/archives/31
// $days и $month - массивы или строка (через пробел) названия дней недели и месяцев
function mso_date_convert($format = 'Y-m-d H:i:s', $data = '', $timezone = true, $days = false, $month = false)
{
    $part = explode(' ', $data);

    if (isset($part[0]))
        $ymd = explode('-', $part[0]);
    else
        $ymd = [0, 0, 0];

    if (isset($part[1]))
        $hms = explode(':', $part[1]);
    else
        $hms = [0, 0, 0];

    $y = $ymd[0];
    $m = $ymd[1];
    $d = $ymd[2];
    $h = $hms[0];
    $n = $hms[1];
    $s = $hms[2];

    $time = mktime($h, $n, $s, $m, $d, $y);

    if ($timezone) {
        $tz = (float) getinfo('time_zone'); // на всякий случай делаем приведение типа (php 7.1 ???)

        if ($timezone === -1) // в случаях, если нужно убрать таймзону
            $time = $time - $tz * 60 * 60;
        else
            $time = $time + $tz * 60 * 60;
    }

    $out = date($format, $time);

    if ($days) {
        if (!is_array($days)) $days = explode(' ', trim($days));

        $day_en = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $out = str_replace($day_en, $days, $out);

        $day_en = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $out = str_replace($day_en, $days, $out);
    }
    if ($month) {
        if (!is_array($month)) $month = explode(' ', trim($month));

        $month_en = ['January', 'February', 'March', 'April', 'May', 'June', 'July',    'August', 'September', 'October', 'November', 'December'];

        $out = str_replace($month_en, $month, $out);

        // возможна ситуация, когда стоит английский язык, поэтому следующая замена приведет к ошибке
        // поэтому заменим $month_en на что-то своё

        $out = str_replace($month_en, ['J_anuary', 'F_ebruary', 'M_arch', 'A_pril', 'M_ay', 'J_une', 'J_uly',    'A_ugust', 'S_eptember', 'O_ctober', 'N_ovember', 'D_ecember'], $out);

        $month_en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $out = str_replace($month_en, $month, $out);

        // теперь назад
        $out = str_replace(
            ['J_anuary', 'F_ebruary', 'M_arch', 'A_pril', 'M_ay', 'J_une', 'J_uly',    'A_ugust', 'S_eptember', 'O_ctober', 'N_ovember', 'D_ecember'],
            ['January', 'February', 'March', 'April', 'May', 'June', 'July',    'August', 'September', 'October', 'November', 'December'],
            $out
        );
    }

    return $out;
}

// переобразование даты в формат MySql
function mso_date_convert_to_mysql($year = 1970, $mon = 1, $day = 1, $hour = 0, $min = 0, $sec = 0)
{
    if ($day > 31) {
        $day = 1;
        $mon++;
        $year++;
    }

    if ($mon > 12) {
        $mon = 1;
        $year++;
    }

    if ($mon < 10)  $mon = '0' . $mon;
    if ($day < 10)  $day = '0' . $day;
    if ($hour < 10) $hour = '0' . $hour;
    if ($min < 10)  $min = '0' . $min;
    if ($sec < 10)  $sec = '0' . $sec;

    $res = $year . '-' . $mon . '-' . $day . ' ' . $hour . ':' . $min . ':' . $sec;

    return $res;
}

// преобразовать строку из чисел, разделенных запятыми, в массив
// если $int = true, то дополнительно преобразуется в числа
// если $probel = true, то разделителем может быть пробел
// если $unique = true, то убираем дубли
// если $range_dot = true, то формируется диапазон - «1..10» преобразуется в 1 2 3 4 5 6 7 8 9 10
function mso_explode($s = '', $int = true, $probel = true, $unique = true, $range_dot = true)
{
    $s = trim(str_replace(';', ',', $s));

    if ($probel) {
        $s = trim(str_replace('     ', ',', $s));
        $s = trim(str_replace('    ', ',', $s));
        $s = trim(str_replace('   ', ',', $s));
        $s = trim(str_replace('  ', ',', $s));
        $s = trim(str_replace(' ', ',', $s));
    }

    $s = trim(str_replace(',,', ',', $s));

    if ($range_dot and strpos($s,  '..') !== false) {
        $d = explode('..', trim($s));

        if (isset($d[0]) and isset($d[1])) {
            $k = range($d[0], $d[1]);
            $s = implode(',', $k);
        }
    }

    $s = explode(',', trim($s));
    if ($unique) $s = array_unique($s);

    $out = [];

    // foreach ($s as $key => $val) {
    foreach ($s as $val) {
        if ($int) {
            if ((int) $val > 0) $out[] = $val; // id в массив
        } else {
            if (trim($val)) $out[] = trim($val);
        }
    }

    if ($unique) $out = array_unique($out);

    return $out;
}

// обрезаем строку на кол-во слов
function mso_str_word($text, $counttext = 10, $sep = ' ')
{
    $words = explode($sep, $text);

    if (count($words) > $counttext)
        $text = implode($sep, array_slice($words, 0, $counttext));

    return $text;
}

// подсчет кол-ва слов в тексте
// можно предварительно удалить все тэги и преобразовать CR в $delim
function mso_wordcount($str = '', $delim = ' ', $strip_tags = true, $cr_to_delim = true)
{
    if ($strip_tags) $str = strip_tags($str);
    if ($cr_to_delim) $str = str_replace("\n", $delim, $str);

    // $out = str_replace($delim . $delim, $delim, $str);

    return count(explode($delim, $str));
}

// для юникода отдельный wordwrap
// часть кода отсюда: http://us2.php.net/manual/ru/function.wordwrap.php#78846
// переделал и исправил ошибки я
// ширина, разделитель
function mso_wordwrap($str, $wid = 80, $tag = ' ')
{
    $pos = 0;
    $tok = [];
    $l = mb_strlen($str, 'UTF8');

    if ($l == 0) return '';

    $flag = false;

    $tok[0] = mb_substr($str, 0, 1, 'UTF8');

    for ($i = 1; $i < $l; ++$i) {
        $c = mb_substr($str, $i, 1, 'UTF8');

        if (!preg_match('/[a-z\'\"]/i', $c)) {
            ++$pos;
            $flag = true;
        } elseif ($flag) {
            ++$pos;
            $flag = false;
        }

        if (isset($tok[$pos]))
            $tok[$pos] .= $c;
        else
            $tok[$pos] = $c;
    }

    $linewidth = 0;
    $pos = 0;
    $ret = [];
    $l = count($tok);

    for ($i = 0; $i < $l; ++$i) {
        if ($linewidth + ($w = mb_strwidth($tok[$i], 'UTF8')) > $wid) {
            ++$pos;
            $linewidth = 0;
        }
        if (isset($ret[$pos]))
            $ret[$pos] .= $tok[$i];
        else
            $ret[$pos] = $tok[$i];

        $linewidth += $w;
    }

    return implode($tag, $ret);
}

// функция возвращает массив $path_url-файлов по указанному $path - каталог на сервере
// $full_path - нужно ли возвращать полный адрес (true) или только имя файла (false)
// $exts - массив требуемых расширений. По-умолчанию - картинки
function mso_get_path_files($path = '', $path_url = '', $full_path = true, $exts = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'svg'], $minus = true)
{
    // если не указаны пути, то отдаём пустой массив
    if (!$path) return [];
    if (!is_dir($path)) return []; // это не каталог

    $CI = &get_instance(); // подключение CodeIgniter
    $CI->load->helper('directory'); // хелпер для работы с каталогами

    $files = directory_map($path, true); // получаем все файлы в каталоге

    if (!$files) return []; // если файлов нет, то выходим

    $all_files = []; // результирующий массив с нашими файлами

    // функция directory_map возвращает не только файлы, но и подкаталоги
    // нам нужно оставить только файлы. Делаем это в цикле
    foreach ($files as $file) {
        if (@is_dir($path . $file)) continue; // это каталог

        $ext = substr(strrchr($file, '.'), 1); // расширение файла

        // расширение подходит?
        if (in_array($ext, $exts)) {
            if ($minus) {
                if (strpos($file, '_') === 0) continue; // исключаем файлы, начинающиеся с _
                if (strpos($file, '-') === 0) continue; // исключаем файлы, начинающиеся с -
            }

            // добавим файл в массив сразу с полным адресом
            if ($full_path)
                $all_files[] = $path_url . $file;
            else
                $all_files[] = $file;
        }
    }

    natsort($all_files); // отсортируем список для красоты

    return $all_files;
}

// возвращает подкаталоги в указаном каталоге. 
// можно указать исключения из каталогов в $exclude 
// в $need_file можно указать обязательный файл в подкаталоге 
// если $need_file = true то обязательный php-файл в подкаталоге должен совпадать с именем подкаталога
// например для /menu/ это menu.php 
function mso_get_dirs($path, $exclude = [], $need_file = false, $minus = true)
{
    $CI = &get_instance(); // подключение CodeIgniter
    $CI->load->helper('directory'); // хелпер для работы с каталогами

    if ($all_dirs = directory_map($path, true)) {
        $dirs = [];

        foreach ($all_dirs as $d) {
            // нас интересуют только каталоги
            if (is_dir($path . $d) and !in_array($d, $exclude)) {
                if ($minus) {
                    if (strpos($d, '_') === 0) continue; // исключаем файлы, начинающиеся с _
                    if (strpos($d, '-') === 0) continue; // исключаем файлы, начинающиеся с -
                }

                // если указан обязательный файл, то проверяем его существование
                if ($need_file === true and !file_exists($path . $d . '/' . $d . '.php')) continue;
                if ($need_file !== true and $need_file and !file_exists($path . $d . '/' . $need_file)) continue;

                $dirs[] = $d;
            }
        }

        natcasesort($dirs);

        return $dirs;
    } else {
        return [];
    }
}

// Поиск/получение из многострочного текста ключ=значение. Ключи регистрозависимы
// Примеры. На входе текст $text:
// a = 10
// b = 20 30 40
// b = 50 60
//
// результат: mso_text_find_key($text)
// массив [a] => 10
//        [b] => 50 60
// если не указан крритерий поиска, то одинаковые ключи будут затерты нижними
//
// результат: mso_text_find_key($text, 'b')
// строка: 20 30 40
// находится только первый ключ, остальные ниже игнорируются
function mso_text_find_key($text, $find = false, $delim = "=")
{
    $all_text = explode("\n", $text); // в массив весь текст построчно

    $out = [];

    foreach ($all_text as $elem) {
        $elem = explode($delim, trim($elem));
        if (count($elem) < 2) continue; // должно быть как минимум два элемента

        $key = trim($elem[0]); // ключ
        if (!$key) continue; // если ключ пустой, то продолжим цикл

        $val = trim($elem[1]); // данные могут быть любыми

        if ($find !== false) {
            if ($find == $key) {
                // если нужен поиск по ключу
                $out = $val;
                break; // нашли что нужно и выходим
            }
        } else {
            // все элементы в кучу
            $out[$key] = $val;
        }
    }

    return $out;
}

/**
 * Склонение числительных
 *
 * @param $n Число
 * @param $f1 string 1 комментарИЙ
 * @param $f2 string 2 комментарИЯ
 * @param $f5 string 5 комментарИЕВ
 * @return string
 */
function mso_plur($n = 0, $f1 = 'комментарий', $f2 = 'комментария', $f5 = 'комментариев')
{
    $n = (int) $n;
    $word = [$f1, $f2, $f5];
    $ar = [2, 0, 1, 1, 1, 2];
    return $word[($n % 100 > 4 and $n % 100 < 20) ? 2 : $ar[min($n % 10, 5)]];
}



// получение адреса первой картинки IMG в тексте
// адрес обрабатывается, чтобы сформировать адрес полный (full), миниатюра (mini) и превью (prev)
// результат записит от значения $res
// если $res = true => найденный адрес или $default
// если $res = 'mini' => адрес mini
// если $res = 'prev' => адрес prev
// если $res = 'full' => адрес full
// если $res = 'all' => массив из всех сразу:
//  		[full] => http://сайт/uploads/image.jpg
//  		[mini] => http://сайт/uploads/mini/image.jpg
//  		[prev] => http://сайт/uploads/_mso_i/image.jpg
function mso_get_first_image_url($text = '', $res = true, $default = '')
{
    $pattern = '!<img.*?src="(.*?)"!i';

    //$pattern = '!<img.+src=[\'"]([^\'"]+)[\'"].*>!i';

    preg_match_all($pattern, $text, $matches);

    //pr($matches);
    if (isset($matches[1][0])) {
        $url = $matches[1][0];
        if (empty($url)) $url = $default;
    } else {
        $url = $default;
    }

    //_pr($url,1);
    if (strpos($url, '/uploads/smiles/') !== false) return ''; // смайлики исключаем
    if ($res === true) return $url;

    $out = [];

    // если адрес не из нашего uploads, то отдаем для всех картинок исходный адрес
    if (strpos($url, getinfo('uploads_url')) === false) {
        $out['mini'] = $out['full'] = $out['prev'] = $url;

        if ($res == 'mini' or $res == 'prev' or $res == 'full')
            return $out['mini'];
        else
            return $out;
    }

    if (strpos($url, '/mini/') !== false) // если в адресе /mini/ - это миниатюра
    {
        $out['mini'] = $url;
        $out['full'] = str_replace('/mini/', '/', $url);
        $out['prev'] = str_replace('/mini/', '/_mso_i/', $url);
    } elseif (strpos($url, '/_mso_i/') !== false) // если в адресе /_mso_i/ - это превью 100х100
    {
        $out['prev'] = $url;
        $out['full'] = str_replace('/_mso_i/', '/', $url);
        $out['mini'] = str_replace('/_mso_i/', '/mini/', $url);
    } else // обычная картинка
    {
        $fn = end(explode("/", $url)); // извлекаем имя файла
        $out['full'] = $url;
        $out['mini'] = str_replace($fn, 'mini/' . $fn, $url);
        $out['prev'] = str_replace($fn, '_mso_i/' . $fn, $url);
    }

    if ($res == 'mini') return $out['mini'];
    elseif ($res == 'prev') return $out['prev'];
    elseif ($res == 'full') return $out['full'];
    else return $out;
}



// УТОЧНИТЬ: https://dev.mysql.com/doc/refman/8.0/en/information-functions.html#function_found-rows
// лучше SQL_CALC_FOUND_ROWS вообще не использовать
# Функция возвращает массив для пагинации при выполнении предыдущего sql-запроса с SELECT SQL_CALC_FOUND_ROWS
# при помощи sql-запроса SELECT FOUND_ROWS();
# Использовать непосредственно после исходного get-запроса с указанным SQL_CALC_FOUND_ROWS
# $limit - записей на страницу
# $pagination_next_url - сегмент-признак пагинации (для определения текущей страницы пагинации)
/*
    пример использования:
    формируем sql-запрос с SQL_CALC_FOUND_ROWS
    
    $CI->db->select('SQL_CALC_FOUND_ROWS comments_id, ...', false);
    ...
    $limit = 20; // задаем кол-во записей на страницу
    $CI->db->limit($limit, mso_current_paged() * $limit - $limit); // не более $limit
    ...
    $query = $CI->db->get(); // выполнили запрос

    $pagination = mso_sql_found_rows($limit); // получили массив для пагинации
    
    // $pagination - готовый массив для пагинации
    mso_hook('pagination', $pagination); // вывод пагинации

*/
function mso_sql_found_rows($limit = 20, $pagination_next_url = 'next')
{
    $CI = &get_instance();

    // определим общее кол-во записей
    $query_row = $CI->db->query('SELECT FOUND_ROWS() as found_rows', false);

    if ($query_row->num_rows() > 0) {
        $ar = $query_row->result_array();
        $found_rows = $ar[0]['found_rows'];

        $maxcount = ceil($found_rows / $limit); // всего страниц пагинации

        $current_paged = mso_current_paged($pagination_next_url);

        if ($current_paged > $maxcount) $current_paged = $maxcount;

        $offset = $current_paged * $limit - $limit;

        $out = [
            'limit' => $limit, // строк на страницу - для LIMIT
            'offset' => $offset, // смещение для LIMIT
            'found_rows' => $found_rows, // всего записей, как без LIMIT
            'maxcount' => $maxcount, // всего страниц пагинации
            'next_url' => $pagination_next_url, // признак пагинации
        ];
    } else {
        $out = false;
    }

    $CI->db->cache_delete_all();

    return $out;
}

/**
 *  функция возвращает текст Lorem Ipsum 
 *  из http://lpf.maxsite.com.ua/
 *  
 *  @param $count количество слов
 *  @param $color если указан $color, то текст обрамляется в <span> с color: $color
 *  @param $dot : если false, то удаляются все знаки препинания
 *  @param $LoremText : можно задать свой текст
 *  
 *  @return string
 */
function mso_lorem($count = 50, $color = false, $dot = true, $LoremText = true)
{
    if ($LoremText === true)
        $LoremText = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vivamus vitae risus vitae lorem iaculis placerat. Aliquam sit amet felis. Etiam congue. Donec risus risus, pretium ac, tincidunt eu, tempor eu quam. Morbi blandit mollis magna. Suspendisse eu tortor. Donec vitae felis nec ligula blandit rhoncus. Ut a pede ac neque mattis facilisis. Nulla nunc ipsum, sodales vitae, hendrerit non, imperdiet ac ante. Morbi sit amet mi. Ut magna. Curabitur id est. Nulla velit. Sed consectetuer sodales justo. Aliquam dictum gravida libero. Sed eu turpis. Nunc id lorem. Aenean consequat tempor mi. Phasellus in neque. Nunc fermentum convallis ligula. Suspendisse in nulla. Nunc eu ipsum tincidunt risus pellentesque fringilla. Integer iaculis pharetra eros. Nam ut sapien quis arcu ullamcorper cursus. Vestibulum tempor nisi rhoncus eros. Sed iaculis ultricies tellus. Cras pellentesque erat eu urna. Cras malesuada. Quisque congue ultricies neque. Nullam a nisl. Sed convallis turpis a ante. Morbi eu justo sed tortor euismod porttitor. Aenean ut lacus. Maecenas nibh eros, dapibus at, pellentesque in, auctor a enim. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam congue pede a ipsum. Sed libero quam, sodales eget, venenatis non, cursus vel velit. In vulputate. In vehicula. Aenean quam mauris, vehicula non, suscipit at, venenatis sed arcu. Etiam ornare fermentum felis. Donec ligula metus, placerat quis, blandit at, congue molestie ante. Donec viverra nibh et dolor.";

    // перетусуем предложения
    $ar = explode('.', $LoremText);
    shuffle($ar);
    $LoremText = implode('.', $ar);
    $words = explode(' ', $LoremText);

    if (count($words) > $count)
        $text = implode(' ', array_slice($words, 0, $count));
    else
        $text = $LoremText;

    $text = trim($text) . '.';
    $text = str_replace(',.', '.', $text);
    $text = str_replace('..', '.', $text);

    if (!$dot) $text = trim(str_replace(array('.', ','), '', $text));
    if (strpos($text, '.') === 0) $text = mb_substr($text, 1);
    if ($color) $text = '<span style="color: ' . $color . '">' . $text . '</span>';

    return $text;
}

/**
 * Получить url-адрес php-обработчика по правилам MaxSite CMS для ajax-запроса
 * на основе полного имени файла __FILE__
 * На выходе будет ссылка вида: //сайт/ajax/dG4tcGx43dGVz...
 * Файл-обработчик будет находиться там же, где и исходный (обычно это форма)
 * 
 * Файл form.php -> form-ajax.php
 * $ajaxForm = mso_receive_ajax(__FILE__);
 * 
 * Файл form.php -> form-state-ajax.php
 * $ajaxForm = mso_receive_ajax(__FILE__, '-state');
 * 
 *  @param $file: файл 
 *  @param $p: дополнительный префикс
 *  
 *  @return string
 */
function mso_receive_ajax($file, $p = '')
{
    return getinfo('ajax') . base64_encode(str_replace('.php', $p . '-ajax.php', str_replace(str_replace('\\', '/', getinfo('base_dir')), '', str_replace('\\', '/', $file))));
}

/**
 * Получить полный URL-адрес указанного каталога
 * 
 * Файл: /application/maxsite/templates/my/parts/m1/content.php
 * Результат: http://site/application/maxsite/templates/my/parts/m1/
 * $urlThisDir = mso_urldir(__DIR__); 
 * 
 * @param string $dir — каталог
 */
function mso_urldir($dir)
{
    return getinfo('siteurl') . str_replace(str_replace('\\', '/', getinfo('FCPATH')), '', str_replace('\\', '/', $dir)) . '/';
}

# end of file
