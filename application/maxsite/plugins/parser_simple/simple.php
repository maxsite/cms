<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
    (с) Albireo Framework
    (c) MAX — https://maxsite.org/albireo
    (c) MaxSite CMS — https://max-3000.com/
    
    См. https://max-3000.com/book/simple
    
    Версия: 2023-02-25
    
    Возможности
    -----------
    
    _ абзац P
    
    __ блок DIV в одной строке
    
    Тэги в одну строку:
    h1|h2|h3|h4|h5|h6|li|dt|dd|bqq
    
    h1 заголовок H1
    h2 заголовок H2
    h3 заголовок H3
    h4 заголовок H4
    h5 заголовок H5
    h6 заголовок H6
    
    или
    
    # Заголовок h1
    ## Заголовок h2
    ### Заголовок h3
    #### Заголовок h4
    ##### Заголовок h5
    ###### Заголовок h6
    
    bqq цитата blockquote в одной строке
    
    Тэги с обязательным закрывающим тэгом:	
    div|section|article|main|footer|hgroup|header|aside|nav|form|fieldset|label|select|
    pre|blockquote|ol|ul|bq|table|tr|td|th|caption|tbody|thead|tfoot|dl|pcode
    
    div
        текст
    /div
    
    Списки
    ul
        * привет
        * привет
        * привет
    /ul
    
    list
        * привет
        * привет
        * привет
    /list
    
    ol
        * привет
        * привет
        * привет
    /ol
    
    ul
        li привет
        * привет
        li привет
        * привет
    /ul

    hr
    
    bq 
        цитата blockquote
    /bq
    
    Строчные тэги внутри текста (впереди должен быть пробел): 
    
    __ italic __       <i>
    _ em _             <em>
    ** bold **         <b>
    * strong *         <strong>
    @ code @           <code>
    @@ mark @@         <mark>
    ~~ underline ~~    <u>

    Можно задать css-класс, например:
    _(класс)
    h1(класс)
    #(класс)
    ul(класс)
    div(класс)
    div(класс1 класс2)
    
    После класса можно задать style во вторых скобках
    _(класс)(стили)
    h1(класс)(стили)
    div(класс)(стили)
    section(класс)(стили)
    
    Несколько блочных тэгов можно задавать в одной строке через ||
        
    div(layout-center-wrap) || div(layout-wrap)
        текст
    /div || /div
    
    Отключение Simple в блоке
    <!-- nosimple --> текст без обработки <!-- /nosimple -->
    
    Если требуется автоматом расставить тэги <P> для каждой новой строки.

    Пример 1:
    [psimple]
    Hello 1
    Hello 2
    Hello 3
    [/psimple]
    
    Результат:
    <p>Hello 1</p>
    <p>Hello 2</p>
    <p>Hello 3</p>
    
    Пример 2:
    [psimple]
    Hello 1
    Hello 2
    <hr>
    __ Hello 3
    _ Hello 4
    Hello 5
    [/psimple]
    
    Результат:
    <p>Hello 1</p>
    <p>Hello 2</p>
    <hr>
    <div>Hello 3</div>
    <p>Hello 4</p>
    <p>Hello 5</p>

    Если нужно автоматически расставить абзацы во всём тексте, то в него в произвольном месте нужно 
    добавить <!-- paragraphs -->
    
    Для того, чтобы исключить часть кода из любой обработки, можно 
    использовать блок <keep>...</keep> вместо <!-- nosimple -->

    <keep>
        <p>любой текст, _который_ *будет* выведен ~~без обработки~~</p>
    </keep>

    Для <pre><code>...</code></pre> можно использовать сокращение

    pcode(language-php)
        код
    pcode
    
    Результат:
    <pre><code class="language-php"> код </code></pre>
    
    По умолчанию содержимое PRE исключается из парсинга, если его нужно обработать,
    в тексте в произвольном месте нужно указать <!-- -pre -->

*/

function autotag_simple($text)
{
	$text = str_replace("\r", "", $text); // win-dos

    $text = "\n" . $text . "\n";

    // <!-- nosimple --> текст без обработки <!-- /nosimple -->
    $text = preg_replace_callback('!(<\!-- nosimple -->)(.*?)(<\!-- \/nosimple -->)!is', function ($m) {
        return '<simple_base64>' . base64_encode($m[2]) . '</simple_base64>';
    }, $text);
    
    $text = preg_replace_callback('!(<keep>\n)(.*?)(\n<\/keep>)!is', function ($m) {
        return '<simple_base64>' . base64_encode($m[2]) . '</simple_base64>';
    }, $text);
    
    // открывающие и отдельно закрывающие тэги  div ...  /div
    $tags2 = '(div|section|article|main|footer|hgroup|header|aside|nav|form|fieldset|label|select|pre|blockquote|bq|ol|ul|table|tr|td|th|caption|tbody|thead|tfoot|dl|pcode|list)';

    # /div
    $text = preg_replace('!\|\|\s*\/' . $tags2 . '\s*(\n|\|\|)!m', "\n</$1>\n", $text);
    $text = preg_replace('!(\s*)\/' . $tags2 . '(\s*)(\n|\|\|)!m', "$1</$2>$3\n", $text);

    # div(t-red)(font-weight: bold)
    $text = preg_replace('!\|\|\s*' . $tags2 . '\((.*?)\)\((.*?)\)\s*(\n|\|\|)!m', "\n<$1 class=\"$2\" style=\"$3\">\n", $text);
    $text = preg_replace('!(\s*)' . $tags2 . '\((.*?)\)\((.*?)\)\s*(\n|\|\|)!m', "$1<$2 class=\"$3\" style=\"$4\">\n", $text);

    # div(t-red)
    $text = preg_replace('!\|\|\s*' . $tags2 . '\((.*?)\)\s*(\n|\|\|)!m', "\n<$1 class=\"$2\">\n", $text);
    $text = preg_replace('!(\s*)' . $tags2 . '\((.*?)\)\s*(\n|\|\|)!m', "$1<$2 class=\"$3\">\n", $text);

    # div
    $text = preg_replace('!\|\|\s*' . $tags2 . '\s*(\n|\|\|)!m', "\n<$1>\n", $text);
    $text = preg_replace('!(^\s*)' . $tags2 . '\s*(\n|\|\|)!m', "$1<$2>\n", $text);
    
    # замена несуществующего тэга bqq на blockquote
    $text = str_replace(['<bqq', '<bq'], '<blockquote', $text);
    $text = str_replace(['</bqq>', '</bq>'], '</blockquote>', $text);
    
    # замена несуществующего тэга list на ul
    $text = str_replace('<list', '<ul', $text);
    $text = str_replace('</list>', '</ul>', $text);
    
    # pcode
    $text = str_replace('<pcode>', '<pre><code>', $text);
    $text = str_replace('</pcode>', '</code></pre>', $text);
    $text = str_replace('<pcode ', '<pre><code ', $text);

    # убрать \n вокруг <pre><code> — поскольку code делает лишний перенос - а это строковый элемент
    $text = preg_replace('!<pre><code (.*?)>\n!m', "<pre><code $1>", $text);
    $text = str_replace("<pre><code>\n", '<pre><code>', $text);
    $text = str_replace("\n</code></pre>", '</code></pre>', $text);
    
    # по умолчанию защищаем <pre> от парсинга
    # если указан <!-- -pre --> то отключаем такую защиту
    if (strpos($text, '<!-- -pre -->') === false) {
        $text = preg_replace_callback('!<pre(.*?)</pre>!is', function ($m) {
            return '<simple_base64>' . base64_encode('<pre' . $m[1] . '</pre>') . '</simple_base64>';
        }, $text);
    }

    # _ P
    $text = preg_replace('!(^\s*)_\s(.*?)\n!m', "$1<p>$2</p>\n", $text);
    $text = preg_replace('!(^\s*)_\s(.*?)\n!m', "$1<p>$2</p>\n", $text);
    $text = preg_replace('!(^\s*)_\((.*?)\)\((.*?)\)\s(.*?)\n!m', "$1<p class=\"$2\" style=\"$3\">$4</p>\n", $text);
    $text = preg_replace('!(^\s*)_\((.*?)\)\s(.*?)\n!m', "$1<p class=\"$2\">$3</p>\n", $text);

    # __ DIV в одной строке
    $text = preg_replace('!(^\s*)__\s(.*?)\n!m', "$1<div>$2</div>\n", $text);
    $text = preg_replace('!(^\s*)__\s(.*?)\n!m', "$1<div>$2</div>\n", $text);
    $text = preg_replace('!(^\s*)__\((.*?)\)\((.*?)\)\s(.*?)\n!m', "$1<div class=\"$2\" style=\"$3\">$4</div>\n", $text);
    // $text = preg_replace('!^\s*__\((.*?)\)\s(.*?)\n!m', "\n\n<div class=\"$1\">$2</div>\n", $text);
    $text = preg_replace('!(^\s*)__\((.*?)\)\s(.*?)\n!m', "$1<div class=\"$2\">$3</div>\n", $text);

    # __  I __      _ EM _
    $text = preg_replace('! __(.*?)__!', " <i>$1</i>", $text);
    $text = preg_replace('! _(.*?)_!', " <em>$1</em>", $text);

    # ** B **       * STRONG *
    $text = preg_replace('! \*\*(.*?)\*\*!', " <b>$1</b>", $text);
    $text = preg_replace('! \*(.*?)\*!', " <strong>$1</strong>", $text);

    # @@ MARK @@
    $text = preg_replace('! @@(.*?)@@!', " <mark>$1</mark>", $text);

    # @ CODE @
    $text = preg_replace('! \@(.*?)\@!', " <code>$1</code>", $text);

    # ~~ U ~~
    $text = preg_replace('! \~~(.*?)\~~!', " <u>$1</u>", $text);

    # * LI
    $text = preg_replace('!(^\s*)\*\s(.*?)\n!m', "$1<li>$2</li>\n", $text);

    # hr
    $text = preg_replace('!(^\s*)hr\((.*?)\)\((.*?)\)(\s*)\n!m', "$1<hr class=\"$2\" style=\"$3\">$4\n", $text);
    $text = preg_replace('!(^\s*)hr\((.*?)\)(\s*)\n!m', "$1<hr class=\"$2\">$3\n", $text);
    $text = preg_replace('!(^\s*)hr(\s*)\n!m', "$1<hr>$2\n", $text);

    # Заголовки H1..6
    $text = preg_replace('!(^\s*)######!m', "$1h6", $text);
    $text = preg_replace('!(^\s*)#####!m', "$1h5", $text);
    $text = preg_replace('!(^\s*)####!m', "$1h4", $text);
    $text = preg_replace('!(^\s*)###!m', "$1h3", $text);
    $text = preg_replace('!(^\s*)##!m', "$1h2", $text);
    $text = preg_replace('!(^\s*)#!m', "$1h1", $text);
    
    // тэги одной строкой
    $tags1 = '(h1|h2|h3|h4|h5|h6|dt|dd|li|bqq)';

    # h1(bold)(color: red) Заголовок
    $text = preg_replace('!(^\s*)' . $tags1 . '\((.*?)\)\((.*?)\)\s+(.*?)\n!m', "$1<$2 class=\"$3\" style=\"$4\">$5</$2>\n", $text);

    # h1(bold) Заголовок
    $text = preg_replace('!(^\s*)' . $tags1 . '\((.*?)\)\s+(.*?)\n!m', "$1<$2 class=\"$3\">$4</$2>\n", $text);

    # h1 Заголовок
    $text = preg_replace('!(^\s*)' . $tags1 . '\s+(.*?)\n!m', "$1<$2>$3</$2>\n", $text);

    # [br]
    $text = str_replace('[br]', '<br style="clear:both">', $text);
    $text = str_replace('[br none]', '<br>', $text);
    $text = str_replace('[br left]', '<br style="clear:left">', $text);
    $text = str_replace('[br right]', '<br style="clear:right">', $text);
    
    #  [psimple] ... [/psimple] — авторасстановка <p> для всех \n внутри блока 
    $text = preg_replace_callback('!(\[psimple\])(.*?)(\[/psimple\])!is', function ($m) {
        return preg_replace('~(^\s*)(?!\s*<)(.+)\n~m', "<p>$2</p>\n", $m[2]);
    }, $text);
   
    # <!-- paragraphs --> расставить абзацы для всего текста
    if (strpos($text, '<!-- paragraphs -->')) {
        $text = str_replace('<!-- paragraphs -->', '', $text);
        $text = preg_replace('~(^\s*)(?!\s*<)(.+)\n~m', "<p>$2</p>\n", $text);
    }

    $text = preg_replace_callback('!\<simple_base64\>(.*?)\<\/simple_base64\>!is', function ($m) {
        return base64_decode($m[1]);
    }, $text);

    return trim($text);
}

# end of file
