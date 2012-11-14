<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	
	$f_all_comments = false; // только неразрешенные комментарии
	
	if (mso_segment(3) == 'all') $f_all_comments = true; 
	elseif (mso_segment(3) == 'moderation') $f_all_comments = false; 
	
	require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев
	
	# разрешить или запретить
	if ( ( $post = mso_check_post(array('f_session_id', 'f_check_comments')) ) and 
		( isset($_POST['f_aproved_submit']) or isset($_POST['f_unaproved_submit']) ) )
		
	{
		mso_checkreferer();

		$action = '0'; // запретить по-умолчанию
		if (isset($post['f_aproved_submit'])) $action = '1'; // разрешить
		
		$f_check_comments = $post['f_check_comments']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_comments as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
		
		$CI->db->where_in('comments_id', $arr_ids);
		if ($CI->db->update('comments', array('comments_approved'=>$action) ) )
		{
			mso_flush_cache();
			
			// синхронизация количества комментариев у комюзеров
			mso_comuser_update_count_comment();
			
			echo '<div class="update">' . t('Обновлено!') . '</div>';
		}
		else 
			echo '<div class="error">' . t('Ошибка обновления') . '</div>';
	}
	
	
	# удалить комментарий
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_check_comments')) )
	{
		mso_checkreferer();
		// pr($post);
		
		$f_check_comments = $post['f_check_comments']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_comments as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
		
		$CI->db->where_in('comments_id', $arr_ids);
		
		if ( $CI->db->delete('comments') )
		{
			mso_flush_cache();
			
			// синхронизация количества комментариев у комюзеров
			mso_comuser_update_count_comment();
			
			echo '<div class="update">' . t('Удалено!') . '</div>';
		}
		else 
			echo '<div class="error">' . t('Ошибка удаления') . '</div>';
	}
	

?>
<h1><?= t('Комментарии') ?></h1>

<p class="info"><?= t('Последние комментарии') ?></p>

<p><strong><?= t('Фильтр:') ?></strong> <a href="<?= getinfo('site_admin_url') ?>comments/all"><?= t('Все') ?></a> | <a href="<?= getinfo('site_admin_url') ?>comments/moderation"><?= t('Только требующие модерации') ?></a></p>


<?php

	$CI->load->library('table');
	
	$tmpl = array (
				'table_open'		  => '<table class="page tablesorter">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID', '&bull;', '+', t('Текст'));
	
	# подготавливаем выборку из базы
	
	$CI->db->select('SQL_CALC_FOUND_ROWS comments_id, comments_users_id, comments_comusers_id, comments_author_name, comments_date, comments_content, comments_approved, comments_author_ip, users.users_nik, comusers.comusers_nik, page.page_title, page.page_slug', false);
	$CI->db->from('comments');
	$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
	$CI->db->join('page', 'page.page_id = comments.comments_page_id', 'left');
	
	if (!$f_all_comments) $CI->db->where('comments_approved', 0);

	$CI->db->order_by('comments_date', 'desc');
	
	$limit = 20;

	$CI->db->limit($limit, mso_current_paged() * $limit - $limit ); // не более $limit
	
	$query = $CI->db->get();

	$pagination = mso_sql_found_rows($limit); // определим общее кол-во записей для пагинации
	mso_hook('pagination', $pagination);
	
		
	// если есть данные, то выводим
	if ($query->num_rows() > 0)
	{
		$this_url = $MSO->config['site_admin_url'] . 'comments/';
		$view_url = $MSO->config['site_url'] . 'page/';
		
		foreach ($query->result_array() as $row)
		{
			$id = $row['comments_id'];
			
			// для вывода делаем чекбокс + hidden всех комментов для того, чтобы проверить тех,
			// которые окажутся не отмечены - их POST не передает
			$id_out = '<input type="checkbox" name="f_check_comments[' . $id . ']">' . NR;
			
			$act = '<a href="' . $this_url . 'edit/'. $id . '">' . t('Изменить') . '</a>';
			
			$comments_date = $row['comments_date'];
			
			$author = '';
			
			if ( $row['comments_users_id'] ) 
			{
				$author = '<span class="admin">' . $row['users_nik'] . '</span>';
			}
			elseif ($row['comments_comusers_id']) 
			{
				$author = '<span class="comuser">' . $row['comusers_nik'] . '</span> (' . t('комюзер') . ' ' . $row['comments_comusers_id'] . ')';
			}
			else 
			{
				if (!$row['comments_author_name']) $row['comments_author_name'] = t('Аноним');
				$author = '<span class="anonymous">' . $row['comments_author_name'] . '</span> (' . t('анонимно') . ')';
			}
			
			$page_slug = $row['page_slug'];
			$page_title = '<a target="_blank" href="' . $view_url . $page_slug . '#comment-' . $id . '">«' . htmlspecialchars( $row['page_title'] ) . '»</a>';
			
			// определим XSS и визуально выделим такой комментарий
			$comments_content_xss_start = mso_xss_clean($row['comments_content'], '<span style="color: red">XSS!!! ', '');
			if ($comments_content_xss_start) $comments_content_xss_end = '</span>';
				else $comments_content_xss_end = '';
			
			$comments_content = htmlspecialchars($row['comments_content']);
			$comments_content = str_replace('&lt;p&gt;', '<br>', $comments_content);
			$comments_content = str_replace('&lt;/p&gt;', '', $comments_content);
			$comments_content = str_replace('&lt;br /&gt;', '<br>', $comments_content);
			
			if (mb_strlen($comments_content, 'UTF-8') > 300)
				$comments_content = mb_substr($comments_content, 0, 300, 'UTF-8') . ' ...';
			
			
			if ( $row['comments_approved'] > 0 ) $comments_approved = '+';
				else $comments_approved = '-';
			
			$act = '<a href="' . $this_url . 'edit/'. $id . '">' . $author . '</a>';
			
			$out = $comments_content_xss_start 
					// . '<strong>' . $author . '</strong>' . $act . '<br>'
					. $act . '<br>'
					. $comments_date. ' | ' 
					. $row['comments_author_ip'] 
					. ' | '. $page_title 
					. $comments_content_xss_end 
					. '<p>' . $comments_content . '</p>' 
					. NR;
						
			
			$CI->table->add_row($id, $id_out, $comments_approved, $out);
		}
	

		echo '<form  method="post" class="fform admin_comments">' . mso_form_session('f_session_id');
		

		echo $CI->table->generate();
		
		echo '
			<p class="br">' . t('C отмеченными:') . '
			<input type="submit" name="f_aproved_submit" value="' . t('Разрешить') . '">
			<input type="submit" name="f_unaproved_submit" value="' . t('Запретить') . '">
			<input type="submit" name="f_delete_submit" onClick="if(confirm(\'' . t('Уверены?') . '\')) {return true;} else {return false;}" value="' . t('Удалить') . '"></p><br>
			';
		echo '</form>';
		
		echo mso_load_jquery('jquery.tablesorter.js') . '
			<script>
			$(function() {
				$("table.tablesorter").tablesorter( {headers: { 1: {sorter: false}, 2: {sorter: false}, 3: {sorter: false} }});
			});
			</script>';
			
		mso_hook('pagination', $pagination);
	}
	else
	{
		echo '<h3>' . t('Нет комментариев') . '</h3>';  
	
	}

	
?>