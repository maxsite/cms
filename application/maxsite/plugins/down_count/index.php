<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function down_count_autoload($args = array())
{
	mso_hook_add('init', 'down_count_init'); # хук на обработку входящего url
	mso_hook_add('content', 'down_count_content'); # хук на обработку текста
	mso_hook_add('admin_init', 'down_count_admin_init'); # хук на админку
}


# функция выполняется при активации (вкл) плагина
function down_count_activate($args = array())
{	
	mso_create_allow('down_count_edit', t('Админ-доступ к настройкам счетчика переходов (Download count)')); 
	return $args;
}

# функция выполняется при деинстяляции плагина
function down_count_uninstall($args = array())
{	
	mso_delete_option('plugin_down_count', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function down_count_admin_init($args = array()) 
{
	if ( !mso_check_allow('down_count_edit') ) return $args;
			
	$this_plugin_url = 'plugin_down_count'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Счетчик переходов'));
	mso_admin_url_hook ($this_plugin_url, 'down_count_admin_page');
	
	return $args;
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function down_count_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('down_count_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Счетчик переходов') . ' "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Счетчик переходов') . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'down_count/admin.php');
}


# получение массива данных их файла
function down_count_get_data()
{
	// вспомогательная опция, которая получает массив из файла
	// делаем её статик, чтобы не было многократного ображения к файлу
	// когда нужно получить данные из хука на контент
	
	static $data;
	
	if (!isset($data))
	{	
		$metka = 'DOWN COUNT' . NR; // метка в файле - первая строчка
		
		$options = mso_get_option('plugin_down_count', 'plugins', array());
		if (!isset($options['file']) ) $options['file'] = 'dc.dat';

		$fn = getinfo('uploads_dir') . $options['file'];
		
		$CI = & get_instance();
		$CI->load->helper('file'); // хелпер для работы с файлами
		
		if (!file_exists($fn)) // файла нет, нужно его создать
		{
			write_file($fn, $metka); // записываем метку
			$data = array($metka); // сразу формируем результирующий массив
		}
		else
		{
			$data = file($fn); // получим файл
		}
		
	
		if (isset($data[0]) and $data[0] == $metka) // первая строчка - метка
		{	
			// если есть метка, то данные в массиве хранятся в виде обычных строк адрес || count
			// нужно переделать в структуру массива [адрес]['count'] = 47746
			
			$data_new = array(); //сразу готовим массив данных
			
			foreach($data as $url)
			{
				$a = explode(' || ', $url);
				
				if (count($a) == 2)
				{
					$data_new[mso_xss_clean($a[0])]['count'] = trim($a[1]);
				}
			}
			
			$data = $data_new;
		}
		else
		{
			// это старый формат файла - серилизация
			$data = unserialize(read_file($fn)); // получим из файла
			
			// нужно обновить формат файла
			
			$out = $metka;
			
			$data_new = array(); //сразу готовим обновленный массив
			
			foreach($data as $url => $aaa)
			{
				if (mso_xss_clean($url) and strpos($url, 'http://') === 0) // чистка
				{
					$out .= mso_xss_clean($url) . ' || ' . $aaa['count'] . NR;
					
					$data_new[mso_xss_clean($url)]['count'] = $aaa['count'];
				}
			}
			
			write_file($fn, $out); // записываем новый файл
			
			$data = $data_new;
		}
	}
	
	
	return $data;
}

# запись массива в файл
function down_count_save_data($data)
{
	$options = mso_get_option('plugin_down_count', 'plugins', array());
	if (!isset($options['file']) ) $options['file'] = 'dc.dat';

	$fn = getinfo('uploads_dir') . $options['file'];
	
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	
	// $data - массив
	// нужно его переделать в набор строк адрес || count
	
	$out = 'DOWN COUNT' . NR;

	foreach($data as $url => $aaa)
	{
		if (mso_xss_clean($url) and strpos($url, 'http://') === 0) 
		{
			$out .= mso_xss_clean($url) . ' || ' . $aaa['count'] . NR;
		}
	}
	
	write_file($fn, $out); // записываем новый файл
	
}

# функции плагина
function down_count_init($args = array())
{
	# опции плагина
	$options = mso_get_option('plugin_down_count', 'plugins', array());
	if ( !isset($options['prefix']) ) $options['prefix'] = 'dc';
	
	if (mso_segment(1) == $options['prefix'] and mso_segment(2)) 
	{
		if ( !isset($options['referer']) ) $options['referer'] = 1; // запретить скачку с чужих сайтов
		
		if ($options['referer'])
		{
			// если нет реферера, то рубим
			if (!isset($_SERVER['HTTP_REFERER'])) //
				die( sprintf('<b><font color="red">' . t('Данная ссылка доступна только со <a href="%s">страниц сайта</a>') . '</font></b>', getinfo('siteurl')) );
			
			// проверяем реферер - откуда пришел
			$p = parse_url($_SERVER['HTTP_REFERER']);
			if (isset($p['host'])) $p = $p['host'];
				else $p = '';
			if ( $p != $_SERVER['HTTP_HOST'] ) // чужой сайт
				die('<b><font color="red">' . t('Запрещен переход по этой ссылке с чужого сайта') . '</font></b>');
		}
		
		// это редирект на указанный в сегментах url
		
		$url = base64_decode(mso_segment(2)); // декодируем
		
		// проверяем входящий url 
		// в нем может быть закодирована какая-то гадость
		
		$url_check = mso_xss_clean($url);
		if ($url_check != $url) die('<b><font color="red">Achtung! XSS attack!</font></b>');
		
		$url = $url_check;
		
		// получим данные
		$data = down_count_get_data();
		
		// вноисм изменения
		if (isset($data[$url])) // такой url уже есть
			$data[$url]['count'] = $data[$url]['count'] + 1;
		else // нет еще
			$data[$url]['count'] = 1; // записываем один переход
		
		// сохраняем в файл 
		down_count_save_data($data);

		mso_redirect($url, true);
		exit;
	}
	
	return $args;
}


function down_count_content_callback($matches)
{
	//'|\[dc\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/dc\]|ui';
	
	// ститик, чтобы не получать каждый раз опции
	static $prefix, $format, $real_title;
	
	if (!isset($prefix) or !isset($format) or !isset($real_title)) 
	{
		$options = mso_get_option('plugin_down_count', 'plugins', array());
		
		if ( !isset($options['prefix']) ) $options['prefix'] = 'dc';
		$prefix = $options['prefix'];
		
		if ( !isset($options['format']) ) $options['format'] = ' <sup title="' . t('Количество переходов') . '">%COUNT%</sup>';
		$format = $options['format'];

		if ( !isset($options['real_title']) ) $options['real_title'] = 1;
		$real_title = $options['real_title'];
	}
	
	$data = down_count_get_data(); // получаем массив из файла, в котором ведется подсчет колва переходов

	if (isset( $data[$matches[2]]['count'] )) $count = $data[$matches[2]]['count'];
		else $count = 0;
	
	$url  = base64_encode($matches[2]); // кодируем урл в одну строку
	$url  = getinfo('siteurl') . $prefix . '/' . $url;
	
	$format_out = str_replace('%COUNT%', $count, $format);
	$matches[1] = str_replace('%COUNT%', $count, $matches[1]);
	$matches[3] = str_replace('%COUNT%', $count, $matches[3]);

	$title = ($real_title)?(' title="' . $matches[2] . '" '):(' ');
	$out = '<a' . $matches[1] . 'href="' . $url . '"' . $title . $matches[3] . '>' . $matches[4] . '</a>' . $format_out;

	return $out;
}


function down_count_content_callback_url($matches)
{
	// используем down_count_content_callback, только изменим под него $matches
	
	$m[1] = ' ';
	$m[2] = $matches[1];
	$m[3] = '';
	$m[4] = $matches[2];

	return down_count_content_callback($m);
}

# замена ссылок в тексте
function down_count_content($text = '')
{
	// [dc]<a href="http://localhost/codeigniter/">ссылка</a>[/dc]
	$pattern = '|\[dc\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/dc\]|ui';
	$text = preg_replace_callback($pattern, 'down_count_content_callback', $text);
	
	// [dc][url=урл]сайт[/url][/dc]
	$pattern = '|\[dc\]\[url=(.*?)\](.*?)\[/url\]\[/dc\]|ui';
	$text = preg_replace_callback($pattern, 'down_count_content_callback_url', $text);

	return $text;
}


# end file