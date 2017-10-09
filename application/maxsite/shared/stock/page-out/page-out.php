<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 * Шаблонизатор для вывода записей
*/


class Page_out 
{
	protected $formats = array(); // массив форматов функций
	protected $def_formats = array(); // массив форматов дефолтный
	
	var $page = array(); // массив записи
	var $echo = true; // выводить результат по echo
	
	var $num = 0; // номер текущей записи в цикле
	var $max = 1; // всего записей в цикле
	var $last = false; // признак, что это последняя запись
	
	var $count_cells = 2; // кол-во ячеек в одной строке для box_grid()
	var $cur_cells = 1; // текущая ячейка
	var $close_box_grid = false; // признак, что div-строка box_grid не закрыта
	
	var $thumb = ''; // миниатюра для [thumb]
	
	function __construct()
	{
		$this->reset_format();
	}
	
	// сброс форматов аргументов функций до дефолтного
	function reset_format()
	{
		// аргументы совпадают с mso_page_...
		// используются только те что нужно
		$this->def_formats = array(
			
			'title' => array // mso_page_title
				(
					'<h1>',
					'</h1>',
					true, // линк?
				),

			'date' => array
				(
					'Y-m-d H:i:s',
					'',
					''
				),

			'cat' => array
				(
					', ',
					'',
					''
				),

			'tag' => array
				(
					', ',
					'',
					'',
					''
				),

			'feed' => array
				(
					'Подписаться',
					'',
					''
				),

			'comments' => array
				(
					'Обсудить',
					'Посмотреть комментарии',
					'',
					''
				),
				
			'comments_count' => array
				(
					'',
					''
				),
				
			'autor' => array
				(
					'',
					'',
				),
			
			'author' => array // дубль autor
				(
					'',
					'',
				),
				
			'edit' => array
				(
					'Редактировать',
					'',
					''
				),

			'read' => array
				(
					'Читать дальше',
					'',
					''
				),
				
			'view_count' => array
				(
					'',
					''
				),
				
			'meta_description' => array
				(
					'<div class="meta_description">',
					'</div>'
				),	
				
			'meta_title' => array
				(
					'<div class="meta_title">',
					'</div>'
				),
		);
		
		$this->formats = $this->def_formats;
	}
	
	// принимаем массив записи
	function load($page = array())
	{
		$this->page = $page;
		$this->thumb = '';
		
		$this->num++; // счетчик увеличим
		$this->last = ($this->num >= $this->max) ; // ставим признак true, если это последняя запись
	}
	
	// сбросить счетчики
	function reset_counter($max = 1)
	{
		$this->max = $max; // всего записей
		$this->num = 0; // счетчик
	}
	
	// возвращает значение указанного ключа массива $page
	function val($key)
	{
		if (isset($this->page[$key]))
			return $this->page[$key];
		else 
			return '';
	}
	
	// вспомогательная функция для вывода результатов
	protected function out($out)
	{
		if ($this->echo)
		{
			echo $out;
			return $this; // для цепочки вызовов
		}
		
		return $out;
	}
	
	// задание формата вывода
	// вывод по заданному формату осуществляется в $this->line()
	function format()
	{
		$numargs = func_num_args(); // кол-во аргументов переданных в функцию

		if ($numargs === 0) 
		{
			return; // нет аргументов, выходим
		}

		$args = func_get_args(); // массив всех полученных аргументов

		// заносим эти данные в свой массив форматов
		// первый аргумент всегда ключ функции - они предопределены как mso_page_...
		// параметры определяются в каждом конкретном случае
		$this->formats[$args[0]] = array_slice($args, 1);
		
		// сливаем с дефолтным, если есть такой же ключ
		if (isset($this->def_formats[$args[0]]))
		{
			$this->formats[$args[0]] = $this->formats[$args[0]] + $this->def_formats[$args[0]];
		}
	}
	
	// получение из массива formats массива ключа и проверка в нем указанного по номеру аргумента
	// номер аргумента функции начинается с 1
	function get_formats_args($key, $numarg)
	{
		if (isset($this->formats[$key][$numarg-1]))
		{
			// в форматировании могут встречаться специальные замены
			$f = $this->formats[$key][$numarg-1];
			
			// пока указываем некоторые, потом нужно будет подумать как сделать замены по всем ключам val
			$f = str_replace('[page_count_comments]', $this->val('page_count_comments'), $f);
			
			$f = str_replace('[page_date_publish]', $this->val('page_date_publish'), $f);
			
			// [page_date_publish_iso] формирует дату согласно ISO8601
			$dp = mso_date_convert(DATE_ISO8601, $this->val('page_date_publish'), false, false, false);
			$f = str_replace('[page_date_publish_iso]', $dp, $f);
			
			if ($this->val('page_last_modified'))
			{
				$f = str_replace('[page_last_modified]', $this->val('page_last_modified'), $f);
				$dp = mso_date_convert(DATE_ISO8601, $this->val('page_last_modified'), false, false, false);
				$f = str_replace('[page_last_modified_iso]', $dp, $f);
			}
			
			return $f;
		}
		else
		{
			return ''; // нет ключа
		}
	}
	
	// вывод данных по указанному в $out формату
	// $echo позволяет принудительно задать выдачу результата: true - по echo, false - return, 0 - как в $this->echo
	function line($out = '', $do = '', $posle = '', $echo = 0)
	{
		if (!$out) return;
		
		$title = '';
		$autor = '';
		$author = ''; // синоним autor
		$comments = '';
		$comments_count = ''; // только колво комментариев числом
		$cat = '';
		$cat_cat_id = '';
		$tag = '';
		$edit = '';
		$date = '';
		$date_last_modified = '';
		$read = '';
		$feed = '';
		$view_count = '';
		$meta_description = '';
		$meta_title = '';
		$page_url = '';
		
		$content = '';
		$content_words = '';
		$content_chars = '';
		
		// title
		if (strpos($out, '[title]') !== false)
		{
			$title = mso_page_title(
				$this->val('page_slug'), // данные из $page
				$this->val('page_title'), // данные из $page
				$this->get_formats_args('title', 1), // $do = '<h1>', 
				$this->get_formats_args('title', 2), // $posle = '</h1>',
				$this->get_formats_args('title', 3), // $link = true, 
				false);
		}
		
		// адрес страницы
		if (strpos($out, '[page_url]') !== false)
		{
			$page_url = $this->page_url(false);
		}
		
		// mso_page_author_link($users_nik = '', $page_id_autor = '', $do = '', $posle = '', $echo = true, $type = 'author', $link = true
		if (strpos($out, '[autor]') !== false)
		{
			$autor = mso_page_author_link(
				$this->val('users_nik'), // данные из $page
				$this->val('page_id_autor'), // данные из $page
				$this->get_formats_args('autor', 1), // $do = '', 
				$this->get_formats_args('autor', 2), // $posle = '',
				false);
		}
		
		if (strpos($out, '[author]') !== false)
		{
			$author = mso_page_author_link(
				$this->val('users_nik'), // данные из $page
				$this->val('page_id_autor'), // данные из $page
				$this->get_formats_args('author', 1), // $do = '', 
				$this->get_formats_args('author', 2), // $posle = '',
				false);
		}
		
		
		// mso_page_comments_link($page_comment_allow = true, $page_slug = '', $title = 'Обсудить', $do = '', $posle = '', $echo = true, $type = 'page'
		if (strpos($out, '[comments]') !== false)
		{
			$comments = mso_page_comments_link(
				array(
				'page_comment_allow' => $this->val('page_comment_allow'), // разрешены комментарии?
				'page_slug' => $this->val('page_slug'), // короткая ссылка страницы
				
				// титул, если есть ссылка
				'title' => $this->get_formats_args('comments', 1) . ' ('. $this->val('page_count_comments') . ')', 
				
				// титул если комменты запрещены, но они есть
				'title_no_link' => $this->get_formats_args('comments', 2), 
				
				// титул если еще нет комментариев
				'title_no_comments' => $this->get_formats_args('comments', 1), 
				
				'do' => $this->get_formats_args('comments', 3), // текст ДО
				'posle' => $this->get_formats_args('comments', 4), // текст ПОСЛЕ
				'echo' => false, // выводить?
				'page_count_comments' => $this->val('page_count_comments') // колво комментов
				)
			);
		}
		
		// только колво комментариев
		if (strpos($out, '[comments_count]') !== false)
		{
			$comments_count = $this->get_formats_args('comments_count', 1) . $this->val('page_count_comments') . $this->get_formats_args('comments_count', 2);
		}
		
		// mso_page_cat_link($cat = array(), $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'category', $link = true
		if (strpos($out, '[cat]') !== false)
		{
			$cat = mso_page_cat_link(
				$this->val('page_categories'), // данные из $page
				$this->get_formats_args('cat', 1), // $sep 
				$this->get_formats_args('cat', 2), // $do
				$this->get_formats_args('cat', 3), // $posle
				false);
		}
		
		
		// [cat/cat_id] — тоже, что и [cat], только отсекаются рубрики не указанные в cat_id
		// должен быть указан cat_id. Если нет, то отдаём как [cat] 
		if (strpos($out, '[cat/cat_id]') !== false)
		{
			$c = $this->val('page_categories'); // данные из $page
			
			if (isset($this->page['UNIT']['cat_id']))
				$c = array_intersect(mso_explode($this->page['UNIT']['cat_id']), $c);
				
			$cat_cat_id = mso_page_cat_link(
				$c, 
				$this->get_formats_args('cat', 1), // $sep 
				$this->get_formats_args('cat', 2), // $do
				$this->get_formats_args('cat', 3), // $posle
				false);
		}
		
		
		// mso_page_tag_link($tags = array(), $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'tag', $link = true, $class = ''
		if (strpos($out, '[tag]') !== false)
		{
			$tag = mso_page_tag_link(
				$this->val('page_tags'), // данные из $page
				$this->get_formats_args('tag', 1), // $sep 
				$this->get_formats_args('tag', 2), // $do
				$this->get_formats_args('tag', 3), // $posle
				false, // $echo
				'tag', // $type
				true,  // $link
				$this->get_formats_args('tag', 4)  // $class
				);
		}
		
		// edit
		// mso_page_edit_link($id = 0, $title = 'Редактировать', $do = '', $posle = '', $echo = true
		if (strpos($out, '[edit]') !== false)
		{
			$edit = mso_page_edit_link(
				$this->val('page_id'), // данные из $page
				$this->get_formats_args('edit', 1), // $title 
				$this->get_formats_args('edit', 2), // $do
				$this->get_formats_args('edit', 3), // $posle
				false);
		}
		
		
		// date
		//mso_page_date($date = 0, $format = 'Y-m-d H:i:s', $do = '', $posle = '', $echo = true
		if (strpos($out, '[date]') !== false)
		{
			$date = mso_page_date(
				$this->val('page_date_publish'), // данные из $page
					array('format' => tf($this->get_formats_args('date', 1)), // 'd/m/Y H:i:s'
							'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
							'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
				$this->get_formats_args('date', 2), // $do
				$this->get_formats_args('date', 3), // $posle
				false);
		}
		
		if (strpos($out, '[date_last_modified]') !== false)
		{
			if ($this->val('page_last_modified'))
			{
				$date_last_modified = mso_page_date(
					$this->val('page_last_modified'), // данные из $page
						array('format' => tf($this->get_formats_args('date_last_modified', 1)), // 'd/m/Y H:i:s'
								'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
								'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
					$this->get_formats_args('date_last_modified', 2), // $do
					$this->get_formats_args('date_last_modified', 3), // $posle
					false);
			}
		}
		
		// read
		if (strpos($out, '[read]') !== false)
		{
			$read = 
				  $this->get_formats_args('read', 2) // $do
				. $this->page_url(true)
				. $this->get_formats_args('read', 1) // 'читать далее'
				. '</a>'
				. $this->get_formats_args('read', 3); // $posle
				
				//pr($this->formats);
				// pr($read,1);
		}							
		
		// feed
		// mso_page_feed($page_slug = '', $page_title = 'Подписаться', $do = '<p>', $posle = '</p>', $link = true, $echo = true, $type = 'page'
		if (strpos($out, '[feed]') !== false)
		{
			// подписку по rss ставим только если есть разрешение в page_comment_allow
			if ($this->val('page_comment_allow'))
				$feed = mso_page_feed(
					$this->val('page_slug'), // данные из $page
					$this->get_formats_args('feed', 1), // 'Подписаться'
					$this->get_formats_args('feed', 2), // $do
					$this->get_formats_args('feed', 3), // $posle
					true,
					false);
		}		
		
		// view_count
		// mso_page_view_count($page_view_count = 0, $do = '<span>Прочтений:</span> ', $posle = '', $echo = true)
		if (strpos($out, '[view_count]') !== false)
		{
				$view_count = mso_page_view_count(
					$this->val('page_view_count'), // данные из $page
					$this->get_formats_args('view_count', 1), // $do Прочтений
					$this->get_formats_args('view_count', 2), // $posle
					false);
		}
		
		// мета description, если есть
		if (strpos($out, '[meta_description]') !== false)
		{
			if ($meta_description = $this->meta_val('description'))
			{
				$meta_description = $this->get_formats_args('meta_description', 1) // $do
									. $meta_description
									. $this->get_formats_args('meta_description', 2); // $posle
			}
		}
		
		// мета title, если есть
		if (strpos($out, '[meta_title]') !== false)
		{
			if ($meta_title = $this->meta_val('meta_title'))
			{
				$meta_title = $this->get_formats_args('meta_title', 1) // $do
									. $meta_description
									. $this->get_formats_args('meta_title', 2); // $posle
			}
		}
		
		// [meta@price]
		if (strpos($out, '[meta@') !== false)
		{
			//pr($out);
			$out = preg_replace_callback('!(\[meta@)(.*?)(\])!is', array('self', '_line_meta_set'), $out);
			//pr($out);
		}
		
		// [val@price] — произвольный val из page
		if (strpos($out, '[val@') !== false)
		{
			$out = preg_replace_callback('!(\[val@)(.*?)(\])!is', array('self', '_line_val_set'), $out);
		}
		
		// [var@cat] — произвольное значение поля из UNIT
		if (strpos($out, '[var@') !== false)
		{
			$out = preg_replace_callback('!(\[var@)(.*?)(\])!is', array('self', '_line_var_set'), $out);
		}
		
		if (strpos($out, '[content]') !== false)
		{
			$content = $this->get_content();
		}
		
		if (strpos($out, '[content_chars@') !== false)
		{
			$out = preg_replace_callback('!(\[content_chars@)(.*?)(\])!is', array('self', '_line_content_chars'), $out);
		}
		
		if (strpos($out, '[content_words@') !== false)
		{
			$out = preg_replace_callback('!(\[content_words@)(.*?)(\])!is', array('self', '_line_content_words'), $out);
		}
		
		$out = str_replace('[title]', $title, $out);
		$out = str_replace('[page_url]', $page_url, $out);
		$out = str_replace('[autor]', $autor, $out);
		$out = str_replace('[author]', $author, $out);
		$out = str_replace('[comments]', $comments, $out);
		$out = str_replace('[comments_count]', $comments_count, $out);
		$out = str_replace('[cat]', $cat, $out);
		$out = str_replace('[tag]', $tag, $out);
		$out = str_replace('[edit]', $edit, $out);
		$out = str_replace('[date]', $date, $out);
		$out = str_replace('[date_last_modified]', $date_last_modified, $out);
		$out = str_replace('[read]', $read, $out);
		$out = str_replace('[feed]', $feed, $out);
		$out = str_replace('[view_count]', $view_count, $out);
		$out = str_replace('[meta_description]', $meta_description, $out);
		$out = str_replace('[meta_title]', $meta_title, $out);
		$out = str_replace('[cat/cat_id]', $cat_cat_id, $out);
		
		$out = str_replace('[thumb]', $this->thumb, $out);
		
		$out = str_replace('[content]', $content, $out);
		
		if ($out) 
		{
			if ($echo === 0) return $this->out($do . $out . $posle);
			elseif ($echo === true) 
			{
				echo $do . $out . $posle;
				return $this;
			}
			elseif ($echo === false) return $do . $out . $posle;
		}
	}
	
	// колбак для поиска [meta@мета]
	protected function _line_meta_set($matches)
	{
		$m = $matches[2];
		$m = $this->meta_val($m);
		return $m;
	}
	
	// колбак для поиска [val@поле]
	protected function _line_val_set($matches)
	{
		$m = $matches[2];
		$m = $this->val($m);
		return $m;
	}

	// колбак для поиска [var@UNIT]
	protected function _line_var_set($matches)
	{
		$m = 'var@' . $matches[2];
		
		if (isset($this->page['UNIT'][$m]))
			return $this->page['UNIT'][$m];
		else 
			return '';
	}
	
	// колбак для поиска [content_chars@КОЛВО]
	protected function _line_content_chars($matches)
	{
		$m = $matches[2];
		
		$content = $this->content_do_cut($this->get_content());
		
		$m = mb_substr(strip_tags($content), 0, $m, 'UTF-8');
		return $m;
	}
	
	// колбак для поиска [content_chars@КОЛВО]
	protected function _line_content_words($matches)
	{
		$m = $matches[2];
		
		$content = $this->content_do_cut($this->get_content());
		
		$m = mso_str_word(strip_tags($content), $m);
		return $m;
	}			
	
	// только получаем контент через mso_page_content()
	function get_content()
	{
		ob_start();
		mso_page_content($this->val('page_content'));
		$page_content = ob_get_contents();
		ob_end_clean();
		
		return $page_content;
	}
	
	// вывод контента
	function content($do = '<div class="mso-page-content">', $posle = '</div>')
	{
		return $this->out(NR . $do . $this->get_content() . $posle);
	}
	
	// обрезка контента по кол-ву слов
	function content_words($max_words = 15, $cut = '', $do = '<div class="mso-page-content">', $posle = '</div>')
	{
		if ($cut) $cut = ' ' . $this->page_url(true) . $cut . '</a>';
		
		$content = $this->content_do_cut($this->get_content());
		
		return $this->out(NR . $do . mso_str_word(strip_tags($content), $max_words) . $cut . $posle);
	}
	
	// обрезка контента по кол-ву символов
	function content_chars($max_chars = 100, $cut = '', $do = '<div class="mso-page-content">', $posle = '</div>')
	{
		if ($cut) $cut = ' ' . $this->page_url(true) . $cut . '</a>';
		
		$content = $this->content_do_cut($this->get_content());
		
		return $this->out(NR . $do . mb_substr(strip_tags($content), 0, $max_chars, 'UTF-8') . $cut . $posle);
	}
	
	// обрезка контента/текста до [cut]  
	function content_do_cut($content)
	{
		$content = preg_replace('|\[cut\]\s*<br|', '[cut]<br', $content);
		$content = preg_replace('|\[cut\](\&nbsp;)*<br|', '[cut]<br', $content);
		$content = preg_replace('|\[cut\](\&nbsp;)*(\s)*<br|', '[cut]<br', $content);
		
		if (preg_match('/\[cut(.*?)?\]/', $content, $matches) )
		{
			$content = explode($matches[0], $content, 2);
		}
		else
		{
			$content = array($content);
		}
		
		return $content[0];
	}
	
	// вывод мета - только значение мета по return
	function meta_val($meta = '', $default = '', $razd = ', ')
	{
		// mso_page_meta_value($meta = '', $page_meta = array(), $default = '', $razd = ', '
		
		return mso_page_meta_value($meta, $this->val('page_meta'), $default, $razd);
	}
	
	// вывод мета
	function meta($meta = '', $do = '', $posle = '', $razd = ', ')
	{
		// mso_page_meta($meta = '', $page_meta = array(), $do = '', $posle = '', $razd = ', ', $echo = true
		
		return $this->out(mso_page_meta($meta, $this->val('page_meta'), $do, $posle, $razd, false));
	}
	
	// вывод произвольного html
	function html($text = '')
	{
		return $this->out($text);
	}
	
	// полный аналог div
	// чтобы не так резало глаза div
	function tag($text = '', $class = '', $tag = 'div', $style = '')
	{
		if ($class) $class = ' class="' . $class . '"';
		if ($style) $style = ' style="' . $style . '"';
		
		return $this->out('<' . $tag . $class . $style . '>' . $text . '</' . $tag . '>');
	}
	
	// вывод div с указанным css-классом
	// или можно указать свой
	function div($text = '', $class = '', $tag = 'div', $style = '')
	{
		if ($class) $class = ' class="' . $class . '"';
		if ($style) $style = ' style="' . $style . '"';
		
		return $this->out('<' . $tag . $class . $style . '>' . $text . '</' . $tag . '>');
	}
	
	// вывод открывающих div с указанным css-классом
	// сколько аргументов, столько и вложенных div 
	// название аргумента - это css-класс 
	function div_start()
	{
		$numargs = func_num_args();
		
		if ($numargs === 0) 
		{
			return $this->out(NR . '<div>'); // нет аргументов, одиночный div
		}

		$args = func_get_args(); // массив всех полученных аргументов

		$out = '';
		
		foreach ($args as $class)
		{
			// если аргумент начинается с <, значит это какой-то тэг
			// его выводим как есть
			
			if ( 0 === strpos($class, '<'))
				$out .= NR . $class;
			else
			{
				if ($class) 
					$out .= NR . '<div class="' . $class . '">';
				else
					$out .= NR . '<div>';
			}
		}
		
		return $this->out($out);
	}
	
	// аналогичная div_start(), только закрывающая
	function div_end($class = '')
	{
		$numargs = func_num_args();
		
		if ($numargs === 0) 
		{
			return $this->out('</div>' . NR); // нет аргументов, одиночный div
		}

		$args = func_get_args(); // массив всех полученных аргументов
		
		// классы выводятся в обратном порядке
		$args = array_reverse($args);
		
		$out = '';
		$out_comment = '';
		
		foreach ($args as $class)
		{
			if (0 === strpos($class, '<'))
				$out .= $class;
			else
				$out .= '</div>';
			
			if ($class)
			{
				$out_comment .= ' /.' . $class;
			}
			else
			{
				$out_comment .= ' /div';
			}
		}
		
		return $this->out(NR . $out . '<!--' . $out_comment . '-->' . NR . NR);
	}
	
	// вывод div.clearfix
	// или можно указать 
	function clearfix($class = 'mso-clearfix')
	{
		if ($class) $class = ' class="' . $class . '"';
		
		return $this->out('<div' . $class . '></div>');
	}
	
	// функция равна line, только всегда отдает по return
	function line_r($out = '', $do = '', $posle = '')
	{
		return $this->line($out, $do, $posle, false);
	}
	
	// вывод произвольного блока
	// только если $out содержит текст
	function block($out = '', $do = '', $posle = '')
	{
		if ($out) return $this->out($do . $out . $posle);
	}

	// формируем <a>
	// возвращаем только по return
	function link($url = '#', $name = '', $title = '', $class = '')
	{
		if ($class) $class = ' class="' . $class . '"';
		if ($title) $title = ' title="' . htmlspecialchars($title) . '"';
		
		return '<a href="' . $url . '"' . $class . $title . '>' . $name . '</a>';
	}

	// формируем <img>
	// возвращаем только по return
	function img($src = '', $class = '', $title = '', $alt = '', $width = '', $height = '')
	{
		if ($class) $class = ' class="' . $class . '"';
		if ($title) $title = ' title="' . htmlspecialchars($title) . '"';
		if ($alt) $alt = ' alt="' . htmlspecialchars($alt) . '"';
		if ($width) $width = ' width="' . $width . '"';
		if ($height) $height = ' height="' . $height . '"';
		
		return '<img src="' . $src . '"' . $class . $title . $alt . $width . $height . '>';
	}

	// для заголовка можно использовать отдельную функцию
	// в этом случае можно указать отдельные параметры
	// $echo работает как в line 
	function title($do = '<h1>', $posle = '</h1>', $link = true, $echo = 0)
	{
		$out = mso_page_title(
				$this->val('page_slug'), // данные из $page
				$this->val('page_title'), // данные из $page
				$do, 
				$posle,
				$link, 
				false);
		
		if ($out) 
		{
			if ($echo === 0) return $this->out($out);
			elseif ($echo === true)
			{
				echo $out;
				return $this;
			}
			elseif ($echo === false) return $out;
		}
	}

	// возвращает название сайта только по return
	// если это не home, то в виде A-ссылки
	function name_site()
	{
		return !is_type('home') ? 
					$this->link(getinfo('siteurl'), getinfo('name_site')) 
				: 
					getinfo('name_site');
	}

	// возвращает адрес записи всегда по return
	// если $html_link = true, то формирует <a href="адрес">
	function page_url($html_link = false)
	{
		if ($html_link) 
			return '<a href="' . mso_page_url($this->val('page_slug')) . '">';
		else
			return mso_page_url($this->val('page_slug'));
	}

	// парсинг - используется парсер CodeIgniter
	// $template - это шаблон 
	// $data - данные в виде массива
	function parse($template = '', $data = array(), $echo = true)
	{
		$CI = & get_instance();
		$CI->load->library('parser');
		
		$out = $CI->parser->parse_string($template, $data, true);
		
		if ($echo) 
		{
			echo $out;
			return $this;
		}
		
		return $out;
	}

	// парсинг через файл-шаблон, указанный в $file
	// путь указывается полный на сервере
	// $data - данные в виде массива как и в parse()
	function parse_f($file = '', $data = array(), $echo = true)
	{
		if (!file_exists($file)) return;
		
		$tmpl = file_get_contents($file);
		
		return $this->parse($tmpl, $data, $echo);
	}	
	
} // end  class Page_out 


# получение адреса первой картинки IMG в тексте
# адрес обрабатывается, чтобы сформировать адрес полный (full), миниатюра (mini) и превью (prev)
# результат записит от значения $res
# если $res = true => найденный адрес или $default
# если $res = 'mini' => адрес mini
# если $res = 'prev' => адрес prev
# если $res = 'full' => адрес full
# если $res = 'all' => массив из всех сразу:
#  		[full] => http://сайт/uploads/image.jpg
#  		[mini] => http://сайт/uploads/mini/image.jpg
#  		[prev] => http://сайт/uploads/_mso_i/image.jpg
if (!function_exists('mso_get_first_image_url'))
{
	function mso_get_first_image_url($text = '', $res = true, $default = '')
	{
		$pattern = '!<img.*?src="(.*?)"!i';
		
		//$pattern = '!<img.+src=[\'"]([^\'"]+)[\'"].*>!i';
		
		preg_match_all($pattern, $text, $matches);
		
		//pr($matches);
		if (isset($matches[1][0])) 
		{
			$url = $matches[1][0];
			if(empty($url)) $url = $default;
		}
		else
			$url = $default;
		
		//_pr($url,1);
		if (strpos($url, '/uploads/smiles/') !== false) return ''; // смайлики исключаем
		
		if ($res === true) return $url;
		
		$out = array();

		// если адрес не из нашего uploads, то отдаем для всех картинок исходный адрес
		if (strpos($url, getinfo('uploads_url')) === false) 
		{
			$out['mini'] = $out['full'] = $out['prev'] = $url;
			
			if ($res == 'mini' or $res == 'prev' or $res == 'full') return $out['mini'];
				else return $out;
		
		}
		
		if (strpos($url, '/mini/') !== false) // если в адресе /mini/ - это миниатюра
		{
			$out['mini'] = $url;
			$out['full'] = str_replace('/mini/', '/', $url);
			$out['prev'] = str_replace('/mini/', '/_mso_i/', $url);
		}
		elseif(strpos($url, '/_mso_i/') !== false) // если в адресе /_mso_i/ - это превью 100х100
		{
			$out['prev'] = $url;
			$out['full'] = str_replace('/_mso_i/', '/', $url);
			$out['mini'] = str_replace('/_mso_i/', '/mini/', $url);
		}
		else // обычная картинка
		{
			$fn = end(explode("/", $url)); // извлекаем имя файла
			$out['full'] = $url;
			$out['mini'] = str_replace($fn, 'mini/' . $fn, $url);
			$out['prev'] = str_replace($fn, '_mso_i/' . $fn, $url);
		}
		
		if ($res == 'mini') return $out['mini'];
		elseif ($res == 'prev') return $out['prev'];
		elseif ($res == 'full') return $out['full'];
		else return $out;
	}
}


// класс для вывода блоков записей
class Block_pages
{
	protected $param; // массив входящих данных для получения записей
	protected $pages; // полученные записи
	protected $pagination; // если используется пагинация
	
	var $go = false; // признак, что можно делать вывод
	
	function __construct($r1 = array(), $UNIT = array())
	{
		if ($r1 !== false ) $this->get_pages($r1, $UNIT); // сразу получаем записи
	}
	
	// метод, где получаются записи
	protected function get_pages($r, $UNIT = array())
	{
		// дефолтные значения для получения записей
		$default = array(
			'limit' => 1, // колво записей
			'cut' => '»»»', // ссылка cut
			'pagination' => false, // выводить пагинацию
			'cat_id' => 0, // можно указать рубрики через запятую
			'page_id' => 0, // можно указать записи через запятую
			'page_id_autor' => false, // записи автора
			'type' => 'blog', // можно указать тип записей
			'order' => 'page_date_publish', // поле сортировки страниц
			'order_asc' => 'desc', // поле сортировки страниц
			'show_cut' => true, // показывать ссылку cut
			'date_now' => true, // учитывать время публикации
			'exclude_page_allow' => true, // учитывать исключенные ранее страницы
			// 'exclude_page_add' => true, // разрешить добавлять полученные страницы в исключенные
			'function_add_custom_sql' => false, // дополнительная функция для sql-запроса 
			'pages_reverse' => false, // реверс результата 
		);
		
		$this->param = array_merge($default, $r); // объединяем с дефолтом
		
		$exclude_page_id = ($this->param['exclude_page_allow']) ? mso_get_val('exclude_page_id') : array();
			
		$this->pages = mso_get_pages( array ( 
			'limit' => $this->param['limit'], 
			'cut' => $this->param['cut'],
			'pagination' => $this->param['pagination'],
			'cat_id' => $this->param['cat_id'],
			'page_id' => $this->param['page_id'],
			'page_id_autor' => $this->param['page_id_autor'],
			'type' => $this->param['type'],
			'order' => $this->param['order'],
			'order_asc' => $this->param['order_asc'],
			
			'show_cut' => $this->param['show_cut'],
			'show_xcut' => $this->param['show_cut'],
			
			'date_now' => $this->param['date_now'],
			
			'custom_type' => 'home',
			'exclude_page_id' => $exclude_page_id, // исключаем получение уже выведенных записей
			
			'function_add_custom_sql' => $this->param['function_add_custom_sql'],
			'pages_reverse' => $this->param['pages_reverse'],
			
			// 'get_page_categories' => false,
			// 'get_page_count_comments' => false,
				
		), 
		$this->pagination);
		
		$this->go = ($this->pages) ? true : false;
		
		// цепляем $UNIT к каждой записи
		if ($this->pages)
		{	
			for($i = 0; $i < count($this->pages); $i++)
				$this->pages[$i]['UNIT'] = $UNIT;
		}
	}
	
	public function set_pages($pages, $pagination)
	{
		$this->pages = $pages;
		$this->pagination = $pagination;
		if ($pagination) $this->param['pagination'] = true;
		$this->go = ($this->pages) ? true : false;
	}

	// метод, выводящий записи
	public function output($r = array())
	{
		if (!$this->pages) return; // нет записей, выходим
		
		// дефолтный формат вывода
		$default = array(
			'title_start' => '<h3 class="home-last-page">',
			'title_end' => '</h3>',
			
			'date' => 'D, j F Y г. в H:i',
			'date_start' => '<span class="date"><time datetime="[page_date_publish_iso]">',
			'date_end' => '</time></span>',
			
			'cat_start' => ' | <span class="cat">', 
			'cat_end' => '</span>', 
			'cat_sep' => ', ',
			
			'tag_start' => ' | <span class="tag">', 
			'tag_end' => '</span>', 
			'tag_sep' => ', ',
			'tag_class' => '',
			
			'author_start' => '',
			'author_end' => '',
			
			'read' => '»»»',
			'read_start' => '',
			'read_end' => '',
			
			'comments_count_start' => '',
			'comments_count_end' => '',

			'thumb' => true, // использовать миниатюры
			'thumb_width' => 320,
			'thumb_height' => 180,
			'thumb_class' => 'thumb left', // css-класс картинки
			'thumb_link_class' => '', // css-класс ссылки 
			'thumb_link' => true, // формировать ссылку на запись 
			'thumb_add_start' => '', // произвольная добавка перед img 
			'thumb_add_end' => '', // произвольная добавка после img 
			'thumb_type_resize' => 'resize_full_crop_center', // тип создания миниатюры  
			
			// имя файла формируется как placehold_path + placehold_file
			'placehold' => false, // если нет картинки, выводим плейсхолд (true) или ничего (false)
			'placehold_path' => 'http://placehold.it/', // путь к плейсхолдеру
								// getinfo('template_url') . 'images/placehold/'
			'placehold_pattern' => '[W]x[H].png', // шаблон плейсхолдера : width x height .png
												// где [W] меняется на ширину, [H] — высоту, [RND] - число 1..10
			'placehold_file' => false,  // файл плейсхолдера, если false, то будет: width x height .png
										// если равно data, то свой плейсхолдер
			'placehold_data_bg' => '#CCCCCC', // цвет фона, если data
			
			'block_start' => '', // html вначале
			'block_end' => '', // html в конце
			
			'line1' => '[thumb]', // первая линия — перед контентом
			'line1_start' => '',
			'line1_end' => '', 
			
			'line2' => '[title]', // вторая линия — перед контентом
			'line2_start' => '',
			'line2_end' => '',

			'line3' => '[date] [cat]', // третья линия — перед контентом
			'line3_start' => '',
			'line3_end' => '', 
			
			'line4' => '', // четвертая линия — после контента
			'line4_start' => '',
			'line4_end' => '', 			
			
			'line5' => '', // пятая линия — после контента
			'line5_start' => '',
			'line5_end' => '', 	
			
			// вывод контента
			// если указано любое значение, то вывод по этому варианту иначе обычный вывод до cut
			'content' => true, // разрешить вывод контента 
			'content_chars' => 0, // колво символов 
			'content_words' => 0, // колво слов 
			'content_cut' => ' ...', // завершение в контенте 
			'content_start' => '<div class="mso-page-content">', // обрамляющий блок до
			'content_end' => '</div>', // обрамляющий блок после
			
			'clearfix' => false, // отбивать после вывода $p->clearfix();
			
			'page_start' => '', // html в начале вывода записи 
			'page_end' => '', // html в конце вывода записи
			
			'pagination_start' => '', // html в начале пагинации 
			'pagination_end' => '', // html в конце пагинации
			'pagination_in_block' => true, // пагинации внутри block_start и block_end
						
			'exclude_page_add' => true, // разрешить добавлять полученные страницы в исключенные
		);
		
		$r = array_merge($default, $r); // объединяем
		
		$p = new Page_out; // шаблонизатор
		
		eval(mso_tmpl_prepare($r['block_start'], false));
		
		// формат записи
		$p->format('title', $r['title_start'], $r['title_end']);
		$p->format('date', $r['date'], $r['date_start'], $r['date_end']);
		$p->format('author', $r['author_start'], $r['author_end']);
		$p->format('cat', $r['cat_sep'], $r['cat_start'], $r['cat_end']);
		$p->format('tag', $r['tag_sep'], $r['tag_start'], $r['tag_end'], $r['tag_class']);
		$p->format('read', $r['read'], $r['read_start'], $r['read_end']);
		$p->format('comments_count', $r['comments_count_start'], $r['comments_count_end']);
		
		if ($r['exclude_page_add']) $exclude_page_id = mso_get_val('exclude_page_id');
		
		foreach ($this->pages as $page)
		{
			$p->load($page); // загружаем данные записи
			
			eval(mso_tmpl_prepare($r['page_start'], false));
			
			if ($r['thumb']) // миниатюра
			{
				// плейсхолд
				if ($r['placehold'])
				{
					if ($r['placehold_file'])
					{
						if ($r['placehold_file'] == 'data')
						{
							// сами генерируем плейсхолд
						  // mso_holder($width = 100, $height = 100, $text = true, $background_color = '#CCCCCC', $text_color = '#777777', $font_size = 5)
							$t_placehold = mso_holder($r['thumb_width'], $r['thumb_height'], false, $r['placehold_data_bg']);
						}
						else
						{
							$t_placehold = $r['placehold_path'] . $r['placehold_file'];
						}
					}
					else
					{
						$t_placehold_pattern = str_replace('[W]', $r['thumb_width'], $r['placehold_pattern']);
						$t_placehold_pattern = str_replace('[H]', $r['thumb_height'], $t_placehold_pattern);
						$t_placehold_pattern = str_replace('[RND]', rand(1, 10), $t_placehold_pattern);
						
						$t_placehold = $r['placehold_path'] . $t_placehold_pattern;
					}
				}
				else 
				{
					$t_placehold = false;
				}
				
				// если используется thumb_type_resize != resize_full_crop_center, то меняем постфикс
				
				$thumb_postfix = true;
				
				if ($r['thumb_type_resize'] !== 'resize_full_crop_center')
				{
					
					$thumb_postfix = '-' . $r['thumb_width'] . '-' . $r['thumb_height'] . '-' . $r['thumb_type_resize'];
				}
			
				if (
					$thumb = thumb_generate(
						$p->meta_val('image_for_page'), // адрес
						$r['thumb_width'], //ширина
						$r['thumb_height'], //высота
						$t_placehold,
						$r['thumb_type_resize'], // тип создания
						false,
						'mini',
						$thumb_postfix,
						mso_get_option('upload_resize_images_quality', 'general', 90)
					))
				{
					if ($r['thumb_link'])
						$p->thumb = '<a class="' . $r['thumb_link_class'] . '" href="' . mso_page_url($p->val('page_slug')) . '" title="' . htmlspecialchars($p->val('page_title')). '">' . $r['thumb_add_start'] . '<img src="' . $thumb . '" class="' . $r['thumb_class'] . '" alt="' . htmlspecialchars($p->val('page_title')). '">' . $r['thumb_add_end'] . '</a>';
					else
						$p->thumb = $r['thumb_add_start'] . '<img src="' . $thumb . '" class="' . $r['thumb_class'] . '" alt="' . htmlspecialchars($p->val('page_title')). '">' . $r['thumb_add_end'];
				}
			}
			
			$p->line($r['line1'], $r['line1_start'], $r['line1_end']);
			$p->line($r['line2'], $r['line2_start'], $r['line2_end']);
			$p->line($r['line3'], $r['line3_start'], $r['line3_end']);

			if ($r['content'])
			{
				if ($r['content_chars'])
				{
					$p->content_chars($r['content_chars'], $r['content_cut'], $r['content_start'], $r['content_end']);  // текст обрезанный
				}
				elseif ($r['content_words'])
				{
					$p->content_words($r['content_words'], $r['content_cut'], $r['content_start'], $r['content_end']);  // текст обрезанный
				}
				else
				{
					$p->content($r['content_start'], $r['content_end']);
				}
			}
			
			$p->line($r['line4'], $r['line4_start'], $r['line4_end']);
			$p->line($r['line5'], $r['line5_start'], $r['line5_end']);
			
			if ($r['clearfix']) $p->clearfix();
			
			eval(mso_tmpl_prepare($r['page_end'], false));
			
			// сохраняем id записей, чтобы их исключить из вывода
			if ($r['exclude_page_add']) $exclude_page_id[] = $p->val('page_id'); 
		}
		
		if ($r['exclude_page_add']) mso_set_val('exclude_page_id', $exclude_page_id);

		if ($r['pagination_in_block'] == false)
			eval(mso_tmpl_prepare($r['block_end'], false));
		
		if ($this->param['pagination']) 
		{
			if (mso_hook_present('pagination'))
			{
				eval(mso_tmpl_prepare($r['pagination_start'], false));
				mso_hook('pagination', $this->pagination);
				eval(mso_tmpl_prepare($r['pagination_end'], false));
			}
		}
		
		if ($r['pagination_in_block'] == true)
			eval(mso_tmpl_prepare($r['block_end'], false));
	}
} // end block_pages

# end of file