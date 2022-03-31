<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * Шаблонизатор для вывода записей
 */

class Page_out
{
	protected $formats = []; // массив форматов функций
	protected $def_formats = []; // массив форматов дефолтный

	// разобраться с видимостью полей
	// пока все делаем публичными
	public  $page = []; // массив записи
	public  $echo = true; // выводить результат по echo

	public  $num = 0; // номер текущей записи в цикле
	public  $max = 1; // всего записей в цикле
	public  $last = false; // признак, что это последняя запись

	public  $count_cells = 2; // кол-во ячеек в одной строке для box_grid()
	public  $cur_cells = 1; // текущая ячейка
	public  $close_box_grid = false; // признак, что div-строка box_grid не закрыта

	public  $thumb = ''; // миниатюра для [thumb]
	public  $thumb_url = ''; // адрес миниатюры для [thumb]

	public function __construct()
	{
		$this->reset_format();
	}

	// сброс форматов аргументов функций до дефолтного
	public function reset_format()
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

			'date' => array(
				'Y-m-d H:i:s',
				'',
				''
			),

			'cat' => array(
				', ',
				'',
				''
			),

			'tag' => array(
				', ',
				'',
				'',
				''
			),

			'feed' => array(
				'Подписаться',
				'',
				''
			),

			'comments' => array(
				'Обсудить',
				'Посмотреть комментарии',
				'',
				''
			),

			'comments_count' => array(
				'',
				''
			),

			'autor' => array(
				'',
				'',
			),

			'author' => array // дубль autor
			(
				'',
				'',
			),

			'edit' => array(
				'Редактировать',
				'',
				''
			),

			'read' => array(
				'Читать дальше',
				'',
				''
			),

			'view_count' => array(
				'',
				''
			),

			'meta_description' => array(
				'<div class="meta_description">',
				'</div>'
			),

			'meta_title' => array(
				'<div class="meta_title">',
				'</div>'
			),
		);

		$this->formats = $this->def_formats;
	}

	// принимаем массив записи
	public function load($page = [])
	{
		$this->page = $page;
		$this->thumb = '';
		$this->thumb_url = '';
		$this->num++; // счетчик увеличим
		$this->last = ($this->num >= $this->max); // ставим признак true, если это последняя запись
	}

	// сбросить счетчики
	public function reset_counter($max = 1)
	{
		$this->max = $max; // всего записей
		$this->num = 0; // счетчик
	}

	// возвращает значение указанного ключа массива $page
	public function val($key)
	{
		if (isset($this->page[$key]))
			return $this->page[$key];
		else
			return '';
	}

	// вспомогательная функция для вывода результатов
	protected function out($out)
	{
		if ($this->echo) {
			echo $out;
			return $this; // для цепочки вызовов
		}

		return $out;
	}

	// задание формата вывода
	// вывод по заданному формату осуществляется в $this->line()
	public function format()
	{
		$numargs = func_num_args(); // кол-во аргументов переданных в функцию

		if ($numargs === 0) {
			return; // нет аргументов, выходим
		}

		$args = func_get_args(); // массив всех полученных аргументов

		// заносим эти данные в свой массив форматов
		// первый аргумент всегда ключ функции - они предопределены как mso_page_...
		// параметры определяются в каждом конкретном случае
		$this->formats[$args[0]] = array_slice($args, 1);

		// сливаем с дефолтным, если есть такой же ключ
		if (isset($this->def_formats[$args[0]])) {
			$this->formats[$args[0]] = $this->formats[$args[0]] + $this->def_formats[$args[0]];
		}
	}

	// получение из массива formats массива ключа и проверка в нем указанного по номеру аргумента
	// номер аргумента функции начинается с 1
	public function get_formats_args($key, $numarg)
	{
		if (isset($this->formats[$key][$numarg - 1])) {
			// в форматировании могут встречаться специальные замены
			$f = $this->formats[$key][$numarg - 1];

			// пока указываем некоторые, потом нужно будет подумать как сделать замены по всем ключам val
			$f = str_replace('[page_count_comments]', $this->val('page_count_comments'), $f);

			$f = str_replace('[page_date_publish]', $this->val('page_date_publish'), $f);

			// [page_date_publish_iso] формирует дату согласно ISO8601
			$dp = mso_date_convert(DATE_ISO8601, $this->val('page_date_publish'), false, false, false);
			$f = str_replace('[page_date_publish_iso]', $dp, $f);

			if ($this->val('page_last_modified')) {
				$f = str_replace('[page_last_modified]', $this->val('page_last_modified'), $f);
				$dp = mso_date_convert(DATE_ISO8601, $this->val('page_last_modified'), false, false, false);
				$f = str_replace('[page_last_modified_iso]', $dp, $f);
			}

			return $f;
		} else {
			return ''; // нет ключа
		}
	}

	// вывод данных по указанному в $out формату
	// $echo позволяет принудительно задать выдачу результата: true - по echo, false - return, 0 - как в $this->echo
	public function line($out = '', $do = '', $posle = '', $echo = 0)
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
		//$content_words = '';
		//$content_chars = '';

		// title
		if (strpos($out, '[title]') !== false) {
			$title = mso_page_title(
				$this->val('page_slug'), // данные из $page
				$this->val('page_title'), // данные из $page
				$this->get_formats_args('title', 1), // $do = '<h1>', 
				$this->get_formats_args('title', 2), // $posle = '</h1>',
				$this->get_formats_args('title', 3), // $link = true, 
				false
			);
		}

		// адрес страницы
		if (strpos($out, '[page_url]') !== false) {
			$page_url = $this->page_url(false);
		}

		// mso_page_author_link($users_nik = '', $page_id_autor = '', $do = '', $posle = '', $echo = true, $type = 'author', $link = true
		if (strpos($out, '[autor]') !== false) {
			$autor = mso_page_author_link(
				$this->val('users_nik'), // данные из $page
				$this->val('page_id_autor'), // данные из $page
				$this->get_formats_args('autor', 1), // $do = '', 
				$this->get_formats_args('autor', 2), // $posle = '',
				false
			);
		}

		if (strpos($out, '[author]') !== false) {
			$author = mso_page_author_link(
				$this->val('users_nik'), // данные из $page
				$this->val('page_id_autor'), // данные из $page
				$this->get_formats_args('author', 1), // $do = '', 
				$this->get_formats_args('author', 2), // $posle = '',
				false
			);
		}

		// mso_page_comments_link($page_comment_allow = true, $page_slug = '', $title = 'Обсудить', $do = '', $posle = '', $echo = true, $type = 'page'
		if (strpos($out, '[comments]') !== false) {
			$comments = mso_page_comments_link(
				array(
					'page_comment_allow' => $this->val('page_comment_allow'), // разрешены комментарии?
					'page_slug' => $this->val('page_slug'), // короткая ссылка страницы

					// титул, если есть ссылка
					'title' => $this->get_formats_args('comments', 1) . ' (' . $this->val('page_count_comments') . ')',

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
		if (strpos($out, '[comments_count]') !== false) {
			$comments_count = $this->get_formats_args('comments_count', 1) . $this->val('page_count_comments') . $this->get_formats_args('comments_count', 2);
		}

		// mso_page_cat_link($cat = [], $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'category', $link = true
		if (strpos($out, '[cat]') !== false) {
			$cat = mso_page_cat_link(
				$this->val('page_categories'), // данные из $page
				$this->get_formats_args('cat', 1), // $sep 
				$this->get_formats_args('cat', 2), // $do
				$this->get_formats_args('cat', 3), // $posle
				false
			);
		}

		// [cat/cat_id] — тоже, что и [cat], только отсекаются рубрики не указанные в cat_id
		// должен быть указан cat_id. Если нет, то отдаём как [cat] 
		if (strpos($out, '[cat/cat_id]') !== false) {
			$c = $this->val('page_categories'); // данные из $page

			if (isset($this->page['UNIT']['cat_id']))
				$c = array_intersect(mso_explode($this->page['UNIT']['cat_id']), $c);

			$cat_cat_id = mso_page_cat_link(
				$c,
				$this->get_formats_args('cat', 1), // $sep 
				$this->get_formats_args('cat', 2), // $do
				$this->get_formats_args('cat', 3), // $posle
				false
			);
		}

		// mso_page_tag_link($tags = [], $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'tag', $link = true, $class = ''
		if (strpos($out, '[tag]') !== false) {
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
		if (strpos($out, '[edit]') !== false) {
			$edit = mso_page_edit_link(
				$this->val('page_id'), // данные из $page
				$this->get_formats_args('edit', 1), // $title 
				$this->get_formats_args('edit', 2), // $do
				$this->get_formats_args('edit', 3), // $posle
				false
			);
		}

		// date
		//mso_page_date($date = 0, $format = 'Y-m-d H:i:s', $do = '', $posle = '', $echo = true
		if (strpos($out, '[date]') !== false) {
			$date = mso_page_date(
				$this->val('page_date_publish'), // данные из $page
				array(
					'format' => tf($this->get_formats_args('date', 1)), // 'd/m/Y H:i:s'
					'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
					'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')
				),
				$this->get_formats_args('date', 2), // $do
				$this->get_formats_args('date', 3), // $posle
				false
			);
		}

		if (strpos($out, '[date_last_modified]') !== false) {
			if ($this->val('page_last_modified')) {
				$date_last_modified = mso_page_date(
					$this->val('page_last_modified'), // данные из $page
					array(
						'format' => tf($this->get_formats_args('date_last_modified', 1)), // 'd/m/Y H:i:s'
						'days' => tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
						'month' => tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')
					),
					$this->get_formats_args('date_last_modified', 2), // $do
					$this->get_formats_args('date_last_modified', 3), // $posle
					false
				);
			}
		}

		// read
		if (strpos($out, '[read]') !== false) {
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
		if (strpos($out, '[feed]') !== false) {
			// подписку по rss ставим только если есть разрешение в page_comment_allow
			if ($this->val('page_comment_allow'))
				$feed = mso_page_feed(
					$this->val('page_slug'), // данные из $page
					$this->get_formats_args('feed', 1), // 'Подписаться'
					$this->get_formats_args('feed', 2), // $do
					$this->get_formats_args('feed', 3), // $posle
					true,
					false
				);
		}

		// view_count
		// mso_page_view_count($page_view_count = 0, $do = '<span>Прочтений:</span> ', $posle = '', $echo = true)
		if (strpos($out, '[view_count]') !== false) {
			$view_count = mso_page_view_count(
				$this->val('page_view_count'), // данные из $page
				$this->get_formats_args('view_count', 1), // $do Прочтений
				$this->get_formats_args('view_count', 2), // $posle
				false
			);
		}

		// мета description, если есть
		if (strpos($out, '[meta_description]') !== false) {
			if ($meta_description = $this->meta_val('description')) {
				$meta_description = $this->get_formats_args('meta_description', 1) // $do
					. $meta_description
					. $this->get_formats_args('meta_description', 2); // $posle
			}
		}

		// мета title, если есть
		if (strpos($out, '[meta_title]') !== false) {
			if ($meta_title = $this->meta_val('meta_title')) {
				$meta_title = $this->get_formats_args('meta_title', 1) // $do
					. $meta_description
					. $this->get_formats_args('meta_title', 2); // $posle
			}
		}

		// [meta@price]             [meta@@price] — если нужно преобразовать в html
		// [meta@price|<b>|</b>]    [meta@@price|<b>|</b>]
		if (strpos($out, '[meta@') !== false) {
			//pr($out);
			$out = preg_replace_callback('!(\[meta@)(.*?)(\])!is', array('self', '_line_meta_set'), $out);
			//pr($out);
		}

		// [val@price] — произвольный val из page
		if (strpos($out, '[val@') !== false) {
			$out = preg_replace_callback('!(\[val@)(.*?)(\])!is', array('self', '_line_val_set'), $out);
		}

		// Склонение числительных 
		// [plur@VAL|фраза1|фраза2|фраза5]
		// [plur@page_view_count|комментарий|комментария|комментариев]
		// [plur@page_count_comments|комментарий|комментария|комментариев]
		// VAL — ключ из $this->page
		if (strpos($out, '[plur@') !== false) {
			$out = preg_replace_callback('!(\[plur@)(.*?)(\])!is', array('self', '_line_plur_set'), $out);
		}

		if (strpos($out, '[content]') !== false) {
			$content = $this->get_content();
		}

		if (strpos($out, '[content_chars@') !== false) {
			$out = preg_replace_callback('!(\[content_chars@)(.*?)(\])!is', array('self', '_line_content_chars'), $out);
		}

		if (strpos($out, '[content_words@') !== false) {
			$out = preg_replace_callback('!(\[content_words@)(.*?)(\])!is', array('self', '_line_content_words'), $out);
		}

		/**
		 * количество записей в указанной рубрике [cat_pages_count@7]
		 */
		if (strpos($out, '[cat_pages_count@') !== false) {
			$out = preg_replace_callback('!(\[cat_pages_count@)(.*?)(\])!is', array('self', '_cat_pages_count'), $out);
		}

		/**
		 * произвольная php-функция [func@my_f]
		 * до 3-х аргументов [func@my_f|аргумент] или [func@my_f|аргумент1|аргумент2]
		 * в функцию передаём текущую page и аргументы. Функция должна быть уже объявлена
		 * function my_f($p, $a1 = 0, $a2 = 10, $a3 = 0) {...}
		 */
		if (strpos($out, '[func@') !== false) {
			$out = preg_replace_callback('!(\[func@)(.*?)(\])!is', array('self', '_line_func'), $out);
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
		$out = str_replace('[thumb_url]', $this->thumb_url, $out);
		$out = str_replace('[content]', $content, $out);

		if ($out) {
			if ($echo === 0) return $this->out($do . $out . $posle);
			elseif ($echo === true) {
				echo $do . $out . $posle;
				return $this;
			} elseif ($echo === false) return $do . $out . $posle;
		}
	}

	// колбак для поиска [meta@мета] или [meta@@мета]
	// если используется два @@ [meta@@мета], то нужно обрамить meta в htmlspecialchars
	// можно указать обрамление: [meta@мета|<b>|</b>]
	// если мета пустое, то обрамление не выводится
	protected function _line_meta_set($matches)
	{
		$m = $matches[2];

		if (strpos($m, '|') !== false) {
			$k = explode('|', $m);
            
            if (strpos($k[0], '@') === 0) {
                $k[0] = substr($k[0], 1);
                $m = htmlspecialchars($this->meta_val($k[0]));
            } else {
                $m = $this->meta_val($k[0]);
            }
			
			if ($m) {
				if (isset($k[1])) $m = $k[1] . $m;
				if (isset($k[2])) $m .= $k[2];
			}
		} else {
			$m = $this->meta_val($m);
		}

		return $m;
	}

	// колбак для поиска [val@поле]
	protected function _line_val_set($matches)
	{
		$m = $matches[2];
		$m = $this->val($m);
		return $m;
	}

	// колбак для поиска [plur@] 
	// нужно указывать все данные
	// [plur@page_view_count|комментарий|комментария|комментариев]
	// [plur@page_count_comments|комментарий|комментария|комментариев]
	protected function _line_plur_set($matches)
	{
		$k = explode('|', $matches[2]);

		if (count($k) != 4) return;

		return mso_plur($this->val(trim($k[0])), $k[1], $k[2], $k[3]);
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

	// колбак для [cat_pages_count@7]
	protected function _cat_pages_count($matches)
	{
		return mso_get_cat_pages_count($matches[2]);
	}

	// колбак для [func@my_f] или [func@my_f|аргумент] или [func@my_f|a1|a2|a3]
	// в функцию передаём текущую page и аргументы. Функция должна быть уже объявлена
	// function my_f($p, $a1 = 0, $a2 = 10, $a3 = 0) {...}
	protected function _line_func($matches)
	{
		$m = $f = $matches[2];
		$a1 = $a2 = $a3 = '';

		if (strpos($m, '|') !== false) {
			// есть аргумент
			$k = explode('|', $m);
			if (isset($k[0])) $f = $k[0]; // функция
			if (isset($k[1])) $a1 = $k[1]; // аргумент 1
			if (isset($k[2])) $a2 = $k[2]; // аргумент 2
			if (isset($k[3])) $a3 = $k[3]; // аргумент 3
		}

		if ($f and function_exists($f)) return $f($this->page, $a1, $a2, $a3);

		return '';
	}

	// только получаем контент через mso_page_content()
	public function get_content()
	{
		ob_start();
		mso_page_content($this->val('page_content'));
		$page_content = ob_get_contents();
		ob_end_clean();
        
		return $page_content;
	}

	// вывод контента
	public function content($do = '<div class="mso-page-content">', $posle = '</div>')
	{
		return $this->out($do . $this->get_content() . $posle);
	}

	// обрезка контента по кол-ву слов
	public function content_words($max_words = 15, $cut = '', $do = '<div class="mso-page-content">', $posle = '</div>')
	{
		if ($cut) $cut = ' ' . $this->page_url(true) . $cut . '</a>';

		$content = $this->content_do_cut($this->get_content());

		return $this->out($do . mso_str_word(strip_tags($content), $max_words) . $cut . $posle);
	}

	// обрезка контента по кол-ву символов
	public function content_chars($max_chars = 100, $cut = '', $do = '<div class="mso-page-content">', $posle = '</div>')
	{
		if ($cut) $cut = ' ' . $this->page_url(true) . $cut . '</a>';

		$content = $this->content_do_cut($this->get_content());

		return $this->out($do . mb_substr(strip_tags($content), 0, $max_chars, 'UTF-8') . $cut . $posle);
	}

	// обрезка контента/текста до [cut]  
	public function content_do_cut($content)
	{
		$content = preg_replace('|\[cut\]\s*<br|', '[cut]<br', $content);
		$content = preg_replace('|\[cut\](\&nbsp;)*<br|', '[cut]<br', $content);
		$content = preg_replace('|\[cut\](\&nbsp;)*(\s)*<br|', '[cut]<br', $content);

		if (preg_match('/\[cut(.*?)?\]/', $content, $matches)) {
			$content = explode($matches[0], $content, 2);
		} else {
			$content = array($content);
		}

		return $content[0];
	}

	// вывод мета - только значение мета по return
	public function meta_val($meta = '', $default = '', $razd = ', ')
	{
		// mso_page_meta_value($meta = '', $page_meta = [], $default = '', $razd = ', '
		return mso_page_meta_value($meta, $this->val('page_meta'), $default, $razd);
	}

	// вывод мета
	public function meta($meta = '', $do = '', $posle = '', $razd = ', ')
	{
		// mso_page_meta($meta = '', $page_meta = [], $do = '', $posle = '', $razd = ', ', $echo = true
		return $this->out(mso_page_meta($meta, $this->val('page_meta'), $do, $posle, $razd, false));
	}

	// вывод произвольного html
	public function html($text = '')
	{
		return $this->out($text);
	}

	// полный аналог div
	// чтобы не так резало глаза div
	public function tag($text = '', $class = '', $tag = 'div', $style = '')
	{
		if ($class) $class = ' class="' . $class . '"';
		if ($style) $style = ' style="' . $style . '"';

		return $this->out('<' . $tag . $class . $style . '>' . $text . '</' . $tag . '>');
	}

	// вывод div с указанным css-классом
	// или можно указать свой
	public function div($text = '', $class = '', $tag = 'div', $style = '')
	{
		if ($class) $class = ' class="' . $class . '"';
		if ($style) $style = ' style="' . $style . '"';

		return $this->out('<' . $tag . $class . $style . '>' . $text . '</' . $tag . '>');
	}

	// вывод открывающих div с указанным css-классом
	// сколько аргументов, столько и вложенных div 
	// название аргумента - это css-класс 
	public function div_start()
	{
		$numargs = func_num_args();

		if ($numargs === 0) {
			return $this->out('<div>'); // нет аргументов, одиночный div
		}

		$args = func_get_args(); // массив всех полученных аргументов
		$out = '';

		foreach ($args as $class) {
			// если аргумент начинается с <, значит это какой-то тэг
			// его выводим как есть

			if (0 === strpos($class, '<'))
				$out .= $class;
			else {
				if ($class)
					$out .= '<div class="' . $class . '">';
				else
					$out .= '<div>';
			}
		}

		return $this->out($out);
	}

	// аналогичная div_start(), только закрывающая
	public function div_end($class = '')
	{
		$numargs = func_num_args();

		if ($numargs === 0) {
			return $this->out('</div>'); // нет аргументов, одиночный div
		}

		$args = func_get_args(); // массив всех полученных аргументов

		// классы выводятся в обратном порядке
		$args = array_reverse($args);
		$out = '';
		$out_comment = '';

		foreach ($args as $class) {
			if (0 === strpos($class, '<'))
				$out .= $class;
			else
				$out .= '</div>';

			if ($class) {
				$out_comment .= ' /.' . $class;
			} else {
				$out_comment .= ' /div';
			}
		}

		return $this->out($out . '<!--' . $out_comment . '-->');
	}

	// вывод div.clearfix
	// или можно указать 
	public function clearfix($class = 'mso-clearfix')
	{
		if ($class) $class = ' class="' . $class . '"';

		return $this->out('<div' . $class . '></div>');
	}

	// функция равна line, только всегда отдает по return
	public function line_r($out = '', $do = '', $posle = '')
	{
		return $this->line($out, $do, $posle, false);
	}

	// вывод произвольного блока
	// только если $out содержит текст
	public function block($out = '', $do = '', $posle = '')
	{
		if ($out) return $this->out($do . $out . $posle);
	}

	// формируем <a>
	// возвращаем только по return
	public function link($url = '#', $name = '', $title = '', $class = '')
	{
		if ($class) $class = ' class="' . $class . '"';
		if ($title) $title = ' title="' . htmlspecialchars($title) . '"';

		return '<a href="' . $url . '"' . $class . $title . '>' . $name . '</a>';
	}

	// формируем <img>
	// возвращаем только по return
	public function img($src = '', $class = '', $title = '', $alt = '', $width = '', $height = '')
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
	public function title($do = '<h1>', $posle = '</h1>', $link = true, $echo = 0)
	{
		$out = mso_page_title(
			$this->val('page_slug'), // данные из $page
			$this->val('page_title'), // данные из $page
			$do,
			$posle,
			$link,
			false
		);

		if ($out) {
			if ($echo === 0) return $this->out($out);
			elseif ($echo === true) {
				echo $out;
				return $this;
			} elseif ($echo === false) return $out;
		}
	}

	// возвращает название сайта только по return
	// если это не home, то в виде A-ссылки
	public function name_site()
	{
		return !is_type('home') ?
			$this->link(getinfo('siteurl'), getinfo('name_site'))
			: getinfo('name_site');
	}

	// возвращает адрес записи всегда по return
	// если $html_link = true, то формирует <a href="адрес">
	public function page_url($html_link = false)
	{
		if ($html_link)
			return '<a href="' . mso_page_url($this->val('page_slug')) . '">';
		else
			return mso_page_url($this->val('page_slug'));
	}

	// парсинг - используется парсер CodeIgniter
	// $template - это шаблон 
	// $data - данные в виде массива
	public function parse($template = '', $data = [], $echo = true)
	{
		$CI = &get_instance();
		$CI->load->library('parser');

		$out = $CI->parser->parse_string($template, $data, true);

		if ($echo) {
			echo $out;
			return $this;
		}

		return $out;
	}

	// парсинг через файл-шаблон, указанный в $file
	// путь указывается полный на сервере
	// $data - данные в виде массива как и в parse()
	public function parse_f($file = '', $data = [], $echo = true)
	{
		if (!file_exists($file)) return;

		$tmpl = file_get_contents($file);

		return $this->parse($tmpl, $data, $echo);
	}
}

# end of file
