<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// хук на обработку шорткодов в тексте
function mso_shortcode_content($content = '')
{
	global $MSO;

	foreach ($MSO->shortcode as $name => $func) {
		if (function_exists($func)) {
			// [toc][/toc] и [toc]text[/toc]
			$pattern = '~\[' . $name . '()\](.*?)\[\/' . $name . '\]~si';

			if (preg_match($pattern, $content) === 1) {
				$content = preg_replace_callback($pattern, $func, $content);
			}

			// [toc param][/toc] и [toc=param]text[/toc]
			$pattern = '~\[' . $name . '[= ]+(.*?)\](.*?)\[\/' . $name . '\]~si';

			if (preg_match($pattern, $content) === 1) {
				$content = preg_replace_callback($pattern, $func, $content);
			}

			/* // старый вариант
			if (strpos($content, '[' . $name) !== false) // есть вхождения
			{
				$content = preg_replace_callback('~\[' . $name . '[= ]?(.*?)\](.*?)\[\/' . $name . '\]~si', $func, $content);
			}
			*/
		}
	}

	return $content;
}

// добавляет для шорткода функцию
// [class t-red bold]text page[/class]
// или [class=t-red bold]text page[/class]
// mso_shortcode_add('class', 'my_class');
// функция my_class определяется на уровне шаблона или плагина - в ней вся обработка
function mso_shortcode_add($shortcode, $function)
{
	global $MSO;

	// проверка — защита от "дурака"
	if ($shortcode and $function) $MSO->shortcode[$shortcode] = $function;
}

// функция, если нужно расспарсить шорткод вида:
// [shortcode par=val par1=val1 par2="val val val"]text page[/shortcode]
// Array:
// 		[par] => val
// 		[par1] => val1
// 		[par2] => val val val
// 		[content] => text page
function mso_shortcode_parse($attr, $def = [], $sep = ' ')
{
	$par = [];

	$par['content'] = $attr[2];

	// замена атрибутов в кавычках
	$attr[1] = preg_replace_callback('!\=\"(.*?)\"!', function ($matches) {
		return '=' . str_replace(' ', '__NBSP__', $matches[1]);
	}, $attr[1]);

	if ($sep)
		$s = explode($sep, trim($attr[1]));
	else
		$s = array(trim($attr[1]));

	$s = array_map('trim', $s);

	foreach ($s as $p) {
		$p = str_replace('__NBSP__', ' ', $p);
		$p1 = explode('=', $p);

		if (count($p1) == 2)
			$par[$p1[0]] = $p1[1];
		else
			$par[] = $p;
	}

	$par = array_merge($def, $par);

	return $par;
}

// вывод произвольного шорткода
// $shortcode — имя шорткода
// $func — функция обработчик шорткода
// $content — текст содержащий сам шорткод
function mso_shortcode($shortcode, $func, $content)
{
	if (function_exists($func)) {
		// [toc][/toc] и [toc]text[/toc]
		$pattern = '~\[' . $shortcode . '()\](.*?)\[\/' . $shortcode . '\]~si';

		if (preg_match($pattern, $content) === 1)
			$content = preg_replace_callback($pattern, $func, $content);

		// [toc param][/toc] и [toc=param]text[/toc]
		$pattern = '~\[' . $shortcode . '[= ]+(.*?)\](.*?)\[\/' . $shortcode . '\]~si';

		if (preg_match($pattern, $content) === 1)
			$content = preg_replace_callback($pattern, $func, $content);

		/*
		if (strpos($content, '[' . $shortcode) !== false) // есть вхождения
		{
			$content = preg_replace_callback('~\[' . $shortcode . '[= ]?(.*?)\](.*?)\[\/' . $shortcode . '\]~si', $func, $content);
		}
		*/
	}

	return $content;
}

# end of file
