<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Добавления Илья Земсков <ilya@vizr.ru>
 */

/*
	приоритеты и обновление:
	главная - 1 daily
	страница (не blog) - 0.7 monthly
	запись (blog) - 0.5 weekly
	рубрика - 0.3 weekly
*/

# функция автоподключения плагина
function xml_sitemap_autoload($args = array())
{
	mso_hook_add('edit_category', 'xml_sitemap_custom');
	mso_hook_add('new_category', 'xml_sitemap_custom');
	mso_hook_add('delete_category', 'xml_sitemap_custom');
	mso_hook_add('new_page', 'xml_sitemap_custom');
	mso_hook_add('edit_page', 'xml_sitemap_custom');
}

# функция выполняется при активации (вкл) плагина
function xml_sitemap_activate($args = array())
{	
	mso_create_allow('xml_sitemap_to_hook_edit', t('Админ-доступ к настройкам', 'plugins') . ' «' . t('XML Sitemap') . '»');
	xml_sitemap_custom();
	return $args;
}

# функция выполняется при деинсталяции плагина
function xml_sitemap_uninstall($args = array())
{
	mso_delete_option('plugin_xml_sitemap', 'plugins'); # удалим созданные опции
	mso_remove_allow('xml_sitemap_to_hook_edit'); # удалим созданные разрешения
	return $args;
}

# функция плагина - создание sitemap.xml
function xml_sitemap_custom($args = array())
{
	// Настройки по-умолчанию
	$options = mso_get_option('plugin_xml_sitemap', 'plugins', array());

	if(!isset($options['page_hide'])) $options['page_hide'] = '';
	$options['page_hide'] = mso_explode($options['page_hide']);
	
	if(!isset($options['page_cats_hide'])) $options['page_cats_hide'] = '';
	$options['page_cats_hide'] = mso_explode($options['page_cats_hide']);
	
	if(!isset($options['categories_show'])) $options['categories_show'] = '';
	$options['categories_show'] = mso_explode($options['categories_show']);
	
	if(!isset($options['tags_show'])) $options['tags_show'] = true;
	if(!isset($options['comusers_show'])) $options['comusers_show'] = true;
	if(!isset($options['users_show'])) $options['users_show'] = true;
	
	if(!isset($options['url_protocol'])) $options['url_protocol'] = '';

	$freq_priority = array(
		'home' => array( 'changefreq' => 'daily', 'priority' => '1' ),
		'notblog' => array( 'changefreq' => 'monthly', 'priority' => '0.7' ),
		'blog' => array( 'changefreq' => 'weekly', 'priority' => '0.5' ),
		'category' => array( 'changefreq' => 'weekly', 'priority' => '0.3' ),
		'tag' => array( 'changefreq' => 'weekly', 'priority' => '0.3' ),
		'comuser' => array( 'changefreq' => 'weekly', 'priority' => '0.3' ),
		'user' => array( 'changefreq' => 'weekly', 'priority' => '0.3' ),
	);
	if( isset($options['freq_priority']) && $options['freq_priority'] ) 
	{
		$fp = explode(NR, trim($options['freq_priority']));
		foreach($fp as $ln)
		{
			$params = array_map('trim', explode('|', trim($ln)));
			$freq_priority[$params[0]] = array('changefreq' => $params[1], 'priority' => $params[2]);
		}
	}
	$options['freq_priority'] = $freq_priority;

	if(!isset($options['custom_urls'])) $options['custom_urls'] = '';
	$options['custom_urls'] = array_map('trim', explode(NR, trim($options['custom_urls'])));

	if(!isset($options['sitemap-mode'])) $options['sitemap-mode'] = 'simple';
	if(!isset($options['sitemap-index-links']) or $options['sitemap-index-links'] == '') $options['sitemap-index-links'] = 1000;
	if($options['sitemap-index-links'] > 50000 ) $options['sitemap-index-links'] = 5000; # Лимит описан в протоколе на sitemas.org

	$sitemap = array(); # Массив для предварительного накопления ссылок

	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
		
	// временная зона сайта в формат +03:00 из 3.00
	$time_zone = getinfo('time_zone'); // 3.00 -11.00;
	$znak = ( (int) $time_zone >= 0) ? '+' : '-';
	$time_zone = abs($time_zone);
	if ($time_zone == 0) $time_zone = '0.0';
	$time_zone = trim( str_replace('.', ' ', $time_zone) );
	$time_z = explode(' ', $time_zone);
	if (!isset($time_z[0])) $time_z[0] = '0';
	if (!isset($time_z[1])) $time_z[1] = '0';
	if ($time_z[0] < 10) $time_z[0] = '0' . $time_z[0];
	if ($time_z[1] < 10) $time_z[1] = '0' . $time_z[1];
	$time_zone = $znak . $time_z[0] . ':' . $time_z[1];
		
	$url = getinfo('siteurl');

	if($options['url_protocol'])
	{
		$url = str_replace('http://', '', $url);
		$url = str_replace('https://', '', $url);
		$url = $options['url_protocol'] . $url;
	}

	#
	# Добавляем главную страницу (home)
	#
	$sitemap[] = array(
			'loc' 		=> $url,
			'lastmod'	=> date('Y-m-d').'T'.date('H:i:s').$time_zone,
			'changefreq'	=> $options['freq_priority']['home']['changefreq'],
			'priority'	=> $options['freq_priority']['home']['priority'],
			);
	
	#
	# Добавляем записи
	#
	// временная зона для запросов с page_date_publish
	$tz = getinfo('time_zone');
	if ($tz < 10 and $tz > 0) $tz = '0' . $tz;
	elseif ($tz > -10 and $tz < 0) 
	{ 
		$tz = '0' . $time_zone; 
		$tz = str_replace('0-', '-0', $time_zone); 
	}
	else $tz = '00.00';
	$tz = str_replace('.', ':', $tz);
		
	// страницы notblog
	$CI->db->select('page.page_id, page_slug, page_date_publish');
	$CI->db->from('page');

	if(count($options['page_cats_hide']) > 0) 
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where('( category_id NOT IN ('.implode(',', $options['page_cats_hide']).') or category_id IS NULL )');
	}
	
	if(count($options['page_hide']) > 0) 
	{
		$CI->db->where_not_in('page.page_id', $options['page_hide']);
	}
	
	$CI->db->where('page_type_name !=', 'blog');
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $tz . '" HOUR_MINUTE)', false);
	//$CI->db->where('page_date_publish <', mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s')));
	
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->order_by('page_date_publish', 'desc');
	$CI->db->group_by('page.page_id');
	
	$query = $CI->db->get();
	if ($query->num_rows()>0)
	{
		foreach ($query->result_array() as $row)
		{
			$date = str_replace(' ', 'T', $row['page_date_publish']) . $time_zone;
			
			$sitemap[] = array(
						'loc' 		=> $url.'page/'.$row['page_slug'],
						'lastmod'	=> $date,
						'changefreq'	=> $options['freq_priority']['notblog']['changefreq'],
						'priority'	=> $options['freq_priority']['notblog']['priority'],
					);
		}
	}
		
	// страницы blog
	$CI->db->select('page.page_id, page_slug, page_date_publish');
	$CI->db->from('page');
	
	if(count($options['page_cats_hide']) > 0) 
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where('( category_id NOT IN ('.implode(',', $options['page_cats_hide']).') or category_id IS NULL )');
	}
	
	if(count($options['page_hide']) > 0) 
	{
		$CI->db->where_not_in('page.page_id', $options['page_hide']);
	}
	
	$CI->db->where('page_type_name', 'blog');
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $tz . '" HOUR_MINUTE)', false);
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->group_by('page.page_id');
	$CI->db->order_by('page_date_publish', 'desc');
	$query = $CI->db->get();
	if ($query->num_rows()>0)
	{
		foreach ($query->result_array() as $row)
		{
			$date = str_replace(' ', 'T', $row['page_date_publish']) . $time_zone;

			$sitemap[] = array(
						'loc' 		=> $url.'page/'.$row['page_slug'],
						'lastmod'	=> $date,
						'changefreq'	=> $options['freq_priority']['blog']['changefreq'],
						'priority'	=> $options['freq_priority']['blog']['priority'],
					);
		}
	}

	// единая дата-время обновления урла
	$date = date('Y-m-d').'T'.date('H:i:s').$time_zone; 

	#
	# Добавляем рубрики
	#
	if(count($options['categories_show']) > 0) $CI->db->or_where_in('category_id', $options['categories_show']);
	$CI->db->where('category_type', 'page');
	
	$query = $CI->db->get('category');
	
	if ($query->num_rows()>0)
	{
		foreach ($query->result_array() as $row)
		{
			// $date = str_replace(' ', 'T', date('Y-m-d')) . $time_zone;

			$sitemap[] = array(
						'loc' 		=> $url.'category/'.$row['category_slug'],
						'lastmod'	=> $date,
						'changefreq'	=> $options['freq_priority']['category']['changefreq'],
						'priority'	=> $options['freq_priority']['category']['priority'],
					);
		}
	}		
	
	#
	# Добавляем метки
	#
	if($options['tags_show'])
	{		
		$alltags = mso_get_all_tags_page();
		
		foreach ($alltags as $tag => $count) 
		{
			$sitemap[] = array(
						'loc' 		=> $url.'tag/'.htmlentities(urlencode($tag)),
						'lastmod'	=> $date,
						'changefreq'	=> $options['freq_priority']['tag']['changefreq'],
						'priority'	=> $options['freq_priority']['tag']['priority'],
					);
		}
	}
	
	#
	# Добаляем комюзеров
	#	
	if($options['comusers_show'])
	{
		$CI->db->select('comusers_id');
		
		$query = $CI->db->get('comusers');
		
		if ($query->num_rows() > 0)	
		{	
			foreach ($query->result_array() as $row)
			{
				$sitemap[] = array(
							'loc' 		=> $url.'users/'.$row['comusers_id'],
							'lastmod'	=> $date,
							'changefreq'	=> $options['freq_priority']['comuser']['changefreq'],
							'priority'	=> $options['freq_priority']['comuser']['priority'],
						);
			}
		}
	}
		
        #
	# Добавялем юзеров (авторов)
	#
	if($options['users_show'])
	{
		$CI->db->select('users_id');
		
		$query = $CI->db->get('users');
		
		if ($query->num_rows() > 0)	
		{	
			foreach ($query->result_array() as $row)
			{
				$sitemap[] = array(
							'loc' 		=> $url.'author/'.$row['users_id'],
							'lastmod'	=> $date,
							'changefreq'	=> $options['freq_priority']['user']['changefreq'],
							'priority'	=> $options['freq_priority']['user']['priority'],
						);
			}
		}
	}
		
	#
	# Добавляем кастомные урлы
	#
	if($options['custom_urls'])
	{
		if( !isset($options['freq_priority']['notblog']['changefreq']) ) $options['freq_priority']['notblog']['changefreq'] = 'monthly';
		if( !isset($options['freq_priority']['notblog']['priority']) ) $options['freq_priority']['notblog']['priority'] = '0.7';
			
		foreach ($options['custom_urls'] as $url)
		{
			if( $url )
			{
				$url = parse_url($url, PHP_URL_PATH);
				$url = substr($url, 0, 1) == '/' ? substr($url, 1) : $url;
				$url = getinfo('site_url').$url;
				
				$sitemap[] = array(
							'loc' 		=> $url,
							'lastmod'	=> $date,
							'changefreq'	=> $options['freq_priority']['notblog']['changefreq'],
							'priority'	=> $options['freq_priority']['notblog']['priority'],
						);
			}
		}
	}
	
	// формирование sitemap.xml
	$out = '';

	if($sitemap)
	{
		if($options['sitemap-mode'] == 'simple' or ($options['sitemap-index-links'] and $options['sitemap-index-links'] > count($sitemap))) # простой вариант или если общее количество ссылок меньше 
		{
			$out .= '<?xml version="1.0" encoding="UTF-8"?>'.NR.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.NR;

			foreach($sitemap as $url)
			{
				$out .= '<url>'.NR;
				$out .= '<loc>'.$url['loc'].'</loc>'.NR;
				$out .= '<lastmod>'.$url['lastmod'].'</lastmod>'.NR;
				$out .= '<changefreq>'.$url['changefreq'].'</changefreq>'.NR;
				$out .= '<priority>'.$url['priority'].'</priority>'.NR;
				$out .= '</url>'.NR;
			}

			$out .= mso_hook('xml_sitemap'); # хук, если нужно добавить свои данные

			$out .= '</urlset>'.NR;

			file_put_contents(getinfo('FCPATH').'sitemap.xml', $out); # Сохраняем полный файл
		}
		else # вариант с разбиением sitemap.xml на части
		{
			$i = 1; 	# счётчик частей
			$indx = '';     # контент индексного файла
			$part = array();# массив ссылок для одной части sitemap
			
			do {
				$part = array_splice( $sitemap, 0, $options['sitemap-index-links'] ); # вырезаем из массива часть

				if($part) # Если массив ссылок не пуст, то записываем очередную часть sitemap.xml
				{
					# формируем содержимое "частичного" sitemap.xml
					$content = '<?xml version="1.0" encoding="UTF-8"?>'.NR.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.NR;
					foreach( $part as $k => $url )
					{
						$link = '';

						$link .= '<url>'.NR;
						$link .= '<loc>'.$url['loc'].'</loc>'.NR;
						$link .= '<lastmod>'.$url['lastmod'].'</lastmod>'.NR;
						$link .= '<changefreq>'.$url['changefreq'].'</changefreq>'.NR;
						$link .= '<priority>'.$url['priority'].'</priority>'.NR;
						$link .= '</url>'.NR;

						$content .= $link;
						$out .= $link;
					}
				
					$content .= '</urlset>'.NR;
	
					file_put_contents(getinfo('FCPATH').'sitemap-'.$i.'.xml', $content);

					# Формируем содержимое индексного sitemap-файла
					$indx .= '<sitemap>'.NR.'<loc>'.getinfo('siteurl').'sitemap-'.$i.'.xml</loc>'.NR.'<lastmod>'.date('Y-m-d').'T'.date('H:i:s').$time_zone.'</lastmod>'.NR.'</sitemap>'.NR;

					if($sitemap) $i++;
				}

			} while($part);

			# Сохраняем индексный sitemap-файл
			if($indx)
			{
				$content = '<?xml version="1.0" encoding="UTF-8"?>'.NR.'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.NR.$indx.'</sitemapindex>'.NR;
				file_put_contents(getinfo('FCPATH').'sitemap.xml', $content); # Сохраняем индексный файл
			}

			if($options['sitemap-full'] == 'yes')
			{
				$out = mso_hook('xml_sitemap_conv', $out); # хук, если нужно как-то обработать результирующий файл. Например, разбить на части

				$out = '<?xml version="1.0" encoding="UTF-8"?>'.NR.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.NR.$out.'</urlset>'.NR;
			
				file_put_contents(getinfo('FCPATH').'sitemap-full.xml', $out); # Сохраняем полный файл
			}

		}
	}
		
	return $args; // для обеспечения цепочки хуков
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function xml_sitemap_mso_options()
{
	if(!mso_check_allow('xml_sitemap_to_hook_edit'))
	{
		echo t('Доступ запрещен');
		return;
	}
	
    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_xml_sitemap', 'plugins', 
        array(
			'freq_priority' => array(
							'type' => 'textarea', 
							'name' => 'Приоритеты и частота обновления', 
							'description' => t('Укажите значения параметров <b>changefreq</b> и <b>priority</b> для разных групп страниц сайта по формату: Группа страниц | changefreq | priority<br>Допустимые группы страниц: home, notblog, blog, category, tag, comusers, users<br>Допустимые значения changefreq: always, hourly, daily, weekly, monthly, yearly, never<br>Допустимы значения priority: от 0.0 до 1.0'),
							'default' =>	'home | daily | 1' . NR .
									'notblog | monthly | 0.7' . NR . 
									'blog | weekly | 0.5' . NR .
									'category | weekly | 0.3' . NR.
									'tag | weekly | 0.3' . NR .
									'comuser | weekly | 0.3' . NR .
									'user | weekly | 0.3',
						),

			'tags_show' => array(
                            'type' => 'checkbox', 
                            'name' => t('Добавить страницы меток'),
                            'description' => '', 
                            'default' => 0,
							'group_start' => '<hr>',
						),
						
            'comusers_show' => array(
                            'type' => 'checkbox', 
                            'name' => t('Добавить страницы комюзеров (комментаторов)'), 
                            'description' => '', 
                            'default' => 0
                        ),
						
            'users_show' => array(
                            'type' => 'checkbox', 
                            'name' => t('Добавить страницы авторов'), 
                            'description' => '', 
                            'default' => 0,
							'group_end' => '<hr>',
                        ),
			
            'page_hide' => array(
                            'type' => 'text', 
                            'name' => t('Исключить страницы (записи)'), 
                            'description' => t('Перечислите через запятую ID записей, которые <b>не будут</b> добавлены в sitemap.xml'), 
                            'default' => ''
                        ),
						
            'page_cats_hide' => array(
                            'type' => 'text', 
                            'name' => t('Скрывать страницы из рубрик'), 
                            'description' => t('Перечислите через запятую ID рубрик, страницы из которых не попадут в sitemap.xml'), 
                            'default' => ''
                        ),
						
            'categories_show' => array(
                            'type' => 'text', 
                            'name' => t('Добавить страницы рубрик'), 
                            'description' => t('Перечислите через запятую ID рубрик, страницы которых будут добавлены в sitemap.xml. Оставьте поле пустым, если нужно добавить страницы всех имеющихся рубрик'), 
                            'default' => ''
                        ),
						
			'custom_urls' => array(
                            'type' => 'textarea', 
                            'name' => 'Кастомные адреса для добавления', 
                            'description' => t('Укажите (по одному в каждой строке) абсолютные или относительные адреса нестандартных страниц или файлов (например, DOCX-файл из папки UPLOADS) сайта, которые необходимо добавить в sitemap.xml. Для этих адресов будет использованы настройки приоритета и частоты от <b>notblog</b>.'),
                            'default' => '',
			),
						
			'url_protocol' => array(
                            'type' => 'select', 
                            'name' => t('HTTP протокол сайта'), 
                            'values' => '||Не менять # http://||http # https://||https',
                            'description' => t('Можно явно задать протокол сайт.'), 
                            'default' => ''
                        ),
				
			'sitemap-mode' => array(
                            'type' => 'select', 
                            'name' => t('Режим формирования sitemap.xml'), 
                            'values' => 'simple||Формировать единый файл#complex||Разбивать на части',
                            'description' => t('Выберите режим, который будет задавать принцип формирования файла sitemap.xml. Разбиение на части реализовано согласно <a href="https://www.sitemaps.org/ru/protocol.html#index" target=_blank>описанному протоколу</a>.'), 
                            'default' => 'simple'
                        ),

			'sitemap-index-links' => array(
				'type' => 'text',
				'name' => 'Количество ссылок в каждой части индексного sitemap.xml',
				'description' => 'Если sitemap.xml разбивается на части, то в данном поле можно задать количество ссылок, которые попадут в кажду часть. Максимум для одной части - 50000 ссылок.',
				'default' => '1000'
			),
						
			'sitemap-full' => array(
                            'type' => 'select', 
                            'name' => t('Сохранять единый файл при режиме разбиения sitemap.xml на части?'), 
                            'values' => 'yes||Да#no||Нет',
                            'description' => t('Для отладки и других работ может потребоваться версия sitemap.xml без разбиения на части. Если выбрать вариант "ДА", то будет сохраняться единый файл с именем sitemap-full.xml.'), 
                            'default' => 'no'
                        ),

            ),
		t('Настройки XML Sitemap'), # Заголовок страницы с настройками плагина
		
		t('C помощью настроек плагина можно настроить частоту и приоритет обновления информации о страницах, а также убрать вывод ненужных адресов страниц в файле <b>sitemap.xml</b> и тем самым уменьшить количество сообщений об ошибках индексации в панелях вебмастера в поисковых системах («<a href="http://webmaster.yandex.ru/">Яндекс.Вебмастер</a>» и «<a href="https://www.google.com/webmasters/tools/home?hl=ru">Инструменты для веб-мастеров в Google</a>»).')  // инфа
    );
		
	if ($_POST) xml_sitemap_custom();
}

# end of file