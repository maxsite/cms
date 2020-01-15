<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */
 
// профилирование - старт
// первый параметр метка
function _mso_profiler_start($point = 'first', $echo = false)
{
	global $_points;

	$CI = &get_instance();

	$CI->benchmark->mark($point . '_start'); // отмечаем время

	$mem0 = round(memory_get_usage() / 1024 / 1024, 2); // текущая память

	$_points[$point]['mem0'] = $mem0;

	if ($echo)
		pr('start ' . $point . ': ' . $_points[$point]['mem0'] . 'MB');
}

// профилирование конец
function _mso_profiler_end($point = 'first', $echo = true)
{
	global $_points;

	$CI = &get_instance();

	$CI->benchmark->mark($point . '_end');

	$_points[$point]['time'] = $CI->benchmark->elapsed_time($point . '_start', $point . '_end');

	$mem1 = round(memory_get_usage() / 1024 / 1024, 2); // текущая память

	$_points[$point]['mem1'] = $mem1;
	$_points[$point]['mem'] = round($mem1 - $_points[$point]['mem0'], 4); // разница

	if ($echo)
		pr(
			$point . ': '
				. $_points[$point]['mem'] . 'MB t: '
				. $_points[$point]['time'] . 's Total: '
				. $_points[$point]['mem1'] . 'MB'
		);
	else
		return $_points[$point];
}

/**
 *	Функция для записи лога в файл
 *
 *	mso_log('текст', 'заголовок', '_log.txt');
 *	Для сброса: mso_log() или mso_log(0, '', 'файл') 
 *
 *	Вместо текста можно передать любые данные — будет использован вывод через print_r()
 *	Файл указывается относительно корня сайта
 *	если $framing = true, то добавляется рамка, иначе все в одну строчку как есть и без $name
 *
 */
function mso_log($var = 0, $name = 'LOG', $f = '_log.txt', $framing = true)
{
	if ($var === 0) {
		file_put_contents(FCPATH . $f,  '');
		return;
	}

	if (!is_scalar($var)) $var = print_r($var, true);

	if ($framing)
		file_put_contents(FCPATH . $f, "\n================= " . $name . " =================\n" . $var . "\n================= /" . $name . " =================\n", FILE_APPEND);
	else
		file_put_contents(FCPATH . $f, $var, FILE_APPEND);
}

# end of file
