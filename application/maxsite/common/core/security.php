<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// правильность email
function mso_valid_email($email = '')
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
    // из helpers/email_helper.php
    // return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? false : true;
}

// функция проверяет существование POST, а также обязательных полей
// которые передаются в массиве ['f_session_id','f_desc','f_edit_submit']
// если полей нет, то возвращается false
// если поля есть, то возвращается $_POST
function mso_check_post($args = [])
{
    if ($_POST) {
        $check = true;
        foreach ($args as $key => $field) {
            if (!isset($_POST[$field])) {
                // нет значения - выходим
                $check = false;
                break;
            }
        }

        if (!$check)
            return false;
        else
            return $_POST;
    } else {
        return false;
    }
}

// проверем рефер на xss-атаку
// работает только если есть POST
function mso_checkreferer()
{
    if ($_POST) {
        if (!isset($_SERVER['HTTP_REFERER']))
            die('<b><font color="red">Achtung! XSS attack! No REFERER!</font></b>');

        $ps = parse_url(mso_clean_str($_SERVER['HTTP_REFERER'], 'xss'));

        if (isset($ps['host']))
            $p = $ps['host'];
        else
            $p = '';

        if ($p and isset($ps['port']) and $ps['port'] != 80) $p .= ':' . $ps['port'];

        if ($p != $_SERVER['HTTP_HOST'])
            die('<b><font color="red">Achtung! XSS attack!</font></b>');
    }
}

// защита сессии
// сравниваем переданную сессию с текущей
// и если указан редирект, то в случае несовпадения переходим по нему
// иначе возвращаем true - если все ок и false - ошибка сессии
function mso_checksession($session_id, $redirect = false)
{
    global $MSO;

    $result = ($MSO->data['session']['session_id'] == $session_id);

    if ($redirect and !$result) {
        mso_redirect($redirect);
        return;
    }

    return $result;
}

// удаляем все лишнее в формах
// если второй параметр = true то возвращает false, если данные после стрипа изменились и $s - теже
function mso_strip($s = '', $logical = false, $arr_strip = ['\\', '|', '/', '%', '*', '`', '<', '>'])
{
    $s1 = $s;
    $s1 = stripslashes($s1);
    $s1 = strip_tags($s1);
    // $s1 = htmlspecialchars($s1, ENT_QUOTES);
    $s1 = str_replace($arr_strip, '', $s1);
    $s1 = trim($s1);

    if ($logical) {
        if ($s1 === $s)
            return $s;
        else
            return false;
    } else {
        return $s1;
    }
}

// генератор md5 свой
function mso_md5($t = '')
{
    global $MSO;

    if ($MSO->config['secret_key'])
        return strrev(md5($t . $MSO->config['secret_key']));
    else
        return strrev(md5($t . $MSO->config['site_url']));
}

// функция преобразует русские и украинские буквы в английские
// также удаляются все служебные символы
function mso_slug($slug)
{
    $slug = mso_hook('slug_do', $slug);

    if (!mso_hook_present('slug')) {
        // таблица замены
        $repl = [
            "А" => "a", "Б" => "b",  "В" => "v",  "Г" => "g",   "Д" => "d",
            "Е" => "e", "Ё" => "jo", "Ж" => "zh",
            "З" => "z", "И" => "i",  "Й" => "j",  "К" => "k",   "Л" => "l",
            "М" => "m", "Н" => "n",  "О" => "o",  "П" => "p",   "Р" => "r",
            "С" => "s", "Т" => "t",  "У" => "u",  "Ф" => "f",   "Х" => "h",
            "Ц" => "c", "Ч" => "ch", "Ш" => "sh", "Щ" => "shh", "Ъ" => "",
            "Ы" => "y", "Ь" => "",   "Э" => "e",  "Ю" => "ju", "Я" => "ja",

            "а" => "a", "б" => "b",  "в" => "v",  "г" => "g",   "д" => "d",
            "е" => "e", "ё" => "jo", "ж" => "zh",
            "з" => "z", "и" => "i",  "й" => "j",  "к" => "k",   "л" => "l",
            "м" => "m", "н" => "n",  "о" => "o",  "п" => "p",   "р" => "r",
            "с" => "s", "т" => "t",  "у" => "u",  "ф" => "f",   "х" => "h",
            "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shh", "ъ" => "",
            "ы" => "y", "ь" => "",   "э" => "e",  "ю" => "ju",  "я" => "ja",

            // Украина
            "Є" => "ye", "є" => "ye", "І" => "i", "і" => "i",
            "Ї" => "yi", "ї" => "yi", "Ґ" => "g", "ґ" => "g",

            // Беларусь
            "Ў" => "u", "ў" => "u", "'" => "",

            // Румыния
            "ă" => 'a', "î" => 'i', "ş" => 'sh', "ţ" => 'ts', "â" => 'a',

            "«" => "", "»" => "", "—" => "-", "`" => "", " " => "-",
            "[" => "", "]" => "", "{" => "", "}" => "", "<" => "", ">" => "",

            "?" => "", "," => "", "*" => "", "%" => "", "$" => "",

            "@" => "", "!" => "", ";" => "", ":" => "", "^" => "", "\"" => "",
            "&" => "", "=" => "", "№" => "", "\\" => "", "/" => "", "#" => "",
            "(" => "", ")" => "", "~" => "", "|" => "", "+" => "", "”" => "", "“" => "",
            "'" => "",

            "’" => "",
            "—" => "-", // mdash (длинное тире)
            "–" => "-", // ndash (короткое тире)
            "™" => "tm", // tm (торговая марка)
            "©" => "c", // (c) (копирайт)
            "®" => "r", // (R) (зарегистрированная марка)
            "…" => "", // (многоточие)
            "“" => "",
            "”" => "",
            "„" => "",
        ];

        $slug = strtr(trim($slug), $repl);
        $slug = htmlentities($slug); // если есть что-то из юникода
        $slug = strtr(trim($slug), $repl);
        $slug = strtolower($slug);

        // разрешим расширение .html
        $slug = str_replace('.htm', '@HTM@', $slug);
        $slug = str_replace('.', '', $slug);
        $slug = str_replace('@HTM@', '.htm', $slug);
        $slug = str_replace('---', '-', $slug);
        $slug = str_replace('--', '-', $slug);
        $slug = str_replace('-', ' ', $slug);
        $slug = str_replace(' ', '-', trim($slug));
    } else {
        $slug = mso_hook('slug', $slug);
    }

    $slug = mso_hook('slug_posle', $slug);

    return $slug;
}

// проверка комбинации логина-пароля
// если указан act - то сразу смотрим разрешение на действие
function mso_check_user_password($login = false, $password = false, $act = false)
{
    if (!$login or !$password) return false;

    $CI = &get_instance();

    $CI->db->select('users_id, users_groups_id');
    $CI->db->where(['users_login' => $login, 'users_password' => $password]);
    $CI->db->limit(1);

    $query = $CI->db->get('users');

    if ($query->num_rows() > 0) {
        // есть такой юзер
        if ($act) {
            // нужно проверить по users_groups_id разрешение для этого юзера для этого действия
            $r = $query->result_array();

            return mso_check_allow($act, $r[0]['users_id']);
        } else {
            // если act не указан, значит можно
            return true;
        }
    } else {
        return false;
    }
}

// проверка доступа для юзера для указанного действия/функции
// если $cache = true то данные можно брать из кэша, иначе всегда из SQL
function mso_check_allow($act = '', $user_id = false, $cache = true)
{
    global $MSO;

    if (!$act) return false;

    if ($user_id == false) {
        // если юзер не указан
        if (!$MSO->data['session']['users_id']) // и в сесии
            return false;
        else
            $user_id = $MSO->data['session']['users_id']; // берем его номер из сессии

        if ($MSO->data['session']['users_groups_id'] == '1') // отмечена первая группа - это админы
            return true; // админам всё можно

    } else {
        // юзер указан явно - нужно проверять
        $user_id = (int) $user_id;
    }

    // если есть кэш этого участника, где уже хранятся его разрешения
    // то берем кэш, если нет, то выполняем запрос полностью
    if ($cache)
        $k = mso_get_cache('user_rules_' . $user_id);
    else
        $k = false;

    if (!$k) {
        // нет кэша
        // по номеру участника получаем номер группы
        // по номеру группы получаем все разрешения этой группы

        $CI = &get_instance();
        $CI->db->select('users_groups_id, groups_rules, groups_id'); // groups_name
        $CI->db->limit(1);
        $CI->db->where('users_id', $user_id);
        $CI->db->from('users');
        $CI->db->join('groups', 'groups.groups_id = users.users_groups_id');

        $query = $CI->db->get();

        foreach ($query->result_array() as $rw) {
            $rules = $rw['groups_rules'];
            $groups_id = $rw['groups_id'];
        }

        if ($groups_id == 1) return true; // админам можно всё

        $rules = unserialize($rules); // востанавливаем массив с разрешениями этой группы
        mso_add_cache('user_rules_' . $user_id, $rules); // сразу в кэш добавим
    } else {
        // есть кэш
        $rules = $k;
    }

    /*
    $rules = Array (
        [edit_users_group] => 1
        [edit_users_admin_note] => 1
        [edit_other_users] => 1
        [edit_self_users] => 1 )
    */

    if (isset($rules[$act])) // если действие есть в массиве
    {
        if ($rules[$act] == 1)
            return true; // и разрешено
        else
            return false; // запрещено
    } else {
        // действия вообще нет в разрешениях
        return false;
    }
}


// проверка на XSS-атаку входящего текста
// если задан $out_error, то отдаем сообщение
// если $die = true, то рубим выполнение с сообщением $out_error
// иначе возвращаем очищенный текст
// если xss не определен и есть $out_no_error, то возвращаем не текст, а $out_no_error
function mso_xss_clean($text, $out_error = '_mso_xss_clean_out_error', $out_no_error = '_mso_xss_clean_out_no_error', $die = false)
{
    $CI = &get_instance();

    // выполняем XSS-фильтрацию
    $text_xss = $CI->security->xss_clean($text, false);

    // если тексты не равны, значит существует опасность XSS-атаки
    if ($text != $text_xss) {
        if ($die) {
            die($out_error);
        } else {
            if ($out_error != '_mso_xss_clean_out_error')
                return $out_error;
            else
                return $text_xss;
        }
    } else {
        // тексты нормальные
        if ($out_no_error != '_mso_xss_clean_out_no_error')
            return $out_no_error;
        else
            return $text;
    }
}

// функция прогоняет ключи входящего массив данных через xss_clean
// если $strip_tags = true то удяляются все html-тэги 
// если $htmlspecialchars = true то преобразование в html-спецсимволы
function mso_xss_clean_data($data = [], $keys = [], $strip_tags = false, $htmlspecialchars = false)
{
    $CI = &get_instance();

    foreach ($keys as $key) {
        if (isset($data[$key])) {
            // есть данные
            if (!is_scalar($data[$key])) continue;

            if ($strip_tags) $data[$key] = strip_tags($data[$key]);
            if ($htmlspecialchars) $data[$key] = htmlspecialchars($data[$key]);

            $data[$key] = $CI->security->xss_clean($data[$key], false);
        }
    }

    return $data;
}

// прогоняем строку $str через фильтры, согласно указанным в $rules правилам
// правила
//  xss - xss-обработка
//  trim - удаление ведущих и конечных пустых символов
//  integer или int - преобразовать в число
//  strip_tags - удалить все тэги
//  htmlspecialchars - преобразовать в html-спецсимволы
//  valid_email или email - если это неверный адрес, вернет пустую строчку
//   not_url - удалить все признаки url
// если правило равно base, то это cработают правила: trim|xss|strip_tags|htmlspecialchars
// $s = mso_clean_str($s, 'trim|xss');
function mso_clean_str($str = '', $rules = 'base')
{
    if (!$str) return $str;
    if (!$rules) return $str;

    $CI = &get_instance();

    $rules = explode('|', $rules);
    $rules = array_map('trim', $rules); // обработка элементов массива
    $rules = array_unique($rules); // удалим повторы

    foreach ($rules as $rule) {
        if ($rule == 'trim' or $rule == 'base') $str = trim($str);
        if ($rule == 'xss' or $rule == 'base') $str = $CI->security->xss_clean($str, false);
        if ($rule == 'strip_tags' or $rule == 'base') $str = strip_tags($str);
        if ($rule == 'htmlspecialchars' or $rule == 'base') $str = htmlspecialchars($str);

        if ($rule == 'int' or $rule == 'integer') $str = intval($str);
        if ($rule == 'valid_email' or $rule == 'email') $str = (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? '' : $str;

        if ($rule == 'not_url') {
            $str = str_replace([
                'http://',
                'https://',
                '\\', '|', '/', '?', '%', '*', '`', '<', '>', '#',
                '&amp;', '^', '&', '(', ')', '+', '$'
            ], '', $str);
        }
    }

    return $str;
}

// функция возвращает массив $post обработанный по указанным правилам 
// входящий массив состоит из пары 'поле'=>'правила'
// где поле - ключ массива $post, а правила - правила валидации
// mso_clean_post(array('my_name'=>'trim|xss'))
// если массив $post не указан, то используется $_POST
// правила см. в mso_clean_str()
function mso_clean_post($keys = [], $post = false)
{
    if (!$post) {
        if ($_POST)
            $post = $_POST;
        else
            return $post;
    }

    foreach ($keys as $key => $rules) {
        if (isset($post[$key])) {
            // есть данные
            $post[$key] = mso_clean_str($post[$key], $rules);
        }
    }

    return $post;
}

/**
 *  функция кодирования/декодирования данных
 *  см. http://php.net/manual/ru/function.openssl-encrypt.php
 *  
 *  $data - входные данные 
 *  $encode - 'encode' - закодировать, 'decode' - раскодировать
 *  $ekey - ключ шифра, если false, то используется секретная фраза сайта
 * 
 *  // кодирование
 *  $a = mso_de_code('текст', 'encode');
 *  
 *  // раскодирование
 *  $b = mso_de_code($a, 'decode');
 *  
 */
function mso_de_code($data, $encode = 'encode', $ekey = false)
{
    global $MSO;

    if (!$ekey) $ekey = $MSO->config['secret_key'];

    $cipher = "AES-128-CBC";
    $sha2len = 32;

    // для php 5.3
    if (!defined('OPENSSL_RAW_DATA')) define('OPENSSL_RAW_DATA', (int) 1);

    if ($encode == 'encode') // кодировать
    {
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($data, $cipher, $ekey, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $ekey, true);

        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

        // добавляем префикс, указывающий на само кодирование
        return 'MSO-' . $ciphertext;
    } else {
        // расскодирование
        // если нет префикса, то отдаем текст как есть
        if (strpos($data, 'MSO-') === 0) {
            $data = substr($data, 4);

            $c = base64_decode($data);
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len);
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            $original = openssl_decrypt($ciphertext_raw, $cipher, $ekey, OPENSSL_RAW_DATA, $iv);

            return $original;
        } else {
            return $data;
        }
    }
}

/**
 *  Функция проверяет корректность формирования пути к файлу, чтобы исключить относительные пути
 *  из каталога и имени файла
 *  @param $dir — каталог
 *  @param $file — файл в этом каталоге
 *  @return если корректный путь, то полный путь к файлу, иначе false
 *          если файла нет, то возвращает false
 *  
 */
function mso_check_dir_file($dir, $file)
{
	$fullPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $dir . $file); // win/linux
    $fileReal = realpath($fullPath);
    
    if ($fileReal and $fileReal === $fullPath) 
        return $fileReal;
    else
        return false;
}

# end of file
