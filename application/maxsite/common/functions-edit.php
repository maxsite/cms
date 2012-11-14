<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Функции для редактирования, удаления, добавления
 *
 */

##### ------------------------------------------------------------------------------
##### Рубрики
##### ------------------------------------------------------------------------------

	# редактирование рубрики
	function mso_edit_category($data)
	{
		global $MSO;

		$CI = & get_instance();

		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];

		# проверка доступа этому пользователю с этим паролем и этим разрешением
		if ( !mso_check_user_password($user_login, $password, 'admin_cat') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');

		
		$data = mso_xss_clean_data($data, array('category_name', 'category_slug'), true, true);
		$data = mso_xss_clean_data($data, array('category_desc'));
		
		$category_id = isset($data['category_id']) ? (int) $data['category_id'] : 0;
		$category_id_parent = isset($data['category_id_parent']) ? (int) $data['category_id_parent'] : 0;
		$category_menu_order = isset($data['category_menu_order']) ? (int) $data['category_menu_order'] : 0;
		$category_name = isset($data['category_name']) ? trim($data['category_name']) : '';
		$category_desc = isset($data['category_desc']) ? trim($data['category_desc']) : '';

		$ok = true;

		if ($category_id <= 0) $ok = false;
		if ($ok and ($category_id_parent < 0)) $ok = false;
		if ($ok and ($category_menu_order < 0)) $ok = false;
		if ($ok and ($category_name == '')) $ok = false;
		
		if ($ok) // все ок - выполняем sql запрос
		{
			
			$category_slug = (isset($data['category_slug']) and $data['category_slug']) ? mso_slug($data['category_slug']) : mso_slug($category_name);
			
			# проверим, чтобы category_id_parent был существующим, а также не был равен category_id
			# то есть не ссылался сам на себя
			if ($category_id_parent > 0)
			{
				$all_cats = mso_cat_array_single();
				if (!isset($all_cats[$category_id_parent])) // нет такой рубрики
					$category_id_parent = 0; // сбрасываем парент
				if ($category_id_parent == $category_id) $category_id_parent = 0; // сбрасываем парент
			}

			$CI->db->where('category_id', $category_id);

			$upd_data = array (
					'category_id_parent' => $category_id_parent,
					'category_menu_order' => $category_menu_order,
					'category_name' => $category_name,
					'category_desc' => $category_desc,
					'category_slug' => $category_slug
					);

			$res = ($CI->db->update('category', $upd_data)) ? '1' : '0';

			$response = array(
							'result' => $res,
							'description'=>''
							);
			mso_flush_cache(); // сбросим кэш
		}
		else
		{
			$response = array('result'=> '0', 'description'=>'Error data');
		}

		if ($response['result']) mso_hook('edit_category');

		return $response;
	}

	# добавление новой рубрики
	function mso_new_category($data)
	{
		global $MSO;

		$CI = & get_instance();

		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];

		# проверка доступа этому пользователю с этим паролем и этим разрешением
		if ( !mso_check_user_password($user_login, $password, 'admin_cat') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');
		
		$data = mso_xss_clean_data($data, array('category_name', 'category_slug'), true, true);
		$data = mso_xss_clean_data($data, array('category_desc'));
		
		$category_id_parent = isset($data['category_id_parent']) ? (int) $data['category_id_parent'] : 0;
		$category_menu_order = isset($data['category_menu_order']) ? (int) $data['category_menu_order'] : 0;
		$category_name = isset($data['category_name']) ? trim($data['category_name']) : '';
		$category_desc = isset($data['category_desc']) ? trim($data['category_desc']) : '';
		$category_slug = isset($data['category_slug']) ? mso_slug($data['category_slug']) : '';

		$ok = true;

		if ($ok and ($category_name == '')) $ok = false;
		if ($ok and ($category_slug == '')) $category_slug = mso_slug($category_name);
		if ($ok and ($category_id_parent < 0)) $category_id_parent = 0;
		if ($ok and ($category_menu_order < 0)) $category_menu_order = 0;


		if ($ok) // все ок - выполняем sql запрос
		{
			// проверим существование уже такой же рубрики с таким же именем
			$CI->db->select('category_id');
			// $CI->db->where(array('category_name'=>$category_name, 'category_id_parent'=>$category_id_parent));
			$CI->db->where(array('category_slug'=>$category_slug));
			$query = $CI->db->get('category');
			
			if ($query->num_rows() == 0 ) // нет такого
			{
				# проверим, чтобы category_id_parent был существующим
				if ($category_id_parent > 0)
				{
					$all_cats = mso_cat_array_single();
					if (!isset($all_cats[$category_id_parent])) // нет такой рубрики
						$category_id_parent = 0; // сбрасываем парент
				}

				$ins_data = array (
					'category_id_parent' => $category_id_parent,
					'category_menu_order' => $category_menu_order,
					'category_name' => $category_name,
					'category_desc' => $category_desc,
					'category_slug' => $category_slug
					);

				$res = ($CI->db->insert('category', $ins_data)) ? '1' : '0';

				if ($res)
				{
					$response =	array(
									'result' => $res,
									'description'=>'Inserting new category'
								);
					mso_flush_cache(); // сбросим кэш
				}
				else
					$response =	array(
									'result' => 0,
									'description'=>'Error inserting new category'
								);

			}
			else
			{   // есть такая рубрика - не добавляем
				$response = array('result'=>'0', 'description'=>'Category slug existing');
			}
		}
		else
		{
			$response = array('result'=> '0', 'description'=>'Error data');
		}

		if ($response['result']) mso_hook('new_category');

		return $response;
	}


	# удаление рубрики
	function mso_delete_category($data)
	{
		global $MSO;

		$CI = & get_instance();

		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];

		# проверка доступа этому пользователю с этим паролем и этим разрешением
		if ( !mso_check_user_password($user_login, $password, 'admin_cat') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');

		$category_id = isset($data['category_id']) ? (int) $data['category_id'] : 0;

		if ($category_id > 0)// все ок - выполняем sql запрос
		{

			// нужно удалить у всех страниц этой рубрики эту рубрику
			$CI->db->where('category_id', $category_id);
			$CI->db->delete('cat2obj');

			# а также у всех у кого она стоит родительская
			$CI->db->where('category_id_parent', $category_id);
			$CI->db->update('category', array('category_id_parent'=>'0'));

			$CI->db->where('category_id', $category_id);
			$res = ($CI->db->delete('category')) ? '1' : '0';

			$response = array(
							'result' => $res,
							'description'=>''
						);
			mso_flush_cache(); // сбросим кэш
		}
		else
		{
			$response = array('result'=> '0', 'description'=>'Error data');
		}

		if ($response['result']) mso_hook('delete_category');

		return $response;
	}


##### ------------------------------------------------------------------------------
##### Юзеры
##### ------------------------------------------------------------------------------

	# редактирование юзера
	function mso_edit_user($data)
	{
		global $MSO;

		$CI = & get_instance();

		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];


		# проверка можно ли редактировать этому пользователю с этим паролем и этим разрешением
		if ( !mso_check_user_password($user_login, $password, 'edit_self_users') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');

		$users_id = isset($data['users_id']) ? (int) $data['users_id'] : 0;

		if ( $users_id <= 0 ) return array( 'result' => 0, 'description' => 'Login/password incorrect');

		# получаем данные пользователя, который отправил запрос
		$user_data = mso_get_user_data($user_login, $password);


		if ($users_id != $user_data['users_id']) // если id не совпадают, то проверяем разрешения
		{
			# если можно редактировать чужих пользователей
			if ( !mso_check_user_password($user_login, $password, 'edit_other_users') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');
		}
		
		$data = mso_xss_clean_data($data, array('users_nik'), true, true);
		
		$users_nik = isset($data['users_nik']) ? $data['users_nik'] : false;

		# ошибочный ник
		if (!$users_nik)
			return array( 'result' => 0, 'description' => 'Login/password incorrect');

		$data = mso_xss_clean_data($data, array('users_first_name', 'users_admin_note', 'users_last_name', 'users_email', 'users_icq', 'users_url', 'users_msn', 'users_jaber', 'users_skype', 'users_avatar_url', 'users_description', 'users_show_smiles', 'users_notify', 'users_language', 'users_new_password'));

		$users_first_name = isset($data['users_first_name']) ? $data['users_first_name'] : '';
		$users_admin_note = isset($data['users_admin_note']) ? $data['users_admin_note'] : '';
		$users_last_name = isset($data['users_last_name']) ? $data['users_last_name'] : '';
		$users_email = isset($data['users_email']) ? $data['users_email'] : '';
		$users_icq = isset($data['users_icq']) ? $data['users_icq'] : '';
		$users_url = isset($data['users_url']) ? $data['users_url'] : '';
		$users_msn = isset($data['users_msn']) ? $data['users_msn'] : '';
		$users_jaber = isset($data['users_jaber']) ? $data['users_jaber'] : '';
		$users_skype = isset($data['users_skype']) ? $data['users_skype'] : '';
		$users_avatar_url = isset($data['users_avatar_url']) ? $data['users_avatar_url'] : '';
		$users_description = isset($data['users_description']) ? $data['users_description'] : '';
		$users_date_birth_y = isset($data['users_date_birth_y']) ? (int) $data['users_date_birth_y'] : '1970';
		$users_date_birth_m = isset($data['users_date_birth_m']) ? (int) $data['users_date_birth_m'] : '1';
		$users_date_birth_d = isset($data['users_date_birth_d']) ? (int) $data['users_date_birth_d'] : '1';
		$users_time_zone = isset($data['users_time_zone']) ? (int) $data['users_time_zone'] : '7200';
		$users_show_smiles = isset($data['users_show_smiles']) ? $data['users_show_smiles'] : '0';
		$users_notify = isset($data['users_notify']) ? $data['users_notify'] : '0';
		$users_language = isset($data['users_language']) ? $data['users_language'] : 'ru';

		if (isset($data['users_new_password']))
		{
			$users_new_password = mso_strip($data['users_new_password'], true);
			if ( $users_new_password and ( strlen($users_new_password) < 6) )
				$users_new_password = false;
			else $users_new_password = mso_md5($users_new_password);
		}
		else
			$users_new_password = false;


		# конвер даты в MySQL
		$users_date_birth = mso_date_convert_to_mysql($users_date_birth_y, $users_date_birth_m, $users_date_birth_d);

		$upd_data = array (
			'users_nik' => $users_nik,
			'users_first_name' => $users_first_name,
			'users_last_name' => $users_last_name,
			'users_admin_note' => $users_admin_note,
			'users_email' => $users_email,
			'users_icq' => $users_icq,
			'users_url' => $users_url,
			'users_msn' => $users_msn,
			'users_jaber' => $users_jaber,
			'users_skype' => $users_skype,
			'users_avatar_url' => $users_avatar_url,
			'users_description' => $users_description,
			'users_date_birth' => $users_date_birth,
			'users_time_zone' => $users_time_zone,
			'users_show_smiles' => $users_show_smiles,
			'users_notify' => $users_notify,
			'users_language' => $users_language
			);


		# можно ли менять группу?
		if ($users_id > 1) // если юзер = 1 (админ) - то нельзя
		{
			# проверяем разрешение у того, кто отправляет запрос
			if ( mso_check_allow('edit_users_group', $user_data['users_id'], false) ) // да можно

			{
				$users_groups_id = isset($data['users_groups_id']) ? $data['users_groups_id'] : 0;
				$users_groups_id = (int) $users_groups_id;
				if ( $users_groups_id >0 )
					$upd_data['users_groups_id'] = $users_groups_id; // добавляем еще и это
			}
		}

		### пароль!!!
		if ($users_new_password) // есть новый пароль
		{
			// если id не совпадают, то проверяем разрешения на смену пароля другому юзеру
			if ($users_id != $user_data['users_id'])
			{
				if ( mso_check_allow('edit_users_password', $user_data['users_id']) ) // да можно
					$upd_data['users_password'] = $users_new_password;
			}
			else // меняет сам себе
			{
				$upd_data['users_password'] = $users_new_password;
			}
		}

		$CI->db->where('users_id', $users_id);
		$res = ($CI->db->update('users', $upd_data)) ? '1' : '0';

		$response = array(
						'result' => $res,
						'description' => 'Update'
						);
		mso_flush_cache(); // сбросим кэш

		return $response;
	}


	# новый юзер
	function mso_new_user($data, $check_user_password = true)
	{
		global $MSO;

		$CI = & get_instance();

		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];

		# нужно ли проверять разрешение?
		if ($check_user_password)
		{
			# проверка доступа этому пользователю с этим паролем и этим разрешением
			if ( !mso_check_user_password($user_login, $password, 'edit_add_new_users') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');
		}
		
		
		$data = mso_xss_clean_data($data, array('users_login', 'users_email', 'users_password', 'users_groups_id'));
		
		
		$users_login = isset($data['users_login']) ? $data['users_login'] : false;
		$users_email = isset($data['users_email']) ? $data['users_email'] : false;
		$users_password = isset($data['users_password']) ? $data['users_password'] : false;
		$users_groups_id = isset($data['users_groups_id']) ? $data['users_groups_id'] : false;

		$ok = true;
		$description = 'Error input data';
		
		# множественная проверка входных данных
		if ( !$users_login or !$users_email or !$users_password or !$users_groups_id ) $ok = false;


		if ($ok)
		{
			$users_login = mso_strip($users_login , true);
			$users_email = mso_strip($users_email , true);
			$users_password = mso_strip($users_password , true);
			$users_groups_id = (int) mso_strip($users_groups_id , true);

			if ( !$users_login or !$users_email or !$users_password or !$users_groups_id ) $ok = false;

			if ($ok)
			{
				if ( !mso_valid_email($users_email) ) 
				{
					$ok = false;
					$description = 'Error email';
				}
				if ( $ok and strlen($users_password) < 6) 
				{
					$ok = false;
					$description = 'Error password (less than 6 characters)';
				}
			}
		}

		if ($ok)
		{
			# может такой логин уже занят?
			$CI->db->select('users_id');
			$CI->db->where('users_login', $users_login);
			$CI->db->or_where('users_email', $users_email);
			
			$query = $CI->db->get('users');
			if ($query->num_rows()) // что-то есть
			{
				$response = array(
							'result' => 0,
							'description' => 'Login or email already exists'
						);
			}
			else
			{
				$users_password = mso_md5($users_password);

				$ins_data = array (
					'users_login' => $users_login,
					'users_nik' => $users_login,
					'users_email' => $users_email,
					'users_groups_id' => $users_groups_id,
					'users_password' => $users_password
					);

				$res = ($CI->db->insert('users', $ins_data)) ? '1' : '0';

				$response = array(
								'result' => $res,
								'description' => 'Inserting new user'
								);
				mso_flush_cache(); // сбросим кэш
			}
		}
		else // ошибочные данные
		{
			$response = array(
							'result' => 0,
							'description' => $description
						);
		}

		return $response;
	}



	# удалить юзера
	function mso_delete_user($data, $check_user_password = true)
	{
		global $MSO;

		$CI = & get_instance();

		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];

		# нужно ли проверять разрешение?
		if ($check_user_password)
		{
			# проверка доступа этому пользователю с этим паролем и этим разрешением
			if ( !mso_check_user_password($user_login, $password, 'edit_delete_users') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');
		}

		$users_id = isset($data['users_id']) ? (int) $data['users_id'] : false;
		$delete_user_comments = isset($data['delete_user_comments']) ? $data['delete_user_comments'] : false;
		$delete_user_pages = isset($data['delete_user_pages']) ? $data['delete_user_pages'] : false;

		if ($users_id)
		{
			if ($delete_user_comments)
			{
				# удалить все комментарии юзера
				$CI->db->where( array ('comments_users_id' => $users_id) );
				$CI->db->delete('comments');
			}
			else
			{
				# отметить все комментарии юзера как анонима без имени
				$CI->db->where(array ('comments_users_id' => $users_id));
				$CI->db->update('comments', array('comments_users_id' => '0', 'comments_author_name' => t('Аноним') ) );
			}
			
			
			if ($delete_user_pages)
			{
				# удалить все записи юзера
				$CI->db->where( array ('page_id_autor' => $users_id) );
				$CI->db->delete('page');
			}
			else
			{
				# отметить у записей юзера автор = 1 - админ
				$CI->db->where(array ('page_id_autor' => $users_id));
				$CI->db->update('page', array('page_id_autor' => '1') );			
			}
			
			# теперь можно удалить самого юзера
			$res = $CI->db->delete('users', array ('users_id' => $users_id));			
			
			$response = array(
								'result' => $res,
								'description' => 'Deleting user'
								);
			mso_flush_cache(); // сбросим кэш
		}
		else // ошибочные данные
		{
			$response = array(
							'result' => 0,
							'description' => 'Error input data'
						);
		}

		return $response;
	}



##### ------------------------------------------------------------------------------
##### Страницы
##### ------------------------------------------------------------------------------

	# новая страница
	function mso_new_page($data)
	{
		global $MSO;

		$CI = & get_instance();
		
		$data = mso_xss_clean_data($data, array('user_login', 'password', 'page_id_autor', 'page_title', 'page_slug', 'page_password', 'page_date_publish', 'page_type_id', 'page_id_parent', 'page_status'));
		
		
		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];

		if (!isset($data['page_id_autor'])) $data['page_id_autor'] = $MSO->data['session']['users_id'];


		# проверка доступа этому пользователю с этим паролем и этим разрешением
		if ( !mso_check_user_password($user_login, $password, 'admin_page_new') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');

		# получаем данные пользователя, который отправил запрос
		$user_data = mso_get_user_data($user_login, $password);

		$page_title = $data['page_title'];
		$page_content = $data['page_content'];
		// короткая ссылка
		$page_slug = isset($data['page_slug']) ? mso_slug($data['page_slug']) : false;
		if (!$page_slug)
		{
			if ($page_title) $page_slug = mso_slug($page_title);
				else $page_slug = 'no-title';
			
			if (!$page_slug) $page_slug = 'no-title';
		}

		// нужно проверить есть ли уже такая запись
		// проверяем по заголовку + тексту
		$CI->db->select('page_slug, page_title, page_content');
		$CI->db->where(array('page_title'=>$page_title, 'page_content'=>$page_content ));
		// $CI->db->where(array('page_slug'=>$page_slug, 'page_title'=>$page_title ));
		$query = $CI->db->get('page');
		if ($query->num_rows()) // что-то есть
		{
			$response = array(
							'result' => 0,
							'description' => 'Existing page'
							);
			// pr($response);
			return $response;
		}

		// $page_slug нужно проверить на существование
		// если есть, то нужно добавить скажем их кол-во+1
		$CI->db->select('page_slug');
		$query = $CI->db->get('page'); // получили все slug

		if ($query->num_rows()>0)
		{
			$all = array(); // сделаем массив всех слаг
			foreach ($query->result_array() as $row)
				$all[] = $row['page_slug'];

			$count = 0; // начальное приращения слага
			$in = in_array($page_slug, $all); // признак вхождения -
			while ($in)
			{
				$count++;
				$in = in_array($page_slug . '-' . $count, $all);
			}
			if ($count) $page_slug = $page_slug . '-' . $count;
		}

		$page_password = isset($data['page_password']) ? mso_strip($data['page_password']) : '';


		// дата публикации если нет даты, от ставим текущую
		$page_date_publish = isset($data['page_date_publish']) ? $data['page_date_publish'] : date('Y-m-d H:i:s');

		$page_type_id = isset($data['page_type_id']) ? $data['page_type_id'] : '1';
		$page_id_parent = isset($data['page_id_parent']) ? $data['page_id_parent'] : '0';

		$page_status = isset($data['page_status']) ? $data['page_status'] : 'publish';
		if ( ($page_status != 'publish') and ($page_status != 'draft') and ($page_status != 'private') )
				$page_status = 'publish';

		// если стоит разрешение admin_page_publish, то статус не меняем
		// иначе ставим только draft
		if ( !mso_check_allow('admin_page_publish', $user_data['users_id']) ) $page_status = 'draft';


		$page_comment_allow = isset($data['page_comment_allow']) ? (int) $data['page_comment_allow'] : '1';
		$page_ping_allow = isset($data['page_ping_allow']) ? (int) $data['page_ping_allow'] : '1';
		$page_feed_allow = isset($data['page_feed_allow']) ? (int) $data['page_feed_allow'] : '1';

		$page_menu_order = isset($data['page_menu_order']) ? (int) $data['page_menu_order'] : 0;


		$page_id_autor = isset($data['page_id_autor']) ? (int) $data['page_id_autor'] : -1;

		// нужно проверить вообще есть ли такой юзер $page_id_autor
		$CI->db->select('users_id');
		$CI->db->from('users');
		$CI->db->where(array('users_id'=>$page_id_autor));
		$query = $CI->db->get();
		if (!$query->num_rows()) // нет
			$page_id_autor = '-1';

		if ($page_id_autor != $user_data['users_id']) // смена авторства - проверим разрешения
		{
			if ( !mso_check_allow('edit_page_author', $user_data['users_id'], false) ) // нельзя
			{
				$page_id_autor = $user_data['users_id'];
			}
		}

		$ins_data = array (
			'page_type_id' => $page_type_id,
			'page_id_parent' => $page_id_parent,
			'page_id_autor' => $page_id_autor,
			'page_title' => $page_title,
			'page_content' => $page_content,
			'page_status' => $page_status,
			'page_slug' => $page_slug,
			'page_password' => $page_password,
			'page_comment_allow' => $page_comment_allow,
			'page_ping_allow' => $page_ping_allow,
			'page_feed_allow' => $page_feed_allow,
			'page_date_publish' => $page_date_publish,
			'page_last_modified' => $page_date_publish,
			'page_menu_order' => $page_menu_order,

			// 'page_date_dead' => $,
			// 'page_lang' => $,

			);

		//pr($ins_data);
		//pr($CI->db->_compile_select());

		$res = ($CI->db->insert('page', $ins_data)) ? '1' : '0';

		if ($res)
		{
			$id = $CI->db->insert_id(); // номер добавленной записи

			// добавим теперь рубрики
			// рубрики указаны в виде номеров через запятую
			$page_id_cat = isset($data['page_id_cat']) ? $data['page_id_cat'] : '';
			$page_id_cat = mso_explode($page_id_cat); // в массив

			foreach ($page_id_cat as $key=>$val)
			{
				$ins_data = array (
					'page_id' => $id,
					'category_id' => $val
					);
				$CI->db->insert('cat2obj', $ins_data);
			}



			// $page_tags = метка
			// метки - это мета данные
			// вначале получим существующие метки
			$CI->db->select('meta_id');

			// дефолтные данные
			$def_data = array (
					'meta_key' => 'tags',
					'meta_id_obj' => $id,
					'meta_table' => 'page'
					);

			$CI->db->where($def_data);
			$query = $CI->db->get('meta');

			if (!$query->num_rows()) // нет меток для этой страницы
			{	// значит инсерт
				$page_tags = isset($data['page_tags']) ? $data['page_tags'] : '';
				$tags = mso_explode($page_tags, false, false); // в массив - не только числа

				foreach ($tags as $key=>$val)
				{
					$ins_data = $def_data;
					$ins_data['meta_value'] = $val;
					$CI->db->insert('meta', $ins_data);
				}
			}
			// опции - мета
			require_once( getinfo('common_dir') . 'meta.php' );
			$page_meta_options = isset($data['page_meta_options']) ? $data['page_meta_options'] : '';


			//title##VALUE##титул##METAFIELD##description##VALUE##описание##METAFIELD##keywords##VALUE##ключи##METAFIELD##

			$page_meta_options = explode('##METAFIELD##', $page_meta_options);

			// добавляем через insert
			foreach ($page_meta_options as $key=>$val)
			{
				if (trim($val))
				{
					$meta_temp = explode('##VALUE##', $val);
					$meta_key = trim($meta_temp[0]);
					$meta_value = trim($meta_temp[1]);
					mso_add_meta($meta_key, $id, 'page', $meta_value);
					//$CI->db->insert('meta', array('meta_key'=>$meta_key, 'meta_value'=>$meta_value,
					//							  'meta_table' => 'page', 'meta_id_obj' => $id) );
				}
			}


			// результат возвращается в виде массива
			$res = array($id, $page_slug, $page_status, $page_password, $page_date_publish);
			$response = array(
							'result' => $res,
							'description' => 'Inserting new page'
							);
							
			mso_flush_cache(); // сбросим кэш
		}
		else
		{
			$response = array(
						'result' => 0,
						'description' => 'Error inserting page'
						);
		}

		if ($response['result']) 
		{
			mso_hook('new_page', $response['result']);
			mso_hook('new_page_' . $page_status);
		}

		return $response;
	}



	# редактирование существующих страниц
	function mso_edit_page($data)
	{
		global $MSO;

		$CI = & get_instance();
		
		$data = mso_xss_clean_data($data, array('user_login', 'password', 'page_id_autor', 'page_title', 'page_slug', 'page_password', 'page_date_publish', 'page_type_id', 'page_id_parent', 'page_status'));
		
		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];

		if (!isset($data['page_id_autor'])) $data['page_id_autor'] = $MSO->data['session']['users_id'];

		# проверка доступа этому пользователю с этим паролем и этим разрешением
		if ( !mso_check_user_password($user_login, $password, 'admin_page_edit') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');


		# получаем данные пользователя, который отправил запрос
		$user_data = mso_get_user_data($user_login, $password);

		$page_id = (int) $data['page_id'];
		// проверим, чтобы это было число
		$page_id1 = (int) $page_id;
		if ( (string) $page_id != (string) $page_id1 ) $page_id = false; // ошибочный id

		if (!$page_id)
			return array( 'result' => 0, 'description' => 'Page ID incorrect');


		// есть ли вообще такая страница?
		$CI->db->select('page_id');
		$CI->db->where(array('page_id'=>$page_id));
		$query = $CI->db->get('page');
		if ($query->num_rows() == 0) // нет такого
			return array( 'result' => 0, 'description' => 'Page ID incorrect');


		$page_title = $data['page_title'];
		$page_content = $data['page_content'];


		// короткая ссылка
		$page_slug = isset($data['page_slug']) ? mso_slug($data['page_slug']) : false;
		if (!$page_slug)
		{
			if ($page_title) $page_slug = mso_slug($page_title);
				else $page_slug = 'no-title';
				
			if (!$page_slug) $page_slug = 'no-title';
		}

		// $page_slug нужно проверить на существование
		// если есть, то нужно добавить скажем их кол-во+1
		// при этом исключаем саму редактируемую страницу
		$CI->db->select('page_slug');
		$CI->db->where('page_id != ', $page_id);
		$query = $CI->db->get('page'); // получили все slug

		if ($query->num_rows()>0)
		{
			$all = array(); // сделаем массив всех слаг
			foreach ($query->result_array() as $row)
				$all[] = $row['page_slug'];

			$count = 0; // начальное приращения слага
			$in = in_array($page_slug, $all); // признак вхождения -
			while ($in)
			{
				$count++;
				$in = in_array($page_slug . '-' . $count, $all);
			}
			if ($count) $page_slug = $page_slug . '-' . $count;
		}

		$page_password = isset($data['page_password']) ? mso_strip($data['page_password']) : '';


		// дата публикации
		$page_date_publish = isset($data['page_date_publish']) ? $data['page_date_publish'] : false;


		# дата последней модификации страницы
		$page_last_modified = date('Y-m-d H:i:s');


		$page_type_id = isset($data['page_type_id']) ? $data['page_type_id'] : '1';
		$page_id_parent = isset($data['page_id_parent']) ? $data['page_id_parent'] : '0';

		$page_status = isset($data['page_status']) ? $data['page_status'] : 'publish';
		if ( ($page_status != 'publish') and ($page_status != 'draft') and ($page_status != 'private') )
				$page_status = 'publish';

		// если стоит разрешение admin_page_publish, то статус не меняем
		// иначе ставим только draft
		if ( !mso_check_allow('admin_page_publish', $user_data['users_id']) ) $page_status = 'draft';

		$page_comment_allow = isset($data['page_comment_allow']) ? (int) $data['page_comment_allow'] : '1';
		$page_ping_allow = isset($data['page_ping_allow']) ? (int) $data['page_ping_allow'] : '1';
		$page_feed_allow = isset($data['page_feed_allow']) ? (int) $data['page_feed_allow'] : '1';

		$page_menu_order = isset($data['page_menu_order']) ? (int) $data['page_menu_order'] : 0;


		$page_id_autor = isset($data['page_id_autor']) ? (int) $data['page_id_autor'] : -1;

		// нужно проверить вообще есть ли такой юзер $page_id_autor
		$CI->db->select('users_id');
		$CI->db->from('users');
		$CI->db->where(array('users_id'=>$page_id_autor));
		$query = $CI->db->get();
		if (!$query->num_rows()) // нет
			$page_id_autor = '-1';

		if ($page_id_autor != $user_data['users_id']) // смена авторства - проверим разрешения
		{
			if ( !mso_check_allow('edit_page_author', $user_data['users_id'], false) ) // нельзя
			{
				$page_id_autor = $user_data['users_id'];
			}
		}

		$ins_data = array (
			'page_type_id' => $page_type_id,
			'page_id_parent' => $page_id_parent,
			'page_id_autor' => $page_id_autor,
			'page_title' => $page_title,
			'page_content' => $page_content,
			'page_status' => $page_status,
			'page_slug' => $page_slug,
			'page_password' => $page_password,
			'page_comment_allow' => $page_comment_allow,
			'page_ping_allow' => $page_ping_allow,
			'page_feed_allow' => $page_feed_allow,
			'page_last_modified' => $page_last_modified,
			'page_menu_order' => $page_menu_order,
			// 'page_date_dead' => $,
			// 'page_lang' => $,
			);

		if ($page_date_publish) $ins_data['page_date_publish'] = $page_date_publish;

		$CI->db->where(array('page_id'=>$page_id) );
		$res = ($CI->db->update('page', $ins_data)) ? '1' : '0';

        # $CI->db->cache_delete_all();

		if ($res)
		{
			$id = $page_id;

			// добавим теперь рубрики
			// вначале удалим все записи из рубрик с этим page_id

			$CI->db->where( array('page_id'=>$page_id, 'links_id'=> '0') ); // чтобы линки не грохнуть
			$CI->db->delete('cat2obj');

			// рубрики указаны в виде номеров через запятую
			$page_id_cat = isset($data['page_id_cat']) ? $data['page_id_cat'] : '';
			$page_id_cat = mso_explode($page_id_cat); // в массив

			foreach ($page_id_cat as $key=>$val)
			{
				$ins_data = array (
					'page_id' => $id,
					'category_id' => $val
					);
				$CI->db->insert('cat2obj', $ins_data);
				# $CI->db->cache_delete_all();
			}

			// $page_tags = метка
			// метки - это мета данные
			// дефолтные данные
			$def_data = array (
					'meta_key' => 'tags',
					'meta_id_obj' => $id,
					'meta_table' => 'page'
					);

			// вначале грохнем старые, потом добавим новые
			$CI->db->where($def_data);
			$CI->db->delete('meta');

			// получим существующие метки
			$CI->db->select('meta_id');
			$CI->db->where($def_data);
			$query = $CI->db->get('meta');

			if (!$query->num_rows()) // нет меток для этой страницы
			{	// значит инсерт
				$page_tags = isset($data['page_tags']) ? $data['page_tags'] : '';
				$tags = mso_explode($page_tags, false, false); // в массив - не только числа

				foreach ($tags as $key=>$val)
				{
					$ins_data = $def_data;
					$ins_data['meta_value'] = $val;
					$CI->db->insert('meta', $ins_data);
					# $CI->db->cache_delete_all();
				}
			}


			// опции - мета
			require_once( getinfo('common_dir') . 'meta.php' );
			$page_meta_options = isset($data['page_meta_options']) ? $data['page_meta_options'] : '';

			// title##VALUE##титул##METAFIELD##description##VALUE##описание##METAFIELD##keywords##VALUE##ключи##METAFIELD##

			$page_meta_options = explode('##METAFIELD##', $page_meta_options);

			// вначале удалим все мета этой записи
			//$where_in = array(); // здесь meta_key
			//foreach ($page_meta_options as $key=>$val)
			//{
			//	if (trim($val))
			//	{
			//		$meta_temp = explode('##VALUE##', $val);
			//		$where_in[] = trim($meta_temp[0]);
			//	}
			//}

			// вначале грохнем старые, потом добавим новые
			//$CI->db->where( array('meta_id_obj' => $id, 'meta_table' => 'page') );
			//$CI->db->where_in('meta_key', $where_in );
			//$CI->db->delete('meta');

			// теперь тоже самое, только добавляем через insert
			foreach ($page_meta_options as $key=>$val)
			{
				if (trim($val))
				{
					$meta_temp = explode('##VALUE##', $val);
					$meta_key = trim($meta_temp[0]);
					$meta_value = trim($meta_temp[1]);

					mso_add_meta($meta_key, $id, 'page', $meta_value);

					//$CI->db->insert('meta', array('meta_key'=>$meta_key, 'meta_value'=>$meta_value,
					//							  'meta_table' => 'page', 'meta_id_obj' => $id) );
				}
			}

			// результат возвращается в виде массива
			$res = array($id, $page_slug, $page_status, $page_password, $page_date_publish);
			$response = array(
							'result' => $res,
							'description' => 'Updating page'
							);
							
			mso_flush_cache(); // сбросим кэш
		}
		else
		{
			$response = array(
						'result' => 0,
						'description' => 'Error inserting page'
						);
		}

		if ($response['result']) 
		{	
			mso_hook('edit_page', $response['result']);
			mso_hook('edit_page_' . $page_status);
		}

		return $response;
	}

	# удалить страница
	function mso_delete_page($data)
	{
		global $MSO;

		$CI = & get_instance();

		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];

		# проверка доступа этому пользователю с этим паролем и этим разрешением
		if ( !mso_check_user_password($user_login, $password, 'admin_page_delete') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect or banned action');

		if (!isset($data['page_id']))
				return array( 'result' => 0, 'description' => 'Page ID not found');

		$page_id = (int) $data['page_id'];

		if (!$page_id) // ошибка!
		{
			return array( 'result' => 0, 'description' => 'Page ID incorrect');
		}
		else
		{
			// проверим id, чтобы вообще такая страница была
			$CI->db->select('page_id');
			$CI->db->where(array('page_id'=>$page_id));
			$query = $CI->db->get('page');
			if ($query->num_rows() == 0) // нет такого
			{
				return array( 'result' => 0, 'description' => 'Page not found');
			}
			else
			{	// теперь можно удалять
				// при удалении страницы нужно сразу удалить её, рубрики и мета
				// потом будут еще и комментарии

				$CI->db->where( array('page_id'=>$page_id) );
				$CI->db->delete('cat2obj');

				$CI->db->where( array ('meta_id_obj' => $page_id, 'meta_table' => 'page') );
				$CI->db->delete('meta');

				$CI->db->where( array('page_id'=>$page_id) );
				$CI->db->delete('page');
				
				$CI->db->where( array('comments_page_id'=>$page_id) );
				$CI->db->delete('comments');
				
				mso_flush_cache(); // сбросим кэш

				return array( 'result' => 1, 'description' => 'Page deleted');
			}
		}
	}


	# редактирование существующего комюзера
	function mso_edit_comuser($data)
	{
		global $MSO;

		$CI = & get_instance();
		
		$data = mso_xss_clean_data($data, array('user_login', 'password', 'comusers_email', 'comusers_new_password', 'comusers_nik', 'comusers_icq', 'comusers_url', 'comusers_msn', 'comusers_jaber', 'comusers_skype', 'comusers_avatar_url', 'comusers_description', 'comusers_date_birth_y', 'comusers_date_birth_m', 'comusers_date_birth_d', 'comusers_notify', 'comusers_language', 'comusers_activate_key', 'comusers_activate_string', 'comusers_admin_note'));
		
		if (isset($data['user_login'])) $user_login = $data['user_login'];
			else $user_login = $MSO->data['session']['users_login'];

		if (isset($data['password'])) $password = $data['password'];
			else $password = $MSO->data['session']['users_password'];


		# проверка можно ли редактировать этому пользователю с этим паролем и этим разрешением
		if ( !mso_check_user_password($user_login, $password, 'admin_comusers') )
				return array( 'result' => 0, 'description' => 'Login/password incorrect');

		$comusers_id = isset($data['comusers_id']) ? (int) $data['comusers_id'] : 0;

		if ( $comusers_id <= 0 ) return array( 'result' => 0, 'description' => 'Comuser ID incorrect');

		$comusers_email = isset($data['comusers_email']) ? $data['comusers_email'] : '';

		# нет email - ошибка
		if (!$comusers_email or !mso_valid_email($comusers_email))
			return array( 'result' => 0, 'description' => 'Email incorrect');
		
		
		### пароль
		$comusers_new_password = '';
		if (isset($data['comusers_new_password']))
		{
			$comusers_new_password = mso_strip($data['comusers_new_password'], false);
			
			if ( $comusers_new_password and ( strlen($comusers_new_password) > 6) )
			{
				$comusers_new_password = mso_md5($comusers_new_password);
			}
			else
			{
				return array( 'result' => 0, 'description' => 'Password incorrect');
			}
		}
		
		$comusers_nik = isset($data['comusers_nik']) ? $data['comusers_nik'] : '';
		$comusers_icq = isset($data['comusers_icq']) ? $data['comusers_icq'] : '';
		$comusers_url = isset($data['comusers_url']) ? $data['comusers_url'] : '';
		$comusers_msn = isset($data['comusers_msn']) ? $data['comusers_msn'] : '';
		$comusers_jaber = isset($data['comusers_jaber']) ? $data['comusers_jaber'] : '';
		$comusers_skype = isset($data['comusers_skype']) ? $data['comusers_skype'] : '';
		$comusers_avatar_url = isset($data['comusers_avatar_url']) ? $data['comusers_avatar_url'] : '';
		$comusers_description = isset($data['comusers_description']) ? $data['comusers_description'] : '';
		$comusers_date_birth_y = isset($data['comusers_date_birth_y']) ? $data['comusers_date_birth_y'] : '1970';
		$comusers_date_birth_m = isset($data['comusers_date_birth_m']) ? $data['comusers_date_birth_m'] : '1';
		$comusers_date_birth_d = isset($data['comusers_date_birth_d']) ? $data['comusers_date_birth_d'] : '1';
		$comusers_notify = isset($data['comusers_notify']) ? $data['comusers_notify'] : '0';
		$comusers_language = isset($data['comusers_language']) ? $data['comusers_language'] : 'ru';
		$comusers_activate_key = isset($data['comusers_activate_key']) ? $data['comusers_activate_key'] : '';
		$comusers_activate_string = isset($data['comusers_activate_string']) ? $data['comusers_activate_string'] : '';
		$comusers_admin_note = isset($data['comusers_admin_note']) ? $data['comusers_admin_note'] : '';


		# дату в MySQL
		$comusers_date_birth = mso_date_convert_to_mysql($comusers_date_birth_y, $comusers_date_birth_m, $comusers_date_birth_d);

		$upd_data = array (
			'comusers_nik' => $comusers_nik,
			'comusers_admin_note' => $comusers_admin_note,
			'comusers_email' => $comusers_email,
			'comusers_icq' => $comusers_icq,
			'comusers_url' => $comusers_url,
			'comusers_msn' => $comusers_msn,
			'comusers_jaber' => $comusers_jaber,
			'comusers_skype' => $comusers_skype,
			'comusers_avatar_url' => $comusers_avatar_url,
			'comusers_description' => $comusers_description,
			'comusers_date_birth' => $comusers_date_birth,
			'comusers_notify' => $comusers_notify,
			'comusers_language' => $comusers_language,
			'comusers_activate_string' => $comusers_activate_string,
			'comusers_activate_key' => $comusers_activate_key,
			);
		
		if ($comusers_new_password) $upd_data['comusers_password'] = $comusers_new_password;

		// _pr($upd_data);
		
		$CI->db->where('comusers_id', $comusers_id);
		$res = ($CI->db->update('comusers', $upd_data)) ? '1' : '0';

        mso_flush_cache(); // сбросим кэш

		$response = array(
						'result' => $res,
						'description' => 'Update'
						);

		return $response;
	}
?>