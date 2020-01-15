<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// проверим залогиненность
if (!is_login()) die('no login');

// проверим разрешение на управление рубриками
if (!mso_check_allow('admin_cat')) echo t('Доступ запрещен');

mso_checkreferer(); // защищаем реферер

require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик

if ( $post = mso_check_post(array('do', 'session_id', 'category_id')) )
{
	$post = mso_clean_post( array_keys($_POST) );

	# обновляем рубрику
	if( $post['do'] == 'update' )
	{
		// mso_log(0);
		// mso_log($_POST);
		
		$cat_id = $post['category_id'];

		# подготавливаем данные
		$data = array(
			'category_id' => $cat_id,
			'category_id_parent' => (int) $post['cat'][$cat_id]['category_parent'],
			'category_name' => $post['cat'][$cat_id]['category_name'],
			'category_desc' => $post['cat'][$cat_id]['category_desc'],
			'category_slug' => $post['cat'][$cat_id]['category_slug'],
			'category_menu_order' => (int) $post['cat'][$cat_id]['category_order']
			);

		$old = mso_get_cat_from_id( $cat_id );

		$reload = $old['category_menu_order'] != $data['category_menu_order'] || $old['category_id_parent'] != $data['category_id_parent'] ? true : false;

		# выполняем запрос и получаем результат
		require_once(getinfo('common_dir') . 'functions-edit.php'); // функции редактирования

		$result = mso_edit_category($data);

		if (isset($result['result']) and $result['result'])
		{
			// mso_log($post['cat'][$cat_id]['category_meta']);
			
			// теперь обновим мета
			if (isset($post['cat'][$cat_id]['category_meta']))
			{				
				foreach($post['cat'][$cat_id]['category_meta'] as $meta_key => $meta_value)
				{
					mso_add_meta($meta_key, $cat_id, $meta_table = 'category', $meta_value);
				}
			}
			
			mso_flush_cache(); // сбросим кэш
			$msg = '<div class="update">' . t('Рубрика сохранена!') . '</div>';
		}
		else
			$msg = '<div class="error">' . t('Ошибка сохранения') . '</div>';
		
		// $msg .= $result['upd_data']['category_slug'];
		
		echo json_encode(array(
			'ok' => isset($result['result']) and $result['result'] ? true : false,
			'msg' => $msg,
			'reload' => $reload,
			'name' => $data['category_name'],
			'slug' => $result['upd_data']['category_slug'], // $data['category_slug'],
			'url' => getinfo('siteurl') . 'category/' . $result['upd_data']['category_slug'], // $data['category_slug'],
		));
	}
	# удаляем рубрику
	elseif( $post['do'] == 'delete' )
	{
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		# готовим данные
		$data = array('category_id' => $post['category_id'] );

		$result = mso_delete_category($data);

		echo json_encode($result);
	}
}

# end of file