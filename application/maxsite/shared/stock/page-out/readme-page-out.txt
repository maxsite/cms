 * MaxSite CMS
 * (c) http://maxsite.org/
 * Класс для вывода записи
 
	# пример 1
	
	// подключение библиотеки
	require_once(getinfo('shared_dir') . 'stock/page-out/page-out.php');
	
	
	// подготавливаем объект для вывода записей
	$p = new Page_out;
 
	if ( $pages = mso_get_pages(array(параметры получения записей), $pagination) )
	{
		// зададим формат вывода каждого элемента
		// делать перед циклом поскольку он не меняется внутри
		
		// сброс формата к дефолтному, если необходимо
		$p->reset_format();
		
		// первым параметром указывается элемент
		// указаны дефолтные значения
		// если параметр совпадает с дефолтным, его можно не указывать
		
		# $p->format('title', '<h1>', '</h1>', true); // до, после, формировать ссылку
		# $p->format('date', 'Y-m-d H:i:s', '', ''); // формат даты, до, после
		# $p->format('cat', ', ', '', ''); // разделитель, до, после
		# $p->format('tag', ', ', '', ''); // разделитель, до, после
		# $p->format('feed', 'Подписаться', '', ''); // титул, до, после
		# $p->format('author', '', ''); // до, после
		# $p->format('edit', 'Редактировать', '', ''); // титул, до, после
		# $p->format('read', 'Читать дальше', '', ''); // титул, до, после
		# $p->format('comments', 'Обсудить', 'Посмотреть комментарии', '', ''); // без комментариев, есть комментарии, до, после
		
		
		// можно задать вывод по echo (по-умолчанию) или return
		# $p->echo = false; // данные будут возвращаться по return
		
		// напримре сменим вывод заголовка записи
		$p->format('title', '<h4>', '</h4>');
		
		// цикла вывода записей
		foreach ($pages as $page)
		{
			$p->load($page); // загружаем данные записи
			
			# 	Каждый вывод фо формату это line().
			# 	второй и третий параметры - до, после
			
			# 	[title] - заголовок записи
			# 	[date] - дата
			# 	[autor] - автор
			# 	[comments] - ссылка на комментарии
			# 	[cat] - рубрики
			# 	[tag] - метки
			# 	[edit] - ссылка на редактирование записи
			# 	[feed] - rss
			# 	[read] - аля-читать далее
			
			$p->line('[title]'); 
			$p->line('[date] | [autor] | [comments]', '<p>', '</p>');
			$p->line('[cat]', '<p>', '</p>');
			$p->line('[tag]', '<p>', '</p>');
			$p->line('[edit]', '<p>', '</p>');
			$p->line('[feed]', '<p>', '</p>');
			$p->line('[read]', '<p>', '</p>');
			

			// вывод контента - это отдельная функция
			// можно указать дополнительно до и после
			// до = <div class="page_content">  после = </div> 
			
			$p->content(); // просто обычный вывод 
			# $p->content_words(10); // обрезка по кол-ву слов
			# $p->content_chars(100); // обрезка по кол-ву символов
			
			// вывод указанной мета
			$p->meta('title', 'Титул: ');
			
			
			// можно получить значение указанной meta для дальнейшей работы
			# $meta_title = $p->meta_val('title');
			
			// можно получить значение любого ключа из $page
			# $id = $p->val('page_id')
			
			// можно вывести произвольный текст/html 
			# $p->html('<hr>');
			
			// аналог $p->line, только всегда возвращает результат по return
			# $cat = $p->line_r('[cat]');
			
			// можно вывести блок, обрамленный до, после, при условии, непустого содержимого
			# $bl = 'какой-то текст';
			# $p->block('<p>', '</p>', $bl); // если бы $bl == '', то ничего не выведет
			
			// можно вывести заголовок отдельной функцией
			# $p->title(NR . '<li>', '</li>');
		}
	}
 
	# пример 2
	
	require_once(getinfo('shared_dir') . 'stock/page-out/page-out.php');
	
	$p = new Page_out;
 
	if ( $pages = mso_get_pages(array(параметры получения записей), $pagination) )
	{
		// цикла вывода записей
		foreach ($pages as $page)
		{
			$p->load($page); // загружаем данные записи

			$p->line('[title]'); 
			$p->line('[date] | [autor] | [comments] [edit]', '<p>', '</p>');
			
			// рубрики и метки выведем одним блоком (это пример использования line_r и block)
			$bl = $p->line_r('[cat]', 'Рубрики: ');
			$bl .= $p->line_r('[tag]', 'Метки: ');
			$p->block('<p>', '</p>', $bl);
			
			$p->line('[feed]', '<p>', '</p>');
			$p->line('[read]', '<p>', '</p>');

			$p->content();

			$p->meta('title', 'Титул: ');
			
			$p->div('Текст', 'css-класс'); // произвольный текст в DIV.CLASS
			
			$p->html('<hr>');
			
		}

	}
 
	# пример 3 использование счетчика
	
	$p->reset_counter(count($pages)); // сбросить счетчик и утановить всего записей в цикле
	
	foreach($pages as $page)
	{
		$p->load($page);
		
		// можно сменить заголовок записи
		$p->page['page_title'] = '№' . $p->num . ' «' . $p->page['page_title'] . '»';
		
		$p->line('[title]');
		
		...
		
	
		if (!$p->last) $p->html('<hr>'); // если не последняя запись
	}

	
	# блоки
	
	
	// box - аналог таблиц
	
	$p->box_start(); // старт - если одна строка
		$p->cell('111'); // вывод ячейки
		$p->cell('222'); // вывод ячейки
		$p->cell('333'); // вывод ячейки
	$p->box_end(); // конец

	
	// если нужно явно задавать row
	$p->box_start('css-класс', false); // старт
	
		$p->row_start(); // старт строки
			$p->cell('111', 'css-класс'); // вывод ячейки
			$p->cell('222'); // вывод ячейки
			$p->cell('333'); // вывод ячейки
		$p->row_end(); // конец строки	
		
		$p->row_start(); // старт строки
			$p->cell('444', 'css-класс'); // вывод ячейки
			$p->cell('555'); // вывод ячейки
			$p->cell('666'); // вывод ячейки
		$p->row_end(); // конец строки			
		
	$p->box_end(); // конец
	
	
	// или такой вывод ячейки
	
	... 
			$p->cell_start('css-класс'); 
			
				$p->line('[date]', '<p class="info">', '</p>');
				$p->content_words(20, '...');

			$p->cell_end();
	
	...
	
	
	
	// less-стили для box
	div.box {
		display: table;
		width: 100%;
		
		div.row {
			display: table-row;
			
			div.cell {
				display: table-cell;
			}
		}
	}	
	
	
	# вывод в виде ячеек таблицы
	# 1 2
	# 3 4
	# 5 6
	# 7 8
	
	$p->box_grid(2); // задать кол-во ячеек в одной строке
	
	foreach($pages)
	{ 
		$p->box_grid_cell('w50'); // начало ячейки - задали css-класс
		
			... вывод записей
			
		$p->box_grid_next(); // конец ячейки
	
	}
	
	$p->box_grid_end(); // стоп вывода ячеек
	
	
	
	
	# Прочие возможности
	
	// формирование <a>-ссылки 
	$link = $p->link('ссылка', 'название', 'подсказка', 'css-класс');
	
		Пример:
		$name_site = getinfo('name_site');
		if (!is_type('home')) $name_site = $p->link(getinfo('siteurl'), $name_site);
	
	
	// название сайта
	$name_site = $p->name_site(); 
		Если это главная, то возвращается только навзвание
		иначе - название в виде ссылки.
	
	
	// формирование div-блоков
	
	// открываем
	$p->div_start('block1', 'wrap');
	
		результат:
			<div class="block1"><div class="wrap">
	
	Каждый параметр - это новый div.класс
	
	//закрываем
	
	$p->div_end('block1', 'wrap');
	
		результат:
			</div></div>
	
	
	// формирование div.clearfix
	$p->clearfix();
	
	
	// произвольный html-тэг
	// полный аналог $p->div()
	$p->tag('текст', 'css-класс', 'div');
	
	
	# парсинг-шаблонизатор
	
	// готовим данные
	$data = array(
		'name_site' => 'Мой сайт',
		'descr_site' => 'Лучший сайт',
	);
	
	// шаблон вывода
	$template = <<<EOF
		<div class="my_site">
			<div class="name_site">{name_site}</div>
			<div class="descr_site">{descr_site}</div>
		</div>
	EOF;

	// сам вывод
	$p->parse($template, $data);

	
	// возможен парсинг через файл
	// имя файла произвольно
	
	$p->parse_f(getinfo('template_dir') . 'ns-search.tpl', $data);
	
	Файл ns-search.tpl:
	
		<div class="my_site">
			<div class="name_site">{name_site}</div>
			<div class="descr_site">{descr_site}</div>
		</div>

	
	
	