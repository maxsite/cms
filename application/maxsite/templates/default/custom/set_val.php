<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

mso_set_val('head_section_html_add', 'lang="ru"');
mso_set_val('mso-page-content-add-class', 'lightgallery1'); // класс лайтгалери для блока записи

// можно указать css-класс для BODY
if (is_type('home'))
	mso_set_val('body_class', 'mso-body-home lightgallery1'); // класс лайтгалери для главной
else
	mso_set_val('body_class', 'mso-body-all mso-body-' . getinfo('type'));

// mso_set_val('show_thumb', false);
// mso_set_val('show_thumb_type_page', false);


// получение и обработка опции размеров миниатюр для генерации в info-top-файлах
// первый вариант для малой миниатюры (2/3 колонки), второй для большой (1 колонка)
$wh = mso_get_option('thumb_width_height', getinfo('template'), '640/480, 800/600');
$wh = explode(',', $wh);

$wh1 = $wh[0] ?? '640/480';
$wh2 = $wh[1] ?? '800/600';

$wh1a = explode('/', $wh1);
$wh2a = explode('/', $wh2);

$w1 = $wh1a[0] ?? 640;
$h1 = $wh1a[1] ?? 480;
$w2 = $wh2a[0] ?? 800;
$h2 = $wh2a[1] ?? 600;

$w1 = (int) $w1;
$h1 = (int) $h1;
$w2 = (int) $w2;
$h2 = (int) $h2;

if ($w1 > 1200) $w1 = 1200;
if ($w1 < 1) $w1 = 640;
if ($h1 > 1200) $h1 = 1200;
if ($h1 < 1) $h1 = 480;

if ($w2 > 1200) $w2 = 1200;
if ($w2 < 1) $w2 = 800;
if ($h2 > 1200) $h2 = 1200;
if ($h2 < 1) $h2 = 600;

// pr($w1); pr($h1); pr($w2); pr($h2);

mso_set_val('thumb-width1', $w1);
mso_set_val('thumb-height1', $h1);
mso_set_val('thumb-width2', $w2);
mso_set_val('thumb-height2', $h2);

if (mso_fe('assets/images/thumb-placehold.jpg'))
	mso_set_val('thumb-placehold', getinfo('template_url') . 'assets/images/thumb-placehold.jpg');

# end of file
