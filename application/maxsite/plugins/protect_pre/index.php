<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

/**
 *  Функция автоподключения плагина
 */
function protect_pre_autoload()
{
    mso_hook_add('content', 'protect_pre_custom'); // хук на вывод контента
}

/**
 *  Функция хука на обработку текста записи
 */
function protect_pre_custom($text = '')
{
    $text = preg_replace_callback('!(<pre><code.*?>)(.*?)(</code></pre>)!is', '_protect_pre', $text);
    $text = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', '_protect_pre', $text);
    $text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', '_protect_pre', $text);

    $text = preg_replace_callback('!@html_base64@(.*?)@/html_base64@!is', function ($m) {
        return base64_decode($m[1]);
    }, $text);

    return $text;
}

/**
 *  Функция, где происходит замена html-символов
 */
function _protect_pre($matches)
{
    $text = $matches[2];

    $text = str_replace('[', '&#91;', $text);
    $text = str_replace(']', '&#93;', $text);
    $text = str_replace("<br>", "\n", $text);
    $text = str_replace("<br />", "<br>", $text);
    $text = str_replace("<br/>", "<br>", $text);
    $text = str_replace("<br>", "\n", $text);

    $text = str_replace('<', '&lt;', $text);
    $text = str_replace('>', '&gt;', $text);

    // $text = str_replace('<p>', '', $text);
    // $text = str_replace('</p>', '', $text);
    // $text = str_replace('&lt;pre', '<pre', $text);
    // $text = str_replace('&lt;/pre', '</pre', $text);
    // $text = str_replace('pre&gt;', 'pre>', $text);

    $text =  '@html_base64@' . base64_encode($matches[1] . $text . $matches[3]) . '@/html_base64@';

    return $text;
}

# end of file
