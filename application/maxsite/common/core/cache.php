<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// добавить кеш
// ключ, значение, время
// Функция взята из _write_cache output.php - немного переделанная
function mso_add_cache($key, $output, $time = false, $custom_fn = false)
{
	global $MSO;

	// если определен отдельный хук, то выполняем его
	if (mso_hook_present('mso_add_cache'))
		return mso_hook('mso_add_cache', [
			'key' => $key,
			'output' => $output,
			'time' => $time,
			'custom_fn' => $custom_fn
		]);

	// если разрешено динамическое кэширование
	if (mso_get_option('cache_dinamic', 'general', 0)) {
		// опции не сохраняем - у них свой кэш
		if ($key !== 'options') $MSO->cache[$key] = $output;
	}

	$CI = &get_instance();

	$cache_path = getinfo('cache_dir');

	if (!is_dir($cache_path) or !is_writable($cache_path)) return;

	if (!$custom_fn)
		$cache_path .= mso_md5($key . $CI->config->item('base_url'));
	else
		$cache_path .= $key;

	if (!$fp = @fopen($cache_path, 'wb')) return;
	if (!$time) $time = $MSO->config['cache_time'];

	$expire = time() + $time;
	$output = serialize($output);

	flock($fp, LOCK_EX);
	fwrite($fp, $expire . 'TS--->' . $output);
	flock($fp, LOCK_UN);
	fclose($fp);

	if (!is_writable($cache_path)) @chmod($cache_path, 0777);
}

// удаление файла в кэше файлов, начинающихся с указаной строки
function mso_flush_cache_mask($mask = '')
{
	if (!$mask) return;

	// если определен отдельный хук, то выполняем его
	if (mso_hook_present('mso_flush_cache_mask'))
		return mso_hook('mso_flush_cache_mask', array('mask' => $mask));

	$CI = &get_instance();
	$cache_path = getinfo('cache_dir');

	if (!is_dir($cache_path) or !is_writable($cache_path)) return;

	$CI->load->helper('directory');
	$files = directory_map($cache_path, true); // только в текущем каталоге

	if (!$files) return; // нет файлов вообще

	foreach ($files as $file) {
		if (@is_dir($cache_path . $file)) continue; // это каталог

		$pos = strpos($file, $mask);

		if ($pos !== false and $pos === 0) {
			@unlink($cache_path . $file);
		}
	}
}

// сбросить кэш - если указать true, то удалится кэш из вложенных каталогов
// если указан $dir, то удаляется только в этом каталоге
// если указать $file, то удаляется только этот файл в кэше
function mso_flush_cache($full = false, $dir = false, $file = false)
{
	global $MSO;

	// если определен отдельный хук, то выполняем его
	if (mso_hook_present('mso_flush_cache'))
		return mso_hook('mso_flush_cache', [
			'full' => $full,
			'dir' => $dir,
			'file' => $file
		]);

	$MSO->cache = [];
	$CI = &get_instance();

	$cache_path = getinfo('cache_dir');

	if (!is_dir($cache_path) or !is_writable($cache_path)) return false;

	// находим в каталоге все файлы и их удалаяем
	if ($full) {
		$CI->load->helper('file_helper'); // этот хелпер удаляет все Файлы и во вложенных каталогах
		@delete_files($cache_path);
	} else {
		// удаляем файлы только в текущем каталоге кэша
		// переделанная функция delete_files из file_helper
		$mso_cache_last = $cache_path . '_mso_cache_last.txt';

		if ($dir) $cache_path .= $dir . '/'; // если указан $dir, удаляем только в нем

		if ($file) {
			// указан конкретный файл
			if (file_exists($cache_path . $file)) @unlink($cache_path . $file);
		} else {
			if (!$current_dir = @opendir($cache_path)) return false;

			while (FALSE !== ($filename = @readdir($current_dir))) {
				if ($filename != "." and $filename != "..") {
					if (!is_dir($cache_path . $filename)) @unlink($cache_path . $filename);
				}
			}

			@closedir($current_dir);
		}

		// создадим служебный файл _mso_cache_last.txt который используется для сброса кэша по дате создания
		// при инициализации смотрится дата этого файла и если он создан позже, чем время жизни кэша, то кэш сбрасывается mso_flush_cache
		if (!$dir) {
			$fp = @fopen($mso_cache_last, 'w');
			flock($fp, LOCK_EX);
			fwrite($fp, time());
			flock($fp, LOCK_UN);
			fclose($fp);
		}
	}

	// если используется родное CodeIgniter sql-кэширование, то нужно очистить и его
	$CI->db->cache_delete_all();
}

// получить кеш по ключу
// Функция взята из _display_cache output.php - переделанная
function mso_get_cache($key, $custom_fn = false)
{
	global $MSO;

	// если определен отдельный хук, то выполняем его
	if (mso_hook_present('mso_get_cache'))
		return mso_hook('mso_get_cache', [
			'key' => $key,
			'custom_fn' => $custom_fn
		]);

	if (mso_get_option('cache_dinamic', 'general', 0)) {
		// кэш может быть и в динамическом $MSO->cache
		if (isset($MSO->cache[$key]) and $MSO->cache[$key]) {
			return $MSO->cache[$key];
		}
	}

	$CI = &get_instance();

	$cache_path = getinfo('cache_dir');

	if (!is_dir($cache_path) or !is_writable($cache_path)) return false;

	if (!$custom_fn)
		$filepath = $cache_path . mso_md5($key . $CI->config->item('base_url'));
	else
		$filepath = $cache_path . $key;

	if (!@file_exists($filepath)) return false;
	if (!$fp = @fopen($filepath, 'rb')) return false;

	flock($fp, LOCK_SH);

	$cache = '';

	if (filesize($filepath) > 0)
		$cache = fread($fp, filesize($filepath));

	flock($fp, LOCK_UN);
	fclose($fp);

	if (!preg_match("/(\d+TS--->)/", $cache, $match)) return false;

	if (time() >= trim(str_replace('TS--->', '', $match['1']))) {
		@unlink($filepath);
		return false;
	}

	$out = str_replace($match['0'], '', $cache);
	$out = @unserialize($out);

	if (mso_get_option('cache_dinamic', 'general', 0)) $MSO->cache[$key] = $out;

	return $out;
}

# end of file
