<?php
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

if (version_compare(PHP_VERSION, '7.1', '<'))
	die('<p>Required version PHP 7.1 and higher. Your version <b>' . PHP_VERSION . '</b></p>');

define('INSTALLER', dirname(realpath(__FILE__)) . '/installer/');
define('MSODIR', realpath(INSTALLER . '../../') . '/');

$url = ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != "off") ? "https" : "http");
$url .= "://" . $_SERVER['HTTP_HOST'];
$url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

define('URL', $url . 'installer/');
unset($url);

require INSTALLER . 'functions.php';

$lang = detectLang(['ru', 'en', 'uk'], 'ru');

if (file_exists(INSTALLER . 'langs/' . $lang . '.php'))
	$words = require INSTALLER . 'langs/' . $lang . '.php';
else
	$words = require INSTALLER . 'langs/ru.php';

t($words); // init language

$showForm = true;

?><!DOCTYPE HTML>
<html lang="<?= $lang ?>">
<head>
	<title><?= t('title'); ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="generator" content="MaxSite CMS">
	<meta name="robots" content="noindex, nofollow">
	<link rel="shortcut icon" href="<?= URL ?>images/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="<?= URL ?>css/berry-normalize.min.css">
	<link rel="stylesheet" href="<?= URL ?>css/berry-colors-lite.min.css">
	<link rel="stylesheet" href="<?= URL ?>css/style.css">
</head>
<body class="bg-gray100">
<div class="layout-center-wrap-tablet"><div class="layout-wrap bg-white pad30-rl mar20-tb pad20-tb bordered rounded">
	<div class="flex flex-vcenter">
		<figure class="flex-grow0 b-flex mar0"><img src="<?= URL ?>images/favicon.png" alt=""></figure>
		<h1 class="flex-grow5 t-red700 mar0 mar20-l t230"><?= t('title') ?></h1>
		<div class="t-right t90"><a href="https://max-3000.com/book" target="_blank"><?= t('help'); ?></a></div>
	</div>
	<hr class="bor-dotted mar20-tb bor1">
	<?php
	require INSTALLER . 'first.php';
	require INSTALLER . 'post.php';
	if ($showForm) require INSTALLER . 'form.php';
	?>
</div></div>
</body>
</html>