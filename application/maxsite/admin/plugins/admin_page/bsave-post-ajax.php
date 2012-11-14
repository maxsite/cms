<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	// основа кода из edit.php
	// принимаем ajax
	// post в виде серилизованного массива
	
	
	// проверим залогиненность
	if (!is_login()) die('no login');
	
	// проверим разрешение на редактирование записей
	if (!mso_check_allow('admin_page_edit')) die('no allow');
	
	
	if ( $post = mso_check_post(array('params', 'id')) )
	{
		mso_checkreferer(); // защищаем реферер
		
		$id = $post['id'];
	
		// проверим, чтобы это было число
		if (!is_numeric($id)) $id = false; // не число
			else $id = (int) $id;
		
		if ($id) // есть корректный сегмент
		{
			$CI = & get_instance();
			
			# проверим текущего юзера и его разрешение на правку чужих страниц
			# если admin_page_edit=1, то есть разрешено редактировать в принципе (уже проверили раньше!),
			# то смотрим admin_page_edit_other. Если стоит 1, то все разрешено
			# если false, значит смотрим автора страницы и если он не равен юзеру, рубим доступ
			
			if ( !mso_check_allow('admin_page_edit_other') )
			{
				$current_users_id = getinfo('session');
				$current_users_id = $current_users_id['users_id'];
				
				# получаем данные страницы
				$CI->db->select('page_id');
				$CI->db->from('page');
				$CI->db->where(array('page_id'=>$id, 'page_id_autor'=>$current_users_id));
				$query = $CI->db->get();
				if ($query->num_rows() == 0) // не автор
				{
					echo t('Вам не разрешено редактировать чужие записи!');
					die;
				}
			}
			
			$params = $post['params'];
			
			parse_str($params, $post);
			
			function _array_stripcslashes($s)
			{
				if (is_string($s)) 
					return stripcslashes($s);
				else 
					return $s;
			}
			
			$post = array_map('_array_stripcslashes', $post);
			
			// в $post все переданные поля формы
			
			require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик
			require_once( getinfo('common_dir') . 'meta.php' ); // функции meta - для меток
			
			// номер записи хратится как ключ массива f_submit - переделываем
			$post['f_submit'] = array($id => '');
			
			require(getinfo('admin_plugins_dir') . 'admin_page/post-edit.php');
		}
	}

	