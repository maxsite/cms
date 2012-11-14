<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1><?= t('Редактирование комментатора') ?></h1>
<p><a href="<?= $MSO->config['site_admin_url'] . 'comusers' ?>"><?= t('Вернуться к списку комментаторов') ?></a></p>

<?php

	$CI = & get_instance();
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		// получаем номер комюзера id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_submit']); 
		
		// подготавливаем данные для xmlrpc
		$data = array(
			
			'user_login' => $MSO->data['session']['users_login'],
			'password' => $MSO->data['session']['users_password'],
			
			'comusers_id' => $f_id,
			'comusers_nik' => $post['f_nik'],
			'comusers_email' => $post['f_email'],
			'comusers_icq' => $post['f_icq'],
			'comusers_url' => $post['f_url'],
			'comusers_msn' => $post['f_msn'],
			'comusers_jaber' => $post['f_jaber'],
			'comusers_skype' => $post['f_skype'],
			'comusers_avatar_url' => $post['f_avatar_url'],
			'comusers_description' => $post['f_description'],
			'comusers_date_birth_y' => $post['f_date_birth_y'],
			'comusers_date_birth_m' => $post['f_date_birth_m'],
			'comusers_date_birth_d' => $post['f_date_birth_d'],
			'comusers_notify' => $post['f_notify'],
			'comusers_language' => $post['f_language'],
			'comusers_activate_key' => $post['f_activate_key'],
			'comusers_activate_string' => $post['f_activate_string'],
			'comusers_admin_note' => $post['f_admin_note'],
			
			);
		
		if ( $post['f_new_password'] and ($post['f_new_password'] == $post['f_new_confirm_password']) )
		{
			$data['comusers_new_password'] = $post['f_new_password'];
		}

		
		// pr($data);
		
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		$result = mso_edit_comuser($data);
		
		//pr($result);
		
		if (isset($result['result']) and $result['result']) 
		{
			echo '<div class="update">' . t('Обновлено!') . '</div>'; // . $result['description'];
			mso_flush_cache(); // сбросим кэш
		}
		else
			echo '<div class="error">' . t('Ошибка обновления') . ' (' . $result['description'] . ')</div>';
		
	}


	
	# вспомогательная функция
	# имя поле значение
	function _mso_add_row($title, $field, $val)
	{
		$CI = & get_instance();
		$CI->table->add_row($title, 
			form_input( array( 'name'=>$field, 'style'=>'width: 99%',
			'value'=>$val ) ) );
	}
		
	$id = (int) mso_segment(4); // номер пользователя по сегменту url
	
	if ($id) // есть корректный сегмент
	{
		# подготавливаем выборку из базы
		$CI->db->select('*');
		$CI->db->from('comusers');
		$CI->db->where('comusers_id', $id);
		$query = $CI->db->get();
		
		// если есть данные, то выводим
		if ($query->num_rows() > 0)
		{
			
			$CI->load->helper('form');
			$CI->load->library('table');
			
			$tmpl = array (
						'table_open'		  => '<table class="page" border="0" width="99%">
						<colgroup><colgroup>',
						'row_alt_start'		  => '<tr class="alt">',
						'cell_alt_start'	  => '<td class="alt">',
				  );

			$CI->table->set_template($tmpl); // шаблон таблицы
			
			// заголовки
			$CI->table->set_heading( t('Название'), t('Значение'));
			
			foreach ($query->result_array() as $row)
			{
				$id = $row['comusers_id'];
		
				$CI->table->add_row('ID', $id );
									
				_mso_add_row(t('Ник'), 'f_nik', $row['comusers_nik'] );
				_mso_add_row(t('E-mail'), 'f_email', $row['comusers_email'] );
				_mso_add_row(t('ICQ'), 'f_icq', $row['comusers_icq']);
				_mso_add_row(t('Сайт'), 'f_url', $row['comusers_url']);
				_mso_add_row(t('Twitter'), 'f_msn', $row['comusers_msn']);
				_mso_add_row(t('Jabber'), 'f_jaber', $row['comusers_jaber']);
				_mso_add_row(t('Skype'), 'f_skype', $row['comusers_skype']);
				_mso_add_row(t('URL аватара'), 'f_avatar_url', $row['comusers_avatar_url']);
				
				
				$CI->table->add_row(t('Описание'), '<textarea name="f_description" cols="90" rows="3">' . htmlspecialchars($row['comusers_description']) . '</textarea>');
				
				$CI->table->add_row(t('Примечание админа'), '<textarea name="f_admin_note" cols="90" rows="3">' . htmlspecialchars($row['comusers_admin_note']) . '</textarea>');
			
				// ДР это три поля
				$y = mso_date_convert('Y', $row['comusers_date_birth']);
				$m = mso_date_convert('n', $row['comusers_date_birth']);
				$d = mso_date_convert('j', $row['comusers_date_birth']);
				
				$y_r = array_flip(range(1960, 2008));
				foreach ($y_r as $key=>$val) $y_r[$key] = $key;

				$m_r = array_flip(range(1, 12));
				foreach ($m_r as $key=>$val) $m_r[$key] = $key;
				
				$d_r = array_flip(range(1, 31));
				foreach ($d_r as $key=>$val) $d_r[$key] = $key;			
				
				$CI->table->add_row(t('Дата рождения'), 
				t('Год:') . ' ' . form_dropdown('f_date_birth_y', $y_r, $y, ' style="width: 100px;" ') . 
				' ' . t('Месяц:') . ' ' . form_dropdown('f_date_birth_m', $m_r, $m, ' style="width: 100px;" ' ) . 
				' ' . t('День:') . ' ' . form_dropdown('f_date_birth_d', $d_r, $d, ' style="width: 100px;" ' ) 
				);

		
	
				###!!! что за уведомления? для чего???
				$CI->table->add_row(t('Уведомления'), 
					form_dropdown('f_notify', array('0'=>t('Без уведомлений'), '1'=>t('Подписаться')), $row['comusers_notify'], ' style="width: 300px;" '));
				
				###!!! языки взять из CodeIgniter !!!
				$CI->table->add_row(t('Язык'), 
					form_dropdown('f_language', array('ru'=>t('Русский'), 'en'=> t('Английский'), 'ua'=>'' . t('Украинский')), $row['comusers_language'], ' style="width: 300px;" '));	
				
				
				
				_mso_add_row(t('Новый пароль (только английские символы, длина > 6 символов)'), 'f_new_password', '');
				_mso_add_row(t('Подтвердите пароль'), 'f_new_confirm_password', '');
				
				_mso_add_row(t('Ключ активации'), 'f_activate_key', $row['comusers_activate_key']);
				_mso_add_row(t('Подтверждение активации'), 'f_activate_string', $row['comusers_activate_string']);


				###!!! здесь же по-идее нужно смотреть и мета для данного юзера
				###!!! и выводить её в виде - ключ-значение
				###!!! meta_table = 'users'  meta_id_obj = $id
				
			}
			
			echo '<form method="post">' . mso_form_session('f_session_id');
			echo $CI->table->generate();
			echo '<p class="br"><input type="submit" name="f_submit[' . $id . ']" value="' . t('Изменить') . '"></p>';
			echo '</form>';
		}
		else echo '<div class="error">' . t('Ошибочный запрос') . '</div>';
	}
	else
	{
		echo '<div class="error">' . t('Ошибочный запрос') . '</div>';
	}

# End of file
