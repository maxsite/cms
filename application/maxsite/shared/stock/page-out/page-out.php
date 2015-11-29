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
		
		// mso_page_tag_link($tags = array(), $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'tag', $link = true
		if (strpos($out, '[tag]') !== false)
		{
			$tag = mso_page_tag_link(
				$this->val('page_tags'), // данные из $page
				$this->get_formats_args('tag', 1), // $sep 
				$this->get_formats_args('tag', 2), // $do
				$this->get_formats_args('tag', 3), // $posle
				false);
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
				
				//pr($this->page);
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
		
		$out = str_replace('[thumb]', $this->thumb, $out);
		
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
		// pr($m);
		return $m;
	}
	
	// колбак для поиска [val@поле]
	protected function _line_val_set($matches)
	{
		$m = $matches[2];
		$m = $this->val($m);
		// pr($m);
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
		
		return $this->out(NR . $do . mso_str_word(strip_tags($this->get_content()), $max_words) . $cut . $posle);
	}
	
	// обрезка контента по кол-ву символов
	function content_chars($max_chars = 100, $cut = '', $do = '<div class="mso-page-content">', $posle = '</div>')
	{
		if ($cut) $cut = ' ' . $this->page_url(true) . $cut . '</a>';
		
		return $this->out(NR . $do . mb_substr(strip_tags($this->get_content()), 0, $max_chars, 'UTF-8') . $cut . $posle);
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
	
	
	# формирование таблиц из строк и ячеек аля-таблица
	// если $rows1 = true, то сразу открываем row поскольку она одна
	function box_start($class = 'table-box', $rows1 = true)
	{
		if ($class) $class = ' class="' . $class . '"';
		
		if ($rows1) $text = NR . '<div' . $class . '><div class="row">';
		else $text = NR . '<div' . $class . '>';
	
		return $this->out($text);
	}
	
	// закрываем блок
	function box_end($rows1 = true)
	{
		if ($rows1) $text = NR . '</div></div>' . NR;
		else $text = NR . '</div>' . NR;
	
		return $this->out($text);
	}	
	
	// открываем row
	function row_start()
	{
		return $this->out(NR . '<div class="row">');
	}	
	
	// закрываем row
	function row_end()
	{
		return $this->out('</div>');
	}		
	
	// выводим содержимое ячейки
	function cell($text = '', $class = '')
	{
		if ($class) $class = ' ' . $class;
		
		return $this->out(NR . '<div class="cell' . $class . '"><div class="wrap">' . $text . '</div></div>');
	}
	
	// выводим ячейку в качестве пустого разделителя
	function cell_sep($text = '&nbsp;', $class = 'sep')
	{
		if ($class) $class = ' class="' . $class . '"';
		
		return $this->out(NR . '<div' . $class . '>' . $text . '</div>');
	}
	
	// старт cell
	function cell_start($class = '')
	{
		if ($class) $class = ' ' . $class;
		
		return $this->out(NR . '<div class="cell' . $class . '"><div class="wrap">');
	}
	
	// завершение cell
	function cell_end()
	{
		return $this->out(NR . '</div></div>');
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
	
	
	// вывод записей по принципу ячеек таблицы
	// Здесь задается кол-во ячеек в одной строке
	//	1 2
	//  3 4
	//  5 6
	function box_grid($count_cells = 2)
	{
		$this->count_cells = $count_cells;
		$this->cur_cells = 1;
	}
	
	
	// вывод записи
	function box_grid_cell($class = '', $class_box = 'table-box')
	{
		// для первой ячейки нужно открыть блоки
		if ($this->cur_cells == 1)
		{
			$this->box_start($class_box, false);
			$this->row_start();
			$this->close_box_grid = false;
		}
		
		// добавляем специфичные классы для ячейки
		// автоматом формируем номер ячейки
		
		if ($class) 
			$class .= ' cell_' . $this->cur_cells;
		else
			$class = 'cell_' . $this->cur_cells;
			
		$this->cell_start($class);
	}	
	
	// следующая итерация цикла - увеличиваем счетчик
	function box_grid_next()
	{
		$this->cell_end(); // закрыли ячейку
		
		// если это последняя ячейка в строке, то закрываем
		if ($this->cur_cells >= $this->count_cells)
		{
			$this->cur_cells = 1;
			$this->row_end();
			$this->box_end(false);
			$this->close_box_grid = true;
		
		}
		else
		{
			$this->cur_cells++;
		}
	}	
	
	// закрываем все открытие ячейки
	function box_grid_end()
	{
		if (!$this->close_box_grid) // есть не закрытый div 
		{
			$this->row_end();
			$this->box_end(false);
			$this->close_box_grid = true;
		}
	}
	
} // end  class Page_out 




/*
	Класс для вывода записей в колонках
*/

class Columns 
{
	protected $cols_count = 3; // количество колонок
	protected $pages_count = 1; // всего количество записей
	protected $cut = 1; // кол-во записей в одной колонке
	protected $_echo = true; // выводить данные по echo - иначе return
	protected $cut_i = 1; // номер записи в колонке
	protected $cut_num_col = 1; // номер колонки
	protected $cut_close_div = false; // признак закрытого DIV
	
	function __construct($cols_count = 3, $pages_count = 1, $class = 'columns', $_echo = true)
	{
		// запомним
		$this->cols_count = $cols_count;
		$this->pages_count = $pages_count;
		
		// режим вывода
		$this->_echo = $_echo;
		
		// вычислим
		$this->cut = ceil($pages_count/$cols_count); // кол-во записей в одной колонке
		
		// основной контейнер
		if ($this->_echo) echo NR . NR . '<div class="' . $class . '"><div class="' . $class . '-wrap">';
				else return NR . NR . '<div class="' . $class . '"><div class="' . $class . '-wrap">';

	}
	
	// вывод внутри цикла
	function out($class = 'left', $style = '')
	{
		if ($this->cut_i == 1)
		{
			$this->cut_close_div = false;
			
			if ($style) $style = ' style="' . $style . '"';
			
			$out = NR . '<div class="' . $class 
					. ' column column-' . $this->cut_num_col
					. ' column-' . $this->cut_num_col . '-of-' . $this->cols_count
					. ( ($this->cut_num_col == 1) ? ' column-first':'' ) 
					. ( ($this->cut_num_col == $this->cols_count) ? ' column-last':'' ) 
					. '"' 
					. $style . '>'
					. NR . '<div class="column-content">';
					
			if ($this->_echo) echo $out;
				else return $out;
		}
	}
	
	// следующая итерация
	function next()
	{
		$this->cut_i++;

		if ($this->cut_i > $this->cut)
		{
			$this->cut_i = 1;
			$this->cut_close_div = true;
			$this->cut_num_col++;

			if ($this->_echo) echo '</div></div>' . NR;
				else return '</div></div>' . NR;
		}
	}
	
	// завершение вывода колонок
	function close()
	{
		$out = '';
		
		// незакрытый div left
		if (!$this->cut_close_div) $out .= '</div></div>' . NR;
		
		// основной контейнер
		$out .= '<div class="clearfix"></div></div></div><!-- end columns -->' . NR;

		if ($this->_echo) echo $out;
		else return $out;
		
	}
	
	// можно задать старт новой колонки явно
	function new_col($class = 'left', $style = '')
	{
		$this->cut_close_div = true; // флаг, чтобы не ставить лишний div в close()
		
		if ($style) $style = ' style="' . $style . '"';
			
		$out = NR . '<div class="' . $class 
				. ' column"' 
				. $style . '>'
				. '<div class="column-content">';
				
		if ($this->_echo) echo $out;
			else return $out;
	}
	
	
	// можно задать конец колонки явно
	function end_col()
	{
		if ($this->_echo) echo '</div></div>' . NR;
				else return '</div></div>' . NR;
	}	
	
	
	// подчистка float
	function clearfix()
	{
		if ($this->_echo) echo '<div class="clearfix"></div>' . NR;
				else return '<div class="clearfix"></div>' . NR;
	}	
	
} // end  class Columns 



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
	
	function __construct($r1 = array())
	{
		if ($r1 !== false ) $this->get_pages($r1); // сразу получаем записи
	}
	
	
	// метод, где получаются записи
	protected function get_pages($r)
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
			
			// 'get_page_categories' => false,
			// 'get_page_count_comments' => false,
				
		), 
		$this->pagination);
		
		$this->go = ($this->pages) ? true : false;
	}
	
	public function set_pages($pages, $pagination)
	{
		$this->pages = $pages;
		$this->pagination = $pagination;
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
			'line3_start' => '<p class="home-last-page-info">',
			'line3_end' => '</p>', 
			
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
			
			// колонки
			'columns' => 0, // можно указать кол-во колонок
			'columns_class_row' => 'onerow', // css-класс
			'columns_class_cell' => 'col w1-2', // css-класс для ячейки (по-умолчанию 2 колонки)
			
			'clearfix' => false, // отбивать после вывода $p->clearfix();
			
			'page_start' => '', // html в начале вывода записи 
			'page_end' => '', // html в конце вывода записи
			
			'pagination_start' => '', // html в начале пагинации 
			'pagination_end' => '', // html в конце пагинации
			
			// колонки в виде ячеек таблицы 1 2 / 3 4 / 5 6
			// может конфликтовать с columns
			'box_grid' => 0, // указывается количество ячеек в одной строке
			'box_grid_class' => 'w50', // указывается css-класс ячейки
			'box_grid_box_class' => 'table-box', // указывается css-класс строки-контейнера
			
			'exclude_page_add' => true, // разрешить добавлять полученные страницы в исключенные

		);
		
		$r = array_merge($default, $r); // объединяем
		
		// $r = array_map('trim', $r);
		
		$p = new Page_out; // шаблонизатор
		
		// echo $r['block_start'];
		eval(mso_tmpl_prepare($r['block_start'], false));
		
		// формат записи
		$p->format('title', $r['title_start'], $r['title_end']);
		$p->format('date', $r['date'], $r['date_start'], $r['date_end']);
		$p->format('author', $r['author_start'], $r['author_end']);
		$p->format('cat', $r['cat_sep'], $r['cat_start'], $r['cat_end']);
		$p->format('tag', $r['tag_sep'], $r['tag_start'], $r['tag_end']);
		$p->format('read', $r['read'], $r['read_start'], $r['read_end']);
		$p->format('comments_count', $r['comments_count_start'], $r['comments_count_end']);
		
		if ($r['exclude_page_add']) $exclude_page_id = mso_get_val('exclude_page_id');
		
		if ($r['columns'])
		{
			$my_columns = new Columns($r['columns'], count($this->pages), $r['columns_class_row']);
		}
		
		if ($r['box_grid']) $p->box_grid($r['box_grid']);
		
		foreach ($this->pages as $page)
		{
			$p->load($page); // загружаем данные записи
			
			if ($r['box_grid']) $p->box_grid_cell($r['box_grid_class'], $r['box_grid_box_class']); 
			if ($r['columns']) $my_columns->out($r['columns_class_cell']);
			
			// echo $r['page_start'];
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
				
	
				if (
					$thumb = thumb_generate(
						$p->meta_val('image_for_page'), // адрес
						$r['thumb_width'], //ширина
						$r['thumb_height'], //высота
						$t_placehold
					))
				{
					$p->thumb = '<a href="' . mso_page_url($p->val('page_slug')) . '" title="' . htmlspecialchars($p->val('page_title')). '"><img src="' . $thumb . '" class="' . $r['thumb_class'] . '" alt="' . htmlspecialchars($p->val('page_title')). '"></a>';
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
			
			// echo $r['page_end'];
			eval(mso_tmpl_prepare($r['page_end'], false));
			
			
			if ($r['columns']) $my_columns->next();
			if ($r['box_grid']) $p->box_grid_next();
			
			// сохраняем id записей, чтобы их исключить из вывода
			if ($r['exclude_page_add']) $exclude_page_id[] = $p->val('page_id'); 
		}
		
		
		if ($r['columns']) $my_columns->close();
		
		if ($r['box_grid']) $p->box_grid_end();
		
		if ($r['exclude_page_add']) mso_set_val('exclude_page_id', $exclude_page_id);
		
		
		if ($this->param['pagination']) 
		{
			if (mso_hook_present('pagination'))
			{
				// echo $r['pagination_start'];
				eval(mso_tmpl_prepare($r['pagination_start'], false));
				
				mso_hook('pagination', $this->pagination);
				
				// echo $r['pagination_end'];
				eval(mso_tmpl_prepare($r['pagination_end'], false));
			}
		}
		
		// echo $r['block_end'];
		eval(mso_tmpl_prepare($r['block_end'], false));
	}
} // end block_pages



# end file