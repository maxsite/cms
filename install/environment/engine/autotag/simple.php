<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Простенький autotag

	Подключение в variables.php:
		$VAR['autotag_my'] = 'simple';

	Возможности:

	Блочные (перенос строки/enter перед):

	_ абзац P
	
	h1 заголовок H1
	h2 заголовок H2
	h3 заголовок H3
	h4 заголовок H4
	h5 заголовок H5
	h6 заголовок H6

	div
		текст
	/div
	
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
	
	hr
	
	bq 
		цитата blockquote
	/bq

	
	Строчные тэги (указать пробел перед): 
	
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
	
*/

function autotag_simple($text)
{
	$text = str_replace(array("\r\n", "\r"), "\n", $text); // win-dos
	
	$text = "\n" . $text . "\n";
	
	$text = preg_replace('!\n\s*_\s(.*?)\n!', "\n\n<p>$1</p>\n", $text);
	$text = preg_replace('!\n\s*_\s(.*?)\n!', "\n\n<p>$1</p>\n", $text);
	
	$text = preg_replace('!\n\s*_\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n\n<p class=\"$1\" style=\"$2\">$3</p>\n", $text);
	$text = preg_replace('!\n\s*_\((.*?)\)\s(.*?)\n!', "\n\n<p class=\"$1\">$2</p>\n", $text);
	
	$text = preg_replace('!\n\s*h1\s(.*?)\n!', "\n<h1>$1</h1>\n", $text);
	$text = preg_replace('!\n\s*h2\s(.*?)\n!', "\n<h2>$1</h2>\n", $text);
	$text = preg_replace('!\n\s*h3\s(.*?)\n!', "\n<h3>$1</h3>\n", $text);
	$text = preg_replace('!\n\s*h4\s(.*?)\n!', "\n<h4>$1</h4>\n", $text);
	$text = preg_replace('!\n\s*h5\s(.*?)\n!', "\n<h5>$1</h5>\n", $text);
	$text = preg_replace('!\n\s*h6\s(.*?)\n!', "\n<h6>$1</h6>\n", $text);
	
	$text = preg_replace('!\n\s*h1\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h1 class=\"$1\" style=\"$2\">$3</h1>\n", $text);
	$text = preg_replace('!\n\s*h2\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h2 class=\"$1\" style=\"$2\">$3</h2>\n", $text);
	$text = preg_replace('!\n\s*h3\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h3 class=\"$1\" style=\"$2\">$3</h3>\n", $text);
	$text = preg_replace('!\n\s*h4\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h4 class=\"$1\" style=\"$2\">$3</h4>\n", $text);
	$text = preg_replace('!\n\s*h5\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h5 class=\"$1\" style=\"$2\">$3</h5>\n", $text);
	$text = preg_replace('!\n\s*h6\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h6 class=\"$1\" style=\"$2\">$3</h6>\n", $text);
	
	$text = preg_replace('!\n\s*h1\((.*?)\)\s(.*?)\n!', "\n<h1 class=\"$1\">$2</h1>\n", $text);
	$text = preg_replace('!\n\s*h2\((.*?)\)\s(.*?)\n!', "\n<h2 class=\"$1\">$2</h2>\n", $text);
	$text = preg_replace('!\n\s*h3\((.*?)\)\s(.*?)\n!', "\n<h3 class=\"$1\">$2</h3>\n", $text);
	$text = preg_replace('!\n\s*h4\((.*?)\)\s(.*?)\n!', "\n<h4 class=\"$1\">$2</h4>\n", $text);
	$text = preg_replace('!\n\s*h5\((.*?)\)\s(.*?)\n!', "\n<h5 class=\"$1\">$2</h5>\n", $text);
	$text = preg_replace('!\n\s*h6\((.*?)\)\s(.*?)\n!', "\n<h6 class=\"$1\">$2</h6>\n", $text);
	
	$text = preg_replace('!\n\s*hr\s*\n!', "\n<hr>\n", $text);
	$text = preg_replace('!\n\s*hr\((.*?)\)\s*\n!', "\n<hr class=\"$1\">\n", $text);
	
	$text = preg_replace('!\n\s*div\n!', "\n<div>\n", $text);
	$text = preg_replace('!\n\s*\/div\n!', "\n</div>\n", $text);
	
	$text = preg_replace('!\n\s*div\((.*?)\)\((.*?)\)\n!', "\n<div class=\"$1\" style=\"$2\">\n", $text);
	$text = preg_replace('!\n\s*div\((.*?)\)\n!', "\n<div class=\"$1\">\n", $text);	
	
	$text = preg_replace('!\n\s*ul\n!', "\n<ul>\n", $text);
	$text = preg_replace('!\n\s*\/ul\n!', "\n</ul>\n", $text);
	$text = preg_replace('!\n\s*ul\((.*?)\)\n!', "\n<ul class=\"$1\">\n", $text);
	
	$text = preg_replace('!\n\s*ol\n!', "\n<ol>\n", $text);
	$text = preg_replace('!\n\s*\/ol\n!', "\n</ol>\n", $text);	
	$text = preg_replace('!\n\s*ol\((.*?)\)\n!', "\n<ol class=\"$1\">\n", $text);
	
	$text = preg_replace('!\n\s*\*\s(.*?)\n!', "\n<li>$1</li>\n", $text);
	$text = preg_replace('!\n\s*\*\s(.*?)\n!', "\n<li>$1</li>\n", $text);
	
	$text = preg_replace('!\n\s*bq\n!', "\n<blockquote>\n", $text);
	$text = preg_replace('!\n\s*\/bq\n!', "\n</blockquote>\n", $text);
	
	$text = preg_replace('!\n\s*bq\((.*?)\)\n!', "\n<blockquote class=\"$1\">\n", $text);
	
	$text = preg_replace('! __(.*?)__!', " <i>$1</i>", $text);
	$text = preg_replace('! _(.*?)_!', " <em>$1</em>", $text);
	
	$text = preg_replace('! \*\*(.*?)\*\*!', " <b>$1</b>", $text);
	$text = preg_replace('! \*(.*?)\*!', " <strong>$1</strong>", $text);
	
	$text = preg_replace('! \@(.*?)\@!', " <code>$1</code>", $text);

	
	return trim($text);
}

# end of file