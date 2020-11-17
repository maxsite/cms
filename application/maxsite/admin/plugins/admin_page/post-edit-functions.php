<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// вспомогательные функции

// из входящего POST делаем массив годный для mso_new_page() и mso_edit_page()
function post_prepare($post)
{
	global $MSO;
	
	$data = array();
	
	$f_content = $post['f_content'];
	
	// возможно задан парсер
	if ( isset($post['f_options']['parser_content']) and $post['f_options']['parser_content'] != 'none' )
	{
		$parser = $post['f_options']['parser_content']; // парсер
		$parser_all = mso_hook('parser_register', array()); // все зарегистрированные парсеры
		
		$func = $parser_all[$parser]['content_post_edit']; // функцию, которую нужно выполнить
		if ( function_exists($func) ) $f_content = $func($f_content);
	}
	
	$f_header = $post['f_header'];
	
	$f_tags = (isset($post['f_tags']) and $post['f_tags']) ? $post['f_tags'] : '';
	$f_slug = (isset($post['f_slug']) and $post['f_slug']) ? $post['f_slug'] : mso_slug($f_header);
	$f_cat = isset($post['f_cat']) ? $post['f_cat'] : array();
	$f_password = (isset($post['f_password']) and $post['f_password']) ? $post['f_password'] : '';
	$f_status = isset($post['f_status']) ?  $post['f_status'][0] : 'publish';
	$f_page_type = isset($post['f_page_type']) ? $post['f_page_type'][0] : '1';
	$f_page_parent = (isset($post['f_page_parent']) and $post['f_page_parent']) ? (int) $post['f_page_parent'] : '0';
	$f_comment_allow = isset($post['f_comment_allow']) ? '1' : '0';
	$f_ping_allow = isset($post['f_ping_allow']) ? '1' : '0';
	$f_feed_allow = isset($post['f_feed_allow']) ? '1' : '0';
	$page_menu_order = isset($post['f_menu_order']) ? (int) $post['f_menu_order'] : '';
	$f_user_id = isset($post['f_user_id']) ? (int) $post['f_user_id'] : $MSO->data['session']['users_id'];
	
	// все мета
	$f_options = '';
	if (isset($post['f_options']))
	{
		foreach ($post['f_options'] as $key => $val)
			$f_options .= $key . '##VALUE##' . trim($val) . '##METAFIELD##';
	}
	
	$f_date_change = isset($post['f_date_change']) ? '1' : '0'; // сменить дату?
	
	if ( // проверяем есть ли дата
			$f_date_change and
			isset($post['f_date_y']) and 
			isset($post['f_date_m']) and
			isset($post['f_date_d']) and 
			isset($post['f_time_h']) and
			isset($post['f_time_m']) and
			isset($post['f_time_s']) and
			$post['f_date_y'] > -1 and $post['f_date_y'] < 3000 and
			$post['f_date_m'] > -1 and $post['f_date_m'] < 13 and
			$post['f_date_d'] > -1 and $post['f_date_d'] < 32 and
			$post['f_time_h'] > -1 and $post['f_time_h'] < 25 and
			$post['f_time_m'] > -1 and $post['f_time_m'] < 61 and
			$post['f_time_s'] > -1 and $post['f_time_s'] < 61 )
	{
		$page_date_publish_y = (int) $post['f_date_y'];
		$page_date_publish_m = (int) $post['f_date_m'];
		$page_date_publish_d = (int) $post['f_date_d'];
		$page_date_publish_h = (int) $post['f_time_h'];
		$page_date_publish_n = (int) $post['f_time_m'];
		$page_date_publish_s = (int) $post['f_time_s'];
		
		$page_date_publish = date('Y-m-d H:i:s', mktime($page_date_publish_h, $page_date_publish_n, $page_date_publish_s, $page_date_publish_m, $page_date_publish_d, $page_date_publish_y) );
	}
	else
		$page_date_publish = false;
	
	// подготавливаем данные
	$data = array(
		'user_login' => getinfo('session_users_login'),
		'password' => getinfo('session_users_password'),
		'page_title' => $f_header,
		'page_content' => $f_content,
		'page_type_id' => $f_page_type,
		'page_id_cat' => implode(',', $f_cat),
		'page_id_parent' => $f_page_parent,
		'page_id_autor' => $f_user_id,
		'page_status' => $f_status,
		'page_slug' => $f_slug,
		'page_password' => $f_password,
		'page_comment_allow' => $f_comment_allow,
		'page_ping_allow' => $f_ping_allow,
		'page_feed_allow' => $f_feed_allow,
		'page_tags' => $f_tags,
		'page_meta_options' => $f_options,
		'page_menu_order' => $page_menu_order,
	);
	
	if ($page_date_publish) $data['page_date_publish'] = $page_date_publish;
	
	return $data;
}

// все метки
function post_all_tags($editor_options)
{
	// вывод через плагин tagclouds
	if (!function_exists('tagclouds_widget_custom')) 
		require_once(getinfo('plugins_dir') . 'tagclouds/index.php');
		
	
	$f_all_tags = '
<script>
function addTag(t)
{
	var elem = document.getElementById("f_tags");
	e = elem.value;
	if ( e != "" ) { elem.value = e + ", " + t; }
	else { elem.value = t; };
}
function shtags(sh)
{
	var elem1 = document.getElementById("f_all_tags_max_num");
	var elem2 = document.getElementById("f_all_tags_all");
	
	if (sh == 1) 
	{ 
		elem1.style.display = "none"; 
		elem2.style.display = "block"; 
	}
	else
	{
		elem1.style.display = "block"; 
		elem2.style.display = "none"; 				
	}
}
</script>' . NR;
	
	// только первые 20
	$f_all_tags .= tagclouds_widget_custom(array(
		'max_num' => isset($editor_options['tags_count']) ? $editor_options['tags_count'] : 20,
		'max_size' => '130',
		'sort' => isset($editor_options['tags_sort']) ? $editor_options['tags_sort'] : 0, 
		'block_start' => '<p id="f_all_tags_max_num">',
		'block_end' => ' <a title="' . t('Показать все метки') . '" href="#" onClick="shtags(1); return false;">&gt;&gt;&gt;</a></p>',
		'format' => '<span style="font-size: [SIZE]%"><a href="#" onClick="addTag(\'[TAG]\'); return false;">[TAG]</a><sub style="font-size: 13px;">[COUNT]</sub></span>'
	));
	
	// все метки
	$f_all_tags .= tagclouds_widget_custom(array(
		'max_num' => 9999,
		'max_size' => '130',
		'sort' => isset($editor_options['tags_sort']) ? $editor_options['tags_sort'] : 0, 
		'block_start' => '<p id="f_all_tags_all" style="display: none;">',
		'block_end' => ' <a title="' . t('Показать только самые популярные метки') . '" href="#" onClick="shtags(2); return false;">&lt;&lt;&lt;</a></p>',
		'format' => '<span style="font-size: [SIZE]%"><a href="#" onClick="addTag(\'[TAG]\'); return false;">[TAG]</a><sub style="font-size: 13px;">[COUNT]</sub></span>'
	));
	
	return $f_all_tags;
}

// олучаем все типы страниц
// и для скрытия метаполей в зависимости от типа записи
function post_all_post_types($f_page_type)
{
	$CI = & get_instance();
	
	$all_post_types = '';
	
	$query = $CI->db->get('page_type');
	
	$page_type_js_obj = '{'; // для скрытия метаполей в зависимости от типа записи
	
	foreach ($query->result_array() as $row)
	{
		if ($f_page_type == $row['page_type_id']) $che = 'checked="checked"';
			else $che = '';
			
		$page_type_desc = $row['page_type_desc'] ? ' <em>(' . t($row['page_type_desc']) . ')</em>' : '';
			
		$all_post_types .= '<label class="nocell"><input name="f_page_type[]" type="radio" ' . $che 
								. ' value="' . $row['page_type_id'] . '"> ' 
								. $row['page_type_name'] . $page_type_desc . '</label><br>';
								
		$page_type_js_obj .= $row['page_type_name'] . ':' . $row['page_type_id'] . ',';
	}
	
	$page_type_js_obj .= '}';
	$page_type_js_obj = str_replace(',}', '}', $page_type_js_obj);
	
	$out['all_post_types'] = $all_post_types;
	$out['page_type_js_obj'] = $page_type_js_obj;
	
	return $out;
}


// все авторы-юзеры
function post_all_users($f_user_id)
{
	$CI = & get_instance();
	
	$all_users = array();

	// получаем список юзеров
	if (!mso_check_allow('edit_page_author')) // если не разрешено менять автора
	{
		$CI->db->where('users_id', $f_user_id); // ставим только текущего автора
	}
	
	$CI->db->select('users_id, users_login, users_nik');
	
	$query = $CI->db->get('users');
	
	if ($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $row)
			$all_users[$row['users_id']] = $row['users_login'] . ' (' . $row['users_nik'] . ')';
	}
	
	$CI->load->helper('form');
	
	$all_users = form_dropdown('f_user_id', $all_users, $f_user_id);
	
	return $all_users;
}

// дата-время
function post_date_time($date = false)
{
	if ($date === false) $date = date('Y-m-d H:i:s');
	
	// pr($date); // 2017-11-04 12:00:00
	
	$k_y = mso_date_convert('Y', $date, false, false);
	$k_m = mso_date_convert('m', $date, false, false);
	$k_d = mso_date_convert('d', $date, false, false);
	$k_h = mso_date_convert('H', $date, false, false);
	$k_i = mso_date_convert('i', $date, false, false);
	$k_s = mso_date_convert('s', $date, false, false);
	
	$date_all_y = array();
	$maxYear = date('Y') + 10;
	for ($i = 2005; $i < $maxYear; $i++) $date_all_y[$i] = $i;
	
	$date_all_m = array();
	for ($i=1; $i<13; $i++) $date_all_m[$i] = $i;
	
	$date_all_d = array();
	for ($i=1; $i<32; $i++) $date_all_d[$i] = $i;
	
	$out['date_y'] = form_dropdown('f_date_y', $date_all_y, $k_y, ' style="width: 100px;" ');
	$out['date_m'] = form_dropdown('f_date_m', $date_all_m, $k_m, ' style="width: 60px;" ');
	$out['date_d'] = form_dropdown('f_date_d', $date_all_d, $k_d, ' style="width: 60px;" ');
	
	$time_all_h = array();
	for ($i=0; $i<24; $i++) $time_all_h[$i] = $i;
	
	$time_all_m = array();
	for ($i=0; $i<60; $i++) $time_all_m[$i] = $i;

	$time_all_s = $time_all_m;
	
	$out['time_h'] = form_dropdown('f_time_h', $time_all_h, $k_h, ' style="width: 60px;" ');
	$out['time_m'] = form_dropdown('f_time_m', $time_all_m, $k_i, ' style="width: 60px;" ');
	$out['time_s'] = form_dropdown('f_time_s', $time_all_s, $k_s, ' style="width: 60px;" ');
	
	return $out;
}

// все страницы для parent
function post_all_pages($editor_options, $f_page_parent)
{
	$CI = & get_instance();
	
	$all_pages = '<select name="f_page_parent">';
	$all_pages .= '<option value="0">' . t('Нет') . '</option>';
	
	// если отмечена опция отрображать блок
	if (!isset($editor_options['page_all_parent']) or (isset($editor_options['page_all_parent']) and $editor_options['page_all_parent']))
	{ 
		$CI->db->select('page_id, page_title');
		$CI->db->where('page_status', 'publish');
		$CI->db->order_by('page_date_publish', 'desc');
		$query = $CI->db->get('page');	
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$sel = ($row['page_id'] == $f_page_parent) ? ' selected="selected"' : '';
				
				$all_pages .= '<option ' . $sel . 'value="' . $row['page_id'] . '">' . $row['page_id'] . ' - ' . htmlspecialchars($row['page_title']) . '</option>';
			}
		}
	}
	
	$all_pages .= '</select>';
	
	return $all_pages;
}

// вспомогательная функция, которая формирует <options> на основе массива
// позволяет задавать selected по ключу 
function form_select_options($values = array(), $selected = '')
{
	/*
		value => текст||title
		
	form_select_options(array(
			'width' => 'по ширине',
			'height' => 'по высоте',
			'max' => 'по максимальной',
			'no' => 'не менять'
			), 'width')
	*/
	
	$opt = '';
	
	foreach($values as $v => $s)
	{
		$title = '';
		
		if (strpos($s, '||') !== false) // указан title для элемента
		{
			$a = explode('||', $s);
			$s = $a[0];
			if (isset($a[1]) and $a[1]) 
				$title = ' title="' . htmlspecialchars($a[1]) . '"';  
		}
		
		if ($v == $selected)
			$opt .= '<option' . $title . ' value="' . $v . '" selected="selected">' . $s . '</option>';
		else
			$opt .= '<option' . $title . ' value="' . $v . '">' . $s . '</option>';
	}
	
	return $opt;
}

# end of file