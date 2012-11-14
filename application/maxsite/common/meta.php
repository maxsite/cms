<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Функции для meta, включая метки
 */


# получаем все метки указанной страницы
function mso_get_tags_page($id = 0)
{
	$id = (int) $id;
	if (!$id) return array();

	$CI = & get_instance();

	$CI->db->select('meta_value');
	$CI->db->where( array ( 'meta_key' => 'tags', 'meta_id_obj' => $id, 'meta_table' => 'page' ) );
	$CI->db->group_by('meta_value');
	$query = $CI->db->get('meta');

	if ($query->num_rows() > 0)
	{
		$tags = array();
		foreach ($query->result_array() as $row)
			$tags[] = $row['meta_value'];

		return $tags;
	}
	else return array();
}

# получаем все метки в массиве
function mso_get_all_tags_page($options = array())
{
	$CI = & get_instance();

	$CI->db->select('meta_value, COUNT(meta_value) AS meta_count');
	$CI->db->where( array (	'meta_key' => 'tags', 'meta_table' => 'page' ) );
	$CI->db->join('page', 'page.page_id = meta.meta_id_obj' );

	$CI->db->where( 'page_status', 'publish'); // только опубликованные
	//$CI->db->where('page_date_publish <', date('Y-m-d H:i:s')); // и только раньше текущей
	$CI->db->where('page_date_publish < ', 'NOW()', false); // и только раньше текущей

	$CI->db->group_by('meta_value');
	$query = $CI->db->get('meta');

	// переделаем к виду [метка] = кол-во
	if ($query->num_rows() > 0)
	{
		$tags = array();
		foreach ($query->result_array() as $row)
			$tags[$row['meta_value']] = $row['meta_count'];

		return $tags;
	}
	else return array();
}

# получение данных из таблицы mso_meta
# если указать id, то возвращается meta_value для указанного meta_id_obj
# если не указано, то возвращаются все meta_value ключа
function mso_get_meta($meta_key = '', $meta_table = '', $id = 0)
{
	if (!$meta_key or !$meta_table) return array();

	$CI = & get_instance();

	$CI->db->select('meta_value, meta_id_obj, meta_desc, meta_slug, meta_menu_order');
	if ($id) $CI->db->where( array ( 'meta_key' => $meta_key, 'meta_id_obj' => $id, 'meta_table' => $meta_table ) );
		else $CI->db->where( array ( 'meta_key' => $meta_key, 'meta_table' => $meta_table ) );

	// $CI->db->group_by('meta_value');
	$CI->db->order_by('meta_menu_order');
	$query = $CI->db->get('meta');

	if ($query->num_rows() > 0)
	{
		return $query->result_array();
	}
	else return array();
}

# Запись данных в таблицу mso_meta
function mso_add_meta($meta_key = '', $meta_id_obj = '', $meta_table = '', $meta_value = '',
		$meta_desc = '', $meta_menu_order = 0, $meta_slug = '')
{

    # Если обязательные поля отсутствуют возвращаем ошибку
    if( !$meta_key or !$meta_id_obj or !$meta_table ) return false;

    $data = array(
        'meta_key' => $meta_key,
        'meta_id_obj' => $meta_id_obj,
        'meta_table' => $meta_table,
        'meta_value' => $meta_value,
        'meta_desc' => $meta_desc,
        'meta_menu_order' => $meta_menu_order,
        'meta_slug' => $meta_slug
    );

    //pr($data);

    $CI = &get_instance();

    # Ищем ID записи по meta_key и meta_id_obj
    $CI->db->select('meta_id');
    $CI->db->where(array('meta_key' => $meta_key, 'meta_id_obj' => $meta_id_obj, 'meta_table' => $meta_table ));
    $query = $CI->db->get('meta');

    if ( $query->num_rows() == 0 )
    {
		# такой записи нет, ее нужно вставить
		if ( $meta_value > '')
		{
			//pr('insert ' . $meta_value);
			$res = $CI->db->insert('meta', $data);
			$CI->db->cache_delete_all();
			return $res;
		}
		//else pr('no insert');
    }
    else
    {
		# такая запись есть, ее нужно обновить
		//pr('update');
        $row = $query->row();
        $res = $CI->db->update('meta', $data, 'meta_id = ' . $row->meta_id);
        $CI->db->cache_delete_all();
        return $res;
    }
}


# end file