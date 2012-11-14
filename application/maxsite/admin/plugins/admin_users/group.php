<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1><?= t('Группы пользователей') ?></h1>
<p class="info"><?= t('Здесь вы можете настроить группы пользователей. Вы не можете удалить группы <strong>«admins»</strong> и <strong>«users»</strong>. Группе <strong>«admins»</strong> разрешены все действия.') ?></p>

<?php

	$CI = & get_instance();
	
	if ( $post = mso_check_post(array('f_session_id', 'f_new_submit', 'f_new')) )
	{
		# добавление новой группы
		mso_checkreferer();
		
		$f_new = mso_strip($post['f_new']);
		
		if ($f_new)
		{
			$CI->db->select('groups_id');
			$CI->db->where(array('groups_name'=>$f_new));
			
			$query = $CI->db->get('groups');
			
			if ($query->num_rows() == 0 ) // нет такого типа страниц
			{
					if ($CI->db->insert('groups', array( 'groups_name'=>$f_new)))
							echo '<div class="update">' . t('Новая группа добавлена!') . '</div>';
						else
							echo '<div class="error">' . t('Ошибка добавления!') . '</div>';
			}
			else
				echo '<div class="error">' . t('Такая группа уже существует!') . '</div>';
		}
		else echo '<div class="error">' . t('Ошибочное имя') . '</div>';
			
	}
	elseif ( $post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_delete_check')) )
	{
		# удаление группы
		mso_checkreferer();
		
		$w_in = array();
		foreach ($post['f_delete_check'] as $f_name=>$val)
			$w_in[] = $f_name;
		
		if ( $w_in )
		{
			$CI->db->where_in('groups_id', $w_in);
			$CI->db->where_not_in('groups_name', array('admins', 'users'));
			
			if ( $CI->db->delete('groups') )
						echo '<div class="update">' . t('Удаление выполнено') . '</div>';
					else
						echo '<div class="error">' . t('Ошибка удаления!') . '</div>';
			
			// нужно изменить у всех юзеров этой группы группу на users = 2
			$CI->db->where_in('users_groups_id', $w_in);
			$CI->db->update('users', array('users_groups_id'=>'2'));
			
		}
		else echo '<div class="error">Ошибка удаления!</div>';
	}
	elseif ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		# изменение разрешений
		
		mso_checkreferer();
		
		// если не отмечен f_check то это значит нужно сбросить флаг для этой группы-действия
		// разрешения хрянятся в виде массива для каждой группы, например:
		//		'demo_plugin_admin' => 1,
		//		'demo_plugin_admin-34' => 0
		
		// таким образом мы дожны пройтись по всем группам и сформировать массив $rules
		
		// pr($_POST);
		$all = mso_get_option('groups_allow', 'general');
		
		// обнулим все значения разрешений
		// нужно чтобы потом просто объединить массивы
		foreach ($all as $key=>$val) $all[$key] = 0;
		
		
		// получаем группы
		$CI->db->order_by('groups_name');
		$query = $CI->db->get('groups');
		$us = $query->result_array();
		
		foreach ($us as $row) 
		{
			$name = $row['groups_name'];
			$id  = $row['groups_id'];
			$rules = array();
			
			if (isset($post['f_check'][$id]))
				// есть отметки чекбоксов
				// смешиваем массиы и получаем разрешения для группы
				$rules = array_merge($all, $post['f_check'][$id]);
			else
				// нет отметок
				$rules = $all;
			
			// нормализуем массив, чтобы убрать on и оставить только 0 и 1
			// тут же смотрим админов
			foreach ($rules as $key=>$val) 
			{
				if ($name == 'admins') 
				{
					$rules[$key] = 1;
				}
				elseif ($val) $rules[$key] = 1;
			}	
				
			// pr($rules);
			
			$rules = serialize($rules);
			// готово
			// pr($rules);
			// добавлем в базу
			$CI->db->where('groups_id', $id);
			$CI->db->update('groups', array( 'groups_rules'=>$rules ));
		}
		// сбросить весь кэш
		mso_flush_cache();
		
		echo '<div class="update">' . t('Обновление выполнено') . '</div>';
	}
	elseif ($_POST) echo '<div class="error">' . t('Ошибочный запрос') . '</div>';
	
	$CI->load->library('table');
	
	$tmpl = array (
				'table_open'		  => '<table class="page tablesorter">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы
	$data_table = array(); // вся таблица в массиве
	
	// все разрешения
	$all = mso_get_option('groups_allow', 'general');
	ksort($all); // сортируем по алфавиту
	
	if ($all)
	{	
		// все группы
		$query = $CI->db->get('groups');
		$us = $query->result_array();
		

		// формируем первую строчку таблицы
		$r = array(t('Действие'), t('Код'));
		
		
		foreach ($us as $row) $r[] = $row['groups_name'];
		
		$data_table[] = $r; // добавим первую строчку
	
		// проходимся в циклах и добавляем все остальные строчки
		foreach ($all as $key => $val)
		{
			$r = array(); // действие 
			// $r[] = '<strong>' . $val . '</strong> (' . $key . ')';
			$r[] = '<strong>' . $val . '</strong>';
			$r[] = $key;
			
			foreach ($us as $row)
			{	
				$id = $row['groups_id'];
				$rules = (array) @unserialize($row['groups_rules']);
				
				$name = $row['groups_name'];

				if ( $id == 1 ) // if ( $name == 'admins' ) // админы всегда
					$sel = ' checked="checked" checked disabled="disabled" '; // у админов всегда все разрешено
				else 
				{	// проверяем текущие разрешения у этой группы
					// для checked
					if (isset($rules[$key]))
					{	// есть действие в groups_rules
						$sel = $rules[$key] ? ' checked="checked" checked ' : '';
					}
					else // нет действия - запрет
					{
						$sel = '';
					}
				}
				
				$key = urldecode($key);
				
				$r[] = '<input type="checkbox" name="f_check[' . $id . '][' . $key . ']"' . $sel . '>';
			}
			$data_table[] = $r; // добавим строчку
		}
		
		$dop = '<p class="br"><input type="submit" name="f_submit" value="' . t('Изменить разрешения') . '"></p>';
		
		
		echo mso_load_jquery('jquery.tablesorter.js') . '
		<script>
		$(function() {
			$("table.tablesorter").tablesorter( {headers: { 2: {sorter: false}, 3: {sorter: false} }});
		});
		</script>';
	
		// добавляем форму, а также текущую сессию
		echo '<form method="post">' . mso_form_session('f_session_id');
		echo $CI->table->generate($data_table); // вывод подготовленной таблицы
		echo $dop;
		echo '</form>';
		
		
		$delete = '';
		foreach ($us as $row) 
		{
			$name = $row['groups_name'];
			if (($name == 'admins') or ($name == 'users')) continue;
			
			//$name = urldecode($name);
			$id = $row['groups_id'];
			$delete .= '<label><input type="checkbox" name="f_delete_check[' . $id . ']"> ' . $name . '</label><br>';
		}
		
		if ($delete) 
		{
			$delete = '<p class="input checkbox wleft br"><strong>' . t('Удалить группы') . ' </strong>' . $delete . '</p><p class="input_submit"><input type="submit" name="f_delete_submit" value="' . t('Удалить отмеченные группы') . '" onClick="if(confirm(\'' . t('Уверены?') . '\')) {return true;} else {return false;}" ></p>';
		}
		
		$delete = '<div class="item usergroup"><h2>' . t('Добавить/Удалить группу пользователей') . '</h2><p class="input"><strong>' . t('Добавить группу') . ' </strong><input type="text" name="f_new"> <input type="submit" name="f_new_submit" value="' . t('Создать новую группу') . '"></p> 
		' . $delete . '</div>';
		
		// добавляем форму, а также текущую сессию
		echo '<form method="post">' . mso_form_session('f_session_id');
		echo $delete;
		echo '</form>';
	}
	else // $all
	{
		echo'<div class="error">' . t('Пока нет ни одного действия...') . '</div>';
	}
	
?>