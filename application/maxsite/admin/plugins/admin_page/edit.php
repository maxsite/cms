<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1><?= t('Редактирование страницы') ?></h1>
<p class="ret-to-pages"><a href="<?= $MSO->config['site_admin_url'] . 'page' ?>"><?= t('Cписок записей') ?></a>

<?php
	
	$id = mso_segment(3); // номер страницы по сегменту url
	
	// проверим, чтобы это было число
	if (!is_numeric($id)) $id = false; // не число
		else $id = (int) $id;
	
	if ($id) // есть корректный сегмент
	{
		$CI = & get_instance();
		
		# проверим текущего юзера и его разрешение на правку чужих страниц
		# если admin_page_edit=1, то есть разрешено редактировать в принципе (уже проверили раньше!),
		# то смотрим admin_page_edit_other. Если стоит 1, то все разрешено
		# если false, значит смотрим автора страницы и если он не равен юзеру, рубим доступ
		
		if ( !mso_check_allow('admin_page_edit_other') )
		{
			$current_users_id = getinfo('session');
			$current_users_id = $current_users_id['users_id'];
			
			# получаем данные страницы
			$CI->db->select('page_id');
			$CI->db->from('page');
			$CI->db->where(array('page_id'=>$id, 'page_id_autor'=>$current_users_id));
			$query = $CI->db->get();
			if ($query->num_rows() == 0) // не автор
			{
				echo '<div class="error">' . t('Вам не разрешено редактировать чужие записи!') . '</div>';
				return;
			}
		}
	
		require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик
		require_once( getinfo('common_dir') . 'meta.php' ); // функции meta - для меток
	
		// этот код почти полностью повторяет код из new.php
		// разница только в том, что указан id
		
		if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_content')) )
		{
			mso_checkreferer();
			
			//pr($_POST);
			
			// прием данных вынесен в post-edit.php
			// он используется и для фонового сохранения
			require(getinfo('admin_plugins_dir') . 'admin_page/post-edit.php'); 
			
		}
		else 
		{
			echo ' | <a href="' . mso_get_permalink_page($id) . '">' . t('Посмотреть запись') . '</a> (<a target="_blank" href="' . mso_get_permalink_page($id) . '">' . t('в новом окне') . '</a>)</p>';
			
			// получаем данные записи
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
			else
			{
				echo '<div class="error">' . t('Ошибочная страница (нет такой страницы)') . '</div>';
				return;
			}
		
		}
		
		// получим все опции редактора
		$editor_options = mso_get_option('editor_options', 'admin', array());
		
		$f_header = htmlspecialchars($f_header);
		$f_tags = htmlspecialchars($f_tags);
		$f_all_tags = ''; // все метки

		if (function_exists('tagclouds_widget_custom')) 
		{
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
				'max_size' => '180',
				'sort' => isset($editor_options['tags_sort']) ? $editor_options['tags_sort'] : 0, 
				'block_start' => '<p id="f_all_tags_max_num">',
				'block_end' => ' <a title="' . t('Показать все метки') . '" href="#" onClick="shtags(1); return false;">&gt;&gt;&gt;</a></p>',
				'format' => '<span style="font-size: %SIZE%%"><a href="#" onClick="addTag(\'%TAG%\'); return false;">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>'
			));
			
			// все метки
			$f_all_tags .= tagclouds_widget_custom(array(
				'max_num' => 9999,
				'max_size' => '180',
				'sort' => isset($editor_options['tags_sort']) ? $editor_options['tags_sort'] : 0, 
				'block_start' => '<p id="f_all_tags_all" style="display: none;">',
				'block_end' => ' <a title="' . t('Показать только самые популярные метки') . '" href="#" onClick="shtags(2); return false;">&lt;&lt;&lt;</a></p>',
				'format' => '<span style="font-size: %SIZE%%"><a href="#" onClick="addTag(\'%TAG%\'); return false;">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>'
			));
	
		}
		
		$fses = mso_form_session('f_session_id'); // сессия

		// получаем все типы страниц
		$all_post_types = '';
		$query = $CI->db->get('page_type');
		
		$page_type_js_obj = '{'; // для скрытия метаполей в зависимости от типа записи
		
		foreach ($query->result_array() as $row)
		{
			if ($f_page_type == $row['page_type_id']) $che = 'checked="checked"';
				else $che = '';
			
			$page_type_desc = $row['page_type_desc'] ? ' <em>(' . t($row['page_type_desc']) . ')</em>' : '';
			
			$all_post_types .= '<p><label><input name="f_page_type[]" type="radio" ' . $che 
									. ' value="' . $row['page_type_id'] . '"> ' 
									. $row['page_type_name'] . $page_type_desc . '</label></p>';
			$page_type_js_obj .= $row['page_type_name'] . ':' . $row['page_type_id'] . ',';						
									
		}
		
		$page_type_js_obj .= '}';
		$page_type_js_obj = str_replace(',}', '}', $page_type_js_obj);

	
		
		// получаем все рубрики чекбоксы
		$all_cat = mso_cat_ul('<label><input name="f_cat[]" type="checkbox" %CHECKED% value="%ID%" title="id = %ID%"> %NAME%</label>', true, $f_cat, $f_cat);

		
		if ($f_comment_allow) $f_comment_allow = 'checked="checked"';
			else $f_comment_allow = '';
			
		if ($f_feed_allow) $f_feed_allow = 'checked="checked"';
			else $f_feed_allow = '';
		
		
		// не используется
		if ($f_ping_allow) $f_ping_allow = 'checked="checked"';
			else $f_ping_allow = '';			
			
		
		# получаем список юзеров
		if ( !mso_check_allow('edit_page_author') ) // если не разрешено менять автора
		{
			$CI->db->where('users_id', $f_user_id); // ставим только текущего автора
		}
		$CI->db->select('users_id, users_login, users_nik');
		$query = $CI->db->get('users');
		
		$all_users = array();
		
		// если есть данные, то выводим
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
				$all_users[$row['users_id']] = $row['users_login'] . ' (' . $row['users_nik'] . ')';
		}
		
		$CI->load->helper('form');
		$all_users = form_dropdown('f_user_id', $all_users, $f_user_id, ' style="width: 99%;" ');
		
		
		$f_status_draft = $f_status_private = $f_status_publish = '';
		if ($f_status == 'draft') $f_status_draft = 'checked';
		elseif ($f_status == 'private') $f_status_private = 'checked';
		else $f_status_publish = 'checked'; // ($f_status == 'publish') 
		
		$name_submit = 'f_submit[' . $id . ']';
		
		
		// дата публикации
		$f_date_change = ''; // сменить дату не нужно - будет время автоматом поставлено текущее
		
		$date_cur = strtotime($page_date_publish);
		
		$date_time = t('Сохранено:') . ' ' . $page_date_publish;
		
		$date_time .= '<br>' . t('На блоге как:') . ' ' . mso_date_convert('Y-m-d H:i:s', $page_date_publish);
		$date_time .= '<br>' . t('Тек. время:') . ' ' . date('Y-m-d H:i:s');
		
		$date_cur_y = date('Y', $date_cur);
		$date_cur_m = date('m', $date_cur);
		$date_cur_d = date('d', $date_cur);	
		$tyme_cur_h = date('H', $date_cur);
		$tyme_cur_m = date('i', $date_cur);
		$tyme_cur_s = date('s', $date_cur);
		
		$date_all_y = array();
		for ($i=2005; $i<2021; $i++) $date_all_y[$i] = $i;
		
		$date_all_m = array();
		for ($i=1; $i<13; $i++) $date_all_m[$i] = $i;
		
		$date_all_d = array();
		for ($i=1; $i<32; $i++) $date_all_d[$i] = $i;
		
		$date_y = form_dropdown('f_date_y', $date_all_y, $date_cur_y, ' style="margin-top: 5px; width: 60px;" ');
		$date_m = form_dropdown('f_date_m', $date_all_m, $date_cur_m, ' style="margin-top: 5px; width: 60px;" ');
		$date_d = form_dropdown('f_date_d', $date_all_d, $date_cur_d, ' style="margin-top: 5px; width: 60px;" ');
		
		$time_all_h = array();
		for ($i=0; $i<24; $i++) $time_all_h[$i] = $i;
		
		$time_all_m = array();
		for ($i=0; $i<60; $i++) $time_all_m[$i] = $i;

		$time_all_s = $time_all_m;
		
		$time_h = form_dropdown('f_time_h', $time_all_h, $tyme_cur_h, ' style="margin-top: 5px; width: 60px;" ');
		$time_m = form_dropdown('f_time_m', $time_all_m, $tyme_cur_m, ' style="margin-top: 5px; width: 60px;" ');
		$time_s = form_dropdown('f_time_s', $time_all_s, $tyme_cur_s, ' style="margin-top: 5px; width: 60px;" ');
		
		
		// получаем все страницы, для того чтобы отобразить их в паренте
		$all_pages = NR . '<select name="f_page_parent"  style="margin-top: 5px; width: 99%;" >' . NR;
		$all_pages .= NR . '<option value="0">' . t('Нет') . '</option>';
		
		// если отмечена опция отрображать блок
		if (!isset($editor_options['page_all_parent']) or (isset($editor_options['page_all_parent']) and $editor_options['page_all_parent']))
		{
			$CI->db->select('page_id, page_title');
			$CI->db->where('page_status', 'publish');
			$CI->db->where('page_id !=', $id);
			$CI->db->order_by('page_date_publish', 'desc');
			$query = $CI->db->get('page');
			if ($query->num_rows() > 0)
			{
				
				foreach ($query->result_array() as $row)
				{
					if ($row['page_id'] == $f_page_parent) $sel = ' selected="selected"';
						else $sel = '';
						$all_pages .= NR . '<option ' . $sel . 'value="' . $row['page_id'] . '">' . $row['page_id'] . ' - ' . htmlspecialchars($row['page_title']) . '</option>';
				}
			}
		}
		
		$all_pages .= NR . '</select>' . NR;
		
		
		# мета большие,вынесена в отдельный файл
		# из неё получается $all_meta = '<p>Нет</p>';
		require($MSO->config['admin_plugins_dir'] . 'admin_page/all_meta.php');
		
		$f_return = '';
	
		// быстрое сохранение только в режиме редактирования
		$f_bsave = ' <button id="bsave" type="button">' . t('Сохранить в фоне') . '</button><div class="bsave_result"></div>';
		
		# форма вынесена в отдельный файл, поскольку она одна и таже для new и edit
		# из неё получается $do и $posle
		require($MSO->config['admin_plugins_dir'] . 'admin_page/form.php');
	
		$f_content = htmlspecialchars($f_content);
		
		$ad_config = array(
					'action'=> '',
					'content' => $f_content,
					'do' 	=> $do,
					'posle' => $posle,
					);

		# отображаем редактор
		# есть ли хук на редактор: если да, то получаем эту функцию
		# если нет, то отображаем стандартный editor_jw
		if (mso_hook_present('editor_custom')) mso_hook('editor_custom', $ad_config);
			else editor_markitup($ad_config);
			
	////////////////////////////////////////////////////////////////////////////////

	
	}
	else
	{
		echo '<div class="error">' . t('Ошибочный запрос') . '</div>'; // id - ошибочный
	}

# end file