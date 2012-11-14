<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	
	require_once( getinfo('common_dir') . 'page.php' ); 			// функции страниц 
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_page_delete')) )
	{
		mso_checkreferer();
		
		// pr($post);
		
		$page_id = (int) $post['f_page_delete'];
		if (!is_numeric($page_id)) $page_id = false; // не число
			else $page_id = (int) $page_id;

		if (!$page_id) // ошибка! 
		{
			echo '<div class="error">' . t('Ошибка удаления') . '</div>';
		}
		else 
		{
			$data = array(
				'user_login' => $MSO->data['session']['users_login'],
				'password' => $MSO->data['session']['users_password'],
				'page_id' => $page_id,
			);
			
			require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
			
			$result = mso_delete_page($data);
			
			if (isset($result['result']) and $result['result'])
			{
				if ( $result['result'] ) 
				{
					# mso_flush_cache(); // сбросим кэш перенес в mso_delete_page
					echo '<div class="update">' . t('Страница удалена') . '</div>';
				}
				else
				{
					echo '<div class="error">' . t('Ошибка при удалении') . ' ('. $result['description'] . ')</div>';
				}
			}
			else
			{
				echo '<div class="error">' . t('Ошибка при удалении') . ' ('. $result['description'] . ')</div>';
			}
			
			/*
			$CI->db->select('page_id');
			$CI->db->where(array('page_id'=>$page_id));
			$query = $CI->db->get('page');
			if ($query->num_rows() == 0) // нет такого
			{
				echo '<div class="error">Ошибочный номер страницы</div>';
			}
			else 
			{	// теперь можно удалять
				// при удалении страницы нужно сразу удалить её, рубрики и мета
				// потом будут еще и комментарии
				
				$CI->db->where( array('page_id'=>$page_id) );
				$CI->db->delete('cat2obj');
				
				$CI->db->where( array ('meta_id_obj' => $page_id, 'meta_table' => 'page') );
				$CI->db->delete('meta');
				
				$CI->db->where( array('page_id'=>$page_id) ); 
				$CI->db->delete('page');
				
				echo '<div class="update">Страница удалена</div>';
			}
			*/
		}
	}
	

?>
<h1><?= t('Страницы') ?></h1>
<p class="info"><?= t('Список всех записей') ?></p>

<?php

	$CI->load->library('table');
	$CI->load->helper('form');
	
	$tmpl = array (
				'table_open'		  => '<table class="page tablesorter">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID', t('Заголовок'), t('Дата'), t('Тип'), t('Статус'), t('Автор'));
	
	
	if ( !mso_check_allow('admin_page_edit_other') )
	{
		# echo 'запрещено редактировать чужие страницы';
		$current_users_id = getinfo('session');
		$current_users_id = $current_users_id['users_id'];
	}
	else $current_users_id = false;
	
	
	$par = array( 
			'limit' => 30, // колво записей на страницу
			'type' => false, // любой тип страниц
			'custom_type' => 'home', // запрос как в home
			'order' => 'page_date_publish', // запрос как в home
			'order_asc' => 'desc', // в обратном порядке
			'page_status' => false, // статус любой
			'date_now' => false, // любая дата
			//'content'=> false, // без содержания
			'page_id_autor'=> $current_users_id, // только указанного автора
			'cut' => ' ',
			);
	
	$CI->db->select('category_id, category_name');
	$CI->db->order_by('category_name');
	$CI->db->where('category_type', 'page');
	
	$query = $CI->db->get('category');

	if ($query and $query->num_rows() > 0) 
	{
		//echo '<h1>Страницы по рубрикам</h1>';
		$cat_segment_id = 0;
		
		if (mso_segment(3) == 'category') $cat_segment_id = (int) mso_segment(4);
		
		echo '<p class="admin_page_filtr"><strong>'
				. t('Рубрика')
				. ':</strong> ';
		
		/*
		if (mso_segment(3) and mso_segment(3) != 'next')
		{
			echo '<a class="no_filtr" href="' . getinfo('site_admin_url') . 'page">' . t('Без фильтра') . '</a> ';
		}
		else
		{
			echo '<a class="current" href="' . getinfo('site_admin_url') . 'page">' . t('Без фильтра') . '</a> ';
		
		}
		*/
		
		
		require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик
		
		$all_cats = mso_cat_array_single('page', 'category_id', 'ASC', ''); // все рубрики для вывода кол-ва записей
		# pr($all_cats);
		
		echo '<select class="admin_page_filtr">';
		
		$selected = (mso_segment(3) and mso_segment(3) != 'next') ? '' : ' selected';
		
		echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любая') . '</option>';
		
		foreach ($query->result_array() as $nav) 
		{
			
			$selected = ($cat_segment_id != $nav['category_id']) ? '' : ' selected';
			
			echo '<option value="' . getinfo('site_admin_url'). 'page/category/' . $nav['category_id'] .'"' . $selected . '>' . $nav['category_name'] . ' ('. count($all_cats[$nav['category_id']]['pages']) . ')</option>';
		
			/*
			if ($cat_segment_id != $nav['category_id']) 
			{
				echo ' <a href="' . getinfo('site_admin_url'). 'page/category/' . $nav['category_id'] .'">'
					. $nav['category_name'] 
					. ' <small>('.  count($all_cats[$nav['category_id']]['pages']) . ')</small></a> ';
			} 
			else 
			{
				echo ' <a class="current" href="' . getinfo('site_admin_url') . 'page/category/' . $nav['category_id'] . '">' . $nav['category_name'] . ' <small>('.  count($all_cats[$nav['category_id']]['pages']) . ')</small></a> ';
			}
			*/
		}

		echo '</select>';
	}

	
	
	$CI->db->select('page_type_id, page_type_name');
	$CI->db->order_by('page_type_name');
	
	$query = $CI->db->get('page_type');
	
	if ($query->num_rows() > 0) 
	{
		$type_segment_id = 0;

		if (mso_segment(3) == 'type') 
		{
			$type_segment_id = (int) mso_segment(4); 
			$type_segment_name = '';
		}
		
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'
				. t('Тип')
				. ':</strong> ';
		
		echo '<select class="admin_page_filtr">';
		
		$selected = (mso_segment(3) and mso_segment(3) != 'next') ? '' : ' selected';
		
		echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любой') . '</option>';
		
		foreach ($query->result_array() as $nav) 
		{
		
			$selected = ($type_segment_id != $nav['page_type_id']) ? '' : ' selected';
			
			echo '<option value="' . getinfo('site_admin_url'). 'page/type/' . $nav['page_type_id'] .'"' . $selected . '>' . $nav['page_type_name'] . '</option>';
			
			if ($selected) $type_segment_name = $nav['page_type_name'];
			
			/*
		
			if ($type_segment_id != $nav['page_type_id']) 
			{
				echo ' <a href="' . getinfo('site_admin_url') . 'page/type/' . $nav['page_type_id'] . '">' . $nav['page_type_name']. '</a> ';
			}
			else 
			{
				$type_segment_name = $nav['page_type_name'];
				
				echo ' <a class="current" href="' . getinfo('site_admin_url') . 'page/type/' . $nav['page_type_id'] . '">' . $nav['page_type_name'] . '</a> ';
			}
			*/
			
			
		}
		
		echo '</select>';
	}
	
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'
			. t('Статус')
			. ':</strong> ';
	
	$all_status = array('publish', 'draft', 'private');
	
	echo '<select class="admin_page_filtr">';
	
	$selected = (!mso_segment(4) and mso_segment(3) != 'status') ? '' : ' selected';
	
	echo '<option value="' . getinfo('site_admin_url') . 'page"' . $selected . '>' . t('Любой') . '</option>';
	
	foreach($all_status as $status)
	{
		$selected = (mso_segment(4) == $status) ? ' selected' : '';
			
		echo '<option value="' . getinfo('site_admin_url'). 'page/status/' . $status .'"' . $selected . '>' . t($status) . '</option>';
		
		/*
		if (mso_segment(4) == $status)
			echo '<a class="current" href="' . getinfo('site_admin_url') . 'page/status/' . $status . '">' . t($status) . '</a> ';
		else
			echo '<a href="' . getinfo('site_admin_url') . 'page/status/' . $status . '">' . t($status) . '</a> ';
		*/
			
	}
	
	echo '</select>';
	
	echo '</p>';	
	
	
	//  переход на указанный url
	echo '<script>
	$("select.admin_page_filtr").change(function(){
		window.location = $(this).val();
	});
	</script>';
		

	if (mso_segment(3) == 'category') 
	{
		if (mso_segment(4) != '') 
		{
			$par['cat_id'] = abs(intval(mso_segment(4)));
		}
	}
	elseif (mso_segment(3) == 'type') 
	{
		if (mso_segment(4) != '') 
		{
			$par['type'] = $type_segment_name;
		}
	}
	elseif (mso_segment(3) == 'status') 
	{
		if (in_array(mso_segment(4), $all_status)) 
		{
			$par['page_status'] = mso_segment(4);
		}
	}
	
	mso_remove_hook('content'); // удаляем все хуки по content
	
	$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
	
	$all_pages = array(); // сразу список всех страниц для формы удаления
	
	$this_url = getinfo('site_admin_url') . 'page_edit/';
	$view_url = getinfo('siteurl') . 'page/';
	$view_url_cat = getinfo('siteurl') . 'category/';
	$view_url_tag = getinfo('siteurl') . 'tag/';
		
	if ($pages) // есть страницы
	{ 	
		foreach ($pages as $page) // выводим в цикле
		{
			// pr($page);
			// $act = '<a href="' . $this_url . $page['page_id'] . '">Изменить</a>';
			
			$page['page_title'] = htmlspecialchars($page['page_title']);
			
			$qhint = strip_tags($page['page_content']);
			
			if (mb_strlen($qhint) > 250)
			{
				$qhint = mb_substr($qhint, 0, 250, 'UTF-8') . '...';
			}
			
			$qhint = htmlspecialchars(str_replace("\n", "", $qhint));
			
			
			if (!$page['page_title']) $page['page_title'] = 'no-title';
			
			$all_pages[$page['page_id']] = $page['page_id'] . ' - ' . $page['page_title']
				. ' - ' . $page['page_date_publish'] . ' - ' . $page['page_status'];
			
			$cats = '';
			$tags = '';
			
			foreach ($page['page_categories_detail'] as $key => $val)
			{
				$cats .= '<a href="' . $view_url_cat . $page['page_categories_detail'][$key]['category_slug'] . '">'
					. $page['page_categories_detail'][$key]['category_name'] . '</a>  ';
			}
			
			$cats = str_replace('  ', ', ', trim($cats));
			
			foreach ($page['page_tags'] as $val)
			{
				$tags .= '<a href="' . $view_url_tag . $val . '">' . $val . '</a>  ';
			}
			
			$tags = str_replace('  ', ', ', trim($tags));
			
			$title = '<a class="title" href="' . $this_url . $page['page_id'] . '">' . $page['page_title'] . '</a>'
					. ' [<a href="' . $view_url . $page['page_slug'] . '" target="_blank">' . t('Просмотр') . '</a>]';
			
			
			
			if ($cats) $title .= '<br>' . t('Рубрика:') . ' ' . $cats;
			if ($tags) $title .= '<br>' . t('Метки:') . ' ' . $tags;
			
			$title .= '<p class="admin_page_qhint"><small>' . $qhint . '</small></p>';
			
			// $date_p = '<span title="Дата и время сохранения записи">' . $page['page_date_publish'] . '</span>'; // это время публикации как установлено на сервере
			
			$date_p = '<span title="' . t('Дата отображения на блоге с учетом временной поправки') . '">' . mso_date_convert('Y-m-d H:i:s', $page['page_date_publish']) . '</span>';
			
			$CI->table->add_row($page['page_id'], $title, $date_p, 
					$page['page_type_name'], $page['page_status'], $page['users_nik']);
		}
	

		$pagination['type'] = '';
		$pagination['range'] = 10;
		mso_hook('pagination', $pagination);
	
	
		echo mso_load_jquery('jquery.tablesorter.js') . '
			<script>
			$(function() {
				$("table.tablesorter").tablesorter();
			});
			</script>';
	

		echo $CI->table->generate(); // вывод подготовленной таблицы
	

		// добавляем форму для удаления записи
		$all_pages = form_dropdown('f_page_delete', $all_pages, -1, '');
		

		$pagination['type'] = '';
		$pagination['range'] = 10;
		//echo '<br>';
		mso_hook('pagination', $pagination);

		
		echo '<form method="post">' . mso_form_session('f_session_id');
		echo '<h2 class="br">' . t('Удалить страницу') . '</h2><p>';
		echo $all_pages;
		echo ' <input type="submit" name="f_submit" value="' . t('Удалить') . '" onClick="if(confirm(\'' . t('Удалить страницу?') . '\')) {return true;} else {return false;}" ></p>';
		echo '</form>';

	}
	else
	{
		echo '<h2>' . t('Страниц не найдено') . '</h2>';
		
	}
	
?>