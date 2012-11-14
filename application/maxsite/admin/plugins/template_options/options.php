<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

?>

<h1><?= t('Настройка шаблона', 'templates') . ' «'. getinfo('template_name') . '»' ?></h1>
<p class="info"><?= t('Выберите необходимые опции', 'templates') ?></p>

<?php

	// функции для работы с ini-файлом
	require_once( getinfo('common_dir') . 'inifile.php' );

	// проверка на обновление POST
	if (mso_check_post_ini()) echo '<div class="update">' . t('Обновлено!', 'templates') . '</div>';
	
	// получим ini-файл
	$all = mso_get_ini_file( getinfo('templates_dir') . 'default/options.ini'); // можно использовать дефолтный
	
	if (file_exists(getinfo('template_dir') . 'options.ini'))
	{
		$all_add = mso_get_ini_file( getinfo('template_dir') . 'options.ini'); // и свой
		$all = array_merge($all, $all_add);
	}
	
	if (file_exists(getinfo('template_dir') . 'options-template.ini'))
	{
		$all_add = mso_get_ini_file( getinfo('template_dir') . 'options-template.ini'); // и свой
		$all = array_merge($all, $all_add);
	}
	
	if (file_exists(getinfo('template_dir') . 'custom/my_options.php')) 
	{
		require(getinfo('template_dir') . 'custom/my_options.php');
	}

	if (file_exists(getinfo('template_dir') . 'custom/my_options.ini'))
	{
		$all_add = mso_get_ini_file( getinfo('template_dir') . 'custom/my_options.ini'); // и свой
		$all = array_merge($all, $all_add);
	}
	
	
	// подключим все опции компонентов в components/options
	// в них ini-файлы, а также php-файлы, обслуживающие ini (для PHP_START PHP_END)
	// поэтому подключаем все php-файлы, после все ini-файлы
	// подключаем только те опции и ini компонентов, которые реально существуют
	
	// все компоненты
	$all_component = get_path_files(getinfo('template_dir') . 'components/', getinfo('template_dir') . 'components/', true, array('php'));
	
	// проверяем опции
	foreach($all_component as $file) 
	{
		$file = str_replace('/components/', '/components/options/', $file);
		
		if (file_exists($file)) require($file); // php-файлы
	}
	
	// проверяем ini в options
	foreach($all_component as $file) 
	{
		$file = str_replace('/components/', '/components/options/', $file);
		$file = str_replace('.php', '.ini', $file);
		
		if (file_exists($file))
		{
			$all_add = mso_get_ini_file($file); // css-файлы
			$all = array_merge($all, $all_add);
		}
	}

	// вывод всех ini-опций
	echo mso_view_ini($all);

?>