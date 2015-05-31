<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

# функция языкового перевода
# пока заглушка
function t($text)
{
	return $text;
}

# провека на существование файла и его writable
function v_file_wtitable($fn)
{
	$path = realpath(BASEPATH . '../') . '/';
	
	return ( file_exists($path . $fn) and is_writable($path . $fn) );
}

# провека на существование файла относительно корня сайта
function v_file_exists($fn)
{
	$path = realpath(BASEPATH . '../') . '/';
	
	return file_exists($path . $fn);
}

# адрес сайта
function v_get_host()
{
	$base_url = $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

	return str_replace('/install/', '', $base_url);
}

# robots.txt
function v_new_robots()
{
	$path = realpath(BASEPATH . '../') . '/';
	
	if (!file_exists($path . 'robots.txt')) 
	{
		$base_url = $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

		$host = str_replace('/install/', '', $base_url);
		
		$robots = file_get_contents($path . 'install/distr/robots.txt');
		$robots = str_replace('Host: ', 'Host: ' . $host, $robots);
	
		file_put_contents($path . 'robots.txt', $robots);
		
		return false;
	}
	else 
		return true;
}

# .htacces
function v_new_htaccess()
{
	$path = realpath(BASEPATH . '../') . '/';
	
	if (!file_exists($path . '.htaccess')) 
	{
		if (isset($_SERVER['REQUEST_URI']))
			$subdir = $_SERVER['REQUEST_URI'];
		else
			$subdir = '/';
		
		$subdir = str_replace('/install/', '/', $subdir);
		
		$htaccess = file_get_contents($path . 'install/distr/htaccess.txt');
		$htaccess = str_replace('RewriteBase /', 'RewriteBase ' . $subdir, $htaccess);
		$htaccess = str_replace('RewriteRule ^(.*)$ /', 'RewriteRule ^(.*)$ ' . $subdir, $htaccess);
		
		file_put_contents($path . '.htaccess', $htaccess);
		
		return false;
	}
	else 
		return true;
}

# sitemap.xml
function v_new_sitemap()
{
	$path = realpath(BASEPATH . '../') . '/';
	
	if (!file_exists($path . 'sitemap.xml')) 
	{
		$file = file_get_contents($path . 'install/distr/sitemap.xml');
		file_put_contents($path . 'sitemap.xml', $file);
		
		return false;
	}
	else 
		return true;
}

# генератор случайного пароля
function v_rand_str($len = 15)
{
	$p = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789,:!?.$-+&_@,';
	$p = str_shuffle($p);
	
	$p1 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$p1 = str_shuffle($p1);
	$p1 = substr($p1, 0, 1);
	
	$p = substr($p1 . $p, 0, $len);
	
	return $p;
}

# mso_config.php
function v_new_mso_config()
{
	$path = realpath(BASEPATH . '../') . '/';
	
	if (!file_exists($path . 'application/maxsite/mso_config.php')) 
	{
		$rand = v_rand_str();

		$file = file_get_contents($path . 'application/maxsite/mso_config.php-distr');
		$file = str_replace('$MSO->config[\'secret_key\'] = \'\';', '$MSO->config[\'secret_key\'] = \'' .$rand . '\';', $file);
		
		file_put_contents($path . 'application/maxsite/mso_config.php', $file);
		
		return false;
	}
	else 
		return true;
}

# secret_key из mso_config.php
function v_get_secret_key()
{
	$path = realpath(BASEPATH . '../') . '/';
	
	if (file_exists($path . 'application/maxsite/mso_config.php')) 
	{
		$file = file_get_contents($path . 'application/maxsite/mso_config.php');

		$pattern = '/\$MSO->config\[\'secret_key\'\] = \'(.*?)\';/';
		
		if (preg_match($pattern, $file, $matches, PREG_OFFSET_CAPTURE, 3))
		{
			if ($secret_key = $matches[1][0]) return $secret_key;
		}
		
		return false;
	}
	else
		return false; // ошибка — ключ не получен
}

# database.php
function v_new_database($PV)
{
	$path = realpath(BASEPATH . '../') . '/';
	
	if (file_exists($path . 'application/config/database.php-distr')) 
	{
		$rand = v_rand_str();

		$file = file_get_contents($path . 'application/config/database.php-distr');
		
		$file = str_replace('$db[\'default\'][\'hostname\'] = \'localhost\';', '$db[\'default\'][\'hostname\'] = \'' . $PV['db_hostname'] . '\';', $file);
		
		$file = str_replace('$db[\'default\'][\'username\'] = \'\';', '$db[\'default\'][\'username\'] = \'' . $PV['db_username'] . '\';', $file);
		
		$file = str_replace('$db[\'default\'][\'password\'] = \'\';', '$db[\'default\'][\'password\'] = \'' . $PV['db_password'] . '\';', $file);
		
		$file = str_replace('$db[\'default\'][\'database\'] = \'\';', '$db[\'default\'][\'database\'] = \'' . $PV['db_database'] . '\';', $file);
		
		$file = str_replace('$db[\'default\'][\'dbprefix\'] = \'mso_\';', '$db[\'default\'][\'dbprefix\'] = \'' . $PV['db_dbprefix'] . '\';', $file);
		
		file_put_contents($path . 'application/config/database.php', $file);
		
		return false;
	}
	else 
		return true;
}

# проверка email
function v_valid_email($address)
{
	return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
}

# проверка существования таблиц в БД
function v_mysql_table_exists($mysqli, $tables, $prefix)
{
	$errors = array(); // все ошибки в массив
	
	foreach($tables as $table)
	{
		$table = $mysqli->real_escape_string($prefix . $table);
	
		if ($result = $mysqli->query("SHOW TABLES LIKE '" . $table . "'")) 
		{ 
			if ($result->num_rows > 0)
				$errors[] = t('Существующая таблица: ') . '<b>' . $table . '</b>';
		}
	}

	return $errors;
}

function v_load_sql($file)
{
	$path = realpath(BASEPATH . '../') . '/';
	
	if (file_exists($path . $file)) 
		return file_get_contents($path . $file);
	else
		return '';
}

function v_email($to, $subject, $message)
{
	$from = 'no-reply@' . $_SERVER['SERVER_NAME']; // якобы адрес отправителя с сервера
	$headers = 'From: '. $from . "\r\n"; // от кого
	
	// кодируем заголовок в UTF-8
	$subject = preg_replace("/(\r\n)|(\r)|(\n)/", "", $subject);
	$subject = preg_replace("/(\t)/", " ", $subject);
	$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

	@mail($to, $subject, $message, $headers);
}


# end of file