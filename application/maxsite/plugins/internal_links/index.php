<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function internal_links_autoload()
{
	mso_hook_add('content_content', 'internal_links_custom');
}

# функция выполняется при активации (вкл) плагина
function internal_links_activate($args = array())
{	
	mso_create_allow('internal_links_edit', t('Админ-доступ к настройкам') . ' ' . t('Internal links'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function internal_links_uninstall($args = array())
{	
	mso_delete_option('plugin_internal_links', 'plugins' ); // удалим созданные опции
	mso_remove_allow('internal_links_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function internal_links_mso_options() 
{
	if ( !mso_check_allow('internal_links_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_internal_links', 'plugins', 
		array(
			'links' => array(
							'type' => 'textarea', 
							'rows' => 20,
							'name' => t('Ключевые фразы и их ссылки'), 
							'description' => t('Укажите в формате: <strong>фраза | ссылка | css-класс ссылки</strong><br>Располагайте большие фразы выше мелких, чтобы не было пересечений.'), 
							'default' => ''
						),
			'default_class' => array(
							'type' => 'text', 
							'name' => t('CSS-класс по-умолчанию'), 
							'description' => t('Этот класс будет подставляться для всех ссылок по-умолчанию.'), 
							'default' => ''
						),
			'only_page_type' => array(
							'type' => 'checkbox', 
							'name' => t('Выполнять замены только на одиночных страницах'), 
							'description' => t('На всех остальных страницах сайта замены выполняться не будут'), 
							'default' => '1'
						),
			'max_count' => array(
							'type' => 'text', 
							'name' => t('Максимальное количество ссылок одной фразы в тексте'), 
							'description' => t('Если указать «0», то будут выделены все вхождения.'), 
							'default' => '1'
						),			
			),
		t('Настройки плагина «Внутренние ссылки»'), 
		t('Плагин позволяет выполнить автоматическую замену указанных слов на ссылки.')
	);
	
}



# функции плагина
function internal_links_custom($text = '')
{
	static $a_link; // здесь хранится обработанный массив ссылок - чтобы не обрабатывать несколько раз
	
	global $_internal_links;
	
	$options = mso_get_option('plugin_internal_links', 'plugins', array());
	
	// только на page
	if (!isset($options['only_page_type'])) $options['only_page_type'] = true;
	if ($options['only_page_type'] and !is_type('page')) return $text;
	
	// не указаны ссылки
	if (!isset($options['links'])) return $text; 
	if (!trim($options['links'])) return $text;
	

	if (!isset($options['default_class'])) $options['default_class'] = '';
	if (!isset($options['max_count'])) $options['max_count'] = 1;
		else $options['max_count'] = (int) $options['max_count'];
	if ($options['max_count'] === 0) $options['max_count'] = -1; // замена для preg_replace
	
	$links = explode("\n", str_replace("\r", '', trim($options['links']))); // все ссылки в массив
	
	if (!isset($a_link) or !$a_link)
	{
		$a_link = array();
		foreach ($links as $key => $link)
		{
			$l1 = explode('|', $link);
			
			if ( isset($l1[0]) and isset($l1[1]) ) // фраза | ссылка
			{
				$a_link[$key]['word'] = trim($l1[0]);
				$a_link[$key]['link'] = trim($l1[1]);
				
				if (strpos($a_link[$key]['link'], 'http://') === false)
					$a_link[$key]['link'] = getinfo('siteurl') . $a_link[$key]['link'];
				
				if ( isset($l1[2]) and trim($l1[2]) ) // class
				{
					$a_link[$key]['class'] = trim($l1[2]);
				}
				else
				{
					$a_link[$key]['class'] = trim($options['default_class']);
				}
			}
		}
	}
	
	$current_url = getinfo('siteurl') . mso_current_url(false);

	$limit = $options['max_count'];
	
	foreach ($a_link as $key)
	{
		$word = $key['word'];
		$link = $key['link'];
		
		if ($link == $current_url) continue; // ссылка на себя 
		
		if (mb_stripos($text, $word, 0, 'UTF8') === false) continue; // нет вхождения
		
		if ($key['class']) $class = ' class="' . $key['class']. '"';
			else $class = '';
		
		$regexp = '/(?!(?:[^<\[]+[>\]]|[^>\]]+<\/a>))(' . preg_quote($word, '/') . ')/usUi';
		
		$replace = "<a href=\"" . $link . "\"" . $class . ">\$0</a>";
		
		$text = preg_replace($regexp, $replace , $text, $limit);

	}
	
	
	// pr($text,1);
	return $text;
}



# end file