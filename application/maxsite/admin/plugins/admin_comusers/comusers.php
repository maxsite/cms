<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$CI = & get_instance();
?>

<h1><?= t('Комментаторы') ?></h1>

<p class="info"><?= t('Список комментаторов сайта') ?></p>

<?php

	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_check_comusers')) )
	{
		mso_checkreferer();
		// pr($post);

		$f_check_comusers = $post['f_check_comusers']; // номера отмеченных

		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_comusers as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
			
		$CI->db->where_in('comusers_id', $arr_ids);

		if ( $CI->db->delete('comusers') )
		{
			// заменим в таблице _comments все комментарии удаленных на анонимов
			$CI->db->where_in('comments_comusers_id', $arr_ids);
			$CI->db->update('comments', array('comments_comusers_id' => 0) );		
			
			$CI->db->where_in('comments_comusers_id', $arr_ids);
			$CI->db->update('comments', array('comments_author_name' => t('Аноним')) );		
						
			// удалим всю инфу о комюзере из мета
			$CI->db->where('meta_table', 'comusers');
			$CI->db->where_in('meta_id_obj', $arr_ids);
			$CI->db->delete('meta');
			
			mso_flush_cache();
			echo '<div class="update">' . t('Удалено!') . '</div>';
		}
		else
			echo '<div class="error">' . t('Ошибка удаления') . '</div>';
	}


	$CI->load->library('table');
	$tmpl = array (
				'table_open'			=> '<table class="page tablesorter">',
				'row_alt_start'			=> '<tr class="alt">',
				'cell_alt_start'		=> '<td class="alt">',
		  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	$CI->table->set_heading('ID', ' ', t('Ник'), t('Актив.'), t('Кол.'), t('Последний вход'),  t('E-mail'), t('Сайт'));


	// для пагинации нам нужно знать общее количество записей
	// только после этого выполняем запрос

	$pag = array(); // для пагинации
	$pag['limit'] = 30; // записей на страницу
	$offset = 0;

	$CI->db->select('comusers_id');
	$CI->db->from('comusers');
	$query = $CI->db->get();
	$pag_row = $query->num_rows();

	if ($pag_row > 0)
	{
		$pag['maxcount'] = ceil($pag_row / $pag['limit']); // всего станиц пагинации
		$current_paged = mso_current_paged();
		if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
		$offset = $current_paged * $pag['limit'] - $pag['limit'];
	}
	else $pag = false;


	$CI->db->select('comusers_id, comusers_nik, comusers_email, comusers_url, comusers_activate_key, comusers_activate_string, comusers_date_registr, comusers_last_visit, comusers_count_comments');
	$CI->db->from('comusers');
	$CI->db->order_by('comusers_id');

	if ($pag)
	{
		if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
			else $CI->db->limit($pag['limit']);
	}

	$query = $CI->db->get();

	$this_url = getinfo('site_admin_url') . 'comusers';

	foreach ($query->result_array() as $row)
	{
		$id = $row['comusers_id'];
		$id_out = '<input type="checkbox" name="f_check_comusers[' . $id . ']">' . NR;
		$nik = $row['comusers_nik'];
		$email = $row['comusers_email'];
		$url = $row['comusers_url'];

		# не указан ник
		if (!$nik) $nik = '! ' . t('Комментатор') . ' ' . $id;

		# отмечаем невыполненную активацию
		if ($row['comusers_activate_string'] != $row['comusers_activate_key'])
		{
			$activat = t('нет');
			$nik = '<span style="color: red" title="' . t('Активация не выполнена!') . '">' . $nik . '</span>';
		}
		else $activat = '';

		$nik = '<a href="' . $this_url . '/edit/' . $id . '">'
				. $nik . '</a> [<a href="' . getinfo('siteurl') . 'users/' . $id . '" target="_blank">' . t('Просмотр') . '</a>]';

		if ($row['comusers_date_registr'] != $row['comusers_last_visit'])
			$date = '<span style="color: gray; white-space: nowrap;" title="'. t('Дата регистрации') . '">' . $row['comusers_date_registr']
					. '</span><br>' . $row['comusers_last_visit'];
		else
			$date = $row['comusers_date_registr'];

		$CI->table->add_row($id, $id_out, $nik, $activat, $row['comusers_count_comments'], $date, $email, $url);
	}

	mso_hook('pagination', $pag);
	//echo '<br>'; // вывод навигации

	echo mso_load_jquery('jquery.tablesorter.js') . '
		<script>
		$(function() {
			$("table.tablesorter").tablesorter( {headers: { 1: {sorter: false}, 3: {sorter: false} }});
		});
		</script>';

	echo '<form method="post">' . mso_form_session('f_session_id');

	echo $CI->table->generate(); // вывод подготовленной таблицы

	echo '
		<p class="br">' . t('C отмеченными:') . '
		<input type="submit" name="f_delete_submit" onClick="if(confirm(\'' . t('Уверены?') . '\')) {return true;} else {return false;}" value="' . t('Удалить') . '"></p></form>';
	
	mso_hook('pagination', $pag);

# End of file
