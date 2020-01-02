<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(с) Landing Page Framework (LPF)
	(c) MAX — http://lpf.maxsite.com.ua/

	См. https://max-3000.com/book/simple
	
	Версия: 2020-01-02
	
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
	bqq цитата blockquote в одной строке
    
	Тэги с обязательным закрывающим тэгом:	
	div|section|article|main|footer|hgroup|header|aside|nav|form|fieldset|label|select|
	pre|blockquote|ol|ul|bq|table|tr|td|th|caption|tbody|thead|tfoot|dl
	
	div
		текст
	/div
	
	Списки
	ul
		* привет
		* привет
		* привет
	/ul

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
    
	
	Строчные тэги (впереди должен быть пробел): 
	
	__italic__
	_em_
	
	**bold**
	*strong*
	
	@code@

	Можно задать css-класс, например:
	_(класс)
	h1(класс)
	ul(класс)
	div(класс)
	div(класс1 класс2)
	
	После класса можно задать style
	_(класс)(стили)
	h1(класс)(стили)
	div(класс)(стили)
	section(класс)(стили)
	
	Несколько блочных тэгов можно задавать в одной строке через ||
		
	div(layout-center-wrap) || div(layout-wrap)
		текст
	/div || /div
	

*/


function parser_simple_autoload()
{
	mso_hook_add('parser_register', 'parser_simple_register');  // парсер Simple
}

function parser_simple_register($parsers = array())
{
	$parsers['Simple'] = array( // имя парсера
		'content' => 'parser_simple_content', // функция обработчик на хуке content
		'content_post_edit' => 'parser_simple_post_edit', // функция перед отправкой в БД
	);
	
	return $parsers;
}


function parser_simple_content($text = '')
{
	$text = autotag_simple($text);
	
	return $text;
}


function parser_simple_post_edit($text = '')
{
	// глюк FireFox исправлем замену абсолютного пути src на абсолютный
	$text = str_replace('src="../../', 'src="' . getinfo('site_url'), $text);
	$text = str_replace('src="../', 'src="' . getinfo('site_url'), $text);

	return $text;
}

function autotag_simple($text)
{
	$text = str_replace(array("\r\n", "\r"), "\n", $text); // win-dos
	
	$text = "\n" . $text . "\n";
	
	// <!-- nosimple --> текст без обработки <!-- /nosimple -->
	// $text = preg_replace_callback('!(<\!-- nosimple -->)(.*?)(<\!-- \/nosimple -->)!is', 'autotag_simple_no', $text);
	
	
	# _ P
	$text = preg_replace('!^\s*_\s(.*?)\n!m', "\n\n<p>$1</p>\n", $text);
	$text = preg_replace('!^\s*_\s(.*?)\n!m', "\n\n<p>$1</p>\n", $text);
	$text = preg_replace('!^\s*_\((.*?)\)\((.*?)\)\s(.*?)\n!m', "\n\n<p class=\"$1\" style=\"$2\">$3</p>\n", $text);
	$text = preg_replace('!^\s*_\((.*?)\)\s(.*?)\n!m', "\n\n<p class=\"$1\">$2</p>\n", $text);
	
	# __ DIV в одной строке
	$text = preg_replace('!^\s*__\s(.*?)\n!m', "\n\n<div>$1</div>\n", $text);
	$text = preg_replace('!^\s*__\s(.*?)\n!m', "\n\n<div>$1</div>\n", $text);
	$text = preg_replace('!^\s*__\((.*?)\)\((.*?)\)\s(.*?)\n!m', "\n\n<div class=\"$1\" style=\"$2\">$3</div>\n", $text);
	$text = preg_replace('!^\s*__\((.*?)\)\s(.*?)\n!m', "\n\n<div class=\"$1\">$2</div>\n", $text);
	
	# __  I __      _ EM _
	$text = preg_replace('! __(.*?)__!', " <i>$1</i>", $text);
	$text = preg_replace('! _(.*?)_!', " <em>$1</em>", $text);
	
	# ** B **       * STRONG *
	$text = preg_replace('! \*\*(.*?)\*\*!', " <b>$1</b>", $text);
	$text = preg_replace('! \*(.*?)\*!', " <strong>$1</strong>", $text);
	
	# @ CODE @
	$text = preg_replace('! \@(.*?)\@!', " <code>$1</code>", $text);
	
	# * LI
	$text = preg_replace('!^\s*\*\s(.*?)\n!m', "\n<li>$1</li>\n", $text);
	
	# hr
	$text = preg_replace('!^\s*[^<]hr\s*\n!m', "\n<hr>\n", $text);
	$text = preg_replace('!^\s*[^<]hr\((.*?)\)\s*\n!m', "\n<hr class=\"$1\">\n", $text);
	$text = preg_replace('!^\s*[^<]hr\((.*?)\)\((.*?)\)\s*\n!m', "\n<hr class=\"$1\" style=\"$2\">\n", $text);
	
	// тэги одной строкой
	$tags1 = '(h1|h2|h3|h4|h5|h6|dt|dd|li|bqq)';
	
	# h1(bold)(color: red) Заголовок
	$text = preg_replace('!^\s*' . $tags1 . '\((.*?)\)\((.*?)\)\s+(.*?)\n!m', "\n<$1 class=\"$2\" style=\"$3\">$4</$1>\n", $text);
	
	# h1(bold) Заголовок
	$text = preg_replace('!^\s*' . $tags1 . '\((.*?)\)\s+(.*?)\n!m', "\n<$1 class=\"$2\">$3</$1>\n", $text);
	
	# h1 Заголовок
	$text = preg_replace('!^\s*' . $tags1 . '\s+(.*?)\n!m', "\n<$1>$2</$1>\n", $text);
		
	
	// открывающие и отдельно закрывающие тэги  div ...  /div
	$tags2 = '(div|section|article|main|footer|hgroup|header|aside|nav|form|fieldset|label|select|pre|blockquote|bq|ol|ul|table|tr|td|th|caption|tbody|thead|tfoot|dl)';

	# /div
	$text = preg_replace('!\|\|\s*\/' . $tags2 . '\s*(\n|\|\|)!m', "\n</$1>\n", $text);
	$text = preg_replace('!\s*\/' . $tags2 . '\s*(\n|\|\|)!m', "\n</$1>\n", $text);
	
	# div(t-red)(font-weight: bold)
	$text = preg_replace('!\|\|\s*' . $tags2 . '\((.*?)\)\((.*?)\)\s*(\n|\|\|)!m', "\n<$1 class=\"$2\" style=\"$3\">\n", $text);
	$text = preg_replace('!\s*' . $tags2 . '\((.*?)\)\((.*?)\)\s*(\n|\|\|)!m', "\n<$1 class=\"$2\" style=\"$3\">\n", $text);
	
	# div(t-red)
	$text = preg_replace('!\|\|\s*' . $tags2 . '\((.*?)\)\s*(\n|\|\|)!m', "\n<$1 class=\"$2\">\n", $text);	
	$text = preg_replace('!\s*' . $tags2 . '\((.*?)\)\s*(\n|\|\|)!m', "\n<$1 class=\"$2\">\n", $text);	
		
	# div
	$text = preg_replace('!\|\|\s*' . $tags2 . '\s*(\n|\|\|)!m', "\n<$1>\n", $text);
	$text = preg_replace('!^\s*' . $tags2 . '\s*(\n|\|\|)!m', "\n<$1>\n", $text);
	
	# замена несуществующего тэга
	$text = str_replace(['<bqq', '<bq'], '<blockquote', $text);
	$text = str_replace(['</bqq>', '</bq>'], '</blockquote>', $text);
	
	
	# [br]
	$text = str_replace('[br]', '<br style="clear:both">', $text);
	$text = str_replace('[br none]', '<br>', $text);
	$text = str_replace('[br left]', '<br style="clear:left">', $text);
	$text = str_replace('[br right]', '<br style="clear:right">', $text);
	
	return trim($text);
}

# end of file