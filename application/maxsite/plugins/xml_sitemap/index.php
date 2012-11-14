<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Добавления Илья Земсков <vimruler@gmail.com>
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

# функция выполняется при деактивации (выкл) плагина
function xml_sitemap_to_hook_deactivate($args = array())
{	
	mso_delete_option('plugin_xml_sitemap', 'plugins'); # удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function xml_sitemap_uninstall($args = array())
{
	mso_remove_allow('xml_sitemap_to_hook_edit'); # удалим созданные разрешения
	return $args;
}

# функция плагина
function xml_sitemap_custom($args = array())
{
	/* Настройки по-умолчанию */
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
	
	if(!isset($options['freq_priority'])) 
	{
		$options['freq_priority'] = 'home | daily | 1 ' . NR
									. 'notblog | monthly | 0.7 ' . NR
									. 'blog | weekly | 0.5 ' . NR
									. 'category | weekly | 0.3 ' . NR
									. 'tag | weekly | 0.3 ' . NR
									. 'comuser | weekly | 0.3 ' . NR
									. 'user | weekly | 0.3 ';
	}
	
	$fp = explode(NR, trim($options['freq_priority']));
	
	$options['freq_priority'] = array();
	
	foreach($fp as $ln)
	{
		$params = array_map('trim', explode('|', trim($ln)));
		$options['freq_priority'][$params[0]] = array(	'changefreq' =>$params[1],
														'priority' => $params[2]);
	}
		
	// создание sitemap.xml
	
	// $t = "\t"; // табулятор для отступа
	$t = ''; // отступ для красоты
		
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
	
	
	$out = '<'
	. '?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
<loc>' . $url . '</loc>
<lastmod>' . date('Y-m-d') . 'T' . date('H:i:s') . $time_zone . '</lastmod>
<changefreq>' . $options['freq_priority']['home']['changefreq'] . '</changefreq>
<priority>' . $options['freq_priority']['home']['priority'] . '</priority>
</url>
';

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
		
		

	// страницы не blog
	$CI->db->select('page.page_id, page_slug, page_date_publish');
	
	if(count($options['page_cats_hide']) > 0) 
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where_not_in('category_id', $options['page_cats_hide']);
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
	
	$query = $CI->db->get('page');
	
	if ($query->num_rows()>0)
	{
		foreach ($query->result_array() as $row)
		{
			$date = str_replace(' ', 'T', $row['page_date_publish']) . $time_zone;
			
			$out .= $t . '<url>' . NR;
			$out .= $t . $t . '<loc>' . $url . 'page/' . $row['page_slug'] . '</loc>' . NR;
			$out .= $t . $t . '<lastmod>' . $date . '</lastmod>' . NR;
			$out .= $t . $t . '<changefreq>'.$options['freq_priority']['notblog']['changefreq'].'</changefreq>' . NR;
			$out .= $t . $t . '<priority>'.$options['freq_priority']['notblog']['priority'].'</priority>' . NR;
			$out .= $t . '</url>' . NR;
		}
	}
		
	// страницы
	$CI->db->select('page.page_id, page_slug, page_date_publish');
	
	if(count($options['page_cats_hide']) > 0) 
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where_not_in('category_id', $options['page_cats_hide']);
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
	
	$query = $CI->db->get('page');
	
	if ($query->num_rows()>0)
	{
		foreach ($query->result_array() as $row)
		{
			$date = str_replace(' ', 'T', $row['page_date_publish']) . $time_zone;
			
			$out .= $t . '<url>' . NR;
			$out .= $t . $t . '<loc>' . $url . 'page/' . $row['page_slug'] . '</loc>' . NR;
			$out .= $t . $t . '<lastmod>' . $date . '</lastmod>' . NR;
			$out .= $t . $t . '<changefreq>' . $options['freq_priority']['blog']['changefreq'] . '</changefreq>' . NR;
			$out .= $t . $t . '<priority>' . $options['freq_priority']['blog']['priority'] . '</priority>' . NR;
			$out .= $t . '</url>' . NR;
		}
	}
	
	// рубрики
	if(count($options['categories_show']) > 0) $CI->db->or_where_in('category_id', $options['categories_show']);
	$CI->db->where('category_type', 'page');
	
	$query = $CI->db->get('category');
	
	if ($query->num_rows()>0)
	{
		$date = date('Y-m-d') . 'T' . date('H:i:s') . $time_zone;
		
		foreach ($query->result_array() as $row)
		{
			// $date = str_replace(' ', 'T', date('Y-m-d')) . $time_zone;
			
			$out .= $t . '<url>' . NR;
			$out .= $t . $t . '<loc>' . $url . 'category/' . $row['category_slug'] . '</loc>' . NR;
			$out .= $t . $t . '<lastmod>' . $date . '</lastmod>' . NR;
			$out .= $t . $t . '<changefreq>' . $options['freq_priority']['category']['changefreq'] . '</changefreq>' . NR;
			$out .= $t . $t . '<priority>'.$options['freq_priority']['category']['priority'] . '</priority>' . NR;
			$out .= $t . '</url>' . NR;
		}
	}		
	
	// все метки
	if($options['tags_show'])
	{
		require_once( getinfo('common_dir') . 'meta.php' );
		
		$alltags = mso_get_all_tags_page();
		
		foreach ($alltags as $tag => $count) 
		{
			$out .= $t . '<url>' . NR;
			$out .= $t . $t . '<loc>' . $url . 'tag/' . htmlentities(urlencode($tag)) . '</loc>' . NR;
			$out .= $t . $t . '<lastmod>' . $date . '</lastmod>' . NR;
			$out .= $t . $t . '<changefreq>' . $options['freq_priority']['tag']['changefreq'] . '</changefreq>' . NR;
			$out .= $t . $t . '<priority>' . $options['freq_priority']['tag']['priority'] . '</priority>' . NR;
			$out .= $t . '</url>' . NR;
		}
	}
		
	// и все комюзеры
	if($options['comusers_show'])
	{
		$CI->db->select('comusers_id');
		
		$query = $CI->db->get('comusers');
		
		if ($query->num_rows() > 0)	
		{	
			foreach ($query->result_array() as $row)
			{
				$out .= $t . '<url>' . NR;
				$out .= $t . $t . '<loc>' . $url . 'users/' . $row['comusers_id'] . '</loc>' . NR;
				$out .= $t . $t . '<lastmod>' . $date . '</lastmod>' . NR;
				$out .= $t . $t . '<changefreq>'.$options['freq_priority']['comuser']['changefreq'] . '</changefreq>' . NR;
				$out .= $t . $t . '<priority>'.$options['freq_priority']['comuser']['priority'] . '</priority>' . NR;
				$out .= $t . '</url>' . NR;
			}
		}
	}
		
	// и все юзеры
	if($options['users_show'])
	{
		$CI->db->select('users_id');
		
		$query = $CI->db->get('users');
		
		if ($query->num_rows() > 0)	
		{	
			foreach ($query->result_array() as $row)
			{
				$out .= $t . '<url>' . NR;
				$out .= $t . $t . '<loc>' . $url . 'author/' . $row['users_id'] . '</loc>' . NR;
				$out .= $t . $t . '<lastmod>' . $date . '</lastmod>' . NR;
				$out .= $t . $t . '<changefreq>' . $options['freq_priority']['user']['changefreq'] . '</changefreq>' . NR;
				$out .= $t . $t . '<priority>' . $options['freq_priority']['user']['priority'] . '</priority>' . NR;
				$out .= $t . '</url>' . NR;
			}
		}
	}
		
	$out .= mso_hook('xml_sitemap'); # хук, если нужно добавить свои данные
	
	$out .= NR . '</urlset>' . NR;
	
	$fn = getinfo('FCPATH') . 'sitemap.xml';
	write_file($fn, $out);

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
                            'name' => t('Исключить страницы'), 
                            'description' => t('Перечислите через запятую ID страниц, которые не будут добавлены в sitemap.xml'), 
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
						

            ),
		t('Настройки XML Sitemap'), # Заголовок страницы с настройками плагина
		
		t('C помощью настроек плагина можно настроить частоту и приоритет обновления информации о страницах, а также убрать вывод ненужных адресов страниц в файле <b>sitemap.xml</b> и тем самым уменьшить количество сообщений об ошибках индексации в панелях вебмастера в поисковых системах («<a href="http://webmaster.yandex.ru/">Яндекс.Вебмастер</a>» и «<a href="https://www.google.com/webmasters/tools/home?hl=ru">Инструменты для веб-мастеров в Google</a>»).')  // инфа
    );
	
	if ($_POST) xml_sitemap_custom();
}

# end file