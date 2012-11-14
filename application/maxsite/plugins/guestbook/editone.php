<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	// основной url этого плагина - жестко задается
	$plugin_url = getinfo('site_admin_url') . 'guestbook';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки гостевой книги'), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'edit', t('Редактирование отзывов'), 'select');
	echo $a;
?>
</div>

<h1><?= t('Редактирование отзыва') ?></h1>

<?php
// проверим верность 4-го сектора

$id = mso_segment(4);
if (!is_numeric($id)) $id = false; // не число
	else $id = (int) $id;

if (!$id) 
{
	echo t('Ошибочный номер');
	return; // выходим
}

$CI = & get_instance();


# удаление
if ( $post = mso_check_post(array('f_session_id', 'f_submit_guestbook_delete', 'f_fields_guestbook')) )
{
	mso_checkreferer();
	
	if ($post['f_fields_guestbook']['id'] != $id)
	{
		echo t('Ошибочный номер');
		return;
	}

	$CI->db->where('guestbook_id', $id);
	$CI->db->delete('guestbook');
	
	mso_flush_cache();
	
	echo '<div class="update">' . t('Удалено!') . '</div>';
	return;
}

# редактирование
if ( $post = mso_check_post(array('f_session_id', 'f_submit_guestbook', 'f_fields_guestbook')) )
{
	mso_checkreferer();
	
	if ($post['f_fields_guestbook']['id'] != $id)
	{
		echo t('Ошибочный номер');
		return;
	}
	
	$CI->db->where('guestbook_id', $id);
	
	$data = array();
	$data['guestbook_approved'] = isset($post['f_fields_guestbook']['approved']) ? 1 : 0;
	
	foreach( $post['f_fields_guestbook'] as $key => $val )
	{
		if ($key != 'id' and $key != 'approved') $data['guestbook_' . $key] = $post['f_fields_guestbook'][$key];
	}
	
	// pr($data);
	
	mso_flush_cache();
	
	if ($CI->db->update('guestbook', $data ) )
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	else 
		echo '<div class="error">' . t('Ошибка обновления') . '</div>';
}




$options = mso_get_option('plugin_guestbook', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'guestbook';
echo '<p><a href="' . getinfo('siteurl') . $options['slug']  . '#guestbook-' . $id. '" target="_blank">' . t('Посмотреть отзыв на сайте') . '</a></p>';
	


$CI->load->library('table');
$tmpl = array (
				'table_open'		=> '<table class="page" border="0" width="100%"><colgroup style="width: 100px;"/>',
				'row_alt_start'		=> '<tr class="alt">',
				'cell_alt_start'	=> '<td class="alt" style="vertical-align: top;">',
				'cell_start'		=> '<td style="vertical-align: top;">',
		  );

$CI->table->set_template($tmpl); // шаблон таблицы

// заголовки
$CI->table->set_heading(t('Поле'), t('Значение')); 

// теперь получаем сами записи
$CI->db->from('guestbook');
$CI->db->where('guestbook_id', $id);
			
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$books = $query->result_array();
	
	$out = '';
	foreach ($books as $book) 
	{
		// pr($book);
		// чтобы не париться с полями, выводим по циклу
		
		foreach ( $book as $key=>$val )
		{
			$key = str_replace('guestbook_', '', $key);
			
			$val = htmlspecialchars($val);
			
			if ($key == 'id') // id менять нельзя
			{
				$val_out = $val . '<input name="f_fields_guestbook[' . $key . ']" type="hidden" value="' . $val . '">';
			}
			elseif ($key == 'approved') 
			{
				$check = $val ? ' checked' : '';
				$val_out = '<label><input name="f_fields_guestbook[' . $key . ']" type="checkbox" ' . $check . '/> ' 
					. t('Опубликовать') . '</label>';
			}
			elseif ($key != 'text') // для всех кроме text - input
			{
				$val_out = '<input name="f_fields_guestbook[' . $key . ']" type="text" style="width: 99%;" value="' . $val . '">';
			}
			else
			{
				$val_out = '<textarea name="f_fields_guestbook[' . $key . ']" style="width: 99%; height: 200px;">' . $val . '</textarea>';
			}
			
			$CI->table->add_row('<strong>' . $key . '</strong>', $val_out);
		}
	}
	

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '<input type="submit" name="f_submit_guestbook" value="' . t('Изменить', 'admin') . '" style="margin: 10px 0;">';
	echo ' <input type="submit" name="f_submit_guestbook_delete" onClick="if(confirm(\'' . t('Удалить отзыв?') . '\')) {return true;} else {return false;}" value="' . t('Удалить отзыв') . '">';
	echo '</form>';

}



?>