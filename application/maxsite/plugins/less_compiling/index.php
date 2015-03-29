<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function less_compiling_autoload()
{
	mso_hook_add( 'init', 'less_compiling_init', 5); # хук на init с низким приоритетом
}

# функция выполняется при активации (вкл) плагина
function less_compiling_activate($args = array())
{	
	mso_create_allow('less_compiling_edit', t('Админ-доступ к настройкам less_compiling'));
	return $args;
}


# функция выполняется при деинсталяции плагина
function less_compiling_uninstall($args = array())
{	
	mso_delete_option('plugin_less_compiling', 'plugins' ); // удалим созданные опции
	mso_remove_allow('less_compiling_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function less_compiling_mso_options() 
{
	if ( !mso_check_allow('less_compiling_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_less_compiling', 'plugins', 
		array(
			'enabled' => array(
							'type' => 'checkbox', 
							'name' => t('Включить less-компиляцию'), 
							'description' => '', 
							'default' => 1
						),
			
			'only_users_enabled' => array(
							'type' => 'checkbox', 
							'name' => t('Выполнять компиляцию только для авторов и админов'), 
							'description' => '',
							'default' => 1
						),
						
			'admin_enabled' => array(
							'type' => 'checkbox', 
							'name' => t('Разрешить выполнять компиляцию при работе в админ-панели'), 
							'description' => '',
							'default' => 0
						),
						
			'files' => array(
							'type' => 'textarea', 
							'name' => t('Файлы для компиляции'), 
							'description' => t('Формат (разделитель | ) <pre>+ | файл.less | файл.css | mini cache</pre> <b>+</b> Включение строчки<br><b>-</b> Выключение строчки<br><b>*шаблон</b> Компиляция только в указанном шаблоне (например: *d2 | ...)<br><b>файл.less</b> - исходный файл (путь задается относительно каталога /maxsite/)<br><b>файл.css</b> - конечный файл (путь задается относительно каталога /maxsite/)<br>опции через пробел: <b>mini/nomini</b> - сжимать, <b>cache/nocache</b> - использовать кэш.<br>По-умолчанию используется сжатие и кэширование<br><br>Примеры:<pre>+ | plugins/my/style.less | plugins/my/style.css | cache mini<br>*d2 | templates/d2/css-less/var_style.less | templates/d2/css/var_style.css | nocache mini </pre><br>Результирующий css-файл должен иметь права, разрешающие его перезапись и/или создание (обычно 666).'),
							'default' => ''
						),
						
						
						
			),
		t('Плагин LESS compiling'), // титул
		t('Компиляция будет происходить автоматически.')   // инфо
	);
}

function less_compiling_init($args = array())
{
	$options = mso_get_option('plugin_less_compiling', 'plugins', array());
	if (!isset($options['enabled']) ) return $args; // нет опций
	if (!$options['enabled']) return $args; // выключено
	
	// Выполнять компиляцию только для админов и авторов
	if (!isset($options['only_users_enabled'])) $options['only_users_enabled'] = true;
	if ($options['only_users_enabled'] and !is_login()) return $args;
	
	// Выполнять компиляцию при работе в админ-панели
	if (!isset($options['admin_enabled'])) $options['admin_enabled'] = false;
	
	// не компилировать в админке
	if (!$options['admin_enabled'] and mso_segment(1) == 'admin') return $args; 
	
	
	if (!$options['files']) return $args; // не заданы файлы
	
	$files = explode("\n", $options['files']); // разобъем по строкам
	if (!$files) return $args; // пустой массив
	
	foreach ($files as $file) // перебираем каждую строчку
	{
	
		$row = explode('|', $file); // + | файл.less | файл.css | nomini nocache
		$row = array_map('trim', $row);
		
		// должно быть как минимум 3 непустых элемента
		if (isset($row[0]) and isset($row[1]) and isset($row[2]) and $row[0] and $row[1] and $row[2]) 
		{
			if ($row[0] == '-' ) continue; // должен быть + или *
			
			if ( strpos($row[0], '*') === 0) // компиляция только в указанном шаблоне
			{
				// если текущий шаблон не равен указанному, то выходим 
				if (getinfo('template') != substr($row[0], 1)) 
				{
					//pr(1);
					continue;
				}
			}
			
			
			$less_file = getinfo('base_dir') . $row[1];
			$css_file = getinfo('base_dir') . $row[2];
			
			// проверим наличие файла
			if (file_exists($less_file))
			{			
				// возможно есть опции
				if (isset($row[3]) and $row[3])
				{
					$opt = explode(' ', $row[3]);
					$opt = array_map('trim', $opt);
				}
				else 
				{
					$opt = array();
				}
				
				$use_cache = !in_array('nocache', $opt); // если есть nocache, то не кэшируем
				$use_mini = in_array('mini', $opt);

				// $use_cache = false, $use_mini = true, $use_mini_n = false 
				mso_lessc($less_file, $css_file, 'qwerty', $use_cache, $use_mini, $use_mini);
				
			}
		}
	}
	
	return $args;
}



# компилятор LESS в CSS
# на выходе css-подключение, либо содержимое css-файла (переключается через $css_url)
# если первый параметр — массив, то остальные игнорируются. В массиве ключи — опции
# $less_file - входной less-файл (полный путь на сервере)
# $css_file - выходной css-файл (полный путь на сервере)
# $css_url - полный http-адрес css-файла. Если $css_url = '', то отдается содержимое css-файла
# $use_cache - разрешить использование кэширования LESS-файла (определяется по времени файлов)
# $use_mini - использовать сжатие css-кода
# $use_mini_n - если включено сжатие, то удалять переносы строк

function mso_lessc($less_file = '', $css_file = '', $css_url = '', $use_cache = false, $use_mini = true, $use_mini_n = false)
{

	if (is_array($less_file)) // все параметры в массиве
	{
		$options = $less_file; // для красоты кода и чтобы не путаться
		
		$less_file = isset($options['less_file']) ? $options['less_file'] : '';
		$css_file = isset($options['css_file']) ? $options['css_file'] : '';
		$css_url = isset($options['css_url']) ? $options['css_url'] : '';
		$use_cache = isset($options['use_cache']) ? $options['use_cache'] : false;
		$use_mini = isset($options['use_mini']) ? $options['use_mini'] : true;
		$use_mini_n = isset($options['use_mini_n']) ? $options['use_mini_n'] : false;
	}
	
	if (!$less_file or !$css_file) return; // не указаны файлы
	
	if ($use_cache) // проверка кэша
	{
		if (file_exists($less_file) and file_exists($css_file))
		{
			$flag_compiling = false; // флаг == true — требуется компиляция 
			$t_css = filemtime($css_file); // время css-файла
			
			
			// смотрим все файлы каталога
			$CI = & get_instance(); // подключение CodeIgniter
			$CI->load->helper('file_helper'); // хелпер для работы с файлами
			$all_files_in_dirs = get_filenames(dirname($less_file), true);

			foreach ($all_files_in_dirs as $file)
			{
				if (substr(strrchr($file, '.'), 1) !== 'less') continue; // проверка расширения файла
				
				if (filemtime($file) > $t_css) // файл старше css — нужна компиляция
				{
					$flag_compiling = true; // нужна компиляция
					break;
				}
			}
			
			if (!$flag_compiling) // можно отдать из кеша
			{
				if ($css_url) 
				{
					// в виде имени файла
					return NT . '<link rel="stylesheet" href="' . $css_url . '">';
				}
				else
				{
					// в виде содержимого
					return file_get_contents($css_file);
				}
			}
		}
	}
	
	if (file_exists($less_file)) $fc_all = file_get_contents($less_file);
		else return; // нет файла, выходим

	// проверка на разрешение записывать css-файл
	if (file_exists($css_file) and !is_writable($css_file)) 
	{
		// и что делать???
		return tf('LESS: результирующий css-файл не имеет разрешений на запись.'); 
		// die(tf('Нет возможности выполнить less-компиляцию: ') . $css_file); 
	}
	
	
	if ($fc_all)
	{
	
		require_once(getinfo('plugins_dir') . 'less_compiling/less/lessc.inc.php');

		$compiler = new lessc;
		
		// возможно есть php-файл для своих функций
		// строится как исходный + .php
		// пример http://leafo.net/lessphp/docs/#custom_functions
		if (file_exists($less_file . '.php')) require_once($less_file . '.php');
		
		// это общие custom_functions
		// их набор зависит от версии LESSPHP
		if ($fn = mso_fe(getinfo('plugins_dir') . 'less_compiling/less/custom_functions.php')) require($fn);
		
		$compiler->addImportDir(dirname($less_file)); // новый 0.3.7 api
		$compiler->indentChar = "\t";
		
		// для совметимости со старым вариантом — удалить в январе 2014!!!
		$fc_all = str_replace('@MSO_IMPORT_ALL_FONTS;', '@MSO_IMPORT_ALL(fonts);', $fc_all);
		$fc_all = str_replace('@MSO_IMPORT_ALL_MIXINS;', '@MSO_IMPORT_ALL(mixins);', $fc_all);
		$fc_all = str_replace('@MSO_IMPORT_ALL_BLOCKS;', '@MSO_IMPORT_ALL(blocks);', $fc_all);
		$fc_all = str_replace('@MSO_IMPORT_ALL_HELPERS;', '@MSO_IMPORT_ALL(helpers);', $fc_all);
		$fc_all = str_replace('@MSO_IMPORT_ALL_COMPONENTS;', '@MSO_IMPORT_ALL(components);', $fc_all);
		$fc_all = str_replace('@MSO_IMPORT_ALL_PLUGINS;', '@MSO_IMPORT_ALL(plugins);', $fc_all);
		$fc_all = str_replace('@MSO_IMPORT_ALL_TYPE;', '@MSO_IMPORT_ALL(type);', $fc_all);
		
		
		// универсальная конструкция: @MSO_IMPORT_ALL(каталог);
		
		$fc_all = preg_replace_callback('!(@MSO_IMPORT_ALL\()(.*?)(\);)!is', '_mso_less_import_all_callback', $fc_all);
		
		// в тексте исходного файла $fc_all может быть php-код
		ob_start();
		eval( '?>' . $fc_all . '<?php ');
		$fc_all = ob_get_contents();
		ob_end_clean();
		
		try
		{
			$out = $compiler->compile($fc_all); // новый 0.3.7 api
		}
		catch (Exception $ex) 
		{
			$out = _mso_less_exception($ex->getMessage(), $fc_all);
			die($out); // рубим, ибо нефиг писать с ошибками
		}
		
		// сжатие кода
		if ($use_mini)
		{
			if ($use_mini_n)
			{
				$out = str_replace("\t", ' ', $out);
				$out = str_replace(array("\r\n", "\r", "\n", '  ', '    '), '', $out);
			}
			
			$out = str_replace("\n\t", '', $out);
			$out = str_replace("\n}", '}', $out);
			$out = str_replace('; ', ';', $out);
			$out = str_replace(';}', '}', $out);
			$out = str_replace(': ', ':', $out);
			$out = str_replace('{ ', '{', $out);
			$out = str_replace(' }', '}', $out);
			$out = str_replace(' {', '{', $out);
			$out = str_replace(', ', ',', $out);
			$out = str_replace(' > ', '>', $out);		
			$out = str_replace('} ', '}', $out);
			$out = str_replace('  ', ' ', $out);
		}
		
		$fp = fopen($css_file, "w");
		fwrite($fp, $out);
		fclose($fp);
		
		if ($css_url) 
		{
			return NT . '<link rel="stylesheet" href="' . $css_url . '">'; // в виде имени файла
		}
		else
		{
			// в виде содержимого
			return $out;
		}
	}
}


# колбак функция для @MSO_IMPORT_ALL(каталог);
function _mso_less_import_all_callback($matches)
{
	$dir = trim($matches[2]);

	$files = mso_get_path_files(getinfo('template_dir') . 'css-less/' . $dir . '/', $dir . '/', true, array('less'));

	$m = '';
	foreach($files as $file)
	{
		// $m .= '@import url(\'' . $file . '\'); '; // старый вариант
		$m .= '/* ================== ' . $file . ' ================== */' . NR . NR;
		$m .= file_get_contents(getinfo('template_dir') . 'css-less/' . $file) . NR;
		
	}
		
	return $m;
}


# функция, срабатывающая при ошибке компиляции LESS
function _mso_less_exception($message, $text)
{
	// пробуем оформить более внятный вывод ошибки с исходным кодом
	
	$out = '<pre style="color: red;">lessphp fatal error: ' . $message . '</pre>';
	
	$text = NR . htmlspecialchars($text);
	$text = str_replace("\n", "<li style='margin:0 0 0 30px'>", $text);
	
	$out .= '<ol style="height: 500px; overflow: scroll; background: #eee; font-family: monospace; ">' . $text . '</ol>';
	
	return $out;
}

# end file