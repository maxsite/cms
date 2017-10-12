<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// сохраняем запись в текстовый файл
function export_page($data)
{
	$result['result'] = false;
	
	$CI = & get_instance();

	$CI->db->select('page.*, page_type.*');
	$CI->db->from('page');
	$CI->db->where_in('page.page_id', $data['page_id']);
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->group_by('page.page_id');	
	
	$r = $CI->db->get();
	$pages = $r->result_array();
	
	if ($pages and count($pages) === 1)
	{
		$page = $pages[0];
		
		// здесь нужно получить рубрики записи и все её мета 
		
		$CI->db->select('page_id, category.category_id, category.category_name, category.category_slug, category.category_desc, category.category_id_parent');
		$CI->db->where_in('page_id', $data['page_id']);
		$CI->db->from('cat2obj');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id');

		if ($query = $CI->db->get()) 
			$cat = $query->result_array();
		else 
			$cat = array();

		// переместим все в массив
		$page_cat = array();
		$page_cat_detail = array();
		foreach ($cat as $key=>$val)
		{
			$page_cat[] = $val['category_id'];
			$page_cat_detail[$val['category_id']] = array('category_name' => $val['category_name'], 'category_slug' => $val['category_slug'], 'category_desc' => $val['category_desc'], 'category_id_parent' => $val['category_id_parent'], 'category_id' => $val['category_id']);
		}
		
		$page['page_categories'] = $page_cat;
		$page['page_categories_detail'] = $page_cat_detail;
		
		
		// все метки
		$CI->db->select('SQL_BUFFER_RESULT `meta_id_obj`, `meta_key`, `meta_value`', false);
		$CI->db->where( array (	'meta_table' => 'page' ) );
		$CI->db->where_in('meta_id_obj', $data['page_id']);

		if ($query = $CI->db->get('meta')) 
			$meta = $query->result_array();
		else 
			$meta = array();
		
		// переместим все в массив
		$page_meta = array();
		foreach ($meta as $key=>$val)
		{
			$page_meta[$val['meta_key']][] = $val['meta_value'];
		}
		
		// метки отдельно
		if (isset($page_meta['tags']))
		{
			$page['page_tags'] = $page_meta['tags'];
			unset($page_meta['tags']);
		}
		else
		{
			$page['page_tags'] = array();
		}
	
		$page['page_meta'] = $page_meta;
		
		// все данные получены
	
		$out = 'TITLE: ' . $page['page_title'] . NR;
		
		if ($all_cat = $page['page_categories_detail'])
		{
			$cats = '';
			
			foreach ($all_cat as $cat)
			{
				$cats .= $cat['category_name'] . "\t";
			}
			
			$cats = str_replace("\t", ' / ', trim($cats));
			
			$out .= 'CAT+: ' . $cats . NR;
		}
		
		// TAG
		if ($page['page_tags'])
		{
			$tags = implode("\t", $page['page_tags']);
			$tags = str_replace("\t", ', ', trim($tags));
			$out .= 'TAG: ' . $tags . NR;
		}
		
		// комментарии к странице
		require_once( getinfo('common_dir') . 'comments.php' );
		$page['comments'] = mso_get_comments($data['page_id']);
		
		$out .= 'SLUG: ' . $page['page_slug'] . NR;
		
		$out .= 'DATE: ' . $page['page_date_publish'] . NR;
		
		if ($page['page_type_name'] != 'blog')
			$out .= 'TYPE: ' . $page['page_type_name'] . NR;
		
		if ($page['page_status'] != 'publish')
			$out .= 'STATUS: ' . $page['page_status'] . NR;
		
		
		if ($page['page_menu_order'])
			$out .= 'MENU_ORDER: ' . $page['page_menu_order'] . NR;
		
		if ($page['page_password'])
			$out .= 'PASSWORD: ' . $page['page_password'] . NR;
		
		if ($page['page_menu_order'] === 0)
			$out .= 'COMMENT_ALLOW: 1' . NR;
		
		if ($page['page_feed_allow'] === 0)
			$out .= 'FEED_ALLOW: 1' . NR;
		
		
		
		// все мета выведем в виде META-ключ: значение 
		// [parser_content] => Array
		// (
		// 	[0] => Default
		// )
		// _pr($page['page_meta']);
		
		ksort($page['page_meta']);
		
		foreach($page['page_meta'] as $k => $v)
		{
			if (!isset($v[0])) continue;
			if (!empty($v) and !empty($v[0])) 
			{
				// дефолтный парсер не указываем — нет смысла
				if ($k === 'parser_content' and $v[0] === 'Default') continue;
				
				$u = str_replace("\n", '__NR__', trim($v[0]));
				$out .= 'META-' . $k . ': ' . $u . NR;
			}
		}
		
		
		// $out .= 'ID_AUTHOR: ' . $page['page_id_autor'] . NR; // не используется

		
		// добавим все связанные файлы в _pages 
		$pages_dir = getinfo('uploads_dir') . '_pages/' . $data['page_id'] . '/';
		$pages_url = '[[R_1]]_pages/' . $data['page_id'] . '/';
		
		$CI->load->helper('directory');
		$dirs = directory_map($pages_dir, 2); // только в текущем каталоге
		
		if ($dirs)
		{
			$out .= NR;
			foreach ($dirs as $file)
			{
				if (is_array($file)) continue; // каталог — это массив
				$out .= $pages_url . $file . NR;
			}
		}
		
		$out .= '---' . NR;
		
		// нужно ли делать эту замену???
		$page['page_content'] = str_replace("<br>", "\n", $page['page_content']);
		
		$out .= $page['page_content'] . NR;
		
		
		$out = str_replace(getinfo('uploads_url') . '_pages/' . $data['page_id'] . '/', '[[PAGE_FILES]]', $out);
		
		$out = str_replace(getinfo('uploads_url'), '[[UPLOADS_URL]]', $out);
		$out = str_replace(getinfo('siteurl'), '[[SITE_URL]]', $out);
		
		// спецзамены
		$out = str_replace('[[R_1]]', getinfo('uploads_url'), $out);
		
		
		if ($page['comments'])
		{
			$out .= NR . '---COMMENTS-START---' . NR;
			
			foreach ($page['comments'] as $key => $com)
			{
				$out .= 'comment_author: ' . $com['comments_author_name'] . NR;
				$out .= 'comment_author_IP: ' . $com['comments_author_ip'] . NR;
				$out .= 'comment_date: ' . $com['comments_date'] . NR;
				$out .= 'comment_content: ' . str_replace("\n", '__NR__', trim($com['comments_content'])) . NR;
				$out .= '---' . NR;
			}
			
			$out .= '---COMMENTS-END---' . NR;
			
			$out = str_replace("\n---\n---COMMENTS-END---", "\n---COMMENTS-END---", $out);
		}
		
		// pr($page);
		// pr($out, 1);
		
		$result['result'] = true; // false для теста
		$result['description'] = t('AutoPost');
		$result['out'] = $out;
		$result['fn'] = $page['page_id'] . '-' . $page['page_slug'] . '.txt';
		
		return $result;
	}
	
	return $result;
}


// function _my_meta($page, $key, $meta, $def = false)
// {
// 	if (isset($page['page_meta'][$meta][0]) and $m = $page['page_meta'][$meta][0])  
// 	{
// 		if ($def !== false and $def === $m)
// 			return '';
// 		else
// 			return $key . ': ' . $m . NR;
// 	}
// 	else
// 		return '';
// }

# end of file