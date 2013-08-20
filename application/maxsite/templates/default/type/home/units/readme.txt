Файлы unit для формирования главной.

Например: Настройка шаблона — Главная — Свой вариант вывода главной:

[unit]
file = home-last-page.php
[/unit]

[unit]
file = 2col-cats.php
cats = 1,3
limit = 5
[/unit]

[unit]
file = mini-title.php
[/unit]

Где каждый блок вывода это [unit] ... [/unit].

Обязательный параметр file, в котором имя файла в каталоге type/home/units/ 
Если файл не найден в каталоге шаблона, проверяется его наличие в shared-каталоге.

Каждый unit имеет свои специфичные настройки. Массив настроек доступен в переменной $UNIT.

См. также «Вывод блоков записей в шаблоне» — http://maxsite.org/page/vyvod-blokov-zapisej-v-shablone
