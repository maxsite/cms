<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	# опции редактора в виде массива
	$_options = array(
			
			# сортировка меток
			'tags_sort' => array(
							'type' => 'select', 
							'values' => t('0||По количеству записей (обратно) # 1||По количеству записей # 2||По алфавиту # 3||По алфавиту (обратно)'),
							'name' => t('Сортировка меток'), 
							'description' => t('Используется для отображения облака меток'),
							'default' => '0'
						),
			
			# количество меток
			'tags_count' => array(
							'type' => 'text', 
							'name' => t('Количество меток'), 
							'description' => t('Используется для отображения облака меток'),
							'default' => '20'
						),
						
			# разрешить комментарии
			'comment_allow_checked' => array(
							'type' => 'select', 
							'values' => '1||Отмечать # 0||Не отмечать',
							'name' => t('Разрешить комментирование'), 
							'description' => t('Отмечать опцию «Разрешить комментирование» по умолчанию'),
							'default' => '1'
						),	
						
			# разрешить rss 
			'feed_allow_checked' => array(
							'type' => 'select', 
							'values' => '1||Отмечать # 0||Не отмечать',
							'name' => t('Разрешить публикацию RSS'), 
							'description' => t('Отмечать опцию «Публикация в RSS» по умолчанию'),
							'default' => '1'
						),
						
			# Вывод изображения по умолчанию
			'image_for_page_out_default' => array(
							'type' => 'select', 
							'values' => '||Показывать везде # page||Показывать только на странице записи # no-page||Не показывать на странице записи # no||Не показывать',
							'name' => t('Вывод изображения'), 
							'description' => t('Вывод изображения записи по умолчанию'),
							'default' => ''
						),			
						
			# Высота визуального редактора
			'editor_height' => array(
							'type' => 'text', 
							'name' => t('Высота текстового редактора'), 
							'description' => t('Укажите высоту редактора в пикселах. Значение по-умолчанияю 400'),
							'default' => '400'
						),
			
			# Высота блока рубрик
			'cat_height' => array(
							'type' => 'text', 
							'name' => t('Высота блока рубрик'), 
							'description' => t('Укажите максимальную высоту блока рубрик в пикселах. При превышении этого значения, появятся полосы скроллинга. Если указать 0, то высота не ограничивается. Значение по-умолчанияю - 0.'),
							'default' => '0'
						),			

			'preview' => array(
							'type' => 'checkbox', 
							'name' => t('Предварительный просмотр записи разместить под текстовым редактором'), 
							'description' => 'Иначе просмотр будет выведен в новом окне браузера', 
							'default' => '0'
						),
						
			'previewautorefresh' => array(
							'type' => 'checkbox', 
							'name' => t('Автоматическое обновление предпросмотра записи'), 
							'description' => t('Обновление произойдет при нажатии Enter'), 
							'default' => '0'
						),			
						
			# скрывать ли блоки	
			'temp' => array(
							'type' => 'info',
							'title' => t('Отображение блоков', 'plugins'),
						),
						
			'page_status' => array(
							'type' => 'checkbox', 
							'name' => t('Отображать блок статуса страницы'), 
							'description' => '', 
							'default' => '1'
						),
			'page_files' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать ссылку на Загрузки', 
							'description' => '', 
							'default' => '1'
						),			
			'page_all_parent' => array(
							'type' => 'checkbox', 
							'name' => 'Отображать блок родительской страницы', 
							'description' => '', 
							'default' => '1'
						),
	);
	
	
	# если нужно подключить свои опции используйте хук editor_options
	$_options = mso_hook('editor_options', $_options);
		
	# отображение опций
	mso_admin_plugin_options('editor_options', 'admin', 
		$_options,
		t('Настройки редактора'), // титул
		t('Выберите нужные опции редактора'), // инфо
		false,
		false
	);

# end of file