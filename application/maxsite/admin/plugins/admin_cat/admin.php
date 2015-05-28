<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	$CI = & get_instance();
	
	require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик 
	
	# редактирование существующей рубрики
	if ( $post = mso_check_post(array('f_session_id', 'f_edit_submit', 
									'f_category_id_parent', 'f_category_name', 
									'f_category_desc', 'f_category_slug', 
									'f_category_menu_order')) )
	{
		mso_checkreferer();
		
		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_edit_submit']); 
		
		// подготавливаем данные
		$data = array(
			'category_id' => $f_id,
			'category_id_parent' => (int) $post['f_category_id_parent'][$f_id],
			'category_name' => $post['f_category_name'][$f_id],
			'category_desc' => $post['f_category_desc'][$f_id],
			'category_slug' => $post['f_category_slug'][$f_id],
			'category_menu_order' => (int) $post['f_category_menu_order'][$f_id]
			);
		
		// выполняем запрос и получаем результат
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_edit_category($data);
		
		if (isset($result['result']) and $result['result']) 
		{
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Обновлено!') . '</div>';
		}
		else
			echo '<div class="error">' . t('Ошибка обновления') . '</div>';
	}
	
	# добавление новой рубрики
	if ( $post = mso_check_post(array('f_session_id', 'f_new_submit', 
									'f_new_parent', 'f_new_name', 
									'f_new_desc', 'f_new_slug', 
									'f_new_order')) )
	{
		mso_checkreferer();

		// подготавливаем данные для xmlrpc
		$data = array(
			'category_id_parent' => (int) $post['f_new_parent'],
			'category_name' => $post['f_new_name'],
			'category_desc' => $post['f_new_desc'],
			'category_slug' => $post['f_new_slug'],
			'category_menu_order' => (int) $post['f_new_order']
			);
		
		// выполняем запрос и получаем результат

		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_new_category($data);
		
		if (isset($result['result']) and $result['result']) 
		{
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Добавлено!') . '</div>';
		}
		else
			echo '<div class="error">' . t('Ошибка добавления!'). ' ' . $result['description'] . ' </div>';
	}
	
	# удаление существующей рубрики
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit')) )
	{
		mso_checkreferer();
		
		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_delete_submit']); 
		
		// подготавливаем данные
		$data = array('category_id' => $f_id );
		
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_delete_category($data);
		
		if (isset($result['result']) and $result['result']) 
		{	
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Удалено!') . ' ' . $result['description'] . '</div>';
		}
		else
			echo '<div class="error">' . t('Ошибка удаления') . ' ' . $result['description'] . '</div>';
	}

	
?>
	<h1><?= t('Рубрики') ?></h1>
	<p class="info"><?= t('Настройка рубрик') ?></p>

<?php
	

	$all = mso_cat_array('page', 0, 'category_menu_order', 'asc', 'category_menu_order', 'asc', false, false, false, false, false, false, false);
	
	$format = '
	
	<table class="cats"><tr>
	
	<td class="t1">
		<p>
		
		<span class="fheader"><a href="' . getinfo('siteurl') . 'category/[SLUG_HTML]" target="_blank" title="' . t('Смотреть рубрику на сайте') . '">[ID]</a>
		</span>
		
		<span class="fempty"></span>
		<span class="fempty"></span>
		
		<label><input style="width: 250px;" type="text" title="' . t('Название') . '" name="f_category_name[[ID]]" value="[TITLE_HTML]"></label>
		
		<label>' . t('Ссылка:') . ' <input style="width: 150px;" title="' . t('Короткая ссылка') . '" name="f_category_slug[[ID]]" value="[SLUG_HTML]" type="text"></label>
		
		
		<label>' . t('Родитель:') . ' <input style="width: 60px;" title="' . t('Номер родителя') . '" name="f_category_id_parent[[ID]]" value="[ID_PARENT]" type="number"></label>
		
		<label>' . t('Порядок:') . ' <input style="width: 60px;" title="' . t('Порядок') . '" name="f_category_menu_order[[ID]]" value="[MENU_ORDER]" type="number"></label>
		
		<span><a href="' . getinfo('site_admin_url') . 'page/category/[ID]" title="' . t('Список записей') . '" target="_blank">' . t('Записей:') . ' [COUNT]</a></span>
		
		</p>
		
		<p><span><textarea title="' . t('Описание') . '" name="f_category_desc[[ID]]" rows="2">[DESCR_HTML]</textarea></span></p>
		
	</td>
	
	<td class="t2"> 
		<button type="submit" name="f_edit_submit[[ID]]" class="i save">' . t('Сохранить') . '</button>
		<br><button type="submit" name="f_delete_submit[[ID]]" class="i delete" onClick="if(confirm(\'' . t('Удалить рубрику?') . '\')) {return true;} else {return false;}">' . t('Удалить') . '</button>
	</td> 
	
	</tr></table>';
	
	
	
	$out = mso_create_list($all, 
		array(
			'childs'=>'childs', 
			'format'=>$format, 
			'format_current'=>$format, 
			'class_ul'=>'', 
			
			'class_ul_style'=>'', 
			'class_child_style'=>'', 
			'class_li_style'=>'',
			
			'title'=>'category_name', 
			'link'=>'category_slug', 
			'current_id'=>false, 
			'prefix'=>'category/', 
			'count'=>'pages_count', 
			'id'=>'category_id', 
			'slug'=>'category_slug', 
			'menu_order'=>'category_menu_order', 
			'id_parent'=>'category_id_parent'
			) );
	
	// добавляем форму, а также текущую сессию
	echo '<form method="post" class="mso-cats">' . mso_form_session('f_session_id');
	echo $out;
	
	# строчка для добавления новой рубрики
	echo '
		<div>
			<h3>' . t('Добавить новую рубрику') . '</h3>
			<p><label><span>' . t('Название') . '<span><input type="text" name="f_new_name"></label></p>
			<p><label><span>' . t('Описание') . '<span><textarea name="f_new_desc"></textarea></label></p>
			<p><label><span>' . t('Ссылка') . '<span><input type="text" name="f_new_slug"></label></p>
			<p><label><span>' . t('Родитель') . '<span><input type="text" name="f_new_parent" value=""></label></p>
			<p><label><span>' . t('Порядок') . '<span><input type="text" name="f_new_order"></label></p>
			<p><button type="submit" name="f_new_submit" class="i add-new">' . t('Добавить новую рубрику') . '</button></p>
		</div>
	</form>';
	
# end file