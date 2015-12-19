<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*

TODO
- добавить проверку даты. Принимать любой вариант и форматировать в Y-m-d H:i:s

*/

// добавляем новую запись из файла
// удалять файл после публикации???
function add_new_page($fn, $UP_DIR)
{
	global $MSO;
	
	$data = file_get_contents($fn);
	
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
			
			// дополнительные мета-данные
			// вначале формируем общий массив, после выгоняем его в ##METAFIELD##
			$page_meta_options = array();
			
			$page_meta_options['title'] = (isset($conf['META-TITLE'])) ? $conf['META-TITLE'] : '';
			
			$page_meta_options['description'] = (isset($conf['META-DESCRIPTION'])) ? $conf['META-DESCRIPTION'] : '';
			
			$page_meta_options['keywords'] = (isset($conf['META-KEYWORDS'])) ? $conf['META-KEYWORDS'] : '';
			
			$page_meta_options['image_for_page'] = (isset($conf['META-IMAGE_FOR_PAGE'])) ? $conf['META-IMAGE_FOR_PAGE'] : '';
			
			$page_meta_options['image_for_page_out'] = (isset($conf['META-IMAGE_FOR_PAGE_OUT'])) ? $conf['META-IMAGE_FOR_PAGE_OUT'] : '';
			
			$page_meta_options['page_template'] = (isset($conf['META-PAGE_TEMPLATE'])) ? $conf['META-PAGE_TEMPLATE'] : '';
			
			$page_meta_options['page_css_profiles'] = (isset($conf['META-PAGE_CSS_PROFILES'])) ? $conf['META-PAGE_CSS_PROFILES'] : '';
			
			$page_meta_options['info-top-custom'] = (isset($conf['META-INFO-TOP-CUSTOM'])) ? $conf['META-INFO-TOP-CUSTOM'] : '';
			
			$page_meta_options['parser_content'] = (isset($conf['META-PARSER_CONTENT'])) ? $conf['META-PARSER_CONTENT'] : 'Default';
			
			// прогоняем текст через парсер
			$parser = $page_meta_options['parser_content']; // парсер
			$parser_all = mso_hook('parser_register'); // все зарегистрированные парсеры
			
			// если парсеры не зарегистрированы, то ничего не делаем
			if (isset($parser_all[$parser]['content_post_edit']))
			{
				$func = $parser_all[$parser]['content_post_edit']; // функцию, которую нужно выполнить
				if (function_exists($func)) $text = $func($text); // прогоняем текст через парсер
			}
			
			$meta_options = '';
			
			foreach ($page_meta_options as $key=>$val)
			{
				$meta_options .= $key . '##VALUE##' . trim($val) . '##METAFIELD##';
			}
			
			
			// TYPE: blog — тип записи — делаем запрос к существующим типам
			// поскольку тип нужно будет преобразовать в его id
			$CI = & get_instance();
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
			
			// рубрики указываются CAT_ID или CAT_SLUG или CAT
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
							if (mb_strtolower($catX['category_name']) == $name) $cat[] = $catX['category_id'];
						}
					}
				}
			}
			
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
			
			// функции редактирования
			require_once( getinfo('common_dir') . 'functions-edit.php' ); 
			
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
			echo('Неверный фрормат файла (' . str_replace($UP_DIR, '', $fn) . ')');
		}
	}
	else
	{
		echo('Неверный фрормат файла (' . str_replace($UP_DIR, '', $fn) . ')');
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