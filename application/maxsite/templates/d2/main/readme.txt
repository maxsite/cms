Здесь находятся main-файлы. В подкаталогах - шаблоны вывода (обязательный файл main.php).

Своё подключение файла main-шаблона реализуется в custom/main-template.php

Если файла нет, то используется стандартное переключение шаблона через метаполе записи 
или тип данных (type). Для типов данных main-шаблон располагаются в подкаталоге type:
	main/type/home/main.php
	main/type/category/main.php
	main/type/page/main.php


Код см. в shared/main/main-end.php
