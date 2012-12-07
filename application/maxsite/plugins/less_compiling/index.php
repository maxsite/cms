<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function less_compiling_autoload()
{
	mso_hook_add( 'init', 'less_compiling_init'); # хук на init
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
						
			'files' => array(
							'type' => 'textarea', 
							'name' => t('Файлы'), 
							'description' => t('Формат (разделитель | ) <pre>+ | файл.less | файл.css | mini cache</pre> <b>+</b> или <b>-</b> Включение или отключение строчки<br><b>файл.less</b> - исходный файл (путь задается относительно каталога /maxsite/)<br><b>файл.css</b> - конечный файл (путь задается относительно каталога /maxsite/)<br>опции через пробел: <b>mini/nomini</b> - сжимать, <b>cache/nocache</b> - использовать кэш.<br>По-умолчанию используется сжатие и кэширование<br><br>Пример:<pre>+ | plugins/my/style.less | plugins/my/style.css | cache mini</pre><br>Результирующий css-файл должен иметь права, разрешающие его перезапись и/или создание (обычно 666).'),
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
			if ($row[0] !== '+') continue; // должен быть +
			
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


# end file