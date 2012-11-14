 * MaxSite CMS
 * (c) http://maxsite.org/
 * Класс для формирования колонок
 
	1 4 7
	2 5 8
	3 6

	# 1-й рабочий пример
	# автоматическое формирование колонок
	
	// в $pages - массив записей
	$pages = array(1,2,3,4,5,6,7,8); // например так
	
	// подключение библиотеки
	require_once(getinfo('shared_dir') . 'stock/page-out/page-out.php');
			
	// инициализация на 3 колонки
	$my_columns = new Columns(3, count($pages));

	foreach ($pages as $page)
	{
		// вывод внутри цикла 
		// можно указать дополнительный css-класс колонки
		// второй параметр css-style 
		$my_columns->out('left', 'width: 50%');
		
		pr($page);
		
		// следующая итерация
		$my_columns->next();
	}
	
	// завершение вывода
	$my_columns->close();
	unset($my_columns); // удалим переменную
	
	
	
	# 2-й пример
	# ручное формирование двух колонок
	
	$my_columns = new Columns;
	
	$my_columns->new_col('left w50'); // старт колонки
	
		$pages = array(...); // массив записей
		... вывод записей клонки ... 
	
	$my_columns->end_col(); // конец колонки
	
	
	$my_columns->new_col('left w50'); // старт колонки
	
		$pages = array(...); // массив записей
		... вывод записей клонки ... 
	
	$my_columns->end_col(); // конец колонки
	
	$my_columns->close(); // закрыть колонки 
	
	unset($my_columns); // удалим переменную
	
	
	# html и css-классы колонок на примере 3 колонок

	div.columns
		div.columns-wrap
		
			div.column (+ указанные классы в out) column-1 column-1-of-3 column-first
				div.column-content
					содержимое блока
				/div
			/div
			
			div.column (+ указанные классы в out) column-2 column-2-of-3
				div.column-content
					содержимое блока
				/div
			/div
			
			div.column (+ указанные классы в out) column-3 column-3-of-3 column-last
				div.column-content
					содержимое блока
				/div
			/div
			
		/div
	/div
	
	<div class="clearfix"></div>

	
# css/less-стили для колонок

@col_padding: 15px; // растояние между колонками

div.columns {
	margin-right: -@col_padding;
}

div.column-content {
	margin-right: @col_padding;
	
	.border(silver); // можно добавить рамку
	padding: 0 5px; // и отступ
}

