Работа с колонками.
Файл columns.less

 
1. Произвольная колонка для любого блока.

.column_width(3, 7, 5%);

3 — ширина в колонках 
7 — всего колонок
5% — промежуток

Пример в less-файле:

div.my {

	div.r1 {
		.column_width(4, 7, 5%); // 4 колонки из 7
	}
	
	div.r2 {
		.column_width(2, 7, 5%); // 2 колонки из 7
	}	
	
	div.r3 {
		.column_width(1, 7, 5%); // 1 колонка из 7
	}
	
	// 4 + 2 + 1 = 7
}


2. Колонки на основе готовых css-хелперах (например в тексте записи)

Стили готовы для 12 колонок.

<div class="onerow">
	<div class="col w4-7">первый блок размером 4 колонки</div>
	<div class="col w2-7">первый блок размером 2 колонки</div>
	<div class="col w1-7">первый блок размером 1 колонку</div>
</div>


3. Колонки на основе иммитации таблицы

<div class="table-box">
	<div class="row">
		<div class="cell">Ячейка 1</div>
		<div class="cell">Ячейка 2</div>
		<div class="cell">Ячейка 3</div>
		<div class="cell">Ячейка 4</div>
	</div>
</div>


4. Колонки «классические». (Используется в шаблонизаторе Page_out в php-классе Columns)

В div.column float не указывается, поэтому его следует указывать явно (в этом примере — left).

<div class="columns">
	<div class="columns-wrap">
	
		<div class="column left">
			<div class="column-content">содержимое блока</div>
		</div>
		
		<div class="column left">
			<div class="column-content">содержимое блока</div>
		</div>
		
		<div class="column left">
			<div class="column-content">содержимое блока</div>
		</div>
		
	</div>
</div>

<div class="clearfix"></div>
