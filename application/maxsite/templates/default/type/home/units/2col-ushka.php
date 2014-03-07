<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
* вывод 2 колонок в 2 ушки

[unit]
file = 2col-ushka.php
ushka1 = название ушки1
ushka2 = название ушки2
class1 = css-класс первой колонки
class2 = css-класс второй колонки
class_root = css-класс основного блока
[/unit]

Пример:

[unit]
file = 2col-ushka.php
ushka1 = home1-1
ushka2 = home1-2
class1 = col w1-3
class2 = col w2-3
class_root = onerow clearfix col2-ushka
[/unit]

*/

if (function_exists('ushka')) 
{
	$ushka1 = isset($UNIT['ushka1']) ? ushka(trim($UNIT['ushka1'])) : 'ушка home1-1';
	$ushka2 = isset($UNIT['ushka2']) ? ushka(trim($UNIT['ushka2'])) : 'ушка home1-2';
	
	$class1 = isset($UNIT['class1']) ? trim($UNIT['class1']) : 'col w1-2';
	$class2 = isset($UNIT['class2']) ? trim($UNIT['class2']) : 'col w1-2';
	
	$class_root = isset($UNIT['class_root']) ? trim($UNIT['class_root']) : 'onerow clearfix col2-ushka';
	
	echo NR . '<div class="' . $class_root . '"><div class="' . $class1 . '">' . $ushka1 . '</div><div class="' . $class2 . '">' . $ushka2 . '</div></div><!-- /' . $class_root . ' -->' . NR;
	
}

# end file