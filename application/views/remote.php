<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
/*
	Версия 31 января 2009 г.
	
	в текущем варианте API поддерживается только формат text. Варианты для xml и html будут потом. Когда-нибудь...
	
	
	+ Hello : возвращает Hello!
	+ addTwoNumbers : сложение двух чисел
		
	+ getGeneralInfo : общая информация о сайте
		
	-() getUsersBlogs : список всех авторов блога
	-() getUserInfo : информация о авторе
		
	+ newPost : новый пост
	+ setPost : создать или заменить/редактировать пост
	+ getRecentPostTitles : получить список всех постов (без текстов)
	+ getPost : получить текст поста
	- deletePost : удалить пост
		
	+ getCategoryList : рубрики
	- newCategory : создать рубрику
	- setCategory : заменить/редактировать рубрику
	- deleteCategory : удалить рубрику
	
	+ getTags : все метки
		
	- getFileNameUploads : список уже загруженных файлов
	- newMediaObject : загрузить файл
	- deleteMediaObject : удалить файл
 */ 

################################################################################
#
# начальная функция удаленного постинга
#
################################################################################

function mso_remote_post()
{
	# каждый запрос должен содержать логин, пароль, ключ безопасности, имя функции и её аргументы
	if ( $post = mso_check_post(array('remote_login', 'remote_password', 'remote_key', 'remote_function')) )
	{
		//pr($post);
		$post = _mso_trim_array($post);
		//pr($post);
		$remote_login = $post['remote_login'];
		$remote_password = $post['remote_password'];
		$remote_key = $post['remote_key'];

		$CI = & get_instance();
		
		# всегда проверяем логин и пароль
		
		$CI->db->from('users'); # таблица users
		$CI->db->select('users_id');
		$CI->db->limit(1); # одно значение
		$CI->db->where( array('users_login'=>$remote_login, 
							  'users_password'=>mso_md5($remote_password)) );
		
		$query = $CI->db->get();
		if ($query->num_rows() == 0) # нет такого - возможно взлом
		{
			echo 'MaxSite CMS REMOTE (http://max-3000.com/)ERROR: Login/password incorrect';
			return false;
		}
		else
		{
			if ( $remote_key != getinfo('remote_key') )
			{
				echo 'MaxSite CMS REMOTE (http://max-3000.com/)ERROR: Remote key incorrect';
				return false;
			}
			
			// вход корректный, добавим в $post данные юзера
			$r = $query->result_array();
			$post['remote_users_id'] = $r[0]['users_id'];
			$post['remote_users_login'] = $remote_login;
			$post['remote_users_password'] = mso_md5($remote_password);
		}
		
		
		
		# получаем имя функции
		$remote_function = $post['remote_function'];
		
		# формат вывода - для некоторых функций
		# есть только text, xml и html
		if (!isset($post['remote_format_out'])) $post['remote_format_out'] = 'text'; // формат по-умолчанию
		else
		{
			if ($post['remote_format_out'] == 'xml') $post['remote_format_out'] = 'xml';
			elseif ($post['remote_format_out'] == 'html') $post['remote_format_out'] = 'html';
			else $post['remote_format_out'] = 'text';
		}
		
		# контроль
		# echo 'remote_function : ' . $remote_function . "<br />";
		# echo 'remote_function_args : '; pr($remote_function_args);
		
################################################################################

		# выполняем запрашиваемую функцию
		
		$out = '';
		# hello
		if ($remote_function == 'hello')
		{
			$out = mso_remote_f_hello();
		}
		elseif ($remote_function == 'addtwonumbers')# addtwonumbers
		{
			$out = mso_remote_f_addtwonumbers($post);
		}
		elseif ($remote_function == 'getgeneralinfo') # getGeneralInfo : общая информация о сайте
		{
			$out = mso_remote_f_getgeneralinfo($post);
		}
		elseif ($remote_function == 'newpost') # newPost : новый пост
		{
			$out = mso_remote_f_newpost($post);
		}
		elseif ($remote_function == 'setpost')# setPost : заменить/редактировать пост
		{
			$out = mso_remote_f_setpost($post);
		}
		elseif ($remote_function == 'getrecentposttitles') # getRecentPostTitles : получить список всех постов (без текстов)
		{
			$out = mso_remote_f_getrecentposttitles($post);
		}
		elseif ($remote_function == 'getpost') # getPost : получить текст поста
		{
			$out = mso_remote_f_getpost($post);
		}
		elseif ($remote_function == 'deletepost')# deletePost : удалить пост
		{
			$out = mso_remote_f_deletepost($post);
		}
		elseif ($remote_function == 'getcategorylist')# getCategoryList : рубрики
		{
			$out = mso_remote_f_getcategorylist($post);
		}
		elseif ($remote_function == 'newcategory')# newCategory : создать рубрику
		{
			$out = mso_remote_f_newcategory($post);
		}
		elseif ($remote_function == 'setcategory')# setCategory : заменить/редактировать рубрику
		{
			$out = mso_remote_f_setcategory($post);
		}
		elseif ($remote_function == 'deletecategory')# deleteCategory : удалить рубрику
		{
			$out = mso_remote_f_deletecategory($post);
		}
		elseif ($remote_function == 'gettags')# getTags : все метки
		{
			$out = mso_remote_f_gettags($post);
		}
		elseif ($remote_function == 'getfilenameuploads')# getFileNameUploads : список уже загруженных файлов
		{
			$out = mso_remote_f_getfilenameuploads($post);
		}
		elseif ($remote_function == 'newmediaobject')# newMediaObject : загрузить файл
		{
			$out = mso_remote_f_newmediaobject($post);
		}
		elseif ($remote_function == 'deletemediaobject')# deleteMediaObject : удалить файл
		{
			$out = mso_remote_f_deletemediaobject($post);
		}
		else 
		{
			# ни одной функции не выполнилось - выходим
			$out = 'MaxSite CMS REMOTE (http://max-3000.com/)ERROR: No access (unknow function)';
		}
		
		if ($out) # есть результат
		{
			if ($post['remote_format_out'] == 'xml') header("Content-type: text/xml");
			
			// в начало нужно добавить сигнатуру MaxSite CMS
			// если этой сигнатуры нет, блог-клиент должен определить результат как ошибка
			echo 'MaxSite CMS REMOTE (http://max-3000.com/)' . $out; 
		}
		else
		{
			echo 'MaxSite CMS REMOTE (http://max-3000.com/)'; // фиктивный пустой результ
		}
	}
	else
	{
		echo 'MaxSite CMS REMOTE (http://max-3000.com/)ERROR: missing arguments';
	}
}

################################################################################
#
# обьявления функций
#
################################################################################

# функция addtwonumbers
function mso_remote_f_addtwonumbers($post)
{
	# в начале всегда проверяем корректность полученных аргументов данной функции
	# если неверные, то либо выводим ошибку, либо ставим дефолтные значения
	
	if (!isset($post['remote_function_args'][1])) $post['remote_function_args'][1] = 1;
	if (!isset($post['remote_function_args'][2])) $post['remote_function_args'][2] = 1;
	
	return $post['remote_function_args'][1] + $post['remote_function_args'][2];
}

# функция hello
function mso_remote_f_hello()
{
	return 'Hello!';
}

# getGeneralInfo : общая информация о сайте
function mso_remote_f_getgeneralinfo($post)
{
	$out = '';
	
	# версия MaxSite CMS 
	$out .= 'version=' . getinfo('version');
	# http-адрес сайта 
	$out .= NR . 'siteurl=' . getinfo('siteurl');
	# http-адрес шаблона 
	$out .= NR . 'stylesheet_url=' . getinfo('stylesheet_url');
	# текущий шаблон 
	$out .= NR . 'template=' . getinfo('template');
	# полный путь к каталогу шаблона 
	$out .= NR . 'template_dir=' . getinfo('template_dir');
	# полный путь к каталогам всех шаблонов (templates) 
	$out .= NR . 'templates_dir=' . getinfo('templates_dir');
	# http-адрес для нового комментария 
	$out .= NR . 'url_new_comment=' . getinfo('url_new_comment');
	# http-адрес RSS-ленты сайта 
	$out .= NR . 'rss_url=' . getinfo('rss_url');
	# http-адрес RSS-ленты сайта 
	$out .= NR . 'feed=' . getinfo('feed');
	# полный http-адрес админки (application/maxsite/admin/) 
	$out .= NR . 'admin_url=' . getinfo('admin_url');
	# http-адрес админки (сайт/admin/) 
	$out .= NR . 'site_admin_url=' . getinfo('site_admin_url');
	# полный путь к каталогу common 
	$out .= NR . 'common_dir=' . getinfo('common_dir');
	# полный http-адрес к common (application/maxsite/comon/) 
	$out .= NR . 'common_url=' . getinfo('common_url');
	# http-путь к uploads (сайт/uploads/) 
	$out .= NR . 'uploads_url=' . getinfo('uploads_url');
	# полный путь к каталогу uploads 
	$out .= NR . 'uploads_dir=' . getinfo('uploads_dir');
	# name_site - название сайта 
	$out .= NR . 'name_site=' . getinfo('name_site');
	# description_site - описание сайта 
	$out .= NR . 'description_site=' . getinfo('description_site');
	# титул сайта 
	$out .= NR . 'title=' . getinfo('title');
	# описание сайта в meta description 
	$out .= NR . 'description=' . getinfo('description');
	# keywords - ключевые слова сайта в meta description 
	$out .= NR . 'keywords=' . getinfo('keywords');
	# временная зона сервера 
	$out .= NR . 'time_zone=' . getinfo('time_zone');
	# http-путь к каталогу plugins 
	$out .= NR . 'plugins_url=' . getinfo('plugins_url');
	# полный путь к каталогу plugins 
	$out .= NR . 'plugins_dir=' . getinfo('plugins_dir');
	# http-путь для AJAX (сайт/ajax/) 
	$out .= NR . 'ajax=' . getinfo('ajax');
	# полный путь к каталогу admin/plugins 
	$out .= NR . 'admin_plugins_dir=' . getinfo('admin_plugins_dir');
	# информация о текущем юзере
	$out .= NR . 'remote_users_id=' . $post['remote_users_id'];
	$out .= NR . 'remote_users_login=' . $post['remote_users_login'];
	
	// получаем все типы страниц
	$CI = & get_instance();
	$all_post_types = '';
	$query = $CI->db->get('page_type');
	$out .= NR . 'page_type=';
	foreach ($query->result_array() as $row)
	{
		$out .= $row['page_type_id'] . '=' . $row['page_type_name'] . '!RMTNR!';
	}
	
	return $out;
}

# newPost : новый пост
function mso_remote_f_newpost($post)
{
	if (!isset($post['remote_page_title']) or !$post['remote_page_title']) return 'ERROR: missing arguments (remote_page_title)';
	if (!isset($post['remote_page_content']) or !$post['remote_page_content']) return 'ERROR: missing arguments (remote_page_content)';
	if (!isset($post['remote_page_id_autor']) or !$post['remote_page_id_autor']) return 'ERROR: missing arguments (remote_page_id_autor)';
	 
	// if (!isset($post['remote_page_cat_ids']) or !$post['remote_page_cat_ids']) return 'ERROR: missing arguments (remote_page_cat_ids)';
	// if (!isset($post['remote_page_tags']) or !$post['remote_page_tags']) return 'ERROR: missing arguments (remote_page_tags)';
	// if (!isset($post['remote_page_slug']) or !$post['remote_page_slug']) return 'ERROR: missing arguments (remote_page_slug)';
	// if (!isset($post['remote_page_date_publish']) or !$post['remote_page_date_publish']) return 'ERROR: missing arguments (remote_page_date_publish)';
	// if (!isset($post['remote_page_type_id']) or !$post['remote_page_type_id']) return 'ERROR: missing arguments ()';
	// if (!isset($post['remote_page_status']) or !$post['remote_page_status']) return 'ERROR: missing arguments (remote_page_status)';
	// if (!isset($post['remote_page_password']) or !$post['remote_page_password']) return 'ERROR: missing arguments (remote_page_password)';
	// if (!isset($post['remote_page_id_parent']) or !$post['remote_page_id_parent']) return 'ERROR: missing arguments (remote_page_id_parent)';
	// if (!isset($post['remote_page_meta_options']) or !$post['remote_page_meta_options']) return 'ERROR: missing arguments (remote_page_meta_options)';

	$f_comment_allow = isset($post['remote_page_comment_allow']) ? '1' : '0';
	$f_ping_allow = isset($post['remote_page_ping_allow']) ? '1' : '0';
	$f_feed_allow = isset($post['remote_page_feed_allow']) ? '1' : '0';
	$f_date_change = isset($post['remote_page_date_change']) ? '1' : '0'; // сменить дату?
	
	if ( $f_date_change and isset($post['remote_page_date_publish']) and $post['remote_page_date_publish'] )
		$f_date_publish = $post['remote_page_date_publish'];
	else
		$f_date_publish = false;

	$data = array(
		'page_title' => $post['remote_page_title'],
		'page_content' => $post['remote_page_content'],
		'page_comment_allow' => $f_comment_allow,
		'page_ping_allow' => $f_ping_allow,
		'page_feed_allow' => $f_feed_allow,
		'user_login' => $post['remote_users_login'],
		'password' => $post['remote_users_password'],
		'page_id_autor' => $post['remote_page_id_autor'],
	);
	
	if ($f_date_publish) $data['page_date_publish'] = $f_date_publish;
	
	if (isset($post['remote_page_slug'])) $data['page_slug'] = $post['remote_page_slug'];
	
	if (isset($post['remote_page_tags'])) $data['page_tags'] = $post['remote_page_tags'];
	if (isset($post['remote_page_type_id'])) $data['page_type_id'] = $post['remote_page_type_id'];
	if (isset($post['remote_page_status'])) $data['page_status'] = $post['remote_page_status'];
	if (isset($post['remote_page_password'])) $data['page_password'] = $post['remote_page_password'];
	if (isset($post['remote_page_id_parent'])) $data['page_id_parent'] = $post['remote_page_id_parent'];
	if (isset($post['remote_page_meta_options'])) $data['page_meta_options'] = $post['remote_page_meta_options'];
	if (isset($post['remote_page_id_cat'])) $data['page_id_cat'] = $post['remote_page_id_cat'];

	//return pr($data, false, false);

	require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
	
	
	$result = mso_new_page($data);
	
	if (isset($result['result']) and $result['result']) 
	{
		mso_flush_cache(); // сбросим кэш
		$out = 'OK: ' . $result['description'] 
			. '#' . $result['result'][0] // id
			. '#' . $result['result'][1] // slug
			. '#' . $result['result'][2]; // status
		
		// return pr($result['result'], false, false);
		
		return $out;
	}
	else
	{
		$out = 'ERROR: ' . $result['description'];
		return $out;
	}
}


# setPost : заменить/редактировать пост
function mso_remote_f_setpost($post)
{
	if (!isset($post['remote_page_id']) or !$post['remote_page_id']) return 'ERROR: missing arguments (remote_page_id)';
	if (!isset($post['remote_page_title']) or !$post['remote_page_title']) return 'ERROR: missing arguments (remote_page_title)';
	if (!isset($post['remote_page_content']) or !$post['remote_page_content']) return 'ERROR: missing arguments (remote_page_content)';
	if (!isset($post['remote_page_id_autor']) or !$post['remote_page_id_autor']) return 'ERROR: missing arguments (remote_page_id_autor)';
	 
	// if (!isset($post['remote_page_cat_ids']) or !$post['remote_page_cat_ids']) return 'ERROR: missing arguments (remote_page_cat_ids)';
	// if (!isset($post['remote_page_tags']) or !$post['remote_page_tags']) return 'ERROR: missing arguments (remote_page_tags)';
	// if (!isset($post['remote_page_slug']) or !$post['remote_page_slug']) return 'ERROR: missing arguments (remote_page_slug)';
	// if (!isset($post['remote_page_date_publish']) or !$post['remote_page_date_publish']) return 'ERROR: missing arguments (remote_page_date_publish)';
	// if (!isset($post['remote_page_type_id']) or !$post['remote_page_type_id']) return 'ERROR: missing arguments ()';
	// if (!isset($post['remote_page_status']) or !$post['remote_page_status']) return 'ERROR: missing arguments (remote_page_status)';
	// if (!isset($post['remote_page_password']) or !$post['remote_page_password']) return 'ERROR: missing arguments (remote_page_password)';
	// if (!isset($post['remote_page_id_parent']) or !$post['remote_page_id_parent']) return 'ERROR: missing arguments (remote_page_id_parent)';
	// if (!isset($post['remote_page_meta_options']) or !$post['remote_page_meta_options']) return 'ERROR: missing arguments (remote_page_meta_options)';

	$f_comment_allow = isset($post['remote_page_comment_allow']) ? '1' : '0';
	$f_ping_allow = isset($post['remote_page_ping_allow']) ? '1' : '0';
	$f_feed_allow = isset($post['remote_page_feed_allow']) ? '1' : '0';
	$f_date_change = isset($post['remote_page_date_change']) ? '1' : '0'; // сменить дату?
	
	if ( $f_date_change and isset($post['remote_page_date_publish']) and $post['remote_page_date_publish'] )
		$f_date_publish = $post['remote_page_date_publish'];
	else
		$f_date_publish = false;

	$data = array(
		'page_id' => $post['remote_page_id'],
		'page_title' => $post['remote_page_title'],
		'page_content' => $post['remote_page_content'],
		'page_comment_allow' => $f_comment_allow,
		'page_ping_allow' => $f_ping_allow,
		'page_feed_allow' => $f_feed_allow,
		'user_login' => $post['remote_users_login'],
		'password' => $post['remote_users_password'],
		'page_id_autor' => $post['remote_page_id_autor'],
	);
	
	if ($f_date_publish) $data['page_date_publish'] = $f_date_publish;
	
	if (isset($post['remote_page_slug'])) $data['page_slug'] = $post['remote_page_slug'];
	
	if (isset($post['remote_page_tags'])) $data['page_tags'] = $post['remote_page_tags'];
	if (isset($post['remote_page_type_id'])) $data['page_type_id'] = $post['remote_page_type_id'];
	if (isset($post['remote_page_status'])) $data['page_status'] = $post['remote_page_status'];
	if (isset($post['remote_page_password'])) $data['page_password'] = $post['remote_page_password'];
	if (isset($post['remote_page_id_parent'])) $data['page_id_parent'] = $post['remote_page_id_parent'];
	if (isset($post['remote_page_meta_options'])) $data['page_meta_options'] = $post['remote_page_meta_options'];
	if (isset($post['remote_page_id_cat'])) $data['page_id_cat'] = $post['remote_page_id_cat'];

	//return pr($data, false, false);

	require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
	
	
	$result = mso_edit_page($data);
	
	if (isset($result['result']) and $result['result']) 
	{
		mso_flush_cache(); // сбросим кэш
		$out = 'OK: ' . $result['description'] 
			. '#' . $result['result'][0] // id
			. '#' . $result['result'][1] // slug
			. '#' . $result['result'][2]; // status
		
		// return pr($result['result'], false, false);
		
		return $out;
	}
	else
	{
		$out = 'ERROR: ' . $result['description'];
		return $out;
	}
}


# getRecentPostTitles : получить список всех постов (без текстов)
function mso_remote_f_getrecentposttitles($post)
{
	require_once(getinfo('common_dir') . 'page.php');
	
	$par = array(
		'custom_type'=> 'home', 
		'pagination' => false,
		'content'=> false,
		'no_limit' => true,
		'type' => false,
		'page_id_autor'=> $post['remote_users_id'],  // - только указанного автора
		'date_now' => false, // все, независимо от даты
		'page_status' => false, // не зависимо от статуса
		); 

	// если юзеру разрешено редактировать чужие страницы, то 'page_id_autor' сбрасываем
	// иначе только свои страницы можно загружать
	if ( mso_check_user_password($post['remote_users_login'], $post['remote_users_password'], 'admin_page_edit_other') )
		$par['page_id_autor'] = false;

				
	$pages = mso_get_pages($par, $pagination); // получим странички
	
	$out = '';
	
	if ($pages)
	{
		foreach ($pages as $page)
		{
			$out .= NR  . 'page_id=' . $page['page_id'];
			$out .= '!RMTNR!' . 'page_title=' . $page['page_title'];
			$out .= '!RMTNR!' . 'page_type_name=' . $page['page_type_name'];
			$out .= '!RMTNR!' . 'page_slug=' . $page['page_slug'];
			$out .= '!RMTNR!' . 'page_date_publish=' . $page['page_date_publish'];
			$out .= '!RMTNR!' . 'page_status=' . $page['page_status'];
			$out .= '!RMTNR!' . 'page_id_parent=' . $page['page_id_parent'];
			$out .= '!RMTNR!' . 'page_id_autor=' . $page['page_id_autor'];
		}	
	}
	
	return trim($out);
}



# getPost : получить текст поста
function mso_remote_f_getpost($post)
{
	if (!isset($post['remote_page_id']) or !$post['remote_page_id'])
		return 'ERROR: missing arguments';
		
	require_once(getinfo('common_dir') . 'page.php');
	require_once( getinfo('common_dir') . 'inifile.php' ); // функции для работы с ini-файлом
	
	// удалим хуки для текста - должно отдаваться как в базе
	mso_remove_hook('content');
	mso_remove_hook('content_auto_tag');
	mso_remove_hook('content_balance_tags');
	mso_remove_hook('content_out');
	mso_remove_hook('content_complete');
	
	$par = array(
		'custom_type'=> 'home', 
		'pagination' => false,
		'content'=> true,
		'work_cut' => false, // [cut] не обрабатывать - отдать как есть 
		'type' => false,
		'page_id' => $post['remote_page_id'],
		'date_now' => false, // все, независимо от даты
		'page_status' => false, // не зависимо от статуса
		'page_id_autor'=> $post['remote_users_id'],  // - только указанного автора
		'all_fields'=> true,  // все поля page
		);
		
	// если юзеру разрешено редактировать чужие страницы, то 'page_id_autor' сбрасываем
	// иначе только свои страницы можно загружать
	if ( mso_check_user_password($post['remote_users_login'], $post['remote_users_password'], 'admin_page_edit_other') )
		$par['page_id_autor'] = false;	 
	
	$pages = mso_get_pages($par, $pagination); // получим странички
	
	$out = '';
	if ($pages)
	{
		foreach ($pages as $page)
		{
			foreach($page as $p_name => $p_val)
			{
				
				if ($p_name == 'page_categories_detail') continue;
				elseif ($p_name == 'users_description') continue;
				elseif ($p_name == 'users_avatar_url') continue;
				elseif ($p_name == 'users_login') continue;
				elseif ($p_name == 'page_content') $p_val = str_replace("\n", '!RMTNR!', $p_val);
				elseif ($p_name == 'page_categories') 
				{
					// это массив, где хранятся id рубрик
					$p_val = implode(" ", $p_val);
				}
				elseif ($p_name == 'page_tags') 
				{
					// это массив, где хранятся имена меток
					$p_val = implode('!RMTNR!', $p_val);
				}
				elseif ($p_name == 'page_meta') 
				{
					//  это массивы в массиве
					$pm = '';
					foreach($p_val as $page_meta_key => $page_meta_val)
					{
						$pm .= '!RMTMETA!' . $page_meta_key . '=' . implode('!RMTNR!', $page_meta_val);
					}
					$p_val = $pm;
					
				}
				elseif ($p_name == 'page_date_publish')
				{
					//page_date_publish=2009-01-05 22:27:22
					$out .= NR . 'page_date_publish_year=' . mso_date_convert('Y', $p_val, false);
					$out .= NR . 'page_date_publish_mon=' . mso_date_convert('m', $p_val, false);
					$out .= NR . 'page_date_publish_day=' . mso_date_convert('d', $p_val, false);
					$out .= NR . 'page_date_publish_hour=' . mso_date_convert('H', $p_val, false);
					$out .= NR . 'page_date_publish_min=' . mso_date_convert('i', $p_val, false);
					$out .= NR . 'page_date_publish_sec=' . mso_date_convert('s', $p_val, false);
					
				}
				elseif (is_array($p_val)) continue;

				$out .= NR . $p_name . '=' . $p_val;
			}
		}
		
		# нужно передать и все метаполя, которые в шаблоне и admin_page/meta.ini. 
		# Они могут быть пустыми, поэтому их нет в page_meta
		
		$all = mso_get_ini_file( getinfo('admin_plugins_dir') . 'admin_page/meta.ini');
		
		if (file_exists(getinfo('template_dir') . 'meta.ini')) 
		{
			$meta_templ = mso_get_ini_file( getinfo('template_dir') . 'meta.ini' );
			if ($meta_templ) $all = array_merge($all, $meta_templ);
		}
		$pm = '';
		foreach($all as $key => $val)
		{
			$pm .= '!RMTMETA!' . $key . '=' . _mso_implode($val);
		}
		$out .= NR . 'page_meta_template=' . $pm;
	}
	else
	{
		return 'ERROR: no page';
	}
	
	return trim($out);
}


# deletePost : удалить пост
function mso_remote_f_deletepost($post)
{
	if ( $post = mso_check_post(array('remote_page_id')) )
	{
		$page_id = (int) $post['remote_page_id'];

    $data = array(
//      'user_login' => $MSO->data['session']['users_login'],
//			'password' => $MSO->data['session']['users_password'],
			'page_id' => $page_id,
		);
			
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		$result = mso_delete_page($data);
			
		if (isset($result['result']) and $result['result']) 
		{
			mso_flush_cache(); // сбросим кэш
			$out = 'OK: ' . $result['description'];
			return $out;
		}
		else
		{
			$out = 'ERROR: ' . $result['description'];
			return $out;
		}
	}
}

# getCategoryList : рубрики
function mso_remote_f_getcategorylist($post)
{
	$out = '';
	require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик 

	$pages = mso_cat_array('page', 0);
	
	$out = mso_cat_ul('%ID% %NAME%', true, array(), array());
	
	$out = str_replace('</li>' , '', $out);
	$out = str_replace('</ul>' , '', $out);
	$out = str_replace('<ul>' , '', $out);
	$out = str_replace('<ul class="child">' , '', $out);
	$out = str_replace('<ul class="category">' , '', $out);
	$out = str_replace("\t" , '', $out);
	$out = str_replace("\n\n" , "\n", $out);
	$out = str_replace("\n\n" , "\n", $out);
	$out = str_replace(" " , " ", $out);
	$out = str_replace('<li class="' , '', $out);
	$out = str_replace('">' , ' ', $out);
	$out = str_replace('count' , '', $out);
	$out = str_replace('level' , '', $out);
	
	return trim($out);
}


# newCategory : создать рубрику
function mso_remote_f_newcategory($post)
{
	if ( $post = mso_check_post(array('category_id_parent', 'category_name',
		'category_desc', 'category_slug', 'category_menu_order')) )
	{
		// подготавливаем данные
		$data = array(
			'category_id_parent' => (int) $post['category_id_parent'],
			'category_name' => $post['category_name'],
			'category_desc' => $post['category_desc'],
			'category_slug' => $post['category_slug'],
			'category_menu_order' => (int) $post['category_menu_order'],

			'user_login' => $post['remote_login'],
			'password' => mso_md5($post['remote_password'])   
			);
		
		// выполняем запрос и получаем результат
		require_once( getinfo('common_dir') . 'category.php' );
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_new_category($data);
		
		if (isset($result['result']) and $result['result']) 
		{
			mso_flush_cache(); // сбросим кэш
			$out = 'OK: ' . $result['description'];
			return $out;
		}
		else
		{
			$out = 'ERROR: ' . $result['description'];
			return $out;
		}
	}
	else
	{
		$out = 'ERROR: ' . 'missing arguments';
		return $out;
	}
}


# setCategory : создать или заменить/редактировать рубрику
function mso_remote_f_setcategory($post)
{
	if ( $post = mso_check_post(array('category_id', 'category_id_parent',
		'category_name', 'category_desc', 'category_slug',
		'category_menu_order')) )
	{
		// получаем номер категории 
		$f_id = $post['category_id'];
		
		// подготавливаем данные
		$data = array(
			'category_id' => $f_id,
			'category_id_parent' => (int) $post['category_id_parent'],
			'category_name' => $post['category_name'],
			'category_desc' => $post['category_desc'],
			'category_slug' => $post['category_slug'],
			'category_menu_order' => (int) $post['category_menu_order']
			);
		
		// выполняем запрос и получаем результат
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_edit_category($data);
		
		if (isset($result['result']) and $result['result']) 
		{
			mso_flush_cache(); // сбросим кэш
			$out = 'OK: ' . $result['description'];
			return $out;
		}
		else
		{
			$out = 'ERROR: ' . $result['description'];
			return $out;
		}
	}
	else
	{
		$out = 'ERROR: ' . 'missing arguments';
		return $out;
	}
}

# deleteCategory : удалить рубрику
function mso_remote_f_deletecategory($post)
{
	if ( $post = mso_check_post(array('category_id')) )
	{ 
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования

		// получаем номер категории 
		$f_id = $post['category_id'];

		// подготавливаем данные
		$data = array('category_id' => $f_id );

		$result = mso_delete_category($data);

		if (isset($result['result']) and $result['result']) 
		{	
			mso_flush_cache(); // сбросим кэш
			$out = 'OK: ' . $result['description'];
		}
		else
			$out = 'ERROR: ' . $result['description'];
		return $out;
	}
	else
	{
		$out = 'ERROR: ' . 'missing arguments';
		return $out;
	}
}

# getTags : все метки
function mso_remote_f_gettags($post)
{
	require_once(getinfo('common_dir') . 'meta.php');

	$out = '';
	
	if ($post['remote_format_out'] == 'xml') $out .= '<' . '?xml version="1.0" encoding="UTF-8"?' . '>';
	if ($post['remote_format_out'] == 'xml') $out .= NR . '<tags>';
	
	$all_tags = mso_get_all_tags_page();
	if ($all_tags)
	{
		foreach ($all_tags as $tag_name => $tag_count)
		{
			if ($post['remote_format_out'] == 'xml') 
				$out .= NR . '<tag count="' . $tag_count . '">' . $tag_name . '</tag>';
			else
				$out .= NR . 'tag=' . $tag_count . ' ' . $tag_name;
			
		}
	}
	if ($post['remote_format_out'] == 'xml') $out .= '</tags>';
	return trim($out);
}


# getFileNameUploads : список уже загруженных файлов
function mso_remote_f_getfilenameuploads($post)
{
	$out = 'getFileNameUploads';
	return $out;
}


# newMediaObject : загрузить файл
function mso_remote_f_newmediaobject($post)
{
	$out = 'newMediaObject';
	return $out;
}


# deleteMediaObject : удалить файл
function mso_remote_f_deletemediaobject($post)
{
	$out = 'deleteMediaObject';
	return $out;
}



################################################################################
# 
# Вспомогательные функции
#
################################################################################

// функция разворачивания массивов
function _my_expand_array($ar, $tag = 'item', $format = 'xml')
{
	$out = '';
	foreach($ar as $v_name => $v_val)
	{
		$use_tag = $v_name ? $v_name : $tag;
		$use_tag = is_numeric($use_tag) ? $tag : $use_tag; // xml не любит цифровые тэги
		if (is_array($v_val))
		{
			$out .= _my_expand_array($v_val, $use_tag, $format);
		}
		else
		{
			if ($format == 'xml')
				$out .= NR . '<' . $use_tag . '>' . $v_val . '</' . $use_tag . '>';
			else // текст
				$out .= NR . "\t" . $use_tag . '=' . $v_val;
		}
	}
	return $out;
}


# функция обработки каждого элмента массива trim (чтобы удалить мусор ко краям)
function _mso_trim_array($post)
{
	$out = array();
	foreach($post as $key => $val)
	{
		if (is_array($val))
		{
			$out[$key] = _mso_trim_array($val);
		}
		else
		{
			$out[$key] = trim($val);
		}
	}
	
	return $out;
}


# функция переделки массива в строку
function _mso_implode($ar = array())
{
	$out = '';
	
	foreach($ar as $key=>$val)
	{
		if (is_array($val))
		{
			foreach($val as $key1=>$val1)
			{
				if (is_array($val1)) 
				{
					foreach($val1 as $key2=>$val2)
					{
						if (is_array($val2)) $out .= '!RMTL2!' . 'novalue';
							else $out .= '!RMTL2!' . $key . '=' . $key1 . '=' . $key2 . '=' . $val2;
					}
				}
				else $out .= '!RMTL1!' . $key . '=' . $key1 . '=' . $val1;
			}
		}
		else
		{
			$out .= '!RMTL0!' . $key . '=' . $val;
		}
	}
	
	return $out;
}
################################################################################


mso_remote_post();

