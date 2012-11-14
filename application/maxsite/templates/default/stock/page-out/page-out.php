<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 * Класс для вывода записи
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

			'author' => array
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
				)
		);
		
		$this->formats = $this->def_formats;
	}
	
	// принимаем массив записи
	function load($page = array())
	{
		$this->page = $page;
		
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
			return $this->formats[$key][$numarg-1];
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
		$comments = '';
		$cat = '';
		$tag = '';
		$edit = '';
		$date = '';
		$read = '';
		$feed = '';
		
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
		
		
		// read
		// mso_page_title($page_slug = '', $page_title = 'no title', $do = '<h1>', $posle = '</h1>', $link = true, $echo = true, $type = 'page'
		if (strpos($out, '[read]') !== false)
		{
			$read = 
				  $this->get_formats_args('read', 2) // $do
				. $this->page_url(true)
				. $this->get_formats_args('read', 1) // 'читать далее'
				. $this->get_formats_args('read', 3) // $posle
				. '</a>';
		}							
		
		// feed
		// mso_page_feed($page_slug = '', $page_title = 'Подписаться', $do = '<p>', $posle = '</p>', $link = true, $echo = true, $type = 'page'
		if (strpos($out, '[feed]') !== false)
		{
			$feed = mso_page_feed(
				$this->val('page_slug'), // данные из $page
				$this->get_formats_args('feed', 1), // 'Подписаться'
				$this->get_formats_args('feed', 2), // $do
				$this->get_formats_args('feed', 3), // $posle
				true,
				false);
		}			
		
		$out = str_replace('[title]', $title, $out);
		$out = str_replace('[autor]', $autor, $out);
		$out = str_replace('[comments]', $comments, $out);
		$out = str_replace('[cat]', $cat, $out);
		$out = str_replace('[tag]', $tag, $out);
		$out = str_replace('[edit]', $edit, $out);
		$out = str_replace('[date]', $date, $out);
		$out = str_replace('[read]', $read, $out);
		$out = str_replace('[feed]', $feed, $out);
		
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
	
	// вывод контента
	function content($do = '<div class="page_content">', $posle = '</div>')
	{
		return $this->out(NR . $do . $this->val('page_content') . $posle);
	}
	
	// обрезка контента по кол-ву слов
	function content_words($max_words = 15, $cut = '', $do = '<div class="page_content">', $posle = '</div>')
	{
		return $this->out(NR . $do . mso_str_word(strip_tags($this->val('page_content')), $max_words) . $cut . $posle);
	}
	
	// обрезка контента по кол-ву символов
	function content_chars($max_chars = 100, $cut = '', $do = '<div class="page_content">', $posle = '</div>')
	{
		return $this->out(NR . $do . mb_substr(strip_tags($this->val('page_content')), 0, $max_chars, 'UTF-8') . $cut . $posle);
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
	function tag($text = '', $class = '', $tag = 'div')
	{
		if ($class) $class = ' class="' . $class . '"';
		
		return $this->out('<' . $tag . $class . '>' . $text . '</' . $tag . '>');
	}
	
	// вывод div с указанным css-классом
	// или можно указать свой
	function div($text = '', $class = '', $tag = 'div')
	{
		if ($class) $class = ' class="' . $class . '"';
		
		return $this->out('<' . $tag . $class . '>' . $text . '</' . $tag . '>');
	}
	
	// вывод открывающих div с указанным css-классом
	// сколько аргументов, столько и вложенных div 
	// название аргумента - это css-класс 
	function div_start()
	{
		$numargs = func_num_args();
		
		if ($numargs === 0) 
		{
			return $this->out('<div>'); // нет аргументов, одиночный div
		}

		$args = func_get_args(); // массив всех полученных аргументов

		$out = '';
		
		foreach ($args as $class)
		{
			$out .= '<div class="' . $class . '">';
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
		
		foreach ($args as $class)
		{
			// если класс не указан, то делаем без поясняющего комментария
			if ($class)
				$out .= '</div><!-- /div.' . $class . ' -->';
			else
				$out .= '</div>';
		}
		
		return $this->out($out);
	}
	
	
	// вывод div.clearfix
	// или можно указать 
	function clearfix($class = 'clearfix')
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
	function box_start($class = '', $rows1 = true)
	{
		if ($class) $class = ' ' . $class;
		
		if ($rows1) $text = NR . '<div class="box' . $class . '"><div class="row">';
		else $text = NR . '<div class="box' . $class . '">';
	
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
	function box_grid_cell($class = '')
	{
		// для первой ячейки нужно открыть блоки
		if ($this->cur_cells == 1)
		{
			$this->box_start('', false);
			$this->row_start();
			$this->close_box_grid = false;
		}
		
		// добавляем специфичные классы для ячейки
		// автоматом формируем номер ячейки
		// 
		
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
	
	function __construct($cols_count = 3, $pages_count = 1, $_echo = true)
	{
		// запомним
		$this->cols_count = $cols_count;
		$this->pages_count = $pages_count;
		
		// режим вывода
		$this->_echo = $_echo;
		
		// вычислим
		$this->cut = ceil($pages_count/$cols_count); // кол-во записей в одной колонке
		
		// основной контейнер
		if ($this->_echo) echo NR . NR . '<div class="columns"><div class="columns-wrap">';
				else return NR . NR . '<div class="columns"><div class="columns-wrap">';

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

# end file