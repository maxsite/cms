<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// вывод в виджете

// 1. Активируйте плагин any_file
// 2. Разместите в сайдбаре виджет: any_file_widget author1
// 3. В настройках виджета 
//		- укажите файл: TEMPLATE/parts/widgets/author-1.php
//	    - «Парсер HTML» выберите: Simple
 
?>

div(t-center)

__(t140) Обо мне

<img class="center mar20-tb circle" src="https://i.pravatar.cc/60?u=1" width="" height="" alt="" title="">

_ Ever wanted to sell your digital goods? Sign up with us and we'll help you distribute your products to a wide audience. Get started earning money today! 

_ <a class="t-italic" href="<?= getinfo('siteurl') ?>page/about">Читать дальше...</a>

/div
