<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Вы можете использовать этот файл в своем шаблона вместо index.php
 * В этом файле реализована автоматическое подключение type-файлов,
 * а также неизвестных page_404 по первому сегменту.
 * 
 */
 
 
	# глобальное кэширование выполняется на уровне хука при наличии соответствующего плагина
	# если хук вернул true, значит данные выведены из кэша, то есть выходим
	if (mso_hook('global_cache_start', false)) return;
	
	# можно изменить язык шаблона
	// $MSO->language = 'en';
	
	# для rss используются другие шаблоны
	if (is_feed())
	{
		if (is_type('page')) $mso_type_file = 'feed-page'; 				// только комментарии к странице
		elseif (is_type('comments')) $mso_type_file = 'feed-comments';	// все комментарии
		elseif (is_type('category')) $mso_type_file = 'feed-category'; 	// по рубрикам
		else $mso_type_file = 'feed-home'; 								// все страницы
		
		$fn1 = getinfo('template_dir') . 'type/' . $mso_type_file . '.php'; 		 // путь в шаблоне
		$fn2 = getinfo('templates_dir') . 'default/type/' . $mso_type_file . '.php'; // путь в default
		
		if ( file_exists($fn1) ) require($fn1); // если есть, подключаем шаблонный
		elseif (file_exists($fn2)) require($fn2); // нет, значит дефолтный
			
		exit; // выходим
	}

	# подключаем нужные библиотеки - они используются почти везде
	require_once(getinfo('common_dir') . 'page.php'); 	// функции страниц 
	require_once(getinfo('common_dir') . 'category.php'); // функции рубрик
	
	# в зависимости от типа данных подключаем нужный файл
	$mso_type_file = getinfo('type'); // текущий тип 
	$mso_type_file_404 = false; // признак page_404
	
	# на page_404 может быть свой хук. Тогда ничего не подключаем
	if ($mso_type_file == 'page_404' and mso_hook_present('custom_page_404') and mso_hook('custom_page_404')) $mso_type_file = false;
	elseif ($mso_type_file == 'page_404') $mso_type_file_404 = mso_segment(1); // страница не найдена, попробуем найти по сегменту
	
	# анализ сегментов URL, где переопределяется файл типа
	if ($mso_type_file == 'users')
	{
		if (mso_segment(3)=='edit')	$mso_type_file = 'users-form'; 			// редактирование комюзера
		elseif (mso_segment(3)=='lost') $mso_type_file = 'users-form-lost';	// восстановление пароля комюзера
		elseif (mso_segment(2)=='') $mso_type_file = 'users-all';			// список всех комюзеров
	}
	
	# служебные файлы в type
	if (   
		   $mso_type_file_404 == 'feed-category'
		or $mso_type_file_404 == 'feed-comments'
		or $mso_type_file_404 == 'feed-home'
		or $mso_type_file_404 == 'feed-page'
		or $mso_type_file_404 == 'home-cat-block'
		or $mso_type_file_404 == 'page-comment-form'
		or $mso_type_file_404 == 'page-comments'
		or $mso_type_file_404 == 'users-all'
		or $mso_type_file_404 == 'users-form'
		or $mso_type_file_404 == 'users-form-lost'
		)
	{
		$mso_type_file = 'page_404';
		$mso_type_file_404 = false;
	}
	
	
	if ($mso_type_file !== false) // указан файл?
	{
		$fn1 = getinfo('template_dir') . 'type/' . $mso_type_file . '.php'; 		 // путь в шаблоне
		$fn2 = getinfo('templates_dir') . 'default/type/' . $mso_type_file . '.php'; // путь в default
		
		# страница page_404, попробуем её найти по сегменту
		if ($mso_type_file_404) 
		{
			# файлы не найдены, пробуем подключить файл по первому сегменту
			$fn3 = getinfo('template_dir') . 'type/' . $mso_type_file_404 . '.php'; 		 // путь в шаблоне
			$fn4 = getinfo('templates_dir') . 'default/type/' . $mso_type_file_404 . '.php'; // путь в default
			
			if (file_exists($fn3)) require($fn3); // шаблонный по сегменту
			elseif (file_exists($fn4)) require($fn4); // дефолтный по сегменту
			elseif ( file_exists($fn1) ) require($fn1); // шаблонный по типу
			elseif (file_exists($fn2)) require($fn2); // дефолтный по типу
		}
		else
		{
			if ( file_exists($fn1) ) require($fn1); // шаблонный по типу
			elseif (file_exists($fn2)) require($fn2); // дефолтный по типу
		}
	}
	
	# хук глобального кэша
	mso_hook('global_cache_end');
