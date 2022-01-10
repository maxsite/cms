<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
    (c) Parsedown - https://parsedown.org/
    Markdown Syntax: https://spec.commonmark.org/
*/

function parser_markdown_autoload()
{
    mso_hook_add('parser_register', 'parser_markdown_register');
}

function parser_markdown_register($parsers = array())
{
    $parsers['Markdown'] = array( // имя парсера
        'content' => 'parser_markdown_content', // функция обработчик на хуке content
        'content_post_edit' => 'parser_markdown_post_edit', // функция перед отправкой в БД
    );

    return $parsers;
}

function parser_markdown_content($text = '')
{
    return autotag_markdown($text);
}

function parser_markdown_post_edit($text = '')
{
    // глюк FireFox исправлем замену абсолютного пути src на абсолютный
    $text = str_replace('src="../../', 'src="' . getinfo('site_url'), $text);
    $text = str_replace('src="../', 'src="' . getinfo('site_url'), $text);

    return $text;
}

function autotag_markdown($text)
{
    $text = str_replace("\r", "", $text); // win-dos

    $text = "\n" . $text . "\n";

    $fn = __DIR__ . '/Parsedown/Parsedown.php';

    if (file_exists($fn))
        require_once $fn;
    else
        return $text;

    $Parsedown = new Parsedown();

    // костыль для нормальной обработки <!DOCTYPE HTML> в этом парсере
    $text = str_replace('<!DOCTYPE HTML>', '<!-- <!DOCTYPE HTML> -->', $text);
    $text = $Parsedown->text($text);
    $text = str_replace('<!-- <!DOCTYPE HTML> -->', '<!DOCTYPE HTML>', $text);

    return trim($text);
}

# end of file
