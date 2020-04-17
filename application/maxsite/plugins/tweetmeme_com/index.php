<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function tweetmeme_com_autoload()
{
	if (!is_feed() and (is_type('page') or is_type('home'))) 
		mso_hook_add('content_content', 'tweetmeme_com_content'); # хук на вывод контента
}


# функция выполняется при деинсталяции плагина
function tweetmeme_com_uninstall()
{	
	mso_delete_option('plugin_tweetmeme_com', 'plugins' ); // удалим созданные опции
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function tweetmeme_com_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_tweetmeme_com', 'plugins', 
		array(
			'align' => array(
						'type' => 'select', 
						'name' => t('Выравнивание блока'), 
						'description' => t('Укажите выравнивание блока. Он добавляется в начало каждой записи.'),
						'values' => t('left||Влево # right||Вправо # none||Нет'),
						'default' => 'right'
					),
			'show_only_page' => array(
						'type' => 'select', 
						'name' => t('Отображение'), 
						'description' => t('Выводить ли блок только на одиночной странице'),
						'values' => t('1||Отображать только на одиночной странице # 0||Везде'),
						'default' => '0'
					),
			'page_type' => array(
						'type' => 'text', 
						'name' => t('Тип страниц'), 
						'description' => t('Выводить блок только на указанных типах страниц (типы указывать через запятую).'),
						'default' => 'blog, static'
					),
						
			'style' => array(
						'type' => 'text', 
						'name' => t('Стиль блока'), 
						'description' => t('Укажите свой css-стиль блока.'), 
						'default' => ''
					),

			
			'twitter_data-count' => array(
						'type' => 'select', 
						'name' => t('Вид блока'), 
						'description' => t('Расположение «Tweet» и статистики'),
						'values' => t('vertical||Вертикальное расположение # horizontal||Горизонтальное расположение # none || Не отображать количество твиттов'),
						'default' => 'vertical'
					),
					
			'twitter_data-via' => array(
						'type' => 'text',
						'name' => t('Добавлять в RT «via @ваш-логин»'), 
						'description' => t('Укажите свой логин в Твиттере, который будет добавляться в текст ретрива.'),
						'default' => ''
					),		
					
					
										
			),
		t('Настройки плагина tweetmeme.com'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

# функции плагина
function tweetmeme_com_content($text = '')
{
	if (!is_type('page') and !is_type('home')) return '. ' . $text;
	
	$pageData = mso_get_val('mso_pages', 0, true);

	// если запись не опубликована, не отображаем блок
	if (is_type('page') and isset($pageData['page_status']) and $pageData['page_status'] != 'publish') return $text;
	
	$options = mso_get_option('plugin_tweetmeme_com', 'plugins', array() ); // получаем опции
	
	// отображать только на одиночной странице
	if (!isset($options['show_only_page'])) $options['show_only_page'] = 0; 
	if ($options['show_only_page'] and !is_type('page')) return $text;
	
	if (is_type('page') and isset($options['page_type']) and $options['page_type'])
	{
		$p_type_name = mso_explode($options['page_type'], false);
		
		// нет у указанных типах страниц
		if (!in_array($pageData['page_type_name'], $p_type_name)) return $text;
	}
	
	// стиль выравнивания
	if (!isset($options['style'])) $options['style'] = '';
	if (!isset($options['align'])) $options['align'] = 'right';
	
	if ($options['style']) $style = ' style="' . $options['style'] . '"';
		else
		{
			if ($options['align'] == 'left') $style = ' style="float: left; margin-right: 10px;"';
			elseif ($options['align'] == 'right') $style = ' style="float: right; margin-left: 10px; width: "';
			else $style = '';
		}
	
	// блок выводится с оригинального twitter.com
	/*
	if (is_type('home')) 
	{
		$url = getinfo('site_url') . 'page/' . $pageData['page_slug'];
	}
	else
	{
		$url = mso_current_url(true);
	}
	*/
	$url = mso_current_url(true);
	
	if (!isset($options['twitter_data-count'])) $options['twitter_data-count'] = 'vertical';
	$options['twitter_data-count'] = ' data-count="' . $options['twitter_data-count'] . '" ';
	
	if (!isset($options['twitter_data-via'])) $options['twitter_data-via'] = '';
	if ($options['twitter_data-via']) $options['twitter_data-via'] = ' data-via="' . $options['twitter_data-via'] . '" ';
		
	if ($pageData) {
		$text = '<div class="tweetmeme_com"' . $style . '>' 
		. '<a rel="nofollow" href="https://twitter.com/share" class="twitter-share-button" data-url="' . $url . '"' 
		. $options['twitter_data-count'] 
		. ' data-text="' . $pageData['page_title'] . '" '
		. $options['twitter_data-via']
		. '>Tweet</a>
		<script src="https://platform.twitter.com/widgets.js"></script>' 
		. '</div>' . $text;
	}
	
	return $text;
}


# end file