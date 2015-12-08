<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# Диспетчер опций шаблона
# Сами опции вынесены в ini-файлы каталога options шаблона
# поддерживаются custom/my_options.ini и custom/my_options.php

echo '<h1>' . t('Настройка шаблона') . ' «'. getinfo('template_name') . '»</h1>';
// echo '<p class="info">' . t('Выберите необходимые опции') . '</p>';

// функции для работы с ini-файлом
require_once( getinfo('common_dir') . 'inifile.php' );

// проверка на обновление POST
if (mso_check_post_ini()) echo '<div class="update">' . t('Обновлено!') . '</div>';


$options = array();

// если переменная = true, то подгружаем дефолтные опции из shared/options/default/ 
if (mso_get_val('get_options_default', true))
{
	// получим список всех файлов из options
	$files = mso_get_path_files(getinfo('shared_dir') . 'options/default/', getinfo('shared_dir') . 'options/default/', true, array('ini'));

	foreach($files as $file)
	{
		$add = mso_get_ini_file($file);
		$options = array_merge($options, $add);
	}
	
}

// получим список всех файлов из options шаблона
$files = mso_get_path_files(getinfo('template_dir') . 'options/', getinfo('template_dir') . 'options/', true, array('ini'));

foreach($files as $file)
{
	$add = mso_get_ini_file($file);
	$options = array_merge($options, $add);
}

if (file_exists(getinfo('template_dir') . 'custom/my_options.php')) 
{
	require(getinfo('template_dir') . 'custom/my_options.php');
}

if (file_exists(getinfo('template_dir') . 'custom/my_options.ini'))
{
	$add = mso_get_ini_file( getinfo('template_dir') . 'custom/my_options.ini'); // и свой
	$options = array_merge($options, $add);
}

// подключим все опции компонентов из components
// в них ini-файлы, а также php-файлы, обслуживающие ini (для PHP_START PHP_END)
// поэтому подключаем все php-файлы, после все ini-файлы
// подключаем только те опции и ini компонентов, которые реально существуют

// каждый компонент в своем каталоге
$all_component =  mso_get_dirs(getinfo('template_dir') . 'components/', array(), true);

// проверяем опции (options.php)
foreach($all_component as $dir) 
{
	$file = getinfo('template_dir') . 'components/' . $dir . '/options.php';
	if (file_exists($file)) require($file); // php-файлы
}

// проверяем options.ini
foreach($all_component as $dir) 
{
	$file = getinfo('template_dir') . 'components/' . $dir . '/options.ini';
	
	if (file_exists($file))
	{
		$add = mso_get_ini_file($file);
		
		// чтобы позволить испльзовать в компонентах одинаковые названия опций,
		// добавим к названию имя компонета в скобках
		$a1 = array();
		
		foreach($add as $key => $val)
		{
			$a1[$key . ' (' . $dir . ')'] = $val; 
		}
		
		$options = array_merge($options, $a1);
	}
}

// вывод всех ini-опций
echo mso_view_ini($options);

# end file