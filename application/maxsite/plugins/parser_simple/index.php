<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

function parser_simple_autoload()
{
	mso_hook_add('parser_register', 'parser_simple_register');  // парсер Simple
	require_once(getinfo('plugins_dir') . 'parser_simple/simple.php');
}

function parser_simple_register($parsers = array())
{
	$parsers['Simple'] = array( // имя парсера
		'content' => 'parser_simple_content', // функция обработчик на хуке content
		'content_post_edit' => 'parser_simple_post_edit', // функция перед отправкой в БД
	);
	
	return $parsers;
}

function parser_simple_content($text = '')
{
	$text = autotag_simple($text);
	return $text;
}

function parser_simple_post_edit($text = '')
{
	// глюк FireFox исправлем замену абсолютного пути src на абсолютный
	$text = str_replace('src="../../', 'src="' . getinfo('site_url'), $text);
	$text = str_replace('src="../', 'src="' . getinfo('site_url'), $text);

	return $text;
}


# end of file
