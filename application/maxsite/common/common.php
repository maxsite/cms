<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Основные функции
 */

# подключаем библиотеку mbstring
# какие функции отсутствуют определяется в этом файле

global $mso_install;
if ($mso_install and !function_exists('mb_strlen') ) require('mbstring.php');


define("NR", "\n"); // перенос строки
define("NR2", "\n\n"); // двойной перенос строки
define("TAB", "\t"); // табулятор
define("NT", "\n\t"); // перенос + табулятор


# получение нужного значения
function getinfo($info = '')
{
	global $MSO;

	$out = '';

	switch ($info) :
		case 'version' :
				$out = $MSO->version;
				break;

		case 'site_url' :
		case 'siteurl' :
				$out = $MSO->config['site_url'];
				break;

		case 'stylesheet_url' :
		case 'template_url': 
				$out = $MSO->config['templates_url']
						. $MSO->config['template']
						. '/';
				break;
				
		case 'template' :
				$out = $MSO->config['template'];
				break;

		case 'template_dir' :
				$out = $MSO->config['templates_dir'] . $MSO->config['template'] . '/';
				break;
		
		case 'template_name' :
				$fn_info = $MSO->config['templates_dir'] . $MSO->config['template'] . '/info.php';
				if (file_exists($fn_info))
				{
					require($fn_info);
					return $info['name'];
				}
				else $out = '';
				break;

		case 'templates_dir' :
				$out = $MSO->config['templates_dir'];
				break;
				
		case 'templates_url' :
				$out = $MSO->config['templates_url'];
				break;

		case 'url_new_comment' :
				$out = $MSO->config['site_url'] . 'newcomment';
				break;

		case 'pingback_url' :

				break;

		case 'rss_url' :
		case 'feed' :
				$out = $MSO->config['site_url'] . 'feed';
				break;
		
		case 'rss_comments_url' :
				$out = $MSO->config['site_url'] . 'comments/feed';
				break;

		case 'atom_url' :

				break;

		case 'comments_rss2_url' :
				$out = $MSO->config['site_url'] . 'comments/feed';
				break;

		case 'admin_url' :
				$out = $MSO->config['admin_url']; 
				// [admin_url] => http://localhost/application/maxsite/admin/
				break;
						
		case 'admin_dir' :
				$out = $MSO->config['admin_dir'];
				break;
				
		case 'site_admin_url' :
				$out = $MSO->config['site_admin_url']; // [site_admin_url] => http://localhost/admin/
				break;

		case 'common_dir' :
				$out = $MSO->config['common_dir'];
				break;

		case 'common_url' :
				$out = $MSO->config['common_url'];
				break;

		case 'uploads_url' :
				$out = $MSO->config['uploads_url'];
				break;

		case 'uploads_dir' :
				$out = $MSO->config['uploads_dir'];
				break;

		case 'users_nik' :
				if (isset($MSO->data['session']['users_nik']))
					$out = $MSO->data['session']['users_nik'];
				else $out = '';
				break;

		case 'users_id' :
				if (isset($MSO->data['session']['users_id']))
					$out = $MSO->data['session']['users_id'];
				else $out = '';
				break;
				
		case 'comusers_id' :
				if (isset($MSO->data['session']['comuser']['comusers_id']))
					$out = $MSO->data['session']['comuser']['comusers_id'];
				else $out = '';
				break;
		
		case 'comusers_nik' :
				if (isset($MSO->data['session']['comuser']['comusers_nik']))
					$out = $MSO->data['session']['comuser']['comusers_nik'];
				else $out = '';
				break;
				
				
		case 'name_site' :
				$out = htmlspecialchars(mso_get_option('name_site', 'general'));
				break;

		case 'description_site' :
				$out = mso_get_option('description_site', 'general');
				break;

		case 'title' :
				$out = mso_get_option('title', 'general');
				break;
		
		case 'title_current' : // текущий титул
				$out = $MSO->title;
				break;
		
		case 'description' :
				$out = htmlspecialchars(mso_get_option('description', 'general'));
				break;

		case 'keywords' :
				$out = htmlspecialchars(mso_get_option('keywords', 'general'));
				break;

		case 'time_zone' :
				$out = (string) mso_get_option('time_zone', 'general');
				break;

		case 'plugins_url' :
				$out = $MSO->config['plugins_url'];;
				break;

		case 'plugins_dir' :
				$out = $MSO->config['plugins_dir'];;
				break;

		case 'ajax' :
				$out = $MSO->config['site_url'] . 'ajax/';
				break;
				
		case 'require-maxsite' :
				$out = $MSO->config['site_url'] . 'require-maxsite/';
				break;
				
		case 'admin_plugins_dir' :
				$out = $MSO->config['admin_plugins_dir'];
				break;

		case 'session' :
				$out = $MSO->data['session'];
				break;

		case 'remote_key' :
				$out = $MSO->config['remote_key'];
				break;

		case 'uri_get' :
				$out = $MSO->data['uri_get'];
				break;

				
		case 'cache_dir' :
				$out = $MSO->config['cache_dir'];
				break;

		case 'FCPATH' :
				$out = $MSO->config['FCPATH'];
				break;
				
		case 'type' :
				$out = $MSO->data['type'];
				break;
				
		case 'type_foreach_file':
				$out = isset($MSO->data['type_foreach_file']) ? $MSO->data['type_foreach_file'] : '';
				break;
		
		case 'shared_dir' :
				$out = $MSO->config['base_dir'] . 'shared/';
				break;
		
		case 'shared_url' :
				$out = $MSO->config['base_url'] . 'shared/';
				break;
				
	endswitch;

	return $out;
}


#  функция для отладки
function pr($var, $html = false, $echo = true)
{
	if (!$echo) ob_start();
		else echo '<pre>';
	if (is_bool($var))
	{
		if ($var) echo 'TRUE';
			else echo 'FALSE';
	}
	else
	{
		if ( is_scalar($var) )
		{
			if (!$html) echo $var;
				else
				{
					$var = str_replace('<br />', "<br>", $var);
					$var = str_replace('<br>', "<br>\n", $var);
					$var = str_replace('</p>', "</p>\n", $var);
					$var = str_replace('<ul>', "\n<ul>", $var);
					$var = str_replace('<li>', "\n<li>", $var);
					$var = htmlspecialchars($var);
					$var = wordwrap($var, 300);
					echo $var;
				}
		}
			else print_r ($var);
	}
	if (!$echo)
	{
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	else echo '</pre>';
}


# функция, аналогичная pr, только завершающаяся die() 
# используется для отладки с помощью прерывания
function _pr($var, $html = false, $echo = true)
{
	pr($var, $html, $echo);
	die();
}


# функция, формирующая sql-запрос
# используется для отладки перед $CI->db->get()
function _sql()
{
	$CI = & get_instance();
	$sql = $CI->db->_compile_select();
	return $sql;
}


#  правильность email
function mso_valid_email($address = '')
{
	# из helpers/email_helper.php
	return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
}


# проверем рефер на xss-атаку
# работает только если есть POST
function mso_checkreferer()
{
	if ($_POST)
	{
		if (!isset($_SERVER['HTTP_REFERER'])) die('<b><font color="red">Achtung! XSS attack! No REFERER!</font></b>');

		$ps = parse_url($_SERVER['HTTP_REFERER']);
		
		if (isset($ps['host'])) $p = $ps['host'];
			else $p = '';
		
		if ($p and isset($ps['port']) and $ps['port'] != 80) $p .= ':' . $ps['port'];
		
		if ($p != $_SERVER['HTTP_HOST'])
			die('<b><font color="red">Achtung! XSS attack!</font></b>');
	}
}


# защита сессии
# сравниваем переданную сессию с текущей
# и если указан редирект, то в случае несовпадения переходим по нему
# иначе возвращаем true - если все ок и false - ошибка сессии
function mso_checksession($session_id, $redirect = false)
{
	global $MSO;

	$result = ($MSO->data['session']['session_id'] == $session_id );

	if ($redirect and !$result)
	{
		mso_redirect($redirect);
		return;
	}

	return $result;
}


# удаляем все лишнее в формах
# если второй параметр = true то возвращает false, если данные после стрипа изменились и $s - теже
function mso_strip($s = '', $logical = false, $arr_strip = array('\\', '|', '/', '?', '%', '*', '`', '<', '>'))
{
	$s1 = $s;
	$s1 = stripslashes($s1);
	$s1 = strip_tags($s1);
	$s1 = htmlspecialchars($s1, ENT_QUOTES);

	$s1 = str_replace($arr_strip, '', $s1);
	$s1 = trim($s1);

	if ($logical)
	{
		if ($s1 === $s) return $s;
			else return false;
	}
	else
		return $s1;
}


# функция инициализации
function mso_initalizing()
{
	global $MSO, $mso_install;
	$CI = & get_instance();
	
	
	# считываем файл конфигурации
	$fn = $MSO->config['config_file'];
	if ( file_exists($fn) ) require_once ($fn);

	// если кэш старый, то очищаем его
	// #ci1 $path = $CI->config->item('cache_path');
	
	$path = getinfo('cache_dir');
	
	
	// #ci1 $mso_cache_last = ($path == '') ? BASEPATH . 'cache/' . '_mso_cache_last.txt' : $path . '_mso_cache_last.txt';
	
	$mso_cache_last = $path . '_mso_cache_last.txt';
	
	if (file_exists($mso_cache_last))
	{
		$time = (int) trim(implode('', file($mso_cache_last)));
		$time = $time + $MSO->config['cache_time'] + 60; // запас + 60 секунд
		if (time() > $time) mso_flush_cache(); // время истекло - сбрасываем кэш
	}
	else // файла нет > _mso_cache_last.txt < создадим - наверное совсем старый кэш
	{
		mso_flush_cache();
	}

	# стоит ли флаг, что уже произведена инсталяция?

	if (!isset($mso_install) or $mso_install == false)
	{
		if ( !$CI->db->table_exists('options')) return false; # еще не установлен сайт
	}

	# подключаем опции - они могут быть в кэше
	global $cache_options;

	if ( $opt = mso_get_cache('options') ) # есть кэш опций
		$cache_options = $opt;
	else
		mso_refresh_options(); # обновляем кэш опций

	# проверим текущий шаблон
	$template = mso_get_option('template', 'general'); // считали из опций
	$index = $MSO->config['templates_dir'] . $template . '/index.php'; // проверим в реале

	if (!file_exists($index)) // нет такого шаблона - меняем на дефолтный
	{
		mso_add_option('template', 'default', 'general');
		$MSO->config['template'] = 'default';
	}
	else // все ок
		$MSO->config['template'] = $template;

	# проверяем залогинненость юзера
	if (!isset($CI->session->userdata['userlogged']) or !$CI->session->userdata['userlogged'] )
	{
		// не залогинен
		$CI->session->userdata['userlogged'] = 0;
	}
	else
	{
		// отмечено, что залогинен
		// нужно проверить верность данных юзера
		$CI->db->from('users'); # таблица users
		$CI->db->select('users_id, users_groups_id');
		$CI->db->limit(1); # одно значение

		$CI->db->where( array('users_login'=>$CI->session->userdata['users_login'],
							  'users_password'=>$CI->session->userdata['users_password']) );

		$query = $CI->db->get();

		if (!$query or $query->num_rows() == 0) # нет такого - возможно взлом
		{
			$CI->session->sess_destroy(); // убиваем сессию
			$CI->session->userdata['userlogged'] = 0; // отмечаем, что не залогинен
		}
		else
		{
			// есть что-то
			$row = $query->row();
			// сразу выставим группу
			$MSO->data['session']['users_groups_id'] = $row->users_groups_id;
		}
	}
	
	# сразу обновляем время последней активности сессии
	# раньше было только для users, теперь делаем для всех
	# при этом сохраняем предыдущее значение
	# это значение позволяет отследить периодичность действий посетителя
	if (isset($CI->session->userdata['last_activity']))
	{
		$CI->session->set_userdata('last_activity_prev', $CI->session->userdata['last_activity']);
		$CI->session->set_userdata('last_activity', time());
	}
	else
	{
		$CI->session->set_userdata('last_activity_prev', time());
		$CI->session->set_userdata('last_activity', $CI->session->userdata['last_activity_prev']);
	}


	// аналогично проверяем и комюзера, только данные из куки
	// но при этом сразу сохраняем все данные комюзера, чтобы потом не обращаться к БД

	$comuser = mso_get_cookie('maxsite_comuser', false);
	if ($comuser)
	{
		$comuser = unserialize($comuser);
		/*
		[comusers_id] => 1
		[comusers_password] => 037035235237852
		[comusers_email] => max-3000@list.ru
		[comusers_nik] => Максим
		[comusers_url] => http://maxsite.org/
		[comusers_avatar_url] => http://maxsite.org/avatar.jpg
		*/
		// нужно сверить с тем, что есть

		$CI->db->select('comusers_id, comusers_password, comusers_email');
		$CI->db->where('comusers_id', $comuser['comusers_id']);
		$CI->db->where('comusers_password', $comuser['comusers_password']);
		$CI->db->where('comusers_email', $comuser['comusers_email']);
		$query = $CI->db->get('comusers');
		if ($query->num_rows()) // есть такой комюзер
		{
			$CI->session->userdata['comuser'] = $comuser;
		}
		else // неверные данные
		{
			$CI->session->userdata['comuser'] = 0;
		}
	}
	else $CI->session->userdata['comuser'] = 0;


	# дефолтные хуки
	mso_hook_add('init', '_mso_require_functions_file'); // подключение functions.php текущего шаблона
	mso_hook_add('content_auto_tag', 'mso_auto_tag'); // авторасстановка тэгов
	mso_hook_add('content_balance_tags', 'mso_balance_tags'); // автозакрытие тэгов - их баланс
}


# подключим файл functions.php в шаблоне - если есть
# функиця срабатывает по хуку init
function _mso_require_functions_file($args = '')
{
	global $MSO;
	
	$functions_file = $MSO->config['templates_dir'] . $MSO->config['template'] . '/functions.php';
	if (file_exists($functions_file))
	{
		require_once($functions_file);
	}
	
	return $args;
}


# проверка залогиннености юзера
function is_login()
{
	global $MSO;
	return ($MSO->data['session']['userlogged'] == 1) ? true : false;
}


# проверка залогиннености комюзера
# если есть, то возвращает массив данных
function is_login_comuser()
{
	global $MSO;

	if (isset($MSO->data['session']['comuser']) and ($comuser = $MSO->data['session']['comuser']) ) return $comuser;
		else return false;
}


# загружаем включенные плагины
function mso_autoload_plugins()
{
	global $MSO;

	// функция mso_autoload_custom может быть в mso_config.php
	if ( function_exists('mso_autoload_custom') ) mso_autoload_custom();

	$d = mso_get_option('active_plugins', 'general');
	if (!$d)
	{
		$d = $MSO->active_plugins;
	}

	foreach ($d as $load) 
	{
		mso_plugin_load($load);
	}
}


# проверка типа страницы, который определился в контролере
function is_type($type)
{
	global $MSO;
	return ( $MSO->data['type'] == $type ) ? true : false;
}


# возвращает true или false при проверке $MSO->data['uri_segment'], то есть по сегментам URL
# где например [1] => page  [2] => about
# что означает type = page  slug=about
# http://localhost/page/about
# можно указать только тип или только slug
# тогда неуказанный параметр не учитывается (всегда true)
function is_type_slug($type = '', $slug = '')
{
	global $MSO;

	$rt = $rs = '';
	
	$type = urlencode($type);
	$slug = urlencode($slug);

	// тип
	if ($type and isset($MSO->data['uri_segment'][1]) ) $rt = $MSO->data['uri_segment'][1];

	// slug
	if ( $slug and isset($MSO->data['uri_segment'][2]) ) $rs = $MSO->data['uri_segment'][2];

	return ($rt == $type and $rs == $slug);
}


# проверяем рубрику у страницы
# если это page и есть указанная рубрика, то возвращаем true
# если это не page или нет указанной рубрики, то возвращаем false
# если $and_id = true , то ищем и по id
# если $and_name = true , то ищем и по category_name
function is_page_cat($slug = '', $and_id = true, $and_name = true)
{
	global $MSO, $page;

	if (!$slug) return false; // slug не указан
	if (!is_type('page')) return false; // тип не page
	if (!isset($page['page_categories_detail'])) return false; // нет информации о рубриках

	$result = false;

	// информация о slug, id и name в массиве $page['page_categories_detail']
	foreach($page['page_categories_detail'] as $id => $val)
	{
		if ( $val['category_slug'] == $slug ) $result = true; // slug совпал
		if ( !$result and $and_id and $id == $slug ) $result = true; // можно искать по $id
		if ( !$result and $and_name and $val['category_name'] == $slug ) $result = true; // category_name совпал

		if ($result) break;
	}
	return $result;
}


# проверка если feed
function is_feed()
{
	global $MSO;
	return $MSO->data['is_feed'] ? true : false;
}


# вывод html meta титла дескриптон или keywords страницы
function mso_head_meta($info = 'title', $args = '', $format = '%page_title%', $sep = '', $only_meta = false )
{
	// ошибочный info
	if ( $info != 'title' and $info != 'description' and $info != 'keywords') return '';


	if (mso_hook_present('head_meta')) // если есть хуки, то управление передаем им
	{
		return mso_hook('head_meta', array('info'=>$info, 'args'=>$args, 'format'=>$format, 'sep'=>$sep, 'only_meta'=>$only_meta));
	}

	global $MSO;

	// измененный для вывода титле хранится в $MSO->title description или keywords

	if (!$args) // нет аргумента - выводим что есть
	{
		if ( !$MSO->$info )	$out = $MSO->$info = getinfo($info);
		else $out = $MSO->$info;
	}
	else // есть аргументы
	{
		if (is_scalar($args)) $out = $args; // какая-то явная строка - отдаем её как есть
		else // входной массив - скорее всего это страница
		{
			// %page_title% %title% %category_name%
			// | это разделитель, который = $sep
			// pr($args);

			$category_name = '';
			$category_desc = '';
			$page_title = '';
			$users_nik = '';
			$title = getinfo($info);

			// if ( !$info ) $format = '%title%';

			// название рубрики
			if ( isset($args[0]['category_name']) ) 
			{
				//$category_name = htmlspecialchars($args[0]['category_name']);
				$category_name = $args[0]['category_name'];
				
				// по названию рубрики ищем её описание в $args[0]['page_categories_detail'][$id]['category_desc']
				if (isset($args[0]['page_categories_detail']))
				{
					foreach ($args[0]['page_categories_detail'] as $id => $val)
					{
						if ($args[0]['category_name'] === $val['category_name'] )
						{
							$category_desc = $val['category_desc'];
							break;
						}
					}
				}
				
				if (!$category_desc) $category_desc = $category_name; // если нет описания, то берем название
			}
			
			if ( isset($args[0]['page_title']) ) $page_title = $args[0]['page_title'];
			if ( isset($args[0]['users_nik']) ) $users_nik = $args[0]['users_nik'];

			// если есть мета, то берем её
			if ( isset($args[0]['page_meta'][$info][0]) and $args[0]['page_meta'][$info][0] )
			{
				if ( $only_meta ) $category_name = $category_desc = $title = $sep = '';
				$page_title = $args[0]['page_meta'][$info][0];

				if ( $info!='title') $title = $page_title;
			}
			else
			{
				// для страницы если не указаны свои keywords, попробуем указать из меток
				if ($info == 'keywords' and is_type('page') and isset($args[0]['page_meta']['tags']) and $args[0]['page_meta']['tags'] ) 
				{
					$page_title = implode(', ', $args[0]['page_meta']['tags']); // разбиваем массив меток в строку
				}
			}
			
			$arr_key = array( '%title%', '%page_title%',  '%category_name%', '%category_desc%', '%users_nik%', '|' );
			$arr_val = array( htmlspecialchars($title), htmlspecialchars($page_title), htmlspecialchars($category_name), htmlspecialchars($category_desc), htmlspecialchars($users_nik), $sep );
			//$arr_val = array( $title ,  $page_title, $category_name, $category_desc, $users_nik, $sep );
			
			$out = str_replace($arr_key, $arr_val, $format);
		}
	}

	// отдаем результат, сразу же указывая измененный $info в $MSO
	$out = $MSO->$info = trim($out);

	return $out;
}


# подключение плагина
function mso_plugin_load($plugin = '')
{
	global $MSO;

	$fn_plugin = $MSO->config['plugins_dir'] . $plugin . '/index.php';

	if ( !file_exists( $fn_plugin ) ) return false;
	else
	{
		
		//_mso_profiler_start($plugin);

		require_once ($fn_plugin);

		$auto_load = $plugin . '_autoload';
		if ( function_exists($auto_load) ) $auto_load();
		
		//_mso_profiler_end($plugin);

		# добавим плагин в список активных
		$MSO->active_plugins[] = $plugin;
		$MSO->active_plugins = array_unique($MSO->active_plugins);
		sort($MSO->active_plugins);

		return true;
	}
}


# подключение admin-плагина - выполняется только при входе в админку
function mso_admin_plugin_load($plugin = '')
{
	global $MSO;

	$fn_plugin = $MSO->config['admin_plugins_dir'] . $plugin . '/index.php';

	if ( !file_exists( $fn_plugin ) ) return false;
	else
	{
		require_once ($fn_plugin);

		$auto_load = $plugin . '_autoload';
		if ( function_exists($auto_load) ) $auto_load();

		return true;
	}
}


# подключение функции к хуку
# приоритет по-умолчанию 10.
# если нужно, чтобы хук сработал раньше всех, то ставим более 10
# если нужно сработать последним - ставим приоритет менее 10
# http://forum.max-3000.com/viewtopic.php?p=9550#p9550
function mso_hook_add($hook, $func, $priory = 10)
{
	global $MSO;

	$priory = (int) $priory;

	if ( $priory > 0 ) $MSO->hooks[$hook][$func] = $priory;
		else $MSO->hooks[$hook][$func] = 0;

	ksort($MSO->hooks[$hook]);
	arsort($MSO->hooks[$hook]);
}


# прописываем хук к admin_url_+hook
function mso_admin_url_hook($hook, $func, $priory = 0)
{
	// нельзя указывать хуки на зарезервированные адреса: ???
	$hook = strtolower($hook);
	$no_hook = array('');

	if ( !in_array($hook, $no_hook))
		mso_hook_add ('admin_url_' . $hook, $func, $priory = 0);
}


# выполнение хуков
# название хука - переменная для результата
function mso_hook($hook = '', $result = '', $result_if_no_hook = '_mso_result_if_no_hook')
{
	global $MSO;

	if ($hook == '') return $result;

	$arr = array_keys($MSO->hooks);

	if ( !in_array($hook, $arr) ) // если хука нет
	{
		if ($result_if_no_hook != '_mso_result_if_no_hook') // если указана $result_if_no_hook
			return $result_if_no_hook;
		else return $result;
	}

	//_mso_profiler_start('' .$hook, true);
	
	//$i = 1;
	foreach ( $MSO->hooks[$hook] as $func => $val)
	{
		//_mso_profiler_start('-- ' . $hook . ' - ' . $func . $i);
		
		if ( function_exists($func) ) $result = $func($result);
		
		//_mso_profiler_end('-- ' . $hook . ' - ' . $func . $i);
		// $i++;
	}
	
	//_mso_profiler_end('' . $hook);
	
	return $result;
}


# проверяет существование хука
function mso_hook_present($hook = '')
{
	global $MSO;
	
	if ($hook == '') return false;
	$arr = array_keys($MSO->hooks);
	if ( !in_array($hook, $arr) ) return false;
		else return true;
}


# удаляет из хука функцию
# если функция не указана, то удаляются все функции из хука
function mso_remove_hook($hook = '', $func = '')
{
	global $MSO;

	if ($hook == '') return false;

	$arr = array_keys($MSO->hooks);
	if ( !in_array($hook, $arr) ) return false; // хука нет

	if ($func == '') // удалить весь хук
	{
		unset($MSO->hooks[$hook]);
	}
	else
	{
		if ( !in_array($hook, $arr) ) return false; // нет такой функции
		unset($MSO->hooks[$hook][$func]);
	}
	return true;
}


# динамическое создание функции на хук
# тело функции дожно работать как нормальный php
# функция принимает только один аргумент $args
function mso_hook_add_dinamic( $hook = '', $func = '', $priory = 10)
{
	if ($hook == '') return false;
	if ($func == '') return false;

	$func_name = @create_function('$args', $func);

	return mso_hook_add( $hook, $func_name, $priory);
}


# генератор md5 свой
function mso_md5($t = '')
{
	global $MSO;

	if ($MSO->config['secret_key'])
		return strrev( md5($t . $MSO->config['secret_key']) );
	else
		return strrev( md5($t . $MSO->config['site_url']) );
}


# сброс кэша опций
function mso_refresh_options()
{
	global $cache_options;

	$CI = & get_instance();

	/*
	$cache_options =
		type = array (
				key  = value
				key1 = value2
				)
	*/
	$CI->db->cache_delete_all();

	$query = $CI->db->get('options');

	$cache_options = array();

	foreach ($query->result() as $row)
		$cache_options[$row->options_type][$row->options_key] = $row->options_value;

	mso_add_cache('options', $cache_options);

	return $cache_options;
}


# добавление в таблицу опций options
function mso_add_option($key, $value, $type = 'general')
{
	$CI = & get_instance();
	
	# если value массив или объект, то серилизуем его в строку
	if ( !is_scalar($value) ) $value = '_serialize_' . serialize($value);

	$data = array(
			'options_key'=>$key,
			'options_type'=>$type );


	# проверим есть ли уже такой ключ
	$CI->db->select('options_id');
	$CI->db->from('options');
	$CI->db->where($data);

	$query = $CI->db->get();

	if ($query->num_rows() > 0 ) # есть уже такой ключ, поэтому обновляем его значение
	{
		$CI->db->where($data);
		$data['options_value'] = $value;
		$CI->db->update('options', $data);
	}
	else # новый ключ
	{
		$data['options_value'] = $value;
		$CI->db->insert('options', $data);
	}

	mso_refresh_options(); # обновляем опции из базы

	return true;
}


# удаление в таблице опций options ключа с типом
function mso_delete_option($key, $type = 'general')
{
	$CI = & get_instance();

	$CI->db->limit(1);
	$CI->db->delete('options', array('options_key'=>$key, 'options_type'=>$type ));

	mso_refresh_options(); # обновляем опции из базы

	return true;
}


# удаление в таблице опций options ключа-маски с типом
# маска считается от начала, например mask*
function mso_delete_option_mask($mask, $type = 'general')
{
	$CI = & get_instance();

	$mask = str_replace('_', '/_', $mask);
	$mask = str_replace('%', '/%', $mask);

	$query = $CI->db->query('DELETE FROM ' . $CI->db->dbprefix('options') . ' WHERE options_type="' . $type . '" AND options_key LIKE "'. $mask . '%" ESCAPE "/"');

	mso_refresh_options(); # обновляем опции из базы

	return true;
}


# получение опции из кэша опций
function mso_get_option($key, $type = 'general', $return_value = false)
{
	global $cache_options;
	
	if ( isset($cache_options[$type][$key]) )
		$result = $cache_options[$type][$key];
	else
		$result = $return_value;

	// проверяем на сериализацию
	if (@preg_match( '|_serialize_|A', $result))
	{
		$result = preg_replace( '|_serialize_|A', '', $result, 1 );
		$result = @unserialize($result);
	}

	return $result;
}


# добавление float-опции
# float-опция - это файл из серилизованного текста в каталоге uploads
# аналог опций, хранящейся в отдельном файле/каталоге _mso_float
function mso_add_float_option($key, $value, $type = 'general', $serialize = true, $ext = '', $md5_key = true, $dir = '')
{
	$CI = & get_instance();

	if ($dir) $dir .= '/';

	$path = getinfo('uploads_dir') . '_mso_float/' . $dir;

	if ( ! is_dir($path) ) @mkdir($path, 0777); // нет каталога, пробуем создать

	if ( ! is_dir($path) OR ! is_writable($path)) return false; // нет каталога или он не для записи

	if ($md5_key) $path .= mso_md5($key . $type) . $ext;
		else $path .= $key . $type . $ext;

	if ( ! $fp = @fopen($path, 'wb') ) return false; // нет возможности сохранить файл

	if ($serialize)	$output = serialize($value);
		else $output = $value;

	flock($fp, LOCK_EX);
	fwrite($fp, $output);
	flock($fp, LOCK_UN);
	fclose($fp);
	@chmod($path, 0777);

	// возвращаем имя файла
	if ($md5_key) $return = '_mso_float/' . $dir . mso_md5($key . $type) . $ext;
		else $return = '_mso_float/' . $dir . $key . $type . $ext;

	return $return;
}


# получение данных из flat-опций
function mso_get_float_option($key, $type = 'general', $return_value = false, $serialize = true, $ext = '', $md5_key = true, $dir = '')
{
	$CI = & get_instance();

	if (!$key or !$type) return $return_value;

	if ($dir) $dir .= '/';

	if ($md5_key) $path = getinfo('uploads_dir') . '_mso_float/' . $dir . mso_md5($key . $type) . $ext;
		else $path = getinfo('uploads_dir') . '_mso_float/' . $dir . $key . $type . $ext;

	if ( file_exists($path))
	{
		if ( ! $fp = @fopen($path, 'rb')) return $return_value;

		flock($fp, LOCK_SH);

		$out = $return_value;
		if (filesize($path) > 0)
		{
			if ($serialize) $out = @unserialize(fread($fp, filesize($path)));
				else $out = fread($fp, filesize($path));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $out;
	}
	else return $return_value;
}


# удаление flat-опции если есть
function mso_delete_float_option($key, $type = 'general', $dir = '')
{
	$CI = & get_instance();

	if (!$key or !$type) return false;

	if ($dir) $dir .= '/';

	$path = getinfo('uploads_dir') . '_mso_float/' . $dir . mso_md5($key . $type);

	if ( file_exists($path))
	{
		@unlink($path);
		return true;
	}
	else return false;
}


# добавить кеш
# ключ, значение, время
# Функция взята из _write_cache output.php - немного переделанная
function mso_add_cache($key, $output, $time = false, $custom_fn = false)
{
	global $MSO;
	
	# если определен отдельный хук, то выполняем его
	if (mso_hook_present('mso_add_cache')) 
		return mso_hook('mso_add_cache', array(
		'key' => $key,
		'output' => $output,
		'time' => $time,
		'custom_fn' => $custom_fn
		));

	// если разрешено динамическое кэширование
	if (mso_get_option('cache_dinamic', 'general', 0))
	{
		// опции не сохраняем - у них свой кэш
		if ($key !== 'options') $MSO->cache[$key] = $output;
	}
	
	
	$CI = & get_instance();
	
	$cache_path = getinfo('cache_dir');

	if ( !is_dir($cache_path) or !is_writable($cache_path)) return;

	if (!$custom_fn)
		$cache_path .= mso_md5($key . $CI->config->item('base_url'));
	else
		$cache_path .= $key;

	if ( ! $fp = @fopen($cache_path, 'wb')) return;

	if (!$time) $time = $MSO->config['cache_time'];

	$expire = time() + $time;
	$output = serialize($output);

	flock($fp, LOCK_EX);
	fwrite($fp, $expire.'TS--->' . $output);
	flock($fp, LOCK_UN);
	fclose($fp);
	
	if (!is_writable($cache_path)) @chmod($cache_path, 0777);
	
}


# удаление файла в кэше файлов, начинающихся с указаной строки
function mso_flush_cache_mask($mask = '')
{
	if (!$mask) return;

	# если определен отдельный хук, то выполняем его
	if (mso_hook_present('mso_flush_cache_mask')) 
		return mso_hook('mso_flush_cache_mask', array('mask' => $mask));
	
	$CI = & get_instance();

	$cache_path = getinfo('cache_dir');	

	if ( ! is_dir($cache_path) or ! is_writable($cache_path)) return;

	$CI->load->helper('directory');

	$files = directory_map($cache_path, true); // только в текущем каталоге

	if (!$files) return; // нет файлов вообще

	foreach ($files as $file)
	{
		if (@is_dir($cache_path . $file)) continue; // это каталог

		$pos = strpos($file, $mask);
		if ( $pos !== false and $pos === 0)
		{
			@unlink($cache_path . $file);
		}
	}
}


# сбросить кэш - если указать true, то удалится кэш из вложенных каталогов
# если указан $dir, то удаляется только в этом каталоге
# если указать $file, то удаляется только этот файл в кэше
function mso_flush_cache($full = false, $dir = false, $file = false)
{
	global $MSO;
	
	# если определен отдельный хук, то выполняем его
	if (mso_hook_present('mso_flush_cache')) 
		return mso_hook('mso_flush_cache', array(
		'full' => $full,
		'dir' => $dir,
		'file' => $file
		));
	
	$MSO->cache = array();
	
	$CI = & get_instance();
	
	$cache_path = getinfo('cache_dir');

	if ( !is_dir($cache_path) OR !is_writable($cache_path)) return false;

	// находим в каталоге все файлы и их удалаяем
	if ($full)
	{
		$CI->load->helper('file_helper'); // этот хелпер удаляет все Файлы и во вложенных каталогах
		@delete_files($cache_path);
	}
	else
	{
		// удаляем файлы только в текущем каталоге кэша
		// переделанная функция delete_files из file_helper
		$mso_cache_last = $cache_path . '_mso_cache_last.txt';

		if ($dir) $cache_path .= $dir . '/'; // если указан $dir, удаляем только в нем
		
		if ($file) // указан конкретный файл
		{
			if ( file_exists($cache_path . $file) ) @unlink($cache_path . $file);
		}
		else
		{
			if (!$current_dir = @opendir($cache_path)) return false;
			while (FALSE !== ($filename = @readdir($current_dir)))
			{
				if ($filename != "." and $filename != "..")
				{
					if (!is_dir($cache_path . $filename)) @unlink($cache_path . $filename);
				}
			}
			@closedir($current_dir);
		}

		// создадим служебный файл _mso_cache_last.txt который используется для сброса кэша по дате создания
		// при инициализации смотрится дата этого файла и если он создан позже, чем время жизни кэша, то кэш сбрасывается mso_flush_cache
		if (!$dir)
		{
			$fp = @fopen($mso_cache_last, 'w');
			flock($fp, LOCK_EX);
			fwrite($fp, time());
			flock($fp, LOCK_UN);
			fclose($fp);
		}

	}
	
	// если используется родное CodeIgniter sql-кэширование, то нужно очистить и его
	$CI->db->cache_delete_all();
}


# получить кеш по ключу
# Функция взята из _display_cache output.php - переделанная
function mso_get_cache($key, $custom_fn = false)
{
	global $MSO;
	
	# если определен отдельный хук, то выполняем его
	if (mso_hook_present('mso_get_cache')) 
		return mso_hook('mso_get_cache', array(
		'key' => $key,
		'custom_fn' => $custom_fn
		));

	if (mso_get_option('cache_dinamic', 'general', 0))
	{
		// кэш может быть и в динамическом $MSO->cache
		if (isset($MSO->cache[$key]) and $MSO->cache[$key]) 
		{
			return $MSO->cache[$key];
		}
	}
	
	$CI = & get_instance();

	$cache_path = getinfo('cache_dir');
	
	if ( !is_dir($cache_path) OR ! is_writable($cache_path))
		return FALSE;

	if (!$custom_fn)
		$filepath = $cache_path . mso_md5($key . $CI->config->item('base_url'));
	else
		$filepath = $cache_path . $key;

	if ( !@file_exists($filepath)) return false;

	if ( !$fp = @fopen($filepath, 'rb')) return false;

	flock($fp, LOCK_SH);

	$cache = '';
	if (filesize($filepath) > 0)
		$cache = fread($fp, filesize($filepath));

	flock($fp, LOCK_UN);
	fclose($fp);

	if ( ! preg_match("/(\d+TS--->)/", $cache, $match))
		return FALSE;

	if (time() >= trim(str_replace('TS--->', '', $match['1'])))
	{
		@unlink($filepath);
		return false;
	}

	$out = str_replace($match['0'], '', $cache);
	$out = @unserialize($out);
	
	if (mso_get_option('cache_dinamic', 'general', 0)) $MSO->cache[$key] = $out;
	
	return $out;
}


# преобразование html-спецсимволов в тексте в обычный html
function mso_text_to_html($content)
{
	//$content = str_replace(chr(10), "\n", $content);
	//$content = str_replace(chr(13), " ", $content);
	//$content = str_replace('&lt;', '<', $content);
	//$content = str_replace('&gt;', '>', $content);
	//$content = str_replace('\"', '&quot;', $content);
	//$content = str_replace('\\', '\\\\',$content);
	//$content = str_replace('\'', '&#039',$content);
	//$content = str_replace('&lt;','<', $content);
	//$content = str_replace('&gt;','>', $content);
	//$content = str_replace('&quot;','\"', $content);
	//$content = str_replace('&#039','\'', $content);
	// $content = str_replace('&amp;','&', $content);

	// $content = htmlspecialchars($content, ENT_QUOTES);
	
	return mso_hook('text_to_html', $content);
}


# преобразование html-спецсимволов
function mso_html_to_text($content)
{
	//$content = str_replace(chr(10), " ", $content);
	//$content = str_replace(chr(13), "", $content);
	//$content = str_replace('&lt;', '<', $content);
	//$content = str_replace('&gt;', '>', $content);
	//$content = str_replace('&amp;','&', $content);

	//$content = str_replace('\"', '&quot;', $content);
	//$content = str_replace('\\', '\\\\',$content);
	//$content = str_replace('\'', '&#039',$content);
	//$content = str_replace('&lt;','<', $content);
	//$content = str_replace('&gt;','>', $content);
	//$content = str_replace('&quot;','\"', $content);
	//$content = str_replace('&#039','\'', $content);

	// $content = htmlspecialchars($content, ENT_QUOTES);

	return mso_hook('html_to_text', $content);
}


# подчистка PRE + mso_auto_tag
function mso_clean_pre_special_chars($matches)
{
	if ( is_array($matches) )
	{
		$m = $matches[2];

		$m = str_replace('<p>', '', $m);
		$m = str_replace('</p>', '', $m);
		$m = str_replace("<br />", "<br>", $m);
		$m = str_replace("<br>", "MSO_N", $m);


		$m = htmlspecialchars($m, ENT_QUOTES);

		// для смайлов избежать конфликта
		$arr1 = array(':', '\'', '(', ')', '|', '-', '[', ']');
		$arr2 = array('&#58;', '&#39;', '&#40;', '&#41;', '&#124;', '&#45;', '&#91;', '&#93;');
		$m = str_replace($arr1, $arr2, $m);
		
		// ошибочный & перед < и >
		$m = str_replace('&amp;lt;', '&lt;', $m);
		$m = str_replace('&amp;gt;', '&gt;', $m);
		
		
		
		$text = "" . $matches[1] . $m . "</pre>\n";
	}
	else
		$text = $matches;

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);

	return $text;
}


# подчистка PRE + mso_auto_tag
function mso_clean_pre($matches)
{
	if ( is_array($matches) )
		$text = "" . $matches[1] . $matches[2] . "</pre>\n";
	else
		$text = $matches;
		
	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br>", "MSO_N", $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "MSO_N", $text);
	
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('&lt;pre', '<pre', $text);
	$text = str_replace('&lt;/pre', '</pre', $text);
	$text = str_replace('pre&gt;', 'pre>', $text);
	
	return $text;
}

# pre, которое загоняется в [html_base64]
function mso_clean_pre_do($matches)
{
	$text = trim($matches[2]);

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br>", "MSO_N", $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "MSO_N", $text);
	
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('&lt;pre', '<pre', $text);
	$text = str_replace('&lt;/pre', '</pre', $text);
	$text = str_replace('pre&gt;', 'pre>', $text);

	
	//pr($text);
	$text = $matches[1] . '[html_base64]' . base64_encode($text) . '[/html_base64]'. $matches[3];

	//_pr($text);
	return $text;
}


# подчистка блоковых тэгов
# удаляем в них <p>
function mso_clean_block($matches)
{

	if ( is_array($matches) )
		$text = "" . $matches[1] . $matches[2] . $matches[3] . "\n";
	else
		$text = $matches;

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', 'MSO_N_BLOCK', $text);
	$text = str_replace("<br>", "MSO_N", $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "MSO_N", $text);
	//$text = str_replace("\n", "MSO_N", $text);

	return $text;
}

# аналогично, только еще и [] меняем 
function mso_clean_block2($matches)
{

	if ( is_array($matches) )
		$text = "" . $matches[1] . $matches[2] . $matches[3] . "\n";
	else
		$text = $matches;

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', 'MSO_N_BLOCK', $text);
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "MSO_N", $text);
	//$text = str_replace("\n", "MSO_N", $text);

	return $text;
}

# преобразуем введенный html в тексте между [html] ... [/html] и для [volkman]
# к обычному html
function mso_clean_html($matches)
{
	//$arr1 = array('<p>', '</p>', '<br />',  '<br>',    '&amp;', '&lt;', '&gt;', "\n");
	//$arr2 = array('',    '',     'MSO_N',   'MSO_N',   '&',     '<',    '>',    'MSO_N');
	
	
	$arr1 = array('<br />',  '<br>',    '&amp;', '&lt;', '&gt;', "\n");
	$arr2 = array('MSO_N',   'MSO_N',   '&',     '<',    '>',    'MSO_N');

	$matches[1] = trim( str_replace($arr1, $arr2, $matches[1]) );

	return $matches[1];
}


# предподготовка html в тексте между [html] ... [/html]
# конвертируем все символы в реальный html
# после этого кодируем его в одну строчку base64
# после всех операций в mso_balance_tags декодируем его в обычный текст mso_clean_html_posle
# кодирование нужно для того, чтобы корректно пропустить весь остальной текст
function mso_clean_html_do($matches)
{
	$arr1 = array('&amp;', '&lt;', '&gt;', '<br />', '<br>', '&nbsp;');
	$arr2 = array('&',     '<',    '>',    "\n",     "\n",   ' ');
	
	$m = trim( str_replace($arr1, $arr2, $matches[1]) );

	$m = '[html_base64]' . base64_encode($m) . '[/html_base64]';

	return $m;
}


# декодирование из mso_balance_tags см. mso_clean_html_do
function mso_clean_html_posle($matches)
{
	return base64_decode($matches[1]);
}


# авторасстановка тэгов
function mso_auto_tag($pee, $pre_special_chars = false)
{
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee);
	
	if ( mso_hook_present('content_auto_tag_custom') ) 
		return mso_hook('content_auto_tag_custom', $pee);
	
	$pee = mso_hook('content_auto_tag_do', $pee);

	if ( // отдавать как есть - слово в начале текста
		( strpos($pee, '[volkman]') !== false and strpos(trim($pee), '[volkman]') == 0 ) 
		or ( strpos($pee, '[source]') !== false and strpos(trim($pee), '[source]') == 0 ) 
	)
	{
		$pee = str_replace('[volkman]', '', $pee);
		$pee = str_replace('[source]', '', $pee);
		$pee = mso_clean_html( array('1'=>$pee) );
		$pee = str_replace('MSO_N', "\n", $pee);
		return $pee;
	}
	
	//pr($pee, true); # контроль
	
	$pee = str_replace("\n", "", $pee);
	$pee = $pee . "\n";

	# если html в [html] код [/html]
	$pee = str_replace('<p>[html]</p>', '[html]', $pee);
	$pee = str_replace('<p>[/html]</p>', '[/html]', $pee);
	$pee = preg_replace_callback('!\[html\](.*?)\[\/html\]!is', 'mso_clean_html_do', $pee );

	
	# исправляем стили браузера
	$pee = str_replace('<hr style="width: 100%; height: 2px;">', "<hr>", $pee);
	

	# всё приводим к MSO_N - признак переноса
	$pee = str_replace('<br />', '<br>', $pee);
	$pee = str_replace('<br/>', '<br>', $pee);
	$pee = str_replace('<br>', 'MSO_N', $pee);
	$pee = str_replace("\n", 'MSO_N', $pee); // все абзацы тоже <br>
	
	# удаляем двойные br
	$pee = str_replace('MSO_NMSO_NMSO_NMSO_N', 'MSO_N', $pee); 
	$pee = str_replace('MSO_NMSO_NMSO_N', 'MSO_N', $pee); 
	$pee = str_replace('MSO_NMSO_N', 'MSO_N', $pee); 
	

	# все MSO_N это абзацы
	$pee = str_replace('MSO_N', "\n", $pee); 
	
	
	# преформатированный текст
	$pee = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', 'mso_clean_pre_do', $pee );
	
	
	# удалим перед всеми закрывающими тэгами абзац
	$pee = str_replace("\n</", "</", $pee); 

	//_pr($pee, true);
	
	# отбивка некоторых блоков
	//$pee = str_replace("<pre>", "\n<pre>\n", $pee); 
	//$pee = str_replace("</pre>", "\n</pre>\n", $pee);
	
	# расставим все абзацы по p
	$pee = preg_replace('!(.*)\n!', "\n<p>$1</p>", $pee);
	
	# исправим абзацы ошибочные
	$pee = str_replace("<p></p>", "", $pee); 
	$pee = str_replace("<p><p>", "<p>", $pee); 
	$pee = str_replace("</p></p>", "</p>", $pee); 
	
	# блочные тэги
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|center|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|code|select|form|map|area|blockquote|address|math|style|input|embed|h1|h2|h3|h4|h5|h6|hr|p)';
	
	# здесь не нужно ставить <p> и </p>
	$pee = preg_replace('!<p>(<' . $allblocks . '[^>]*>)</p>!', "\n$1", $pee); # <p><tag></p>
	$pee = preg_replace('!<p>(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p><tag> 
	$pee = preg_replace('!<p>(</' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p></tag> 
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)</p>!', "\n$1", $pee); # <tag></p>
	$pee = preg_replace('!(</' . $allblocks . '>)</p>!', "$1", $pee); # </tag></p>
	$pee = preg_replace('!(</' . $allblocks . '>) </p>!', "$1", $pee); # </tag></p>
	
	$pee = preg_replace('!<p>&nbsp;&nbsp;(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p>&nbsp;&nbsp;<tag> 
	$pee = preg_replace('!<p>&nbsp;(<' . $allblocks . '[^>]*>)!', "\n$1", $pee); # <p>&nbsp;<tag> 
	
	# если был cut, то уберем с ссылки абзац
	$pee = str_replace('<p><a id="cut"></a></p>', '<a id="cut"></a>', $pee); 
	
	# специфичные ошибки
	$pee = str_replace("<blockquote>\n<p>", "<blockquote>", $pee); 
	$pee = preg_replace('!<li>(.*)</p>\n!', "<li>$1</li>\n", $pee); # <li>...</p>
	$pee = str_replace("<ul>\n\n<li>", "<ul><li>", $pee); 
	$pee = str_replace("</li>\n\n<li>", "</li>\n<li>", $pee);
	
	$pee = preg_replace('!<p><a id="(.*)"></a></p>\n!', "<a id=\"$1\"></a>\n", $pee); # <li>...</p>
	
	
	### подчистим некоторые блочные тэги удалим <p> внутри. MSO_N_BLOCK = </p>
	
	# code
	$pee = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', 'mso_clean_block2', $pee );
	$pee = str_replace('MSO_N_BLOCK', "<br>", $pee); // заменим перенос в блочном на <br> 
	
	# blockquote
	$pee = preg_replace_callback('!(<blockquote.*?>)(.*?)(</blockquote>)!is', 'mso_clean_block', $pee );
	$pee = str_replace('MSO_N_BLOCK', "<br>", $pee); // заменим перенос в блочном на ''	
	
	
	# еще раз подчистка
	$pee = str_replace('MSO_N', "\n", $pee); 
	
	$pee = preg_replace('!<p><br(.*)></p>!', "<br$1>", $pee);
	$pee = preg_replace('!<p><br></p>!', "<br>", $pee);
	
	
		
	# завершим [html]
	$pee = str_replace('<p>[html_base64]', '[html_base64]', $pee);
	$pee = str_replace('[/html_base64]</p>', '[/html_base64]', $pee);
	$pee = str_replace('[/html_base64] </p>', '[/html_base64]', $pee);
	
	$pee = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $pee );
	
	
	# [br]
	$pee = str_replace('[br]', '<br style="clear:both">', $pee);
	$pee = str_replace('[br none]', '<br>', $pee);
	$pee = str_replace('[br left]', '<br style="clear:left">', $pee);
	$pee = str_replace('[br right]', '<br style="clear:right">', $pee);
	
	$pee = str_replace('<p><br></p>', '<br>', $pee);
	$pee = preg_replace('!<p><br(.*)></p>!', "<br$1>", $pee);
	

	# принудительный пробел
	$pee = str_replace('[nbsp]', '&nbsp;', $pee);
	
	
	# перенос строки в конце текста
	$pee = $pee . "\n";
	
	# кастомный автотэг
	$pee = mso_hook('content_auto_tag_my', $pee);
	
	// _pr($pee, true); # контроль

	return $pee;

}

# моя функция авторасстановки тэгов
function mso_balance_tags($text)
{
	if ( mso_hook_present('content_balance_tags_custom') ) return $text = mso_hook('content_balance_tags_custom', $text);
	
	$text = mso_hook('content_balance_tags_my', $text);
	
	return $text;
}


# функция преобразует русские и украинские буквы в английские
# также удаляются все служебные символы
function mso_slug($slug)
{
	$slug = mso_hook('slug_do', $slug);

	if (!mso_hook_present('slug'))
	{
		// таблица замены
		$repl = array(
		"А"=>"a", "Б"=>"b",  "В"=>"v",  "Г"=>"g",   "Д"=>"d",
		"Е"=>"e", "Ё"=>"jo", "Ж"=>"zh",
		"З"=>"z", "И"=>"i",  "Й"=>"j",  "К"=>"k",   "Л"=>"l",
		"М"=>"m", "Н"=>"n",  "О"=>"o",  "П"=>"p",   "Р"=>"r",
		"С"=>"s", "Т"=>"t",  "У"=>"u",  "Ф"=>"f",   "Х"=>"h",
		"Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shh", "Ъ"=>"",
		"Ы"=>"y", "Ь"=>"",   "Э"=>"e",  "Ю"=>"ju", "Я"=>"ja",

		"а"=>"a", "б"=>"b",  "в"=>"v",  "г"=>"g",   "д"=>"d",
		"е"=>"e", "ё"=>"jo", "ж"=>"zh",
		"з"=>"z", "и"=>"i",  "й"=>"j",  "к"=>"k",   "л"=>"l",
		"м"=>"m", "н"=>"n",  "о"=>"o",  "п"=>"p",   "р"=>"r",
		"с"=>"s", "т"=>"t",  "у"=>"u",  "ф"=>"f",   "х"=>"h",
		"ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ъ"=>"",
		"ы"=>"y", "ь"=>"",   "э"=>"e",  "ю"=>"ju",  "я"=>"ja",

		# украина
		"Є" => "ye", "є" => "ye", "І" => "i", "і" => "i",
		"Ї" => "yi", "ї" => "yi", "Ґ" => "g", "ґ" => "g",
		
		# беларусь
		"Ў"=>"u", "ў"=>"u", "'"=>"",

		"«"=>"", "»"=>"", "—"=>"-", "`"=>"", " "=>"-",
		"["=>"", "]"=>"", "{"=>"", "}"=>"", "<"=>"", ">"=>"",

		"?"=>"", ","=>"", "*"=>"", "%"=>"", "$"=>"",

		"@"=>"", "!"=>"", ";"=>"", ":"=>"", "^"=>"", "\""=>"",
		"&"=>"", "="=>"", "№"=>"", "\\"=>"", "/"=>"", "#"=>"",
		"("=>"", ")"=>"", "~"=>"", "|"=>"", "+"=>"", "”"=>"", "“"=>"",
		"'"=>"",

		"’"=>"",
		"—"=>"-", // mdash (длинное тире)
		"–"=>"-", // ndash (короткое тире)
		"™"=>"tm", // tm (торговая марка)
		"©"=>"c", // (c) (копирайт)
		"®"=>"r", // (R) (зарегистрированная марка)
		"…"=>"", // (многоточие)
		"“"=>"",
		"”"=>"",
		"„"=>"",

		);

		$slug = strtolower(strtr(trim($slug), $repl));

		# разрешим расширение .html
		$slug = str_replace('.htm', '@HTM@', $slug);
		$slug = str_replace('.', '', $slug);
		$slug = str_replace('@HTM@', '.htm', $slug);

		$slug = str_replace('---', '-', $slug);
		$slug = str_replace('--', '-', $slug);

		$slug = str_replace('-', ' ', $slug);
		$slug = str_replace(' ', '-', trim($slug));
	}
	else $slug = mso_hook('slug', $slug);

	$slug = mso_hook('slug_posle', $slug);
	
	return $slug;
}


# редирект на страницу сайта. путь указывать относительно сайта
# если $absolute = true - переход по указаному пути
# $header - 301 или 302 редирект
function mso_redirect($url = '', $absolute = false, $header = false)
{
	global $MSO;

	$url = strip_tags($url);
	$url = str_replace( array('%0d', '%0a'), '', $url );
	
	$url = mso_xss_clean($url);
	
	if ($header == 301) header('HTTP/1.1 301 Moved Permanently');
	elseif ($header == 302) header('HTTP/1.1 302 Found'); 

	if ($absolute)
	{
		header("Refresh: 0; url={$url}");
		header("Location: {$url}");
	}
	else
	{
		$url = $MSO->config['site_url'] . $url;
		header("Refresh: 0; url={$url}");
		header("Location: {$url}");
	}
	exit();
}


# получение текущего url относительно сайта
# ведущий и конечные слэши удаляем
# если $absolute = true, то возвращается текущий урл как есть
function mso_current_url($absolute = false)
{
	global $MSO;

	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
	$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	if ($absolute) return $url;
	
	$url = str_replace($MSO->config['site_url'], "", $url);

	$url = trim( str_replace('/', ' ', $url) );
	$url = str_replace(' ', '/', $url);

	return $url;
}


# формируем скрытый input для формы с текущей сессией
function mso_form_session($name_form = 'flogin_session_id')
{
	global $MSO;

	return '<input type="hidden" value="' . $MSO->data['session']['session_id'] . '" name="' . $name_form . '">';
}


# вывод логин-формы
# в login_form_auth_title для разделителей используется код [end] - нужен для того, чтобы перечислять через запятую
function mso_login_form($conf = array(), $redirect = '', $echo = true)
{
	global $MSO;

	if ($redirect == '') $redirect = urlencode(mso_current_url());

	$login = (isset($conf['login'])) ? $conf['login'] : '';
	$password = (isset($conf['password'])) ? $conf['password'] : '';
	$submit = (isset($conf['submit'])) ? $conf['submit'] : '';
	$submit_value = (isset($conf['submit_value'])) ? $conf['submit_value'] : tf('Войти');
	$submit_end = (isset($conf['submit_end'])) ? $conf['submit_end'] : '';
	$form_end = (isset($conf['form_end'])) ? $conf['form_end'] : '';
	
	$login_form_auth_title = (isset($conf['login_form_auth_title'])) ? $conf['login_form_auth_title'] : tf('Вход через:') . ' ';

	$action = $MSO->config['site_url'] . 'login';
	$session_id = $MSO->data['session']['session_id'];
	
	$hook_login_form_auth = mso_hook_present('login_form_auth') ? '<span class="login-form-auth-title">' . $login_form_auth_title . '</span>' . mso_hook('login_form_auth') : '';
	
	if ($hook_login_form_auth)
	{
		$hook_login_form_auth = trim(str_replace('[end]', '     ', $hook_login_form_auth));
		$hook_login_form_auth = '<div class="login-form-auth">' . str_replace('     ', ', ', $hook_login_form_auth) . '</div>';
	}
	
	$out = <<<EOF
	<form method="post" action="{$action}" name="flogin" class="flogin fform">
		<input type="hidden" value="{$redirect}" name="flogin_redirect">
		<input type="hidden" value="{$session_id}" name="flogin_session_id">
		
		<p>
			<label><span class="nocell ftitle">{$login}</span>
			<input type="text" value="" name="flogin_user" class="flogin_user">
			</label>
		</p>
		
		<p>
			<label><span class="nocell ftitle">{$password}</span>
			<input type="password" value="" name="flogin_password" class="flogin_password">
			</label>
		</p>
		
		<p>
			<span>{$submit}<button type="submit" name="flogin_submit" class="flogin_submit">{$submit_value}</button>{$submit_end}</span>
		</p>
		
		{$hook_login_form_auth}
		{$form_end}
	</form>
EOF;
	if ($echo) echo $out;
		else return $out;
}


# посыл в хидере no-кэш
# кажется не работает - как проверить хз...
function mso_nocache_headers()
{
	# см. http://www.nomagic.ru/all.php?aid=58
	@header("Cache-Control: no-store, no-cache, must-revalidate"); 
	@header("Expires: " . date("r")); 
	
	// @header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	// @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	// @header('Cache-Control: no-cache, must-revalidate, max-age=0');
	// @header('Pragma: no-cache');
}


# функция проверяет существование POST, а также обязательных полей
# которые передаются в массиве ( array('f_session_id','f_desc','f_edit_submit') )
# если полей нет, то возвращается false
# если поля есть, то возвращается $_POST
function mso_check_post($args = array())
{
	if ($_POST)
	{
		$check = true;
		foreach ($args as $key=>$field)
		{
			if (!isset($_POST[$field]))
			{  // нет значения - выходим
				$check = false;
				break;
			}
		}
		if (!$check) return false;
			else return $_POST;
	}
	else return false;
}


# получение из массива номера $num_key ключа
# array('2'=>'Изменить');
# возвратит 2
function mso_array_get_key($ar, $num_key = 0, $no = false)
{
	$ar = array_keys($ar);

	if (isset($ar[$num_key])) return $ar[$num_key];
		else return $no;
}


# получение из массива ключ значения
# array('2'=>'Изменить');
# mso_array_get_key_value($ar, 'Изменить' ) возвратит 2
function mso_array_get_key_value($ar, $value = false, $no = false)
{
	if (!$value) return $no;
	if (!in_array($value, $ar)) return $no;

	foreach ($ar as $key=>$val)
	{
		if ($val == $value)	return $key;
	}
}


# проверка комбинации логина-пароля
# если указан act - то сразу смотрим разрешение на действие
function mso_check_user_password($login = false, $password = false, $act = false)
{
	if (!$login or !$password) return false;

	$CI = & get_instance();

	$CI->db->select('users_id, users_groups_id');
	$CI->db->where(array('users_login'=>$login, 'users_password'=>$password) );  // where 'users_password' = $password
	$CI->db->limit(1); # одно значение

	$query = $CI->db->get('users');
	if ($query->num_rows() > 0) # есть такой юзер
	{
		if ($act)
		{
			// нужно проверить по users_groups_id разрешение для этого юзера для этого действия
			$r = $query->result_array();
			return mso_check_allow($act, $r[0]['users_id']);
		}
		else return true; // если act не указан, значит можно
	}
	else return false;
}


# получаем данные юзера по его логину/паролю
function mso_get_user_data($login = false, $password = false)
{
	if (!$login or !$password) return false;

	$CI = & get_instance();
	$CI->db->select('*');
	$CI->db->limit(1); # одно значение
	$CI->db->where('users_login', $login); // where 'users_login' = $login
	$CI->db->where('users_password', $password);  // where 'users_password' = $password

	$query = $CI->db->get('users');

	if ($query->num_rows() > 0) # есть такой юзер
	{
		$r = $query->result_array();
		return $r[0];
	}
	else return false;
}


# содание разрешения для действия
function mso_create_allow($act = '', $desc = '')
{
	global $MSO;

	// считываем опцию
	$d = mso_get_option('groups_allow', 'general');

	if (!$d) // нет таких опций вообще
	{
		$d = array($act => $desc); // создаем массив
		mso_add_option ('groups_allow', $d, 'general'); // добавляем опции
		return;
	}
	else // есть опции
	{
		if ( isset($d[$act]) and ($d[$act] == $desc)) return; // ничего не изменилось
		else
		{	// что-то новенькое
			$d[$act] = $desc; // добавляем
			mso_add_option ('groups_allow', $d, 'general');
			return;
		}
	}
}


# удалить действие/функцию
function mso_remove_allow($act = '')
{
	global $MSO;

	$d = mso_get_option('groups_allow', 'general');

	if ( isset($d[$act]) )
	{
		unset($d[$act]);
		mso_delete_option('groups_allow', 'general');
		mso_add_option ('groups_allow', $d, 'general');
	}
}


# проверка доступа для юзера для указанного действия/функции
# если $cache = true то данные можно брать из кэша, иначе всегда из SQL
function mso_check_allow($act = '', $user_id = false, $cache = true)
{
	global $MSO;

	if (!$act) return false;

	if ( $user_id == false ) // если юзер не указан
	{
		if (! $MSO->data['session']['users_id']) // и в сесии
			return false;
		else
			$user_id = $MSO->data['session']['users_id']; // берем его номер из сессии

		if ( $MSO->data['session']['users_groups_id'] == '1' ) // отмечена первая группа - это админы
		{
			return true; // админам всё можно
		}
	}
	else
		$user_id = (int) $user_id; // юзер указан явно - нужно проверять
	// если есть кэш этого участника, где уже хранятся его разрешения
	// то берем кэш, если нет, то выполняем запрос полностью

	if ($cache)	$k = mso_get_cache('user_rules_' . $user_id );
		else $k = false;

	if (!$k) // нет кэша
	{
		// по номеру участника получаем номер группы
		// по номеру группы получаем все разрешения этой группы

		$CI = & get_instance();
		$CI->db->select('users_groups_id, groups_rules, groups_id');// groups_name
		$CI->db->limit(1);
		$CI->db->where('users_id', $user_id);
		$CI->db->from('users');
		$CI->db->join('groups', 'groups.groups_id = users.users_groups_id');

		$query = $CI->db->get();

		foreach ($query->result_array() as $rw)
		{
			$rules = $rw['groups_rules'];
			$groups_id = $rw['groups_id'];
		}

		if ($groups_id == 1) return true; // админам можно всё

		$rules = unserialize($rules); // востанавливаем массив с разрешениями этой группы
		mso_add_cache('user_rules_' . $user_id, $rules); // сразу в кэш добавим
	}
	else // есть кэш
	{
		$rules = $k;
	}

	/*
	$rules = Array (
		[edit_users_group] => 1
		[edit_users_admin_note] => 1
		[edit_other_users] => 1
		[edit_self_users] => 1 )
	*/

	if (isset( $rules[$act] )) // если действие есть в массиве
	{
		if ($rules[$act] == 1) return true; // и разрешено
			else return false; // запрещено
	}
	else return false; // действия вообще нет в разрешениях
}


# получаем название указанного сегменту текущей страницы
# http://localhost/admin/users/edit/1
# mso_segment(3) -> edit
# номер считается от home-сайта
# если в сегменте находится XSS и $die = true, то рубим все
# можно проверить сегмент в своём массиве $my_segments
function mso_segment($segment = 2, $die = true, $my_segments = false)
{
	global $MSO;
	
	$CI = & get_instance();
	
	if ($my_segments === false) $my_segments = $MSO->data['uri_segment'];
	
	if ( count($my_segments) > ($segment - 1) )
		$seg = $my_segments[$segment];
	else $seg = '';
	
	$seg = urldecode($seg);
	
	// $url = $CI->input->xss_clean($seg); // ci < 2
	$url = $CI->security->xss_clean($seg, false);
	
	if ($url != $seg and $die) die('<b><font color="red">Achtung! XSS attack!</font></b>');
	
	return $url;
}


# функция преобразования MySql-даты (ГГГГ-ММ-ДД ЧЧ:ММ:СС) в указанный формат date
# идея - http://dimoning.ru/archives/31
# $days и $month - массивы или строка (через пробел) названия дней недели и месяцев
function mso_date_convert($format = 'Y-m-d H:i:s', $data, $timezone = true, $days = false, $month = false)
{
	$res = '';
	$part = explode(' ' , $data);

	if (isset($part[0])) $ymd = explode ('-', $part[0]);
		else $ymd = array (0,0,0);

	if (isset($part[1])) $hms = explode (':', $part[1]);
		else $hms = array (0,0,0);

	$y = $ymd[0];
	$m = $ymd[1];
	$d = $ymd[2];
	$h = $hms[0];
	$n = $hms[1];
	$s = $hms[2];

	$time = mktime($h, $n, $s, $m, $d, $y);

	if ($timezone)
	{
		if ($timezone === -1) // в случаях, если нужно убрать таймзону
			$time = $time - getinfo('time_zone') * 60 * 60;
		else
			$time = $time + getinfo('time_zone') * 60 * 60;
	}

	$out = date($format, $time);

	if ($days)
	{
		if (!is_array($days)) $days = explode(' ', trim($days));

		$day_en = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
		$out = str_replace($day_en, $days, $out);

		$day_en = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		$out = str_replace($day_en, $days, $out);
	}
	if ($month)
	{
		if (!is_array($month)) $month = explode(' ', trim($month));
		
		//$out = str_replace(' ', '_', $out);
		
		$month_en = array('January', 'February', 'March', 'April', 'May', 'June', 'July',	'August', 'September', 'October', 'November', 'December');
		$out = str_replace($month_en, $month, $out);
		
		# возможна ситуация, когда стоит английский язык, поэтому следующая замена приведет к ошибке
		# поэтому заменим $month_en на что-то своё
		
		
		$out = str_replace($month_en, array('J_anuary', 'F_ebruary', 'M_arch', 'A_pril', 'M_ay', 'J_une', 'J_uly',	'A_ugust', 'S_eptember', 'O_ctober', 'N_ovember', 'D_ecember'), $out);
		
		$month_en = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$out = str_replace($month_en, $month, $out);
		
		# теперь назад
		$out = str_replace(
				array('J_anuary', 'F_ebruary', 'M_arch', 'A_pril', 'M_ay', 'J_une', 'J_uly',	'A_ugust', 'S_eptember', 'O_ctober', 'N_ovember', 'D_ecember'), 
				array('January', 'February', 'March', 'April', 'May', 'June', 'July',	'August', 'September', 'October', 'November', 'December'), $out);
		
		
	}

	return $out;
}


# переобразование даты в формат MySql
function mso_date_convert_to_mysql($year = 1970, $mon = 1, $day = 1, $hour = 0, $min = 0, $sec = 0)
{
	if ($day>31)
	{
		$day = 1;
		$mon ++;
		$year ++;
	}

	if ($mon>12)
	{
		$mon = 1;
		$year ++;
	}

	if ( $mon < 10 ) $mon = '0' . $mon;
	if ( $day < 10 ) $day = '0' . $day;
	if ( $hour < 10 ) $hour = '0' . $hour;
	if ( $min < 10 ) $min = '0' . $min;
	if ( $sec < 10 ) $sec = '0' . $sec;

	$res = $year . '-' . $mon . '-' . $day . ' ' . $hour . ':' . $min . ':' . $sec;
	return $res;
}


# получить пермалинк страницы по её id
# через запрос БД
function mso_get_permalink_page($id = 0, $prefix = 'page/')
{
	global $MSO;
	$id = (int) $id;
	if (!$id) return '';

	$CI = & get_instance();
	$CI->db->select('page_slug, page_id');
	$CI->db->where(array('page_id'=>$id));

	$query = $CI->db->get('page');

	if ($query->num_rows()>0)
	{
		foreach ($query->result_array() as $row)
			$slug = $row['page_slug'];

		return  $MSO->config['site_url'] . $prefix . $slug;
	}
	else return '';
}


# получить пермалинк рубрики по указанному слагу
function mso_get_permalink_cat_slug($slug = '', $prefix = 'category/')
{
	if (!$slug) return '';
	return  getinfo('siteurl') . $prefix . $slug;
}


#  разделить строку из чисел, разделенных запятыми в массив
# если $integer = true, то дополнительно преобразуется в числа
# если $probel = true, то разделителем может быть пробел
# если $unique = true, то убираем дубли
function mso_explode($s = '', $int = true, $probel = true, $unique = true )
{
	//$s = trim( str_replace(',', ',', $s) );
	$s = trim( str_replace(';', ',', $s) );
	if ($probel)
	{
		$s = trim( str_replace('     ', ',', $s) );
		$s = trim( str_replace('    ', ',', $s) );
		$s = trim( str_replace('   ', ',', $s) );
		$s = trim( str_replace('  ', ',', $s) );
		$s = trim( str_replace(' ', ',', $s) );
	}

	$s = trim( str_replace(',,', ',', $s) );
	
	$s = explode(',', trim($s));
	if ($unique) $s = array_unique($s);

	$out = array();
	foreach ( $s as $key => $val )
	{
		if ($int)
		{
			if (  (int) $val > 0 ) $out[] = $val; // id в массив
		}
		else
		{
			if (trim($val)) $out[] = trim($val);
		}
	}

	if ($unique) $out = array_unique($out);

	return $out;
}


#  обрезаем строку на кол-во слов
function mso_str_word($text, $counttext = 10, $sep = ' ')
{
	$words = explode($sep, $text);
	if ( count($words) > $counttext )
		$text = join($sep, array_slice($words, 0, $counttext));
	return $text;
}


# подсчет кол-ва слов в тексте
# можно предварительно удалить все тэги и преобразовать CR в $delim
function mso_wordcount($str = '', $delim = ' ', $strip_tags = true, $cr_to_delim = true)
{
	if ($strip_tags) $str = strip_tags($str);
	if ($cr_to_delim) $str = str_replace("\n", $delim, $str);

	$out = str_replace($delim . $delim, $delim, $str);

	return count( explode($delim, $str) );
}


# получить текущую страницу пагинации
# next - признак сегмент после которого указывается номер страницы
function mso_current_paged($next = 'next')
{
	global $MSO;

	$uri = $MSO->data['uri_segment'];

	if ($n = mso_array_get_key_value($uri, $next))
	{
		if (isset($uri[$n+1])) $n = (int) $uri[$n+1];
			else $n = 1;

		if ($n > 0) $current_paged = $n;
			else $current_paged = 1;
	}
	else $current_paged = 1;

	return $current_paged;
}

# увеличение или уменьшение ссылки next на указанную величину $inc
# http://site.com/home/next/2 -> +1 -> http://site.com/home/next/3
# $url - исходный адрес (относительно сайта). Если адрес = пустой строке, 
#	то берем mso_current_url Если первый сегмент пустой, то это home
# $inc - величина изменения
# $max - максимум - если $inc + текущий > $max, то ставится $max. Если $max = false, то он не учитывается
# $min - минимальное значение
# $next - признак сегмент после которого указывается номер страницы
# $empty_no_range = true - отдает пустую строчку, если текущая paged будет равна конечной
# если $empty_no_range = false, то отдаем ссылку как обычно
function mso_url_paged_inc($max = false, $inc = 1, $empty_no_range = true, $url = '', $min = 1, $next = 'next')
{
	if (!$url) $url = mso_current_url();
	
	$current_paged = mso_current_paged($next);
	$result_paged = $current_paged + $inc;
	
	if ($max) if ($result_paged > $max) $result_paged = $max;
	
	if ($result_paged < $min) $result_paged = $min;
	
	if ($empty_no_range and $result_paged == $current_paged ) return '';
	
	// если нет $url, то это главная
	if (!$url) $url = 'home/' . $next . '/' . $current_paged;
	
	if ( strpos($url, $next . '/') === false ) // нет вхождения next/ - нужно добавить
		$url .= '/' . $next . '/' . $current_paged;
	
	// если $result_paged = , то $min, то	$next не пишем
	if ($result_paged == $min)
		$url = str_replace($next . '/' . $current_paged, '', $url);
	else
		$url = str_replace($next . '/' . $current_paged, $next . '/' . $result_paged, $url);
	
	
	// удалим последние слэши
	$url = trim(str_replace('/', ' ', $url));
	$url = str_replace(' ', '/', $url);
	
	if ($url == 'home') $url = '';
	
	return getinfo('siteurl') . $url;
}

# регистрируем сайдбар
function mso_register_sidebar($sidebar = '1', $title = 'Cайдбар', $options = array() )
{
	global $MSO;

	$MSO->sidebars[$sidebar] = array('title' => t($title), 'options' => $options);
}


# регистрируем виджет
function mso_register_widget($widget = false, $title = 'Виджет')
{
	global $MSO;

	if ($widget) $MSO->widgets[$widget] = t($title);
}


# вывод сайбрара
function mso_show_sidebar($sidebar = '1', $block_start = '<div class="widget widget_[NUMW] widget_[SB]_[NUMW] [FN] [FN]_[NUMF]"><div class="w0"><div class="w1">', $block_end = '</div><div class="w2"></div></div></div>', $echo = true)
{
	global $MSO, $page; // чтобы был доступ к параметрам страниц в условиях виджетов

	static $num_widget = array(); // номер виджета по порядку в одном сайдбаре

	$widgets = mso_get_option('sidebars-' . $sidebar, 'sidebars', array());

	$out = '';

	if ($widgets) // есть виджеты
	{
		foreach ($widgets as $widget)
		{
			$usl_res = 1; // предполагаем, что нет условий, то есть всегда true

			// имя виджета может содержать номер через пробел
			$arr_w = explode(' ', $widget); // в массив
			if ( sizeof($arr_w) > 1 ) // два или больше элементов
			{
				$widget = trim( $arr_w[0] ); // первый - функция
				$num = trim( $arr_w[1] ); // второй - номер виджета

				if (isset($arr_w[2])) // есть какое-то php-условие
				{
					$u = $arr_w; // поскольку у нас разделитель пробел, то нужно до конца все подбить в одну строчку
					$u[0] = $u[1] = '';
					$usl = trim(implode(' ', $u));

					// текст условия, is_type('home') or is_type('category')
					$usl = 'return ( ' . $usl . ' ) ? 1 : 0;';
					$usl_res = eval($usl); // выполяем
					if ($usl_res === false) $usl_res = 1; // возможно произошла ошибка
				}
			}
			else
			{
				$num = 0; // номер виджета не указан, значит 0
			}
			
			// номер функции виджета может быть не только числом, но и текстом
			// если текст, то нужно его преобразовать в slug, чтобы исключить 
			// некоректную замену [NUMF] для стилей
			$num = mso_slug($num);
			
			// двойной - заменим на один - защита id в форме админки
			$num = str_replace('--', '-', $num);
			
			if ( function_exists($widget) and $usl_res === 1)
			{
				if ($temp = $widget($num)) // выполняем виджет если он пустой, то пропускаем вывод
				{
					if (isset($num_widget[$sidebar]['numw'])) //уже есть номер виджета
					{
						$numw = ++$num_widget[$sidebar]['numw'];
						$num_widget[$sidebar]['numw'] = $numw;
					}
					else // нет такого = пишем 1
					{
						$numw = $num_widget[$sidebar]['numw'] = 1;
					}
					
					$st = str_replace('[FN]', $widget, $block_start); // название функции виджета
					$st = str_replace('[NUMF]', $num, $st); // номер функции
					$st = str_replace('[NUMW]', $numw, $st);	//
					$st = str_replace('[SB]', $sidebar, $st); // номер сайдбара

					$en = str_replace('[FN]', $widget, $block_end);
					$en = str_replace('[NUMF]', $num, $en);
					$en = str_replace('[NUMW]', $numw, $en);
					$en = str_replace('[SB]', $sidebar, $en);
					
					
					// обрамим содержимое виджета в div.widget-content
					/*
					if (stripos($temp, mso_get_val('widget_header_end', '</span></h2>')) !== false)
					{
						// есть вхождение заголовка виджета <h2>
						$temp = str_replace(mso_get_val('widget_header_end', '</span></h2>'), mso_get_val('widget_header_end', '</span></h2>') . '<div class="widget-content">', $temp);
						$en = '</div>' . $en;
					}
					else
					{
						$temp = '<div class="widget-content">' . $temp . '</div>';
					}
					*/
					
					$out .= $st . $temp . $en;
				}
			}
		}
		
		if ($echo) echo $out;
		else return $out;
	}
}


# вспомогательная функция, которая принимает глобальный _POST
# и поле $option. Использовать в _update виджетов
function mso_widget_get_post($option = '')
{
	if ( isset($_POST[$option]) )
	    return stripslashes($_POST[$option]);
	else return '';
}


# функция отправки письма по email
# preferences пока не реализована
function mso_mail($email = '', $subject = '', $message = '', $from = false, $preferences = array())
{
	
	$arg = array('email' => $email, 'subject' => $subject, 'message' => $message, 'from' => $from, 'preferences' => $preferences);
	
	# если определен хук mail, то через него отправляем данные
	if (mso_hook_present('mail'))
	{
		return mso_hook('mail', $arg);
	}
	
	$CI = & get_instance();
	$CI->load->library('email');
	
	$CI->email->clear(true);
	
	if (isset($preferences['attach']) and trim($preferences['attach']))
	{
		$CI->email->attach($preferences['attach']);
	}
		
	if ($from) $admin_email = $from;
		else $admin_email = mso_get_option('admin_email_server', 'general', 'admin@site.com');
	
	$config['wordwrap'] = isset($preferences['wordwrap']) ? $preferences['wordwrap'] : TRUE;
	$config['wrapchars'] = isset($preferences['wrapchars']) ? $preferences['wrapchars'] : 90;
	
	# можно отправлять письмо в html-формате
	if (isset($preferences['mailtype']) and $preferences['mailtype'])
		$config['mailtype'] = $preferences['mailtype'];
	
	$CI->email->initialize($config);

	$CI->email->to($email);
	
	if (isset($preferences['from_name']))
		$CI->email->from($admin_email, $preferences['from_name']);
	else	
		$CI->email->from($admin_email, getinfo('name_site'));
	
	$CI->email->subject($subject);
	$CI->email->message($message);
	$CI->email->_safe_mode = true; # иначе CodeIgniter добавляет -f к mail - не будет работать в не safePHP

	$res = @$CI->email->send();
	
	# mail($email, $subject, $message); # проверка
	if (!$res) 
	{
		if (isset($preferences['print_debugger']) and $preferences['print_debugger'])
		{
			echo $CI->email->print_debugger();
		}
	}
	
	$arg['res'] = $res;
	mso_hook('mail_res', $arg); // хук, если нужно отслеживать отправку почты
	
	return $res;
}


# для юникода отдельный wordwrap
# часть кода отсюда: http://us2.php.net/manual/ru/function.wordwrap.php#78846
# переделал и исправил ошибки я
# ширина, разделитель
function mso_wordwrap($str, $wid = 80, $tag = ' ')
{
		$pos = 0;
		$tok = array();
		$l = mb_strlen($str, 'UTF8');

		if($l == 0) return '';

		$flag = false;

		$tok[0] = mb_substr($str, 0, 1, 'UTF8');

		for($i = 1 ; $i < $l ; ++$i)
		{
				$c = mb_substr($str, $i, 1, 'UTF8');

				if(!preg_match('/[a-z\'\"]/i',$c))
				{
						++$pos;
						$flag = true;
				}
				elseif($flag)
				{
						++$pos;
						$flag = false;
				}

				if (isset($tok[$pos])) $tok[$pos] .= $c;
					else $tok[$pos] = $c;
		}

		$linewidth = 0;
		$pos = 0;
		$ret = array();
		$l = count($tok);
		for($i = 0 ; $i < $l ; ++$i)
		{
				if($linewidth + ($w = mb_strwidth($tok[$i], 'UTF8') ) > $wid)
				{
						++$pos;
						$linewidth = 0;
				}
				if (isset($ret[$pos])) $ret[$pos] .= $tok[$i];
					else $ret[$pos] = $tok[$i];

				$linewidth += $w;
		}
		return implode($tag, $ret);
}


# возвращает script с jquery или +url
# в $path можно указать http-путь к файлу
function mso_load_jquery($plugin = '', $path = '')
{
	global $MSO;

	if ( !isset($MSO->js['jquery'][$plugin]) ) // еще нет включения этого плагина
	{
		$MSO->js['jquery'][$plugin] = '1';
	
		if ($plugin)
		{
			if ($path)
			{
				return TAB . '<script src="' . $path . $plugin . '"></script>' . NR;
			}
			else
			{
				return TAB . '<script src="'. getinfo('common_url') . 'jquery/' . $plugin . '"></script>' . NR;
			}
		}
		else
		{
			$jquery_type = mso_get_option('jquery_type', 'general', 'self');
			
			$version = '1.8.2';
			
			if ($jquery_type == 'google') $url = 'http://ajax.googleapis.com/ajax/libs/jquery/' . $version . '/jquery.min.js'; // Google Ajax API CDN 
			elseif ($jquery_type == 'microsoft') $url = 'http://ajax.aspnetcdn.com/ajax/jQuery/jquery-' . $version . '.min.js'; // Microsoft CDN
			elseif ($jquery_type == 'jquery') $url = 'http://code.jquery.com/jquery-' . $version . '.min.js'; //jQuery CDN
			elseif ($jquery_type == 'huyandex') $url = 'http://yandex.st/jquery/' . $version . '/jquery.min.js';
			else $url = getinfo('common_url') . 'jquery/jquery.min.js';
			
			return '<script src="' . $url . '"></script>' . NR;
		}
	}
}


# формируем li-элементы для меню
# элементы представляют собой текст, где каждая строчка один пункт
# каждый пункт делается так:  http://ссылка | название | подсказка | class
# на выходе так:
# <li class="selected"><a href="url"><span>ссылка</span></a></li>
# если первый символ [ то это открывает группу ul 
# если ] то закрывает - позволяет создавать многоуровневые меню
function mso_menu_build($menu = '', $select_css = 'selected', $add_link_admin = false)
{
	# добавить ссылку на admin
	if ($add_link_admin and is_login()) $menu .= NR . 'admin|Admin';

	$menu = str_replace("\r", "", $menu); // если это windows
	$menu = str_replace("_NR_", "\n", $menu);
	$menu = str_replace("\n\n\n", "\n", $menu);
	$menu = str_replace("\n\n", "\n", $menu);

	# в массив
	$menu = explode("\n", trim($menu));
	
	# обработаем меню на предмет пустых строк, корректности и подсчитаем кол-во элементов
	$count_menu = 0;
	foreach ($menu as $elem)
	{
		if (strlen(trim($elem)) > 1) $count_menu++;
	}

	# определим текущий url
	$http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
	$current_url = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$out = '';
	# обходим в цикле
	
	$i = 1; // номер пункта
	$n = 0; // номер итерации цикла 
	
	$group_in = false;
	$group_in_first = false;
	$group_num = 0; // номер группы
	$group_work = false; // открытая группа?
	$selected_present = false; // есть ли выделеный пункт?
	$group_elem = 0; // элемент в группе
	
	foreach ($menu as $elem)
	{
		# разобъем строчку по адрес | название
		$elem = explode('|', $elem);
		
		# должно быть два элемента
		if (count($elem) > 1 )
		{
			$url = trim($elem[0]);  // адрес
			$name = trim($elem[1]); // название
			
			if (isset($elem[2])) $title = ' title="' . htmlspecialchars(trim($elem[2])) . '"';
			else $title = '';
			
			
			
			// нет в адресе http:// - значит это текущий сайт
			if (($url != '#') and strpos($url, 'http://') === false and strpos($url, 'https://') === false) 
			{
				if ($url == '/') $url = getinfo('siteurl'); // это главная
					else $url = getinfo('siteurl') . $url;
			}

			# если текущий адрес совпал, значит мы на этой странице
			if ($url == $current_url)
			{
				$class = ' ' . $select_css;
				$selected_present = true;
			}
			else $class = '';
			
			// возможно указан css-класс
			if (isset($elem[3])) $class .= ' ' . trim($elem[3]);

			# для первого элемента добавляем класс first
			if ($i == 1) $class .= ' first';
			
			if ($group_in_first)
 			{
				$class .= ' group-first';
 				$group_in_first = false;
			}
 				
			# для последнего элемента добавляем класс last
			if ($i == $count_menu) $class .= ' last';

			if ($class == ' ') $class = '';
			
			if ($group_in) // открываем группу
			{
				$group_num++;
				$class .= ' group-num-' . $group_num;
				
				$out .= '<li class="group' . $class . '"><a href="' . $url . '"' . $title . '><span>' . $name . '</span></a>' 
						. NR . '<ul>' . NR;
				
				$group_in = false;
				$group_in_first = true;
			}
			else
			{
				if ($group_elem > 0 and array_key_exists($i, $menu) and isset($menu[$n+1]) and trim($menu[$n+1]) == ']' ) $class .= ' group-last';
				
				$out .= '<li class="' . trim($class) . '"><a href="' . $url . '"' . $title . '><span>' . $name . '</span></a></li>' . NR;
			}
			
			
			if ($url == $current_url and $group_work) // выделяем родителя группы, если в ней выделенный подпункт
			{
				$out = str_replace('group-num-' . $group_num, 'group-num-' . $group_num . ' group-selected', $out);	
				$selected_present = true;
			}
			
			$i++;
			$group_elem++;
		}
		else
		{
			// если это [, то это начало группы ul 
			// если ] то /ul

			if ($elem[0] == '[') 
			{
				$group_in = true;
				$group_work = true;
				$group_elem = 0;
			}
			
			if ($elem[0] == ']') 
			{
				$group_elem = 0;
				$group_in = false;
				$group_work = false;
				$out .= '</ul>' . NR . '</li>' . NR;
			}
			
		}
		
		$n++;
	}
	
	$out = str_replace('<li class="">', '<li>', $out);
	
	// если ничего не выделено, то для первой группы прописываем класс group-default
	if (!$selected_present)
		$out = str_replace('group-num-1', 'group-num-1 group-default', $out);
	
	//pr($out, 1);
	return $out;
}


# добавляем куку ко всему сайту с помощью сессии и редиректа на главную или другую указанную страницу (после главной)
function mso_add_to_cookie($name_cookies, $value, $expire, $redirect = false)
{
	$CI = & get_instance();

	if (isset($CI->session->userdata['_add_to_cookie'])) $add_to_cookie = $CI->session->userdata['_add_to_cookie'];
		else $add_to_cookie = array();

	$add_to_cookie[$name_cookies] = array('value'=>$value, 'expire'=> $expire );

	$CI->session->set_userdata(	array('_add_to_cookie' => $add_to_cookie ) );
	$CI->session->set_userdata(	array('_add_to_cookie_redirect' => $redirect ) ); // куда редиректимся

	if ($redirect)
	{
		mso_redirect(getinfo('siteurl'), true);
		exit;
	}
}


# получаем куку. Если нет вообще или нет в $allow_vals, то возвращает $def_value
function mso_get_cookie($name_cookies, $def_value = '', $allow_vals = false)
{

	if (!isset($_COOKIE[$name_cookies])) return $def_value; // нет вообще

	$value = $_COOKIE[$name_cookies]; // значение куки

	if ($allow_vals)
	{
		if (in_array($value, $allow_vals)) return $value; // нет в разрешенных
		else return $def_value;
	}
	else return $value;
}


# функция построения из массивов списка UL
# вход - массив из с [childs]=>array(...)
function mso_create_list($a = array(), $options = array(), $child = false)
{
	if (!$a) return '';

	if (!isset($options['class_ul'])) $options['class_ul'] = ''; // класс UL
	if (!isset($options['class_ul_style'])) $options['class_ul_style'] = ''; // свой стиль для UL
	if (!isset($options['class_child'])) $options['class_child'] = 'child'; // класс для ребенка
	if (!isset($options['class_child_style'])) $options['class_child_style'] = ''; // свой стиль для ребенка

	if (!isset($options['class_current'])) $options['class_current'] = 'current-page'; // класс li текущей страницы
	if (!isset($options['class_current_style'])) $options['class_current_style'] = ''; // стиль li текущей страницы

	if (!isset($options['class_li'])) $options['class_li'] = ''; // класс LI
	if (!isset($options['class_li_style'])) $options['class_li_style'] = ''; // стиль LI

	if (!isset($options['format'])) $options['format'] = '[LINK][TITLE][/LINK]'; // формат ссылки
	if (!isset($options['format_current'])) $options['format_current'] = '<span>[TITLE]</span>'; // формат для текущей

	if (!isset($options['title'])) $options['title'] = 'page_title'; // имя ключа для титула
	if (!isset($options['link'])) $options['link'] = 'page_slug'; // имя ключа для слага
	if (!isset($options['descr'])) $options['descr'] = 'category_desc'; // имя ключа для описания
	if (!isset($options['id'])) $options['id'] = 'page_id'; // имя ключа для id
	if (!isset($options['slug'])) $options['slug'] = 'page_slug'; // имя ключа для slug
	if (!isset($options['menu_order'])) $options['menu_order'] = 'page_menu_order'; // имя ключа для menu_order
	if (!isset($options['id_parent'])) $options['id_parent'] = 'page_id_parent'; // имя ключа для id_parent

	if (!isset($options['count'])) $options['count'] = 'count'; // имя ключа для количества элементов

	if (!isset($options['prefix'])) $options['prefix'] = 'page/'; // префикс для ссылки
	if (!isset($options['current_id'])) $options['current_id'] = true; // текущая страница отмечается по page_id - иначе по текущему url
	if (!isset($options['childs'])) $options['childs'] = 'childs'; // поле для массива детей
	
	
	// если true, то главная рабрика выводится без ссылки в <span> 
	if (!isset($options['group_header_no_link'])) $options['group_header_no_link'] = false; 

	# функция, которая сработает на [FUNCTION]
	# эта функция получает в качестве параметра текущий массив $elem
	if (!isset($options['function'])) $options['function'] = false;
	
	
	if (!isset($options['nofollow']) or !$options['nofollow']) $options['nofollow'] = ''; // можно указать rel="nofollow" для ссылок
		else $options['nofollow'] = ' rel="nofollow"';
		

	$class_child = $class_child_style = $class_ul = $class_ul_style = '';
	$class_current = $class_current_style = $class_li = $class_li_style = '';
	
	// [LEVEL] - заменяется на level-текущий уровень вложенности
	if ($options['class_child']) $class_child = ' class="' . $options['class_child'] . ' [LEVEL]"';
	
	static $level = 0;
	$class_child = str_replace('[LEVEL]', 'level' . $level, $class_child);
	
	if ($options['class_child_style']) $class_child_style = ' style="' . $options['class_child_style'] . '"';
	if ($options['class_ul']) $class_ul = ' class="' . $options['class_ul'] . '"';
	if ($options['class_ul_style']) $class_ul_style = ' style="' . $options['class_ul_style'] . '"';

	if ($options['class_current']) $class_current = ' class="' . $options['class_current'] . '"';
	if ($options['class_current_style']) $class_current_style = ' style="' . $options['class_current_style'] . '"';
	
	if ($options['class_li']) $class_li = ' class="' . $options['class_li'] . ' group"';
		else $class_li = ' class="group"';
	if ($options['class_li_style']) $class_li_style = ' style="' . $options['class_li_style'] . '"';

	
	

	if ($child) $out = NR . '	<ul' . $class_child . $class_child_style . '>';
		else $out = NR . '<ul' . $class_ul . $class_ul_style . '>';

	$current_url = getinfo('siteurl') . mso_current_url(); // текущий урл
	
	
	// из текущего адресу нужно убрать пагинацию
	$current_url = str_replace('/next/' . mso_current_paged(), '', $current_url);
	 
	foreach ($a as $elem)
	{
		$title = $elem[$options['title']];
		$elem_slug = mso_strip($elem[$options['link']]); // slug элемента
		
		$url = getinfo('siteurl') . $options['prefix'] . $elem_slug;

		// если это page, то нужно проверить вхождение этой записи в элемент рубрики 
		// если есть, то ставим css-класс curent-page-cat
		$curent_page_cat_class = is_page_cat($elem_slug, false, false) ? ' class="curent-page-cat"' : '';

		$link = '<a' . $options['nofollow'] . ' href="' . $url . '" title="' . mso_strip($title) . '"' .$curent_page_cat_class . '>';

		if (isset($elem[$options['descr']])) $descr = $elem[$options['descr']];
		else $descr = '';

		if (isset($elem[$options['count']])) $count = $elem[$options['count']];
		else $count = '';

		if (isset($elem[$options['id']])) $id = $elem[$options['id']];
		else $id = '';

		if (isset($elem[$options['slug']])) $slug = $elem[$options['slug']];
		else $slug = '';

		if (isset($elem[$options['menu_order']])) $menu_order = $elem[$options['menu_order']];
		else $menu_order = '';

		if (isset($elem[$options['id_parent']])) $id_parent = $elem[$options['id_parent']];
		else $id_parent = '';

		$cur = false;

		if ($options['current_id']) // текущий определяем по id страницы
		{
			if (isset($elem['current']))
			{
				$e = $options['format_current'];
				$cur = true;
			}
			else
				$e = $options['format'];
		}
		else // определяем по урлу
		{
			if ($url == $current_url)
			{
				$e = $options['format_current'];
				$cur = true;
			}
			else $e = $options['format'];

		}

		$e = str_replace('[LINK]', $link, $e);
		$e = str_replace('[/LINK]', '</a>', $e);
		$e = str_replace('[TITLE]', $title, $e);
		$e = str_replace('[TITLE_HTML]', htmlspecialchars($title), $e);
		$e = str_replace('[DESCR]', $descr, $e);
		$e = str_replace('[DESCR_HTML]', htmlspecialchars($descr), $e);
		$e = str_replace('[ID]', $id, $e);
		$e = str_replace('[SLUG]', $slug, $e);
		$e = str_replace('[SLUG_HTML]', htmlspecialchars($slug), $e);
		$e = str_replace('[MENU_ORDER]', $menu_order, $e);
		$e = str_replace('[ID_PARENT]', $id_parent, $e);
		$e = str_replace('[COUNT]', $count, $e);
		$e = str_replace('[URL]', $url, $e);

		if ($options['function'] and function_exists($options['function']))
		{
			$function = $options['function']($elem);
			$e = str_replace('[FUNCTION]', $function, $e);
		}
		else $e = str_replace('[FUNCTION]', '', $e);

		if (isset($elem[$options['childs']]))
		{
			
			if ($cur) $out .= NR . '<li' . $class_current . $class_current_style . '>' . $e;
				else 
				{
					if ($options['group_header_no_link'])
						$out .= NR . '<li' . $class_li . $class_li_style . '><span class="group_header">' . $title . '</span>'; 
					else
						$out .= NR . '<li' . $class_li . $class_li_style . '>' . $e; 
				}
				
			++$level;
			$out .= mso_create_list($elem[$options['childs']], $options, true);
			--$level;
			$out .= NR . '</li>';
		}
		else
		{
			if ($child) $out .= NR . '	';
				else $out .= NR;
				
			// если нет детей, то уберем класс group
			$class_li_1 = str_replace('group', '', $class_li);

			if ($cur) $out .= '<li' . $class_current . $class_current_style . '>' . $e . '</li>';
				else $out .= '<li' . $class_li_1 . $class_li_style . '>' . $e . '</li>';
		}
	}

	if ($child) $out .= NR . '	</ul>' . NR;
		else $out .= NR . '</ul>' . NR;
	
	$out = str_replace('<li class="">', '<li>', $out);

	return $out;
}


# устанавливаем $MSO->current_lang_dir в которой хранится
# текущий каталог языка. Это второй параметр функции t()
# в связи с изменением алгоритма перевода, функция считается устаревшей
function mso_cur_dir_lang($dir = false)
{
	global $MSO;
	return $MSO->current_lang_dir = $dir;
}


# Языковой перевод MaxSite CMS
# Описание см. ниже для _t()
# перевод только для frontend - фраз сайта.
function tf($w = '', $file = false)
{
	return _t($w, $file);
}

# перевод только для админки
function t($w = '', $file = false)
{
	return _t($w, $file);
}


# функция трансляции (языковой перевод)
# не использовать эту функцию!
# первый параметр - переводимое слово - учитывается регистр полностью
# второй паарметр:
#  __FILE__ (по нему вычисляется каталог перевода)
#  mytemplate - текущий каталог
# 
#  Всегда подключается  /common/language/ЯЗЫК-f.php
#  если это админка, то подключается еще и /common/language/ЯЗЫК.php
#  Если вторым параметром указан __FILE__ то подключается перевод из каталога language
#  откуда была вызвана функция t() или tf().
#  Если второй параметр это mytemplate, то подключается language текущего шаблона
#  Если второй параметр это install, то подключается /common/language/install/ЯЗЫК.php
function _t($w = '', $file = false)
{
	global $MSO;
	
	static $langs = array(); // общий массив перевода
	static $file_langs = array(); // список уже подключенных файлов
	
	
	// только для получения переводимых фраз и отладки!
	// ОПИСАНИЕ см. в common/language/readme.txt
	if (defined('MSO__PLEASE__RETURN__LANGS'))
	{
		static $all_w = array();
		
		if ($w === '__please__return__langs__') return $langs; 
		if ($w === '__please__return__w__') return array_unique ($all_w);
		if ($w === '__please__return__file_langs__') return array_unique ($file_langs);
		
		if ($w) $all_w[] = $w;
	}
	
	if (!isset($MSO->language)) return $w; // язык вообще не существует, выходим
	if (!($current_language = $MSO->language)) return $w; // есть, но не указан язык, выходим

	// проверим перевод, возможно он уже есть
	if (isset($langs[$w]) and $langs[$w]) return $langs[$w]; // проверка перевода
	
	/*
		на примере en
		
		/common/language/en-f.php - перевод для frontend (функция tf )
		/common/language/en.php - перевод для админки (функция t )
		
		всегда загружается ($file_langs['common'])
			/common/language/en-f.php
			
		если это админка, ($file_langs['admin']) то 
			/common/language/en.php - перевод для админки (функция t )
			
		если __FILE__, то загружаем и его.
	*/
	
	if (!isset($file_langs['common'])) // common был не подключен
	{
		$langs = _t_add_file_to_lang($langs, 'common/language/', $current_language . '-f'); // front
		$file_langs['common'] = 'common/language/' . $current_language . '-f';
	}
	
	// в админке подключаем свой перевод
	if (mso_segment(1) == 'admin')
	{
		if (!isset($file_langs['admin'])) // admin был не подключен
		{
			$langs = _t_add_file_to_lang($langs, 'common/language/', $current_language);
			$file_langs['admin'] = 'common/language/' . $current_language;
		}
	}
	
	// для инсталяции свой перевод
	if ($file == 'install')
	{
		if (!isset($file_langs['install'])) // install был не подключен
		{
			$langs = _t_add_file_to_lang($langs, 'common/language/install/', $current_language);
			$file_langs['install'] = 'common/language/install/' . $current_language;
		}
	}
	
	if ($file == 'mytemplate' and !isset($file_langs['mytemplate'])) // mytemplate был не подключен
	{
		$langs = _t_add_file_to_lang($langs, 'templates/' . $MSO->config['template'] . '/language/', $current_language);
		// $file_langs['mytemplate'] = true;
		$file_langs['mytemplate'] = 'templates/' . $MSO->config['template'] . '/language/' . $current_language;
	}
	
	// возможно указан свой каталог в __FILE__
	// условия оставляю для совместимости со старым переводом
	if ($file and $file != 'admin' and $file != 'plugins' and $file != 'templates' and $file != 'install' and $file != 'mytemplate')
	{
		// ключ = $file так меньше вычислений
		if (!isset($file_langs[$file]))
		{
			// заменим windows \ на /
			$fn = str_replace('\\', '/', $file);
			$bd = str_replace('\\', '/', $MSO->config['base_dir']);

			// если в $file входит base_dir, значит это использован __FILE__
			// нужно вычленить base_dir
			$pos = strpos($fn, $bd);
			if ($pos !== false) // есть вхождение
			{
				$fn = str_replace($bd, '', $fn);
				$fn = dirname($file) . '/language/';

				$langs = _t_add_file_to_lang($langs, $fn, $current_language, true);
			}
			
			$file_langs[$file] = true;
		}
	}
	
	if (isset($langs[$w]) and $langs[$w]) return $langs[$w]; // проверка перевода	

	
	// перевода нет :-(

	return $w;

}

# служебная функция для _t()
# нигде не использовать!
function _t_add_file_to_lang($langs, $path, $current_language, $full_name = false)
{
	global $MSO;
	
	if ($full_name) 
		$fn = $path . $current_language . '.php';
	else
		$fn = $MSO->config['base_dir'] . $path . $current_language . '.php';
	
	
	if (file_exists($fn))
	{
		require_once($fn); // есть такой файл
		if (isset($lang)) $langs = array_merge($langs, $lang); // есть ли в нем $lang ?
	}
	
	return $langs;
}


# получение информации об авторе по его номеру из url http://localhost/author/1
# или явно указанному номеру
function mso_get_author_info($id = 0)
{
	if (!$id) $id = mso_segment(2);
	if (!$id or !is_numeric($id)) return array(); // неверный id

	$key_cache = 'mso_get_author_info_' . $id;
	if ( $k = mso_get_cache($key_cache) ) return $k; // да есть в кэше

	$out = array();

	$CI = & get_instance();
	$CI->db->select('*');
	$CI->db->where('users_id', $id);
	$query = $CI->db->get('users');

	if ($query->num_rows() > 0) # есть такой юзер
	{
		$out = $query->result_array();
		$out = $out[0];
	}

	mso_add_cache($key_cache, $out);

	return $out;
}


# получение текущих сегментов url в массив
# в отличие от CodeIgniter - происходит анализ get и отсекание до «?»
# если «?» нет, то возвращает стандартное $this->uri->segment_array();
function mso_segment_array()
{
	$CI = & get_instance();

	if ( isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] )
	{
		// http://localhost/page/privet?get=dsfsdklfjkldsjflsdf
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
		$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = str_replace($CI->config->config['base_url'], '', $url); // page/privet?get=dsfsdklfjkldsjflsdf

		if ( strpos($url, '?') !== FALSE ) // есть «?»
		{
			$url = explode('?', $url); // разделим в массив
			$url = $url[0]; // сегменты - это только первая часть
			$url = explode('/', $url); // разделим в массив по /

			// нужно изменить нумерацию - начало с 1
			$out = array();
			$i = 1;
			foreach($url as $val)
			{
				if ($val)
				{
					$out[$i] = $val;
					$i++;
				}
			}

			return $out;
		}
		else return $CI->uri->segment_array();
	}
	else return $CI->uri->segment_array();
}


# получение get-строки из текущего адреса
function mso_url_get()
{
	$CI = & get_instance();
	if ( isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] and (strpos($_SERVER['REQUEST_URI'], '?') !== FALSE) )
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
		$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = str_replace($CI->config->config['base_url'], "", $url);
		$url = explode('?', $url);
		return $url[1];
	}
	else return '';
}


# функция преобразования get-строки в массив
# разделитель элементов массива & или &amp;
# значение через стандартную parse_str
function mso_parse_url_get($s = '')
{
	if ($s)
	{
		$s = str_replace('&amp;', '&', $s);
		$s = explode('&', $s);
		$uri_get_array = array();
		foreach ($s as $val)
		{
			parse_str($val, $arr);
			foreach ($arr as $key1 => $val1)
			{
				$uri_get_array[$key1] = $val1;
			}
		}
		return $uri_get_array;
	}
	else return array();
}


# кастомный вывод цикла
# $f - идентификатор цикла
# $f - варианты см. в templates/default/type_foreach/
function mso_page_foreach($type_foreach_file = false)
{
	global $MSO;
	
	# при первом обращении занесем сюда все файлы из шаблонного type_foreach
	# чтобы потом результат считывать из масива, а не по file_exists
	static $files = false; 

	$MSO->data['type_foreach_file'] = $type_foreach_file; // помещаем в $MSO вызываемый тип
	
	// описание см. default/type_foreach/_general.php
	if (file_exists(getinfo('template_dir') . 'type_foreach/general.php'))
		include(getinfo('template_dir') . 'type_foreach/general.php'); 
	
	// можно поменять type_foreach-файл через хук
	$type_foreach_file = mso_hook('type-foreach-file-general', $type_foreach_file);
	
	if ($type_foreach_file)
	{
		if ($files === false)
		{
			$CI = & get_instance();
			$CI->load->helper('directory');
			$files = directory_map(getinfo('template_dir') . 'type_foreach/', true); // только в type_foreach
			if (!$files) $files = array();
		}

		if (in_array($type_foreach_file . '.php', $files)) // есть файл в шаблоне
			return getinfo('template_dir') . 'type_foreach/' . $type_foreach_file . '.php';
		else 
		{	
			// файла нет
			// если есть хук type-foreach-file
			if (mso_hook_present('type-foreach-file'))
			{
				// получим его значение
				// он должен возвращать либо полный путь к файлу, либо false
				if ($out = mso_hook('type-foreach-file', $type_foreach_file)) 
					return $out; // указан путь
				else 
					return false; // вернул false
			}
			else // нет хука type-foreach-file
				return false;
		}
	}
	
	return false;
}


# функция залогирования
# перенесена из application\views\login.php
function _mso_login()
{
	global $MSO;
	
	// обрабатываем POST если есть 
	if ($_POST) $_POST = mso_clean_post(array(
			'flogin_submit' => 'base',
			'flogin_redirect' => 'base',
			'flogin_user' => 'base',
			'flogin_password' => 'base',
			'flogin_session_id' => 'base',
			));
	
	if ($_POST 	and isset($_POST['flogin_submit']) 
				and isset($_POST['flogin_redirect'])
				and isset($_POST['flogin_user'])
				and isset($_POST['flogin_password'])
				and isset($_POST['flogin_session_id'])
		)
	{
		sleep(3); // задержка - примитивная защита от подбора пароля
	
		$flogin_session_id = $_POST['flogin_session_id'];
		
		# защита сесии
		if ($MSO->data['session']['session_id'] != $flogin_session_id) mso_redirect('loginform/error');
		
		$flogin_redirect = urldecode($_POST['flogin_redirect']);
		
		
		if ($flogin_redirect == 'home') $flogin_redirect = getinfo('siteurl');
		
		$flogin_user = $_POST['flogin_user'];
		$flogin_password = $_POST['flogin_password'];
		
		# проверяем на strip - запрещенные символы
		if ( ! mso_strip($flogin_user, true) or ! mso_strip($flogin_password, true) ) mso_redirect('loginform/error');
		
		$flogin_password = mso_md5($flogin_password);

		$CI = & get_instance();
		
		// если это комюзер, то логин = email 
		// проверяем валидность email и если он верный, то ставим куку на этого комюзера 
		// и редиректимся на главную (куку ставить только на главную!)
		// если же это обычный юзер-автор, то проверяем логин и пароль по базе
		
		if (mso_valid_email($flogin_user))
		{
			// если в логине мыло, то проверяем сначала в таблице авторов
			$CI->db->from('users'); # таблица users
			$CI->db->select('*'); # все поля
			$CI->db->limit(1); # одно значение
			
			$CI->db->where('users_email', $flogin_user); // where 'users_login' = $flogin_user
			$CI->db->where('users_password', $flogin_password);  // where 'users_password' = $flogin_password
			
			$query = $CI->db->get();
			
			if ($query->num_rows() > 0) # есть такой юзер
			{
				$userdata = $query->result_array();
				
				# добавляем юзера к сессии
				$CI->session->set_userdata('userlogged', '1');
				
				$data = array(
					'users_id' => $userdata[0]['users_id'],
					'users_nik' => $userdata[0]['users_nik'],
					'users_login' => $userdata[0]['users_login'],
					'users_password' => $userdata[0]['users_password'],
					'users_groups_id' => $userdata[0]['users_groups_id'],
					'users_last_visit' => $userdata[0]['users_last_visit'],
					'users_show_smiles' => $userdata[0]['users_show_smiles'],
					'users_time_zone' => $userdata[0]['users_time_zone'],
					'users_language' => $userdata[0]['users_language'],
					// 'users_levels_id' => $userdata[0]['users_levels_id'],
					// 'users_avatar_url' => $userdata[0]['users_avatar_url'],
					// 'users_skins' => $userdata[0]['users_skins']
				);
				
				$CI->session->set_userdata($data);
				
				// сразу же обновим поле последнего входа
				$CI->db->where('users_id', $userdata[0]['users_id']);
				$CI->db->update('users', array('users_last_visit'=>date('Y-m-d H:i:s')));
				
				mso_redirect($flogin_redirect, true);
			} 
			else 
			{ 
				// это не автор, значит это комюзер
				$CI->db->select('comusers_id, comusers_password, comusers_email, comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
				$CI->db->where('comusers_email', $flogin_user);
				$CI->db->where('comusers_password', $flogin_password);
				$query = $CI->db->get('comusers');
				
				if ($query->num_rows()) // есть такой комюзер
				{
					$comuser_info = $query->row_array(1); // вся инфа о комюзере
					
					// сразу же обновим поле последнего входа
					$CI->db->where('comusers_id', $comuser_info['comusers_id']);
					$CI->db->update('comusers', array('comusers_last_visit'=>date('Y-m-d H:i:s')));
					
					$expire  = time() + 60 * 60 * 24 * 365; // 365 дней
					
					$name_cookies = 'maxsite_comuser';
					$value = serialize($comuser_info); 
					
					mso_add_to_cookie($name_cookies, $value, $expire, $flogin_redirect); // в куку для всего сайта
					
					exit();
				}
				else // неверные данные
				{
					mso_redirect('loginform/error');
					exit;
				}
			}
		}
		else 
		{
			// это обычный автор
			
			$CI->db->from('users'); # таблица users
			$CI->db->select('*'); # все поля
			$CI->db->limit(1); # одно значение
			
			$CI->db->where('users_login', $flogin_user); // where 'users_login' = $flogin_user
			$CI->db->where('users_password', $flogin_password);  // where 'users_password' = $flogin_password
			
			$query = $CI->db->get();
			
			if ($query->num_rows() > 0) # есть такой юзер
			{
				$userdata = $query->result_array();
				
				# добавляем юзера к сессии
				$CI->session->set_userdata('userlogged', '1');
				
				$data = array(
					'users_id' => $userdata[0]['users_id'],
					'users_nik' => $userdata[0]['users_nik'],
					'users_login' => $userdata[0]['users_login'],
					'users_password' => $userdata[0]['users_password'],
					'users_groups_id' => $userdata[0]['users_groups_id'],
					'users_last_visit' => $userdata[0]['users_last_visit'],
					'users_show_smiles' => $userdata[0]['users_show_smiles'],
					'users_time_zone' => $userdata[0]['users_time_zone'],
					'users_language' => $userdata[0]['users_language'],
					// 'users_avatar_url' => $userdata[0]['users_avatar_url'],
					// 'users_levels_id' => $userdata[0]['users_levels_id'],
					// 'users_skins' => $userdata[0]['users_skins']
				);
				
				$CI->session->set_userdata($data);
				
				// сразу же обновим поле последнего входа
				$CI->db->where('users_id', $userdata[0]['users_id']);
				$CI->db->update('users', array('users_last_visit'=>date('Y-m-d H:i:s')));
				
				mso_redirect($flogin_redirect, true);
			}
			else mso_redirect('loginform/error');
		} // автор
	}
	else 
	{
		
		$MSO->data['type'] = 'loginform';
		$template_file = $MSO->config['templates_dir'] . $MSO->config['template'] . '/index.php';
		
		if ( file_exists($template_file) ) require($template_file);
			else show_error('Ошибка - отсутствует файл шаблона index.php');
	};
}


# функция разлогирования
# перенесена из application\views\logout.php
function _mso_logout()
{
	$ci = & get_instance();
	$ci->session->sess_destroy();
	$url = (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	
	// проверяем, чтобы url был текущего сайта
	$pos = strpos($url, getinfo('site_url'));
	if ($pos === false or $pos > 0) $url = ''; // чужой, сбрасываем переход
	
	// сразу же удаляем куку комюзера
	$comuser = mso_get_cookie('maxsite_comuser', false);
	
	if ($comuser) 
	{
		$name_cookies = 'maxsite_comuser';
		$expire  = time() - 31500000;
		$value = ''; 

		//_pr($url);
		// mso_add_to_cookie('mso_edit_form_comuser', '', $expire); 
		//mso_add_to_cookie($name_cookies, $value, $expire, getinfo('siteurl') . mso_current_url()); // в куку для всего сайта
		mso_add_to_cookie($name_cookies, $value, $expire, $url); // в куку для всего сайта
		
	}
	elseif ($url) mso_redirect($url, true);
	else mso_redirect(getinfo('site_url'), true);
}


# проверка на XSS-атаку входящего текста
# если задан $out_error, то отдаем сообщение
# если $die = true, то рубим выполнение с сообщением $out_error
# иначе возвращаем очищенный текст
# если xss не определен и есть $out_no_error, то возвращаем не текст, а $out_no_error
function mso_xss_clean($text, $out_error = '_mso_xss_clean_out_error', $out_no_error = '_mso_xss_clean_out_no_error', $die = false)
{
	$CI = & get_instance();

	// выполняем XSS-фильтрацию
    $text_xss = $CI->security->xss_clean($text, false);

	// если тексты не равны, значит существует опасность XSS-атаки
	if ($text != $text_xss)
	{
		if ($die)
		{
			die($out_error);
		}
		else
		{
			if ($out_error != '_mso_xss_clean_out_error') return $out_error;
			else return $text_xss;
		}
	}
	else // тексты нормальные
	{
		if ($out_no_error != '_mso_xss_clean_out_no_error') return $out_no_error;
		else return $text;
	}
}


# функция прогоняет ключи входящего массив данных через xss_clean
# если $strip_tags = true то удяляются все html-тэги 
# если $htmlspecialchars = true то преобразование в html-спецсимволы
function mso_xss_clean_data($data = array(), $keys = array(), $strip_tags = false, $htmlspecialchars = false)
{
	$CI = & get_instance();
	
	foreach ($keys as $key)
	{
		if (isset($data[$key])) // есть данные
		{
			if (!is_scalar($data[$key])) continue;
			
			if ($strip_tags) $data[$key] = strip_tags($data[$key]);
			if ($htmlspecialchars) $data[$key] = htmlspecialchars($data[$key]);
			
			$data[$key] = $CI->security->xss_clean($data[$key], false);
		}
	}
	
	return $data;
}


# прогоняем строку $str через фильтры, согласно указанным в $rules правилам
# правила
# 	xss - xss-обработка
# 	trim - удаление ведущих и конечных пустых символов
# 	integer или int - преобразовать в число
# 	strip_tags - удалить все тэги
# 	htmlspecialchars - преобразовать в html-спецсимволы
# 	valid_email или email - если это неверный адрес, вернет пустую строчку
#   not_url - удалить все признаки url
# если правило равно base, то это cработают правила: trim|xss|strip_tags|htmlspecialchars
# $s = mso_clean_str($s, 'trim|xss');
function mso_clean_str($str = '', $rules = 'base')
{
	if (!$str) return $str;
	if (!$rules) return $str;
	
	$CI = & get_instance();
	
	$rules = explode('|', $rules);
	$rules = array_map('trim', $rules); // обработка элементов массива
	$rules = array_unique($rules); // удалим повторы
	
	foreach ($rules as $rule)
	{
		if ($rule == 'trim' or $rule == 'base') $str = trim($str);
		if ($rule == 'xss' or $rule == 'base') $str = $CI->security->xss_clean($str, false);
		if ($rule == 'strip_tags' or $rule == 'base') $str = strip_tags($str);
		if ($rule == 'htmlspecialchars' or $rule == 'base') $str = htmlspecialchars($str);
		
		if ($rule == 'int' or $rule == 'integer') $str = intval($str);
		if ($rule == 'valid_email' or $rule == 'email') $str = (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? '' : $str;
		
		if ($rule == 'not_url') 
		{
			$str = str_replace(array('http://','https://', '\\', '|', '/', '?', '%', '*', '`', '<', '>', '#', '&amp;', '^', '&', '(', ')', '+', '$'), '', $str);
		}
	}
	
	return $str;
}


# функция возвращает массив $post обработанный по указанным правилам 
# входящий массив состоит из пары 'поле'=>'правила'
# где поле - ключ массива $post, а правила - правила валидации
# mso_clean_post(array('my_name'=>'trim|xss'))
# если массив $post не указан, то используется $_POST
# правила см. в mso_clean_str()
function mso_clean_post($keys = array(), $post = false)
{
	if (!$post)
	{
		if ($_POST) $post = $_POST;
		else return $post;
	}

	foreach ($keys as $key => $rules)
	{
		if (isset($post[$key])) // есть данные
		{
			$post[$key] = mso_clean_str($post[$key], $rules);
		}
	}
	
	return $post;
}


# Функция возвращает массив для пагинации при выполнении предыдущего sql-запроса с SELECT SQL_CALC_FOUND_ROWS
# при помощи sql-запроса SELECT FOUND_ROWS();
# Использовать непосредственно после исходного get-запроса с указанным SQL_CALC_FOUND_ROWS
# $limit - записей на страницу
# $pagination_next_url - сегмент-признак пагинации (для определения текущей страницы пагинации)
/*
	пример использования:
	формируем sql-запрос с SQL_CALC_FOUND_ROWS
	
	$CI->db->select('SQL_CALC_FOUND_ROWS comments_id, ...', false);
	...
	$limit = 20; // задаем кол-во записей на страницу
	$CI->db->limit($limit, mso_current_paged() * $limit - $limit); // не более $limit
	...
	$query = $CI->db->get(); // выполнили запрос

	$pagination = mso_sql_found_rows($limit); // получили массив для пагинации
	
	// $pagination - готовый массив для пагинации
	mso_hook('pagination', $pagination); // вывод пагинации

*/
function mso_sql_found_rows($limit = 20, $pagination_next_url = 'next')
{
	$CI = & get_instance();
	
	// определим общее кол-во записей
	$query_row = $CI->db->query('SELECT FOUND_ROWS() as found_rows', false);
	
	if ($query_row->num_rows() > 0)
	{
		$ar = $query_row->result_array();
		$found_rows = $ar[0]['found_rows'];
		
		$maxcount = ceil($found_rows / $limit); // всего страниц пагинации

		$current_paged = mso_current_paged($pagination_next_url);
		
		if ($current_paged > $maxcount) $current_paged = $maxcount;
		
		$offset = $current_paged * $limit - $limit;
		
		$out = array(
					'limit' => $limit, // строк на страницу - для LIMIT
					'offset' => $offset, // смещение для LIMIT
					'found_rows' => $found_rows, // всего записей, как без LIMIT
					'maxcount' => $maxcount, // всего страниц пагинации
					'next_url' => $pagination_next_url, // признак пагинации
					);
	}
	else
	{
		$out = false;
	}

	$CI->db->cache_delete_all();
	
	return $out;
}


# формирование rss в <link rel...>
# для страниц и рубрик добавляются свои RSS
function mso_rss()
{
	global $MSO;
	
	$out = '<link rel="alternate" type="application/rss+xml" title="' 
		. tf('Все новые записи') . '" href="' 
		. getinfo('rss_url') . '">' . NR;

	$out .= '	<link rel="alternate" type="application/rss+xml" title="' 
		. tf('Все новые комментарии') . '" href="' 
		. getinfo('rss_comments_url') . '">' . NR;

	if (is_type('page') and mso_segment(2) and (isset($MSO->data['pages_is']) and $MSO->data['pages_is']))
	{
		
		$out .= '	<link rel="alternate" type="application/rss+xml" title="' 
				. tf('Комментарии этой записи') . '" href="' 
				. getinfo('site_url') . mso_segment(1) . '/' . mso_segment(2) . '/feed">' . NR;
	}
	elseif (is_type('category') and mso_segment(2) and (isset($MSO->data['pages_is']) and $MSO->data['pages_is']))
	{
		$out .= '	<link rel="alternate" type="application/rss+xml" title="' 
					. tf('Записи этой рубрики') . '" href="' 
					. getinfo('site_url') . mso_segment(1) . '/' . mso_segment(2) . '/feed">' . NR;
	}

	return $out;
}



# Функция использует глобальный одномерный массив
# который используется для получения значения указанного ключа $key
# Если в массиве ключ не определён, то используется значение $default
# если $array = true, то возвращаем значение ключа массива $key[$default]
# см. примеры к mso_set_val()
function mso_get_val($key = '', $default = '', $array = false)
{
	global $MSO;
	
	// нет такого массива, создаём
	if (!isset($MSO->key_options)) 
	{
		$MSO->key_options = array();
		return $default;
	}
	
	
	if ($array !== false and $default and isset($MSO->key_options[$key][$default]))
	{
		return $MSO->key_options[$key][$default]; 
	}
	else
	{
		// возвращаем значение или дефаулт
		return (isset($MSO->key_options[$key])) ? $MSO->key_options[$key] :	$default; 
	}
}

# Функция обратная mso_get_val() - задаёт для ключа $key значение $val 
# если $val_val == null, значит присваиваем всему $key значание $val
# если $val_val != null, значит $val - это ключ массива
# mso_set_val('type_home', 'cache_time');
#		[type_home]=>'cache_time'
#
# mso_set_val('type_home', 'cache_time', 900); 
#		[type_home] => Array
#		(
#            [cache_time] => 900
#		)
#
function mso_set_val($key, $val, $val_val = null)
{
	global $MSO;
	
	// нет массива, создаём
	if (!isset($MSO->key_options)) 
	{
		$MSO->key_options = array();
	}
	

	if ($val_val !== null)
	{
		$MSO->key_options[$key][$val] = $val_val;
	}
	else
	{
		$MSO->key_options[$key] = $val; // записали значение
	}
	
}

# Функция удаляет ключ $key 
function mso_unset_val($key)
{
	global $MSO;
	
	if (isset($MSO->key_options[$key])) 
	{
		unset($MSO->key_options[$key]);
	}
}

# функция формирует <link rel="$REL" $ADD>
# $rel - тип rel. Если он равен canonical, то формируется канонизация
# http://www.google.com/support/webmasters/bin/answer.py?answer=139066&hl=ru
# <link rel="canonical" href="http://www.example.com/page/about">
function mso_link_rel($rel = 'canonical', $add = '')
{
	if (!$rel) return; // пустой тип
	if ($rel == 'canonical')
	{
		if ($add)
		{
			echo '<link rel="canonical" ' . $add . '>';
		}
		else
		{
			// для разных типов данных формируем разный канонический адрес
			// он напрямую зависит от типа
			
			$url = '';
			
			if (is_type('page') 
				or is_type('category') 
				or is_type('tag') 
				or is_type('author')
				or is_type('users')
				or (mso_segment(1) == 'sitemap')
				or (mso_segment(1) == 'contact')
				)
			{
				if (mso_segment(2))
				{
					$url = getinfo('site_url') . mso_segment(1) . '/' . mso_segment(2);
				}
				else
				{
					$url = getinfo('site_url') . mso_segment(1);
				}
			}
			elseif (is_type('home'))
			{
				$url = getinfo('site_url');
			}
			
			// echo $url;
			
			// пагинация
			if (($cur = mso_current_paged()) > 1) 
			{
				if (is_type('home'))
				{
					$url .= 'home/next/' . $cur;
				}
				else
				{
					$url .= '/next/' . $cur;
				}
			}

			if ($url) 
			{			
				echo '<link rel="canonical" href="' . $url . '">' . NR;
			}
		}
		
	}
	else
	{
		if ($add)
		{
			echo '<link rel="' . $rel . '" ' . $add . '>';
		}
	}
	
}

# функция для виджетов формирует поля формы для form.fform с необходимой html-разметкой
# каждый вызов функции - одна строчка + если есть $hint - вторая
# $form = mso_widget_create_form('Название', поле формы, 'Подсказка');
function mso_widget_create_form($name = '', $input = '', $hint = '')
{
	$out = '<p><span class="ffirst ftitle ftop">' . $name . '</span><label>'
			. $input . '</label></p>';

	if ($hint)
	{
		$out .= '<p class="nop"><span class="ffirst"></span><span class="fhint">'
				. $hint . '</span></p>';
	}
	
	return $out;
}

# компилятор LESS в CSS
# на выходе css-подключение, либо содержимое css-файла (переключается через $css_url)
# $less_file - входной less-файл (полный путь на сервере)
# $css_file - выходной css-файл (полный путь на сервере)
# $css_url - полный http-адрес css-файла. Если $css_url = '', то отдается содержимое css-файла
# $use_cache - разрешить использование кэширования LESS-файла (определяется по времени файлов)
# $use_mini - использовать сжатие css-кода
# $use_mini_n - если включено сжатие, то удалять переносы строк
# пример использования см. в /default/css/less/compiling-less.zip/var_style.php

function mso_lessc($less_file = '', $css_file = '', $css_url = '', $use_cache = false, $use_mini = true, $use_mini_n = false)
{

	if (!$less_file or !$css_file) return; // не указаны файлы
	
	if ($use_cache) // проверка кэша
	{
		if (file_exists($less_file) and file_exists($css_file))
		{
			if (filemtime($less_file) < filemtime($css_file))
			{
				// отдаём из кэша
				
				if ($css_url) 
				{
					// в виде имени файла
					return NT . '<link rel="stylesheet" href="' . $css_url . '">';
				}
				else
				{
					// в виде содержимого
					return file_get_contents($css_file);
				}
			}
		}
	}

	if (file_exists($less_file)) $fc_all = file_get_contents($less_file);
		else return; // нет файла, выходим

	if ($fc_all)
	{
		require_once(getinfo('common_dir') . 'less/lessc.inc.php');
		
		$compiler = new lessc();
		// $compiler->importDir = dirname($less_file); // старый API
		$compiler->addImportDir(dirname($less_file)); // новый 0.3.7 api
		$compiler->indentChar = "\t";
		
		try
		{
			//$out = $compiler->parse($fc_all); // старый API
			$out = $compiler->compile($fc_all); // новый 0.3.7 api
		}
		catch (Exception $ex) 
		{
			die("<pre>lessphp fatal error: " . $ex->getMessage() . '</pre>');
		}
		
		// сжатие кода
		if ($use_mini)
		{
			if ($use_mini_n)
			{
				$out = str_replace("\t", ' ', $out);
				$out = str_replace(array("\r\n", "\r", "\n", '  ', '    '), '', $out);
			}
			
			$out = str_replace("\n\t", '', $out);
			$out = str_replace("\n}", '}', $out);
			$out = str_replace('; ', ';', $out);
			$out = str_replace(';}', '}', $out);
			$out = str_replace(': ', ':', $out);
			$out = str_replace('{ ', '{', $out);
			$out = str_replace(' }', '}', $out);
			$out = str_replace(' {', '{', $out);
			$out = str_replace(', ', ',', $out);
			$out = str_replace(' > ', '>', $out);		
			$out = str_replace('} ', '}', $out);
			$out = str_replace('  ', ' ', $out);
		}
		
		$fp = fopen($css_file, "w");
		fwrite($fp, $out);
		fclose($fp);
		
		if ($css_url) 
		{
			return NT . '<link rel="stylesheet" href="' . $css_url . '">'; // в виде имени файла
		}
		else
		{
			// в виде содержимого
			return $out;
		}
	}
}

# формирует <style> из указанного адреса 
function mso_load_style($url = '')
{
	return NT . '<link rel="stylesheet" href="' . $url . '">';
}

# формирует <script> из указанного адреса 
function mso_load_script($url = '')
{
	return NT . '<script src="' . $url . '"></script>';
}


# Функция возвращает полный путь к файлу, который следует подключить в index.php шаблона
# использовать вместо старого варианта выбора type-файла
# 	if ($fn = mso_dispatcher()) require($fn);
function mso_dispatcher()
{
	# тип данных
	$type = getinfo('type');

	# для rss используются отдельное подключение
	if (is_feed())
	{
		// ищем файл в шаблоне или shared
		if ($f = mso_find_ts_file('type/feed/' . $type . '.php'))
			return $f;
		else
			return mso_find_ts_file('type/feed/home.php');
	}

	# в зависимости от типа данных подключаем нужный файл

	# на page_404 может быть свой хук. Тогда ничего не подключаем
	if ($type == 'page_404' 
		and mso_hook_present('custom_page_404') 
		and mso_hook('custom_page_404')) 
	{
		return false;
	}
	elseif ($type == 'page_404') // страница не найдена, формируем по сегменту
	{
		$seg = mso_strip(mso_segment(1));
		$fn = 'type/' . $seg . '/' . $seg . '.php';
	}
	else
	{
		$fn = 'type/' . $type . '/' . $type . '.php';
	}
	
	if ($f = mso_find_ts_file($fn)) 
		return $f;
	else
		return mso_find_ts_file('type/page_404/page_404.php');
		
}


# поиск файла либо в каталоге шаблона, либо в shared-каталоге
# файл указывается относительно каталога шаблона/shared-каталога
# приоритет имеет файл в шаблоне, после в shared
# если файл не найден, то возвращается $default
# иначе полный путь, годный для require()
# if ($fn = mso_find_ts_file('type/page-comments.php')) require($fn);
function mso_find_ts_file($fn, $default = false)
{
	$fn1 = getinfo('template_dir') . $fn; // путь в шаблоне
	$fn2 = getinfo('shared_dir') . $fn; // путь в shared
	
	if ( file_exists($fn1) ) return $fn1; // если шаблонный
	elseif (file_exists($fn2)) return $fn2; // нет, значит shared
	else return $default;
}



# профилирование - старт
# первый параметр метка
function _mso_profiler_start($point = 'first', $echo = false)
{
	global $_points;
	
	$CI = & get_instance();
	
	$CI->benchmark->mark($point . '_start'); // отмечаем время
	
	$mem0 = round(memory_get_usage()/1024/1024, 2); // текущая память
	
	$_points[$point]['mem0'] = $mem0;
	
	if ($echo)
		pr('start ' . $point . ': ' . $_points[$point]['mem0'] . 'MB');
}

# профилирование конец
function _mso_profiler_end($point = 'first', $echo = true)
{
	global $_points;
	
	$CI = & get_instance();
	
	$CI->benchmark->mark($point . '_end');
	
	$_points[$point]['time'] = $CI->benchmark->elapsed_time($point . '_start', $point . '_end');
	
	$mem1 = round(memory_get_usage()/1024/1024, 2); // текущая память
	
	$_points[$point]['mem1'] = $mem1;
	$_points[$point]['mem'] = round($mem1 - $_points[$point]['mem0'], 4); // разница
	
	if ($echo) 
		pr(
			$point . ': ' 
			. $_points[$point]['mem'] . 'MB t: ' 
			. $_points[$point]['time'] . 's Total: '
			. $_points[$point]['mem1'] . 'MB'
			);
	else return $_points[$point];
}


# end file