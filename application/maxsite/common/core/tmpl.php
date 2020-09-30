<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// HTML-шаблонизатор
// вход - текст : выполняет замены, отдает php-код
// если $replace = true то в коде удаляются табуляторы и двойные \n
// полученный код выполнять через eval();
function mso_tmpl_prepare($template, $replace = true)
{
	$template = '?>' . str_replace(['{{', '}}', '{%', '%}'], ['<?=', '?>', '<?php', '?>'], $template);

	if ($replace)
		$template = str_replace(["\t", "\r", "\n\n"], ["", "", "\n"], $template);

	return $template;
}

// HTML-шаблонизатор
// if (file_exists($fn)) eval(mso_tmpl($fn));
function mso_tmpl($fn, $replace = true)
{
	$template = file_get_contents($fn);

	return mso_tmpl_prepare($template, $replace);
}

// HTML-шаблонизатор
// файл относительно шаблона/shared-каталога как в mso_find_ts_file()
// 	eval(mso_tmpl_ts('article-tmpl.php'));
function mso_tmpl_ts($fn, $replace = true)
{
	if ($fn = mso_find_ts_file($fn))
		return mso_tmpl($fn, $replace);
	else
		return '?>';
}

/*
* Функция подключает файл как текст и пропускает его через html-парсер и php-шаблонизатор
* Результат выводится в браузер
* В тексте могут быть замены: [siteurl] [templateurl]
* $fn — файл указывается относительно каталога шаблона
* $parser — имя функции парсера
* $tmpl == true — использовать php-шаблонизатор
* $echo == true — сразу вывод в браузер
*/
function mso_parse_file($file, $parser = 'autotag_simple', $tmpl = false, $echo = true)
{
	if ($fn = mso_fe($file)) {
		ob_start();
		require $fn;
		$t1 = ob_get_contents();
		ob_end_clean();

		// в файле могут быть свои замены
		$t1 = str_replace('[siteurl]', getinfo('siteurl'), $t1);
		$t1 = str_replace('[site_url]', getinfo('siteurl'), $t1);
		$t1 = str_replace('[templateurl]', getinfo('template_url'), $t1);
		$t1 = str_replace('[template_url]', getinfo('template_url'), $t1);

		// парсер текста
		if ($parser and function_exists($parser)) $t1 = $parser($t1);
		
		if ($echo) {
			// php-шаблонизатор
			if ($tmpl)
				eval(mso_tmpl_prepare($t1, false));
			else
				echo $t1;
		} else {
			ob_start();

			if ($tmpl)
				eval(mso_tmpl_prepare($t1, false));
			else
				echo $t1;

			$t2 = ob_get_contents();
			ob_end_clean();

			return $t2;
		}
	}

	return '';
}


# end of file
