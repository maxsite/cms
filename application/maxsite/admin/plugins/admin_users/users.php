<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	
	# новый автор
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_user_login', 
			'f_user_email', 'f_user_password', 'f_user_group')) )
	{
		mso_checkreferer();
		
		// подготавливаем данные
		$data = array(
			'user_login' => $MSO->data['session']['users_login'],
			'password' => $MSO->data['session']['users_password'],
			
			'users_login' => $post['f_user_login'],
			'users_email' => $post['f_user_email'],
			'users_password' => $post['f_user_password'],
			'users_groups_id' => $post['f_user_group']
			);
		
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_new_user($data);
		
		if (isset($result['result'])) 
		{
			if ($result['result'] == 1)
				echo '<div class="update">' . t('Автор создан!') . '</div>'; // . $result['description'];
			else 
				echo '<div class="error">' . t('Произошла ошибка') . '<p>' . $result['description'] . '</p></div>';
		}
		else
			echo '<div class="error">' . t('Ошибка обновления') . '</div>';
	}
	
	# удаление пользователя
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_user_delete')) )
	{
		mso_checkreferer();

		// подготавливаем данные
		$data = array(
			'user_login' => $MSO->data['session']['users_login'],
			'password' => $MSO->data['session']['users_password'],
			
			'users_id' => $post['f_user_delete'],
			'delete_user_comments' => isset($post['f_delete_user_comments']) ? true : false,
			'delete_user_pages' => isset($post['f_delete_user_pages']) ? true : false
			);
		
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_delete_user($data);
		
		if (isset($result['result'])) 
		{
			if ($result['result'] == 1)
				echo '<div class="update">' . t('Автор удален!') . '</div>'; // . $result['description'];
			else 
				echo '<div class="error">' . t('Произошла ошибка') . '<p>' . $result['description'] . '</p></div>';
		}
		else
			echo '<div class="error">' . t('Ошибка удаления') . '</div>';
	}

?>
<h1><?= t('Авторы сайта') ?></h1>
<p class="info"><?= t('Список авторов сайта') ?></p>

<?php
	$CI->load->library('table');
	
	$tmpl = array (
					'table_open'          => '<table class="page tablesorter">',
					'row_alt_start'          => '<tr class="alt">',
					'cell_alt_start'      => '<td class="alt">',
				);
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID', t('Логин'), t('Ник'), t('E-mail'), t('Сайт'), t('Группа'), t('Действие'));
	
	
	$CI->db->select('*');
	$CI->db->from('users');
	$CI->db->join('groups', 'users.users_groups_id = groups.groups_id');
	$CI->db->order_by('users_groups_id');
	
	$query = $CI->db->get();
	
	$this_url = $MSO->config['site_admin_url'] . 'users';

	$all_users = array(); // массив для удаления пользователей
	
	foreach ($query->result_array() as $row)
	{
		$id = $row['users_id'];
		$login = $row['users_login'];
		$nik = $row['users_nik'];
		$email = $row['users_email'];
		$url = $row['users_url'];
		
		$groups_name = $row['groups_name'];
		
		$act = '<a href="' . $this_url.'/edit/' . $id . '">' . t('Изменить') . '</a>';
		
		# админа (1) удалять нельзя
		if ($id > 1) $all_users[$id] = $id . ' - ' . $nik . ' - ' . $email;
		
		$CI->table->add_row($id, $login, $nik, $email, $url, $groups_name, $act);
	}

	echo mso_load_jquery('jquery.tablesorter.js') . '
		<script>
		$(function() {
			$("table.tablesorter").tablesorter( {headers: { 6: {sorter: false} }});
		});
		</script>';

	// добавляем форму, а также текущую сессию
	echo $CI->table->generate(); // вывод подготовленной таблицы
	
	
	if ( mso_check_allow('edit_add_new_users') ) // если разрешено создавать юзеров
	{
		// новый пользователь создается так:
		// указывается его логин, пароль, емайл, группа
		// создается
		// для того, чтобы отредактировать, нужно войти в его редактирование
		$new_user_login = '';
		$new_user_email = '';
		$new_user_password = '';
		$new_user_group = '';
		
		$CI->db->select('groups_id, groups_name');
		$q = $CI->db->get('groups');
		$groups = array();
		
		foreach ($q->result_array() as $rw)
		{
			$groups[$rw['groups_id']] = $rw['groups_name'];
		}
		
		$form = '';
		
		$CI->load->helper('form');
		
		echo '<div class="item new_user">';

		echo '<h2 class="br">' . t('Создать нового автора') . '</h2>';
		
		echo '<form method="post" class="fform admin_users">' . mso_form_session('f_session_id');
		
		echo '<p>' . t('Если данные некорректны, то пользователь создан не будет. Для нового пользователя-админа нужно обновить разрешения.') . '</p>';		
		
		$form .= '<p><label class="fwrap"><span class="ftitle">' . t('Логин') . ' </span><span>' . form_input( array('name'=>'f_user_login')) . '</span></label></p>';
		
		$form .= '<p><label class="fwrap"><span class="ftitle">E-mail</span><span>'. form_input( array( 'name'=>'f_user_email' ) ) . '</span></label></p>';
		
		$form .= '<p><label class="fwrap"><span class="ftitle">' . t('Пароль') . ' </span><span>'. form_input( array( 'name'=>'f_user_password' ) ) . '</span></label></p>';
		
		$form .= '<p><label class="fwrap"><span class="ftitle">' . t('Группа') . ' </span><span>' . form_dropdown('f_user_group', $groups, '') . '</span></label></p>';	
		
		$form .=  '<p class="hr"><span class="ftitle"></span><span><input type="submit" name="f_submit" value="' . t('Создать автора') . '"></span></p>';
		
		echo $form;
		
		echo '</form>';
		
		echo '</div>';
	}

	if ($all_users and mso_check_allow('edit_delete_users') ) // если разрешено удалять юзеров
	{
		$CI->load->helper('form');
		
		echo '<div class="item delete_user">';
		
		echo '<h2 class="br">' . t('Удалить автора') . '</h2>';
		
		echo '<form method="post" class="fform admin_users">' . mso_form_session('f_session_id');
		
		echo '<p><label class="fwrap"><span class="ftitle">' . t('Удалить') . '</span><span>' . form_dropdown('f_user_delete', $all_users, '', '') . '</span></label></p>';
		
		echo '<p><span class="ftitle"></span><label><input type="checkbox" name="f_delete_user_comments"> ' . t('Удалить все комментарии автора. Иначе комментарии отметятся как анонимные.') . '</label></p>';
		
		echo '<p><span class="ftitle"></span><label><input type="checkbox" name="f_delete_user_pages"> ' . t('Удалить все страницы автора. Иначе у страниц автором станет администратор.') . '</label></p>';
		
		echo '<p class="hr"><span class="ftitle"></span><span><input type="submit" name="f_delete_submit" value="' . t('Удалить автора') . '" onClick="if(confirm(\'' . t('Удалить автора сайта?') . '\')) {return true;} else {return false;}"></span></p>';
		
		echo '</form>';
		
		echo '</div>';
	}


?>