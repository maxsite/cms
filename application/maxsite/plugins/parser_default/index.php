<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function parser_default_autoload()
{
	mso_hook_add('parser_register', 'parser_default_register'); // дефолтный парсер
}

function parser_default_register($parsers = array())
{
	$parsers['Default'] = array( // имя парсера
		'content' => 'parser_default_content', // функция обработчик на хуке content
		'content_post_edit' => 'parser_default_post_edit', // функция перед отправкой в БД
	);
	
	return $parsers;
}

/* ~~~~~~~ Default ~~~~~~~ */

function parser_default_content($text = '')
{
	require_once(getinfo('plugins_dir') . 'parser_default/default.php');
	
	$text = autotag_default($text);
	
	return $text;
}

function parser_default_post_edit($text = '')
{
	$text = trim($text);
	$text = str_replace(chr(10), "<br>", $text);
	$text = str_replace(chr(13), "", $text);
	
	// глюк FireFox исправлем замену абсолютного пути src на абсолютный
	$text = str_replace('src="../../', 'src="' . getinfo('site_url'), $text);
	$text = str_replace('src="../', 'src="' . getinfo('site_url'), $text);
	
	// замены из-за мусора FireFox
	$text = str_replace('-moz-background-clip: -moz-initial;', '', $text);
	$text = str_replace('-moz-background-origin: -moz-initial;', '', $text);
	$text = str_replace('-moz-background-inline-policy: -moz-initial;', '', $text);

	return $text;
}


# end file