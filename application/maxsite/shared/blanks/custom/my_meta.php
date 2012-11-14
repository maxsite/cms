<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# файл, который подключается к дополнительным полям записи
# в отличие от my_meta.ini здесь можно сформировать массив мета, 
# которые нужно присоединить к общему $all
#
# Массив опций можно посмотреть pr($all);
# Добавить свой через array_merge().
# формат ключей совпадает с правилами ini-файлов


/*

// Пример

// задаем массив полей
$my_meta = array(
	'Моё поле 1' => array(
			'options_key' => 'my_field_1',
			'type' => 'textfield',
			'description' => 'Описание поля',
			'default' => '',
	),
	
	'Моё поле 2' => array(
			'options_key' => 'my_field_2',
			'type' => 'textarea',
			'description' => 'Описание поля',
			'default' => '',
	),
	
	'Моё поле 3' => array(
			'options_key' => 'my_field_3',
			'type' => 'radio',
			'values' => 'пункт 1 # пункт 2 # пункт 3',
			'description' => 'Описание поля',
			'default' => 'пункт 1',
			'delimer' => ' &nbsp;&nbsp;&nbsp;',
	),
	
	'Моё поле 4' => array(
			'options_key' => 'my_field_4',
			'type' => 'select',
			'values' => 'пункт 1 # пункт 2 # пункт 3',
			'description' => 'Описание поля',
			'default' => 'пункт 2',
	),
	
	'Моё поле 5' => array(
			'options_key' => 'my_field_5',
			'type' => 'checkbox',
			'description' => 'Описание поля',
			'default' => '0',
	),
	
);

// объединяем с $all
$all = array_merge($all, $my_meta);

*/

/*
# можно удалить любое метаполе, например:

if (isset($all['Ключевые слова страницы (keywords)']))
{
	unset($all['Ключевые слова страницы (keywords)']);
}
	
*/