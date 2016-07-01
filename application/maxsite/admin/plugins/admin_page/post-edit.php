<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// файл функций
require_once(getinfo('admin_plugins_dir') . 'admin_page/post-edit-functions.php');

// подготовка данных из POST
$data = post_prepare($post);
$data['page_id'] = mso_array_get_key($post['f_submit']);

require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
$result = mso_edit_page($data);

// pr($result);

if (isset($result['result']) and $result['result']) 
{
	
	$url = isset($result['result'][0]) ? mso_get_permalink_page($result['result'][0]) : '';
	/*
	
	if (isset($result['result'][0])) 
	{
		
				. '">' . t('Посмотреть запись') . '</a> (<a target="_blank" href="' 
				. mso_get_permalink_page($result['result'][0]) . '">' . t('в новом окне') . '</a>)';		
	}
	else 
		$url = '';
	
	
	if (!isset($post['is_bsave'])) // обычное сохранение
	{
		echo ' | ' . $url;
		echo '<div class="update">' . t('Страница обновлена!') . '</div>'; 
	}
	else // быстрое/фоновое сохранение
	{
		echo '<div class="update">' . t('Страница обновлена!'). ' ' . $url . '</div>'; 
	}
	*/
	echo '<div class="update pos-fixed w200px pad10 pos20-r pos0-t t-center">' . t('Запись сохранена!') . '</div>'; 
	
	echo '<script> $(function(){ $("#permalink_page1").attr("href", "' . $url . '"); $("#permalink_page2").attr("href", "' . $url . '"); }) </script>';
	
	# пулучаем данные страниц
	$CI->db->select('*');
	$CI->db->from('page');
	$CI->db->where(array('page_id'=>$id));
	$query = $CI->db->get();
	if ($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $row)
		{
			// pr($row);
			$f_content = $row['page_content'];
			$f_header = $row['page_title'];
			$f_slug = $row['page_slug'];
			$f_status = $row['page_status'];
			$f_page_type = $row['page_type_id'];
			$f_password = $row['page_password'];
			$f_comment_allow = $row['page_comment_allow'];
			$f_ping_allow = $row['page_ping_allow'];
			$f_feed_allow = $row['page_feed_allow'];
			$f_page_parent = $row['page_id_parent'];
			$f_user_id = $row['page_id_autor'];
			$page_date_publish = $row['page_date_publish'];
			$page_menu_order = $row['page_menu_order'];
		}
		
		$f_cat = mso_get_cat_page($id); // рубрики в виде массива
		$f_tags = implode(', ', mso_get_tags_page($id)); // метки страницы в виде массива			
	}
}
else
	echo '<div class="error pos-fixed w200px pad10 pos20-r pos0-t t-center">' . t('Ошибка обновления') . '</div>';
			