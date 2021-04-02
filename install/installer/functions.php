<?php if (!defined('INSTALLER')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

/**
 * Check Secret Key from mso_config.php
 *
 * @param  mixed $message
 *
 * @return string or boolean
 */
function checkSecretKey($message = true)
{
	$key = false;

	if (file_exists(MSODIR . 'application/maxsite/mso_config.php')) {
		$file = file_get_contents(MSODIR . 'application/maxsite/mso_config.php');

		$pattern = '/\$MSO->config\[\'secret_key\'\] = \'(.*?)\';/';

		if (preg_match($pattern, $file, $matches, PREG_OFFSET_CAPTURE, 3)) {
			if ($secret_key = $matches[1][0])
				$key =  $secret_key;
		}
	}

	if (!$key and $message)
		echo '<p class="t-red">⚠  ' . t('error secret key') . '</p>';

	return $key;
}

/**
 * Create mso_config.php
 *
 * @return void
 */
function newMsoConfig()
{
	$fn = MSODIR . 'application/maxsite/mso_config.php';

	if (!file_exists($fn)) {
		$rand = randomPassword();

		$source = file_get_contents(MSODIR . 'application/maxsite/mso_config.php-distr');
		$source = str_replace('$MSO->config[\'secret_key\'] = \'\';', '$MSO->config[\'secret_key\'] = \'' . $rand . '\';', $source);

		file_put_contents($fn, $source);
	}

	if (file_exists($fn))
		echo '<p class="t-green">✔ ' . t('file created', 'application/maxsite/mso_config.php') . '</p>';
	else
		echo '<p class="t-red">⚠ ' . t('file is not created', 'application/maxsite/mso_config.php') . '</p>';
}

/**
 * Create sitemap.xml
 *
 * @return void
 */
function newSitemap()
{
	if (!file_exists(MSODIR . 'sitemap.xml')) {
		$file = file_get_contents(INSTALLER . 'distr/sitemap.xml');
		file_put_contents(MSODIR . 'sitemap.xml', $file);
	}

	if (file_exists(MSODIR . 'sitemap.xml'))
		echo '<p class="t-green">✔ ' . t('file created', 'sitemap.xml') . '</p>';
	else
		echo '<p class="t-red">⚠ ' . t('file is not created', 'sitemap.xml') . '</p>';
}

/**
 * Create .htacces
 *
 * @return void
 */
function newHtaccess()
{
	if (!file_exists(MSODIR . '.htaccess')) {
		if (isset($_SERVER['REQUEST_URI']))
			$subdir = $_SERVER['REQUEST_URI'];
		else
			$subdir = '/';

		$subdir = str_replace('/install/', '/', $subdir);

		$htaccess = file_get_contents(INSTALLER . 'distr/htaccess.txt');
		$htaccess = str_replace('RewriteBase /', 'RewriteBase ' . $subdir, $htaccess);
		$htaccess = str_replace('RewriteRule ^(.*)$ /', 'RewriteRule ^(.*)$ ' . $subdir, $htaccess);

		file_put_contents(MSODIR . '.htaccess', $htaccess);
	}

	if (file_exists(MSODIR . '.htaccess'))
		echo '<p class="t-green">✔ ' . t('file created', '.htaccess') . '</p>';
	else
		echo '<p class="t-red">⚠ ' . t('file is not created', '.htaccess') . '</p>';
}

/**
 * check Writable file
 *
 * @param  mixed $file
 *
 * @return void
 */
function checkWritable(string $file)
{
	if (!(file_exists(MSODIR . $file) and is_writable(MSODIR . $file))) {
		echo '<p class="t-red">⚠ ' . t('folder not found', $file) . '</p>';
	}
}

/**
 * Create robots.txt
 *
 * @return void
 */
function newRobots()
{
	if (!file_exists(MSODIR . 'robots.txt')) {
		$base_url = $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

		$host = str_replace('/install/', '', $base_url);

		$robots = file_get_contents(INSTALLER . 'distr/robots.txt');
		$robots = str_replace('Host: ', 'Host: ' . $host, $robots);

		file_put_contents(MSODIR . 'robots.txt', $robots);
	}

	if (file_exists(MSODIR . 'robots.txt'))
		echo '<p class="t-green">✔ ' . t('file created', 'robots.txt') . '</p>';
	else
		echo '<p class="t-red">⚠ ' . t('file is not created', 'robots.txt') . '</p>';
}

/**
 * Generate random string
 *
 * @param  mixed $len
 *
 * @return string
 */
function randomPassword($len = 15): string
{
	$p0 = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ'), 0, 1);
	$p1 = str_repeat('ABCDEFGHJKLMNPQRSTUVWXYZ', 1);
	$p2 = str_repeat('abcdefghijkmnpqrstuvwxyz', 7);
	$p3 = str_repeat('*!?#$-+()._', 2);
	$p4 = str_repeat('123456789', 3);

	$p = str_shuffle($p1 . $p2 . $p3 . $p4);

	return $p0 . substr($p, 0, $len - 1);
}

/**
 * Language translation
 *
 * @param  mixed $text
 * @param  mixed $replace "%1" in text
 *
 * @return string
 */
function t($text, $replace = ''): string
{
	static $WORDS = [];

	if (is_array($text)) {
		$WORDS = $text;
		
		return '';
	}

	if (isset($WORDS[$text])) {
		if (!$replace) return $WORDS[$text];

		return str_replace('%1', $replace, $WORDS[$text]);
	}

	return $text;
}

/**
 * Detect Language in Browser
 *
 * @param  mixed $langs
 * @param  mixed $default
 *
 * @return string
 */
function detectLang(array $langs, string $default): string
{
	$user_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

	foreach ($user_langs as $lang) {
		$lang = substr($lang, 0, 2);

		if (in_array($lang, $langs)) return $lang;
	}

	return $default;
}

/**
 * Site host
 *
 * @return string
 */
function getHostSite()
{
	$base_url = $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

	return str_replace('/install/', '', $base_url);
}

/**
 * new database.php
 *
 * @param  mixed $PV
 *
 * @return boolean
 */
function newDatabase($PV)
{
	if (file_exists(MSODIR . 'application/config/database.php-distr')) {
		$file = file_get_contents(MSODIR . 'application/config/database.php-distr');

		$file = str_replace('$db[\'default\'][\'hostname\'] = \'localhost\';', '$db[\'default\'][\'hostname\'] = \'' . $PV['db_hostname'] . '\';', $file);

		$file = str_replace('$db[\'default\'][\'username\'] = \'\';', '$db[\'default\'][\'username\'] = \'' . $PV['db_username'] . '\';', $file);

		$file = str_replace('$db[\'default\'][\'password\'] = \'\';', '$db[\'default\'][\'password\'] = \'' . $PV['db_password'] . '\';', $file);

		$file = str_replace('$db[\'default\'][\'database\'] = \'\';', '$db[\'default\'][\'database\'] = \'' . $PV['db_database'] . '\';', $file);

		$file = str_replace('$db[\'default\'][\'dbprefix\'] = \'mso_\';', '$db[\'default\'][\'dbprefix\'] = \'' . $PV['db_dbprefix'] . '\';', $file);

		file_put_contents(MSODIR . 'application/config/database.php', $file);

		return false;
	} else {
		return true;
	}
}

/**
 * check Table Exists
 *
 * @param  mixed $mysqli
 * @param  mixed $tables
 * @param  mixed $prefix
 *
 * @return array
 */
function checkTableExists($mysqli, $tables, $prefix)
{
	$errors = []; // все ошибки в массив

	foreach ($tables as $table) {
		$table = $mysqli->real_escape_string($prefix . $table);

		if ($result = $mysqli->query("SHOW TABLES LIKE '" . $table . "'")) {
			if ($result->num_rows > 0)
				$errors[] = t('table exists') . ' <b>' . $table . '</b>';
		}
	}

	return $errors;
}

/**
 * send Email
 *
 * @param  mixed $to
 * @param  mixed $subject
 * @param  mixed $message
 *
 * @return void
 */
function sendEmail($to, $subject, $message)
{
	$from = 'no-reply@' . $_SERVER['SERVER_NAME']; // якобы адрес отправителя с сервера
	$headers = 'From: ' . $from . "\r\n"; // от кого

	// кодируем заголовок в UTF-8
	$subject = preg_replace("/(\r\n)|(\r)|(\n)/", "", $subject);
	$subject = preg_replace("/(\t)/", " ", $subject);
	$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

	@mail($to, $subject, $message, $headers);
}

# end of file
