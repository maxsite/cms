<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# авторасстановка тэгов
function autotag_default($text)
{
    $text = str_replace(array("\r\n", "\r"), "\n", $text);

    // отдавать как есть - слово в начале текста
    if (( strpos($text, '[source]') !== false and strpos(trim($text), '[source]') == 0 )) {
        $text = str_replace('[source]', '', $text);
        return $text;
    }

    # если html-код в [html_r] код [/html_r]
    # в отличие от [html] — отдаёт полностью исходный html без обработок
    $text = str_replace('<p>[html_r]</p>', '[html_r]', $text);
    $text = str_replace('<p>[/html_r]</p>', '[/html_r]', $text);
    $text = preg_replace_callback('!\[html_r\](.*?)\[\/html_r\]!is', '_clean_html_r_do', $text );

    # преформатированный текст pre и code защитим от изменений
    $text = preg_replace_callback('!(<pre><code.*?>)(.*?)(</code></pre>)!is', '_clean_pre_do', $text );
    $text = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', '_clean_pre_do', $text );
    $text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', '_clean_pre_do', $text );
    
    /* $text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', '_clean_block2', $text ); */

    // в исходном html убираем переносы
    $text = str_replace("\n", "", $text);
    $text = $text . "\n";

    # если html-код в [html] код [/html]
    $text = str_replace('<p>[html]</p>', '[html]', $text);
    $text = str_replace('<p>[/html]</p>', '[/html]', $text);
    $text = preg_replace_callback('!\[html\](.*?)\[\/html\]!is', '_clean_html_do', $text );

    # исправляем стили браузера
    $text = str_replace('<hr style="width: 100%; height: 2px;">', "<hr>", $text);

    # всё приводим к MSO_N - признак переноса
    $text = str_replace('<br />', '<br>', $text);
    $text = str_replace('<br/>', '<br>', $text);
    $text = str_replace('<br>', 'MSO_N', $text);
    $text = str_replace("\n", 'MSO_N', $text); // все абзацы тоже <br>

    # удаляем двойные br
    $text = str_replace('MSO_NMSO_NMSO_NMSO_N', 'MSO_N', $text);
    $text = str_replace('MSO_NMSO_NMSO_N', 'MSO_N', $text);
    $text = str_replace('MSO_NMSO_N', 'MSO_N', $text);

    # все MSO_N это абзацы
    $text = str_replace('MSO_N', "\n", $text);

    # удалим перед всеми закрывающими тэгами абзац
    $text = str_replace("\n</", "</", $text);

    //_pr($text, true);

    # расставим все абзацы по p
    $text = preg_replace('!(.*)\n!', "\n<p>$1</p>", $text);

    # исправим абзацы ошибочные
    $text = str_replace("<p></p>", "", $text);
    $text = str_replace("<p><p>", "<p>", $text);
    $text = str_replace("</p></p>", "</p>", $text);
    $text = str_replace("</script></p>", "</script>", $text);
    $text = str_replace("<p>    <div", "<div", $text);
    $text = str_replace("<p> </div>", "</div>", $text);

    // замена для шорткодов [shortcode][/shortcode]
    $text = str_replace("<p>[", "[", $text);
    $text = str_replace("]</p>", "]", $text);

    # блочные тэги
    $allblocks = '(?:table|thead|tfoot|caption|colgroup|center|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|select|form|map|area|blockquote|address|math|style|input|embed|h1|h2|h3|h4|h5|h6|hr|p|hgroup|section|header|footer|article|aside|nav|main)';

    # здесь не нужно ставить <p> и </p>
    $text = preg_replace('!<p>(<' . $allblocks . '[^>]*>)</p>!', "\n$1", $text); # <p><tag></p>
    $text = preg_replace('!<p>(<' . $allblocks . '[^>]*>)!', "\n$1", $text); # <p><tag>
    $text = preg_replace('!<p>(</' . $allblocks . '[^>]*>)!', "\n$1", $text); # <p></tag>
    $text = preg_replace('!(<' . $allblocks . '[^>]*>)</p>!', "\n$1", $text); # <tag></p>
    $text = preg_replace('!(</' . $allblocks . '>)</p>!', "$1", $text); # </tag></p>
    $text = preg_replace('!(</' . $allblocks . '>) </p>!', "$1", $text); # </tag></p>

    $text = preg_replace('!<p>&nbsp;&nbsp;(<' . $allblocks . '[^>]*>)!', "\n$1", $text); # <p>&nbsp;&nbsp;<tag>
    $text = preg_replace('!<p>&nbsp;(<' . $allblocks . '[^>]*>)!', "\n$1", $text); # <p>&nbsp;<tag>

    # если был cut, то уберем с ссылки абзац
    $text = str_replace('<p><a id="cut"></a></p>', '<a id="cut"></a>', $text);

    # специфичные ошибки
    $text = str_replace("<blockquote>\n<p>", "<blockquote>", $text);
    $text = preg_replace('!<li>(.*)</p>\n!', "<li>$1</li>\n", $text); # <li>...</p>
    $text = str_replace("<ul>\n\n<li>", "<ul><li>", $text);
    $text = str_replace("</li>\n\n<li>", "</li>\n<li>", $text);

    $text = preg_replace('!<p><a id="(.*)"></a></p>\n!', "<a id=\"$1\"></a>\n", $text); # <li>...</p>

    # подчистим некоторые блочные тэги удалим <p> внутри. MSO_N_BLOCK = </p>
    $text = str_replace('MSO_N_BLOCK', "<br>", $text); // заменим перенос в блочном на <br>

    # blockquote
    $text = preg_replace_callback('!(<blockquote.*?>)(.*?)(</blockquote>)!is', '_clean_block', $text );
    $text = str_replace('MSO_N_BLOCK', "<br>", $text); // заменим перенос в блочном на ''

    # еще раз подчистка
    $text = str_replace('MSO_N', "\n", $text);

    $text = preg_replace('!<p><br(.*)></p>!', "<br$1>", $text);
    $text = preg_replace('!<p><br></p>!', "<br>", $text);

    # завершим [html]
    $text = str_replace('<p>[html_base64]', '[html_base64]', $text);
    $text = str_replace('[/html_base64]</p>', '[/html_base64]', $text);
    $text = str_replace('[/html_base64] </p>', '[/html_base64]', $text);

    $text = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', '_clean_html_posle', $text );

    # [br]
    $text = str_replace('[br]', '<br style="clear:both">', $text);
    $text = str_replace('[br none]', '<br>', $text);
    $text = str_replace('[br left]', '<br style="clear:left">', $text);
    $text = str_replace('[br right]', '<br style="clear:right">', $text);

    $text = str_replace('<p><br></p>', '<br>', $text);
    $text = preg_replace('!<p><br(.*)></p>!', "<br$1>", $text);

    # принудительный пробел
    $text = str_replace('[nbsp]', '&nbsp;', $text);

    # перенос строки в конце текста
    $text = $text . "\n";

    // _pr($text, true); # контроль

    return $text;
}

# аналогично mso_clean_html_do, только без замен — [html_r] ... [/html_r]
function _clean_html_r_do($matches)
{
    return '[html_base64]' . base64_encode($matches[1]) . '[/html_base64]';
}

# предподготовка html в тексте между [html] ... [/html]
# конвертируем все символы в реальный html
# после этого кодируем его в одну строчку base64
# после всех операций в mso_balance_tags декодируем его в обычный текст mso_clean_html_posle
# кодирование нужно для того, чтобы корректно пропустить весь остальной текст
function _clean_html_do($matches)
{
    $arr1 = array('&amp;', '&lt;', '&gt;', '<br />', '<br>', '&nbsp;');
    $arr2 = array('&',     '<',    '>',    "\n",     "\n",   ' ');
    $m = trim( str_replace($arr1, $arr2, $matches[1]) );
    $m = '[html_base64]' . base64_encode($m) . '[/html_base64]';
    return $m;
}


# pre, которое загоняется в [html_base64]
function _clean_pre_do($matches)
{
    return '[html_base64]' . base64_encode($matches[1] . $matches[2] . $matches[3]) . '[/html_base64]';
    
    /*
    $text = $matches[2];

    $text = str_replace('<p>', '', $text);
    $text = str_replace('</p>', '', $text);
    $text = str_replace('[', '&#91;', $text);
    $text = str_replace(']', '&#93;', $text);
    $text = str_replace("<br>", "MSO_N", $text);
    $text = str_replace("<br />", "<br>", $text);
    $text = str_replace("<br/>", "<br>", $text);
    $text = str_replace("<br>", "MSO_N", $text);

    $text = str_replace('<', '&lt;', $text);
    $text = str_replace('>', '&gt;', $text);
    $text = str_replace('&lt;pre', '<pre', $text);
    $text = str_replace('&lt;/pre', '</pre', $text);
    $text = str_replace('pre&gt;', 'pre>', $text);
    
    return $text;
    */
}

# аналогично, только еще и [] меняем
# function _clean_block2($matches)
# {
#
#   if ( is_array($matches) )
#       $text = "" . $matches[1] . $matches[2] . $matches[3] . "\n";
#   else
#       $text = $matches;
#
#   /*
#   $text = str_replace('<p>', '', $text);
#   $text = str_replace('</p>', 'MSO_N_BLOCK', $text);
#   $text = str_replace('[', '&#91;', $text);
#   $text = str_replace(']', '&#93;', $text);
#   $text = str_replace("<br />", "<br>", $text);
#   $text = str_replace("<br/>", "<br>", $text);
#   $text = str_replace("<br>", "MSO_N", $text);
#   */
#
#   return $text;
# }

# подчистка блоковых тэгов
# удаляем в них <p>
function _clean_block($matches)
{
    if ( is_array($matches) )
        $text = "" . $matches[1] . $matches[2] . $matches[3] . "\n";
    else
        $text = $matches;

    $text = str_replace('<p>', '', $text);
    $text = str_replace('</p>', 'MSO_N_BLOCK', $text);
    $text = str_replace("<br>", "MSO_N", $text);
    $text = str_replace("<br />", "<br>", $text);
    $text = str_replace("<br/>", "<br>", $text);
    $text = str_replace("<br>", "MSO_N", $text);
    //$text = str_replace("\n", "MSO_N", $text);

    return $text;
}

# декодирование
function _clean_html_posle($matches)
{
    return base64_decode($matches[1]);
}