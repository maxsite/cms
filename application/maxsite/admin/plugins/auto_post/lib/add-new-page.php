<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// добавляем новую запись из файла
// удалять файл после публикации???
function add_new_page($fn, $UP_DIR)
{
	global $MSO;
	
	$CI = & get_instance();
	
	$data = file_get_contents($fn);
	
	// автоматические замены по всему тексту
	
	// пробуем определить следующий автоинкремент — id записи
	$query  = $CI->db->query("SHOW TABLE STATUS FROM `" . $CI->db->database . "` LIKE '" . $CI->db->dbprefix('page') . "'");
	$result = $query->result_array();
		
	if ($result)
	{
		$next_id = $result[0]['Auto_increment'];
		
		$data = str_replace('[[PAGE_FILES]]', getinfo('uploads_url') . '_pages/' . $next_id . '/', $data);
	}
	
	// прочие замены
	$data = str_replace('[[SITE_URL]]', getinfo('siteurl'), $data);
	$data = str_replace('[[UPLOADS_URL]]', getinfo('uploads_url'), $data);
		
	if ($data and preg_match('!(.*?)\n(---)(.*)!is', $data, $conf))
	{
		// только если есть шапка и текст
		if (isset($conf[1]) and isset($conf[3]))
		{
			$top = trim($conf[1]);
			$text = trim($conf[3]);
			$comments = array(); // комментарии, если есть
			
			// в тексте может быть секция ---COMMENTS-START--- ---COMMENTS-END---
			if (preg_match('!(.*?)\n(---COMMENTS-START---)(.*)\n(---COMMENTS-END---)!is', $text, $t2))
			{
				if (isset($t2[4])) // да есть секция
				{
					$text = trim($t2[1]);
					$comments = trim($t2[3]);
					
					// комментарии в свою очередь разбиваются ---
					$comments = array_map('trim', explode('---', $comments));
				}
			}
			
			$conf = _parse_key_val($top);
			
			// _log($conf, 'conf');
			
			// обязательные поля только TITLE
			if (!isset($conf['TITLE'])) return;

			// формируем данные для постинга
			
			// функции редактирования
			require_once( getinfo('common_dir') . 'functions-edit.php' ); 
			
			// дополнительные мета-данные
			// вначале формируем общий массив, после выгоняем его в ##METAFIELD##

			$page_meta_options = _find_all_meta($conf);
			
			
			// прогоняем текст через парсер
			// если он не указан, то ставим дефолтный
			if (!isset($page_meta_options['parser_content'])) $page_meta_options['parser_content'] = 'Default';
			
			$parser = $page_meta_options['parser_content']; // парсер
			$parser_all = mso_hook('parser_register', array()); // все зарегистрированные парсеры
			
			// если парсеры не зарегистрированы, то ничего не делаем
			if (isset($parser_all[$parser]['content_post_edit']))
			{
				$func = $parser_all[$parser]['content_post_edit']; // функцию, которую нужно выполнить
				if (function_exists($func)) $text = $func($text); // прогоняем текст через парсер
			}
			
			// _log($conf, 'conf');
			// _log($page_meta_options, '$page_meta_options');
			
			$meta_options = '';
			
			foreach ($page_meta_options as $key => $val)
			{
				$meta_options .= $key . '##VALUE##' . trim($val) . '##METAFIELD##';
			}
			
			
			// TYPE: blog — тип записи — делаем запрос к существующим типам
			// поскольку тип нужно будет преобразовать в его id
			
			$query = $CI->db->get('page_type');
			$res = $query->result_array();
			
			$page_type = array();
			
			foreach ($res as $key => $row)
			{
				$page_type[$row['page_type_name']] = $row['page_type_id'];
			}
			
			$type = (isset($conf['TYPE'])) ? $conf['TYPE'] : 'blog';
			
			if (isset($page_type[$type])) 
				$page_type_id = $page_type[$type];
			else
				$page_type_id = $page_type['blog'];
			
			
			// получаем все рубрики, смотрим верно ли они указаны
			// если нет, то рубрику не ставим
			// на выходе массив id реальных рубрик 
			$CI->db->select('category_id, category_name, category_slug');
			$CI->db->where('category_type', 'page');
			$query = $CI->db->get('category');
			$all_cats = $query->result_array();
			
			$cat = array();
			
			// рубрики указываются: CAT_ID или CAT_SLUG или CAT или CAT+
			if (isset($conf['CAT_ID']))
			{
				if ($c1 = mso_explode($conf['CAT_ID'])) // указанные рубрики
				{
					// перебираем и ищем их в $all_cats
					// если есть добавляем номера в общий $cat
					foreach ($c1 as $id) 
					{
						foreach ($all_cats as $catX)
						{
							if ($catX['category_id'] == $id) $cat[] = $id;
						}
					}
				}
			}
			elseif (isset($conf['CAT_SLUG']))
			{
				if ($c1 = mso_explode(strtolower($conf['CAT_SLUG']), false))
				{
					foreach ($c1 as $slug) 
					{
						foreach ($all_cats as $catX)
						{
							if ($catX['category_slug'] == $slug) $cat[] = $catX['category_id'];
						}
					}
				}
			}
			elseif (isset($conf['CAT']))
			{
				// указанные рубрики разделются /
				$c1 = explode('/', trim(mb_strtolower($conf['CAT'])));
				$c1 = array_map('trim', $c1);
				$c1 = array_map('trim', $c1);
				$c1 = array_unique($c1);
				
				if ($c1)
				{
					foreach ($c1 as $name) 
					{
						foreach ($all_cats as $catX)
						{
							if (mb_strtolower($catX['category_name']) == $name) 
							{
								$cat[] = $catX['category_id'];
							}
						}
					}
				}
			}
			elseif (isset($conf['CAT+']))
			{
				// это работает также как и CAT, но если рубрики нет, создает её с указанным именем
				// в $c1 — нормализованные рубрики, в $c2 — исходные с учетом регистра (используется при добавлении)
				$c1 = explode('/', trim($conf['CAT+']));
				$c1 = array_map('trim', $c1);
				$c1 = array_map('trim', $c1);
				$c1 = $c2 = array_unique($c1);
				$c1 = array_map('mb_strtolower', $c1);
				
				if ($c1)
				{
					$cat_add = array(); // массив, где храним новые рубрики
					
					foreach ($c1 as $key => $name) 
					{
						$f_present = false; // флаг найденности рубрики
						
						foreach ($all_cats as $catX)
						{
							if (mb_strtolower($catX['category_name']) == $name)
							{
								$cat[] = $catX['category_id'];
								$f_present = true;
							}
						}
						
						if (!$f_present) // рубрики нет, нужно её добавить
						{
							if (!in_array($c2[$key], $cat_add)) $cat_add[] = $c2[$key];
						}
					}
					
					// pr($cat_add);
					
					// добавляем новые рубрики
					if ($cat_add)
					{
						foreach($cat_add as $c_name)
						{
							$res = mso_new_category(array('category_name' => $c_name));
							
							if ($res['result']) // успешно добавлено
							{
								// заносим id рубрик к записи
								$cat[] = $res['upd_data']['category_id'];
							}
						}
					}
				}
			}

			// _pr('');
			// _log($cat, 'cat');
			// _log($all_cats, 'all_cats');
			
			$data = array(
				'page_title' => $conf['TITLE'],
				'page_content' => $text,
				'page_type_id' => $page_type_id,
				'page_meta_options' => $meta_options,
				'page_date_publish' => (isset($conf['DATE'])) ? $conf['DATE'] : date('Y-m-d H:i:s'),
			);
			
			if (isset($conf['STATUS'])) $data['page_status'] = $conf['STATUS'];
			
			if (isset($conf['MENU_ORDER'])) $data['page_menu_order'] = (int) $conf['MENU_ORDER'];
			
			if (isset($conf['COMMENT_ALLOW'])) $data['page_comment_allow'] = $conf['COMMENT_ALLOW'];
			
			if (isset($conf['PASSWORD'])) $data['page_password'] = $conf['PASSWORD'];
			
			if (isset($conf['FEED_ALLOW'])) $data['page_feed_allow'] = (int) $conf['FEED_ALLOW'];
			
			if (isset($conf['ID_AUTHOR'])) $data['page_id_autor'] = (int) $conf['ID_AUTHOR'];
			
			if (isset($conf['SLUG'])) $data['page_slug'] = $conf['SLUG'];
			
			if (isset($conf['TAG'])) $data['page_tags'] = $conf['TAG'];
			
			if ($cat) $data['page_id_cat'] = implode(',', $cat);
			
			// _log($data, 'mso_new_page');
			
			$result = mso_new_page($data);
			
			// _log();
			// _log($result, 'result');
			
			$page_id = 0;

			if (isset($result['result'][0]))
			{
				$page_id = $result['result'][0];
				
				echo($result['description'] . ' (' . str_replace($UP_DIR, '', $fn) . '): ID = ' . $page_id);
			}
			else
			{
				echo($result['description'] . ' (' . str_replace($UP_DIR, '', $fn) . ')');
			}
			
			// добавляем комментарии
			if ($page_id and $comments)
			{
				foreach($comments as $comment)
				{
					$com = _parse_key_val($comment);
					
					if (isset($com['comment_content']))
					{
						$comment_content = trim(str_replace("__NR__", "\n", $com['comment_content']));
						
						$com_data = array(
							'comments_page_id' => $page_id,
							'comments_content' => $comment_content,
							'comments_date' => (isset($com['comment_date'])) ? $com['comment_date'] : date('Y-m-d H:i:s'),
							'comments_author_ip' => (isset($com['comment_author_IP'])) ? $com['comment_author_IP'] : '1.1.1.1',
							'comments_approved' => 1,
							'comments_author_name' => (isset($com['comment_author'])) ? $com['comment_author'] : 'Аноним',
						);

						$CI->db->insert('comments', $com_data);
					}
				}
			}
			
		}
		else
		{
			echo(t('Неверный формат файла') . ' (' . str_replace($UP_DIR, '', $fn) . ')');
		}
	}
	else
	{
		echo(t('Неверный формат файла') . ' (' . str_replace($UP_DIR, '', $fn) . ')');
	}
}

// разбивает текст на элементы массив
// comment_author: Вася
// comment_author_email: mail@mail.com
// comment_author_IP: 8.8.8.8
// результат: 
// array (
// 		comment_author => Вася
// 		comment_author_email => mail@mail.com
// 		comment_author_IP => 8.8.8.8
//  )
function _parse_key_val($s)
{
	$a = array();
	
	// построчно
	$a1 = explode("\n", $s);
	
	foreach($a1 as $a2)
	{
		$pos = strpos($a2, ":");
		
		if ($pos !== false)
		{
			$a[trim(substr($a2, 0, $pos))] = trim(substr($a2, $pos + 1));
		}
	}
	
	return $a;
}

// поиск всех META-ключ: значение 
// на выходе готовый массив
function _find_all_meta($a)
{
	$o = array();
	
	foreach($a as $k => $v)
	{
		if (strpos($k, 'META-') === 0)
		{
			$o[trim(substr($k, 5))] = trim(str_replace("__NR__", "\n", $v));
		}
	}
	
	return $o;
}

// просто для лога в файл
// Для сброса _log() или _log(0)
function _log($var = 0, $name = 'LOG', $f = 'log-add-page.txt')
{
	if ($var === 0)
	{
		file_put_contents(FCPATH . $f,  "");
		return;
	}
		
	if ( !is_scalar($var) ) $var = print_r($var, true);
	
	file_put_contents(FCPATH . $f, "\n================= " . $name . " =================\n" . $var . "\n================= /" . $name . " =================\n", FILE_APPEND);
}

# end of file