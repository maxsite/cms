<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/
	
	Описание: вывод случайной цитаты через плагин randomtext
		если плагин не активирован, то он подключается автоматом.
*/

if (!function_exists('randomtext_widget_custom'))
{
	require(getinfo('plugins_dir') . 'randomtext/index.php');
}

// цитата через виджет
$cite = randomtext_widget_custom(
		array(
			'header' => '', 
			'block_start' => '', 
			'block_end' => ''
			), 999);

// цитата отделена от текста символом /
$cite = explode('/', $cite);

if (isset($cite[0])) 
{
	$text = '<div class="text">' . trim($cite[0]) . '</div>'; // текст цитаты
	
	if (isset($cite[1])) // автор
		$author = '<div class="author">' . trim($cite[1]) . '</div>';
	else
		$author = '';
	
	$text =  $text . $author;
}
else // нет цитаты
{
	$text = '<div class="text">Человек, по-настоящему мыслящий, черпает из своих ошибок не меньше познания, чем из своих успехов.</div><div class="author">Джон Дьюи</div>';
}

echo '<div class="random-text"><div class="wrap">' . $text . '</div></div>';

# end file