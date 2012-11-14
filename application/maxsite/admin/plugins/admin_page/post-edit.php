<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

			$f_content = $post['f_content'];
			
			if ( mso_hook_present('content_replace_chr10_br') ) 
			{
				// если нужно задать свое начально форматирование, задайте хук content_replace_chr10_br
				$f_content = mso_hook('content_replace_chr10_br', $f_content);
			} 
			else
			{
				$f_content = trim($f_content);
				$f_content = str_replace(chr(10), "<br>", $f_content);
				$f_content = str_replace(chr(13), "", $f_content);
			}
			
			// pr($f_content, true);
			
			// глюк FireFox исправлем замену абсолютного пути src на абсолютный
			$f_content = str_replace('src="../../', 'src="' . $MSO->config['site_url'], $f_content);
			$f_content = str_replace('src="../', 'src="' . $MSO->config['site_url'], $f_content);
			
			// замены из-за мусора FireFox
			$f_content = str_replace('-moz-background-clip: -moz-initial;', '', $f_content);
			$f_content = str_replace('-moz-background-origin: -moz-initial;', '', $f_content);
			$f_content = str_replace('-moz-background-inline-policy: -moz-initial;', '', $f_content);
			
			$f_header = mso_text_to_html($post['f_header']);
			
			if ( isset($post['f_tags']) and $post['f_tags'] ) $f_tags = $post['f_tags'] ;
				else $f_tags = '';
			
			if ( isset($post['f_menu_order'])) $page_menu_order = (int) $post['f_menu_order'] ;
				else $page_menu_order = '';			
			
			if ( isset($post['f_slug']) and $post['f_slug'] ) $f_slug = $post['f_slug'] ;
				else $f_slug = mso_slug($f_header);
				
			if ( isset($post['f_password']) and $post['f_password']) $f_password = $post['f_password'] ;
				else $f_password = '';			
				
			if ( isset($post['f_cat']) ) $f_cat = $post['f_cat'] ;
				else $f_cat = array();
			
			// все мета
			$f_options = '';
			if ( isset($post['f_options']) )
			{
				foreach ($post['f_options'] as $key=>$val)
				{
					$f_options .= $key . '##VALUE##' . trim($val) . '##METAFIELD##';
				}
			}
			
			if ( isset($post['f_status']) ) $f_status = $post['f_status'][0];
				else $f_status = 'publish';	
				
			if ( isset($post['f_page_type']) ) $f_page_type = $post['f_page_type'][0];
				else $f_page_type = '1';
				
			if ( isset($post['f_page_parent']) and $post['f_page_parent'] ) $f_page_parent = (int) $post['f_page_parent'];
				else $f_page_parent = '0';
			
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
				$post['f_time_s'] > -1 and $post['f_time_s'] < 61)
			{
				$page_date_publish_y = (int) $post['f_date_y'];
				$page_date_publish_m = (int) $post['f_date_m'];
				$page_date_publish_d = (int) $post['f_date_d'];
				$page_date_publish_h = (int) $post['f_time_h'];
				$page_date_publish_n = (int) $post['f_time_m'];
				$page_date_publish_s = (int) $post['f_time_s'];
				
				$page_date_publish = date('Y-m-d H:i:s', mktime($page_date_publish_h, $page_date_publish_n, $page_date_publish_s,
										$page_date_publish_m, $page_date_publish_d, $page_date_publish_y) );
				
			}
			else
				$page_date_publish = false;
					
			// если автор указан, то нужно проверять есть разрешение на указание другого
			// если есть разрешение, то все нормуль
			// если нет, то автор остается текущим
			if (isset($post['f_user_id'])) $f_user_id = (int) $post['f_user_id'];
				else $f_user_id = $MSO->data['session']['users_id'];
			
			$f_comment_allow = isset($post['f_comment_allow']) ? '1' : '0';
			$f_ping_allow = isset($post['f_ping_allow']) ? '1' : '0';
			$f_feed_allow = isset($post['f_feed_allow']) ? '1' : '0';
			
		
			// получаем номер опции id из fo_edit_submit[]
			$f_id = mso_array_get_key($post['f_submit']);
			

			// подготавливаем данные
			$data = array(
				'user_login' => $MSO->data['session']['users_login'],
				'password' => $MSO->data['session']['users_password'],
				
				'page_id' => $f_id,
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
				'page_date_publish' => $page_date_publish,
				'page_menu_order' => $page_menu_order,

				);

				
			require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
			$result = mso_edit_page($data);
			
			// pr($result);
			
			if (isset($result['result']) and $result['result']) 
			{
				if (isset($result['result'][0])) 
				{
					$url = '<a href="' 
							. mso_get_permalink_page($result['result'][0])
							. '">' . t('Посмотреть запись') . '</a> (<a target="_blank" href="' 
							. mso_get_permalink_page($result['result'][0]) . '">' . t('в новом окне') . '</a>)';		

				}
				else $url = '';

				echo '<div class="update">' . t('Страница обновлена!') . ' ' . $url . '</div>'; 
				
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
				echo '<div class="error">' . t('Ошибка обновления') . '</div>';
			