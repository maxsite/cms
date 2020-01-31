Здесь модули юнитов. Подключать в units.php

@module модуль

Например:

@module ads/ad2
@module accordions/accordion1
@module calls/call1

********************************************************************************
Каждый модуль размещается в отдельном каталоге в файле index.php.
В этом файле должны быть секции [unit]:

[unit] 
юнит
[/unit]

То есть это обычный юнит.

Если юнит использует require, где вынесена контентная часть, то нужно указать
имя модуля в параметре "module". Например:

Файл "modules/ads/ad1/index.php"

[unit]
module = ads/ad1
require = content.php
parser = autotag_simple
[/unit]

В файле "modules/ads/ad1/content.php" размещается непосредственно текст вывода.
Имя файла может быть произвольным.

Аналогичным образом можно использовать "file":

[unit]
module = pages/pages-tab-block
file = block.php
[/unit]
