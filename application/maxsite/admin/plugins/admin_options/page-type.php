<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();

	// проверяем входящие данные если было обновление
	if ( $post = mso_check_post(array('f_session_id', 'f_desc', 'f_edit_submit')) )
	{
		# защита рефера
		mso_checkreferer();

		# защита сессии - если это нужно то убрать коммент
		// mso_checksession($post['f_session_id'], 'loginform');

		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_edit_submit']);

		// полученное новое значение
		$f_new_value = $post['f_desc'][$f_id];

		// формируем sql-запрос
		$CI->db->where('page_type_id', $f_id);

		if ( $CI->db->update('page_type', array( 'page_type_desc'=>$f_new_value )) )
		{
			echo '<div class="update">' . t('Обновлено!') . '</div>';
			$CI->db->cache_delete_all();
		}
		else
			echo '<div class="error">' . t('Ошибка обновления') . '</div>';
	}

	// проверяем входящие данные если было добавление нового типа
	if ( $post = mso_check_post(array('f_session_id', 'f_new_submit', 'f_new_name', 'f_new_desc')) )
	{
		# защита рефера
		mso_checkreferer();

		# защита сессии - если нужно то убрать коммент
		// mso_checksession($post['f_session_id'], 'loginform');

		// полученное новое значение
		$f_new_name = trim($post['f_new_name']);
		$f_new_desc = trim($post['f_new_desc']);

		if ($f_new_name > '')
		{
			// перед добавлением нужно проверить есть ли уже такой тип
			// если есть, то ничего не добавлять
			$CI->db->select('page_type_id');
			$CI->db->where(array('page_type_name'=>$f_new_name));

			$query = $CI->db->get('page_type');

			if ($query->num_rows() == 0 ) // нет такого типа страниц
			{
				// значит добавляем
				if ($CI->db->insert('page_type', array( 'page_type_name'=>$f_new_name, 'page_type_desc'=>$f_new_desc)))
					echo '<div class="update">' . t('Новый тип добавлен!') . '</div>';
				else
					echo '<div class="error">' . t('Ошибка добавления!') . '</div>';
			}
			else
				echo '<div class="error">' . t('Такой тип страниц уже существует!') . '</div>';
		}
		else
			echo '<div class="error">' . t('Вы не ввели тип страницы!') . '</div>';
	}

?>

<h1><?= t('Типы страниц') ?></h1>
<p class="info"><?= t('Типы страниц предназначены для того, чтобы вы могли группировать свои записи по своему усмотрению. Однако для того, чтобы задействовать дополнительные типы страниц, необходимо чтобы ваш шаблон знал об этих типах. Обычно это требует дополнительного программирования. Стандартно в <strong>MaxSite CMS</strong> использются типы <strong>«blog»</strong> - для отображения записей в обратном хронологическом порядке; а также <strong>«static»</strong> - для прочих статичных страниц.') ?></p>

<?php
	// для вывода будем использовать html-таблицу
	$CI->load->library('table');

	$tmpl = array (
                    'table_open'          => '<table class="page" border="0" width="99%"><colgroup width="30"><colgroup style="width: 150px"><colgroup><colgroup style="width: 200px">',
                    'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
              );

	$CI->table->set_template($tmpl); // шаблон таблицы

	// заголовки
	$CI->table->set_heading('ID', t('Тип'), t('Описание'), t('Действие'));

	// выполним sql-запрос на получение некоторых опций
	$query = $CI->db->get('page_type');

	// обходим в цикле и выводим
	foreach ($query->result_array() as $row)
	{
		$id = $row['page_type_id'];
		$name = $row['page_type_name'];
		$desc = htmlspecialchars($row['page_type_desc'], ENT_QUOTES);

		//$desc = '<input style="width: 99%;" type="text" name="f_desc[' . $id . ']" value="' . $desc . '">';
		$desc = '<input type="text" name="f_desc[' . $id . ']" value="' . $desc . '">';

		$act = '<input type="submit" name="f_edit_submit[' . $id . ']" value="' . t('Изменить') . '">';

		$CI->table->add_row($id, $name, $desc, $act);
	}

	# добавим строчку для добавления нового типа
	//$name = '<b>' . t('Новый тип') . '</b><br><input style="width: 99%;" type="text" name="f_new_name" value="">';
	$name = '<b>' . t('Новый тип') . '</b><br><input type="text" name="f_new_name" value="">';
	//$desc = '<b>' . t('Описание') . '</b><br><input style="width: 99%;" type="text" name="f_new_desc" value="">';
	$desc = '<b>' . t('Описание') . '</b><br><input type="text" name="f_new_desc" value="">';
	$act = '<input type="submit" name="f_new_submit" value="' . t('Добавить новый тип') . '">';

	$CI->table->add_row('', $name, $desc, $act);

	// добавляем форму, а также текущую сессию
	echo '<form method="post">' . mso_form_session('f_session_id');
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '</form>';

?>