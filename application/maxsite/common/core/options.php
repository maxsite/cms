<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// сброс кэша опций
function mso_refresh_options()
{
	global $MSO_CACHE_OPTIONS;

	$CI = &get_instance();

	/*
	$MSO_CACHE_OPTIONS =
		type = array (
				key  = value
				key1 = value2
				)
	*/
	$CI->db->cache_delete_all();
	$query = $CI->db->get('options');

	$MSO_CACHE_OPTIONS = [];

	foreach ($query->result() as $row) {
		$MSO_CACHE_OPTIONS[$row->options_type][$row->options_key] = $row->options_value;
	}

	mso_add_cache('options', $MSO_CACHE_OPTIONS);

	return $MSO_CACHE_OPTIONS;
}

// добавление в таблицу опций options
// при указании $refresh = false не происходит обновление опций из базы,
// использовать для вызове mso_add_option в цикле. После вызвать mso_refresh_options()
function mso_add_option($key, $value, $type = 'general', $refresh = true)
{
	$CI = &get_instance();

	// если value массив или объект, то серилизуем его в строку
	if (!is_scalar($value)) $value = '_serialize_' . serialize($value);

	$data = [
		'options_key' => $key,
		'options_type' => $type
	];

	// проверим есть ли уже такой ключ
	$CI->db->select('options_id');
	$CI->db->from('options');
	$CI->db->where($data);

	$query = $CI->db->get();

	if ($query->num_rows() > 0) {
		// есть уже такой ключ, поэтому обновляем его значение
		$CI->db->where($data);
		$data['options_value'] = $value;
		$CI->db->update('options', $data);
	} else {
		// новый ключ
		$data['options_value'] = $value;
		$CI->db->insert('options', $data);
	}

	if ($refresh) mso_refresh_options(); # обновляем опции из базы

	return true;
}

// удаление в таблице опций options ключа с типом
function mso_delete_option($key, $type = 'general')
{
	$CI = &get_instance();

	$CI->db->limit(1);
	$CI->db->delete('options', ['options_key' => $key, 'options_type' => $type]);

	mso_refresh_options(); # обновляем опции из базы

	return true;
}

// удаление в таблице опций options ключа-маски с типом
// маска считается от начала, например mask*
function mso_delete_option_mask($mask, $type = 'general')
{
	$CI = &get_instance();

	$mask = str_replace('_', '/_', $mask);
	$mask = str_replace('%', '/%', $mask);

	$CI->db->query('DELETE FROM ' . $CI->db->dbprefix('options') . ' WHERE options_type="' . $type . '" AND options_key LIKE "' . $mask . '%" ESCAPE "/"');

	mso_refresh_options(); # обновляем опции из базы

	return true;
}

/**
 * получение опции из кэша опций
 * 
 * если $type === '', то он равен getinfo('template')
 * массив $default_values используется, если нужно получить из него дефолтное значение
 * $default_values = [ type1 => [key], type2 => [key] ] см. mso_get_defoptions_from_ini()
 * 
 */
function mso_get_option($key, $type = 'general', $return_value = false, $default_values = [])
{
	global $MSO_CACHE_OPTIONS;

	if ($type === '') $type = getinfo('template');

	if (isset($MSO_CACHE_OPTIONS[$type][$key]))
		$result = $MSO_CACHE_OPTIONS[$type][$key];
	else {
		if ($default_values) {
			$result = $default_values[$type][$key] ?? $return_value;
		} else {
			$result = $return_value;
		}
	}

	// проверяем на сериализацию
	if (@preg_match('|_serialize_|A', (string) $result)) {
		$result = preg_replace('|_serialize_|A', '', $result, 1);
		$result = @unserialize($result);
	}

	return $result;
}

// добавление float-опции
// float-опция - это файл из серилизованного текста в каталоге uploads
// аналог опций, хранящейся в отдельном файле/каталоге _mso_float
function mso_add_float_option($key, $value, $type = 'general', $serialize = true, $ext = '', $md5_key = true, $dir = '')
{
	// $CI = & get_instance();

	if ($dir) $dir .= '/';

	$path = getinfo('uploads_dir') . '_mso_float/' . $dir;

	if (!is_dir($path)) @mkdir($path, 0777); // нет каталога, пробуем создать
	if (!is_dir($path) or !is_writable($path)) return false; // нет каталога или он не для записи

	if ($md5_key)
		$path .= mso_md5($key . $type) . $ext;
	else
		$path .= $key . $type . $ext;

	if (!$fp = @fopen($path, 'wb')) return false; // нет возможности сохранить файл

	if ($serialize)
		$output = serialize($value);
	else
		$output = $value;

	flock($fp, LOCK_EX);
	fwrite($fp, $output);
	flock($fp, LOCK_UN);
	fclose($fp);
	@chmod($path, 0777);

	// возвращаем имя файла
	if ($md5_key)
		$return = '_mso_float/' . $dir . mso_md5($key . $type) . $ext;
	else
		$return = '_mso_float/' . $dir . $key . $type . $ext;

	return $return;
}

// получение данных из flat-опций
function mso_get_float_option($key, $type = 'general', $return_value = false, $serialize = true, $ext = '', $md5_key = true, $dir = '')
{
	// $CI = & get_instance();

	if (!$key or !$type) return $return_value;

	if ($dir) $dir .= '/';

	if ($md5_key)
		$path = getinfo('uploads_dir') . '_mso_float/' . $dir . mso_md5($key . $type) . $ext;
	else
		$path = getinfo('uploads_dir') . '_mso_float/' . $dir . $key . $type . $ext;

	if (file_exists($path)) {
		if (!$fp = @fopen($path, 'rb')) return $return_value;

		flock($fp, LOCK_SH);

		$out = $return_value;

		if (filesize($path) > 0) {
			if ($serialize)
				$out = @unserialize(fread($fp, filesize($path)));
			else
				$out = fread($fp, filesize($path));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $out;
	} else {
		return $return_value;
	}
}

// удаление flat-опции если есть
function mso_delete_float_option($key, $type = 'general', $dir = '', $ext = '', $md5_key = true)
{
	if (!$key or !$type) return false;
	if ($dir) $dir .= '/';

	if ($md5_key)
		$path = getinfo('uploads_dir') . '_mso_float/' . $dir . mso_md5($key . $type) . $ext;
	else
		$path = getinfo('uploads_dir') . '_mso_float/' . $dir . $key . $type . $ext;

	if (file_exists($path)) {
		@unlink($path);
		return true;
	}

	return false;
}

// Функция использует глобальный одномерный массив
// который используется для получения значения указанного ключа $key
// Если в массиве ключ не определён, то используется значение $default
// если $array = true, то возвращаем значение ключа массива $key[$default] или $array_default
// см. примеры к mso_set_val()
function mso_get_val($key = '', $default = '', $array = false, $array_default = false)
{
	global $MSO;

	// нет такого массива, создаём
	if (!isset($MSO->key_options)) {
		$MSO->key_options = [];

		return $default;
	}

	if ($array !== false) {
		if (isset($MSO->key_options[$key][$default]))
			return $MSO->key_options[$key][$default];
		else
			return $array_default;
	} else {
		// возвращаем значение или дефаулт
		return (isset($MSO->key_options[$key])) ? $MSO->key_options[$key] : $default;
	}
}

// Функция обратная mso_get_val() - задаёт для ключа $key значение $val 
// если $val_val == null, значит присваиваем всему $key значание $val
// если $val_val != null, значит $val - это ключ массива
// mso_set_val('type_home', 'cache_time');
//		[type_home]=>'cache_time'
//
// mso_set_val('type_home', 'cache_time', 900); 
//		[type_home] => Array
//		(
//            [cache_time] => 900
//		)
function mso_set_val($key, $val, $val_val = null)
{
	global $MSO;

	// нет массива, создаём
	if (!isset($MSO->key_options)) {
		$MSO->key_options = [];
	}

	if ($val_val !== null) {
		$MSO->key_options[$key][$val] = $val_val;
	} else {
		$MSO->key_options[$key] = $val; // записали значение
	}
}

// Функция удаляет ключ $key 
function mso_unset_val($key)
{
	global $MSO;

	if (isset($MSO->key_options[$key])) {
		unset($MSO->key_options[$key]);
	}
}

/* 
Преобразование входящего текста опции в массив
по каждой секции по указанному патерну
Вход:

[slide]
link = ссылка изображения
title = подсказка
img = адрес картинки
text = текст с html без переносов. h3 для заголовка
p_line1 = пагинация 1 линия
p_line2 = пагинация 2 линия
[/slide]

Паттерн (по правилам php):
	'!\[slide\](.*?)\[\/slide\]!is'

Выход:

Array
(
	[0] => Array
		(
			[link] => ссылка изображения
			[title] => подсказка
			[img] => адрес картинки
			[text] => текст с html без переносов. h3 для заголовка
			[p_line1] => пагинация 1 линия
			[p_line2] => пагинация 2 линия
		)
 )

$array_default - стартовый массив опций на случай, если в опции нет обязательного ключа
например 
array('link'=>'', 'title'=>'', 'img'=>'', 'text'=>'', 'p_line1'=>'', 'p_line2'=>'')

Если $simple = true, то входящий паттерн используется как слово из которого
будет автоматом сформирован корректный паттерн по шаблону [слово]...[/слово]

Если опция содержит html-код или несколько строк, то её следует обрамить между _START_ и _END_
Например:

...
text = _START_
 текст в несколько строк
 <span class="red">красный текст</span>
_END_
...

Если $as_is == true, то отдается массив вхождений без обработки внутренних полей. 
	$as_is_key — номер ключа массива, если false, то все

*/
function mso_section_to_array($text, $pattern, $array_default = [], $simple = false, $as_is = false, $as_is_key = 1)
{
	$text = preg_replace_callback('!_START_(.*?)_END_!is', '_mso_section_to_array_replace_start', $text);

	if ($simple) $pattern = '!\[' . $pattern . '\](.*?)\[\/' . $pattern . '\]!is';

	// $array_result - массив каждой секции (0 - все вхождения)
	if (preg_match_all($pattern, $text, $array_result)) {
		// массив слайдов в $array_result[1]

		// отдать как есть без обработки
		if ($as_is) {
			if ($as_is_key !== false) // если указан номер ключа
				return $array_result[$as_is_key];
			else
				return $array_result; // отдать всё
		}

		// преобразуем его в массив полей
		$f = []; // массив для всех полей
		$i = 0; // счетчик 

		foreach ($array_result[1] as $val) {
			$val = trim($val);

			//if (!$val) continue; !!! пока убрал, поскольку опция может быть пустой

			$val = str_replace(' = ', '=', $val);
			$val = str_replace('= ', '=', $val);
			$val = str_replace(' =', '=', $val);
			$val = explode("\n", $val); // разделим на строки

			$ar_val = [];

			$f[$i] = $array_default;

			foreach ($val as $pole) {
				$ar_val = explode('=', $pole, 2); // строки разделены = type = select

				if (isset($ar_val[0]) and isset($ar_val[1])) {
					$f[$i][$ar_val[0]] = preg_replace_callback('!\[base64\](.*?)\[\/base64\]!is', '_mso_section_to_array_replace_end', $ar_val[1]);
				}
			}

			$i++;
		}

		return $f;
	}

	return []; // не найдено
}

// callback-функция для mso_section_to_array
// заменяет html между _START_ и _END_ на base64 кодирование
function _mso_section_to_array_replace_start($matches)
{
	$m = '[base64]' . base64_encode(trim($matches[1])) . '[/base64]';

	// уберем = поскольку этот символ используется в опции как разделитель
	$m = str_replace('=', '_RAVNO_', $m);

	return $m;
}

// callback-функция для mso_section_to_array
// обратная функция преобразования [base64] в html
function _mso_section_to_array_replace_end($matches)
{
	return base64_decode(str_replace('_RAVNO_', '=', $matches[1]));
}

# end of file
