<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
/*


КОД БУДЕТ МЕНЯТЬСЯ!


Последние записи
----------------

Простой вариант

[unit]
file = last-pages.php
cats = 1
limit = 3
[/unit]


Полный вариант (указаны значения по-умолчанию):

[unit]
file = last-pages.php

cats = 1
limit = 3
type = blog
order = page_date_publish
order_asc = desc
cut = »»»

thumb_width = 100
thumb_height = 100

class_container = flex flex-wrap pad5-rl
class_page_start = w32 w48-tablet w100-phone pad20 mar15-tb bor1px bor-solid bor-gray400 rounded
class_thumb = b-left

line1 = [title]
line2 = [thumb]
line3 =
line4 =
line5 =

title_start => <h3>
title_end = </h3>

content_words = 20
content_chars = 0
content_cut =  ...

date = D, j F Y г. в H:i
date_start = <span class="my-date"><time datetime="[page_date_publish_iso]">
date_end = </time></span>

cat_start =>  | <span class="my-cat">
cat_end = </span>
cat_sep = ,

tag_start =  | <span class="my-tag">
tag_end = </span>
tag_sep = ,

read_start = »»»
read_end =

comments_count_start =
comments_count_end =

placehold = 0
placehold_path = http://placehold.it/
placehold_pattern = [W]x[H].png
placehold_file =
placehold_data_bg = #CCCCCC
[/unit]

*/

# используем кэширование
$home_cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
$home_cache_key = getinfo('template') . '-' .  __FILE__ . '-' . mso_current_paged();

if ($home_cache_time > 0 and $k = mso_get_cache($home_cache_key) ) echo $k; // да есть в кэше
else
{
	ob_start();

	$cats = isset($UNIT['cats']) ? $UNIT['cats'] : 1;
	$limit = isset($UNIT['limit']) ? (int) $UNIT['limit'] : 3;
	$pagination = isset($UNIT['pagination']) ? (bool) $UNIT['pagination'] : false;

	$type = isset($UNIT['type']) ? (bool) $UNIT['type'] : 'blog';
	$order = isset($UNIT['order']) ? $UNIT['order'] : 'page_date_publish';
	$order_asc = isset($UNIT['order_asc']) ? $UNIT['order_asc'] : 'desc';
	$cut = isset($UNIT['cut']) ? $UNIT['cut'] : '»»»';

	$thumb_width = isset($UNIT['thumb_width']) ? (int) $UNIT['thumb_width'] : 100;
	$thumb_height = isset($UNIT['thumb_height']) ? (int) $UNIT['thumb_height'] : 100;

	$content_words = isset($UNIT['content_words']) ? (int) $UNIT['content_words'] : 20;
	$content_chars = isset($UNIT['content_chars']) ? (int) $UNIT['content_chars'] : 0;
	$content_cut = isset($UNIT['content_cut']) ? $UNIT['content_cut'] : ' ...';

	$line1 = isset($UNIT['line1']) ? $UNIT['line1'] : '[title]';
	$line2 = isset($UNIT['line2']) ? $UNIT['line2'] : '[thumb]';
	$line3 = isset($UNIT['line3']) ? $UNIT['line3'] : '';
	$line4 = isset($UNIT['line4']) ? $UNIT['line4'] : '';
	$line5 = isset($UNIT['line5']) ? $UNIT['line5'] : '';

	$title_start = isset($UNIT['title_start']) ? $UNIT['title_start'] : '<h3>';
	$title_end = isset($UNIT['title_end']) ? $UNIT['title_end'] : '</h3>';

	$class_thumb = isset($UNIT['class_thumb']) ? $UNIT['class_thumb'] : 'b-left mar10-r b-circle';

	$class_page_start = isset($UNIT['class_page_start']) ? $UNIT['class_page_start'] : 'w32 w48-small w48-tablet w100-phone pad20-rl pad10-t pad20-b mar15-tb bor1px bor-solid bor-gray400 rounded';

	$class_container = isset($UNIT['class_container']) ? $UNIT['class_container'] : 'flex flex-wrap pad5-rl';

	$date = isset($UNIT['date']) ? $UNIT['date'] : 'D, j F Y г. в H:i';
	$date_start = isset($UNIT['date_start']) ? $UNIT['date_start'] : '<span class="my-date"><time datetime="[page_date_publish_iso]">';
	$date_end = isset($UNIT['date_end']) ? $UNIT['date_end'] : '</time></span>';

	$cat_start = isset($UNIT['cat_start']) ? $UNIT['cat_start'] : ' | <span class="my-cat">';
	$cat_end = isset($UNIT['cat_end']) ? $UNIT['cat_end'] : '</span>';
	$cat_sep = isset($UNIT['cat_sep']) ? $UNIT['cat_sep'] : ' ,';

	$tag_start = isset($UNIT['tag_start']) ? $UNIT['tag_start'] : ' | <span class="my-tag">';
	$tag_end = isset($UNIT['tag_end']) ? $UNIT['tag_end'] : '</span> ';
	$tag_sep = isset($UNIT['tag_sep']) ? $UNIT['tag_sep'] : ', ';

	$read_start = isset($UNIT['read_start']) ? $UNIT['read_start'] : '»»»';
	$read_end = isset($UNIT['read_end']) ? $UNIT['read_end'] : ' ';

	$comments_count_start = isset($UNIT['comments_count_start']) ? $UNIT['comments_count_start'] : '';
	$comments_count_end = isset($UNIT['comments_count_end']) ? $UNIT['comments_count_end'] : '';

	$placehold = isset($UNIT['placehold']) ? $UNIT['placehold'] : false;
	$placehold_path = isset($UNIT['placehold_path']) ? $UNIT['placehold_path'] : 'http://placehold.it/';
	$placehold_pattern = isset($UNIT['placehold_pattern']) ? $UNIT['placehold_pattern'] : '[W]x[H].png';
	$placehold_file = isset($UNIT['placehold_file']) ? $UNIT['placehold_file'] : '';
	$placehold_data_bg = isset($UNIT['placehold_data_bg']) ? $UNIT['placehold_data_bg'] : '#CCCCCC';

	$b = new Block_pages( array (
				'limit' => $limit,
				'cat_id' => $cats,
				'pagination' => $pagination,
				'type' => $type,
				'order' => $order,
				'order_asc' => $order_asc,
				'cut' => '»»»',
			));

		if ($b->go)
		{
			$b->output(	array (
				'block_start' => '<div class="' . $class_container . '">',
				'block_end' => '</div>',

				'content_words' => $content_words,// колво слов
				'content_chars' => $content_chars, // колво символов
				'content_cut' => $content_cut, // завершение в контенте

				'thumb_width' => $thumb_width,
				'thumb_height' => $thumb_height,
				'thumb_class' => $class_thumb,

				'line1' => $line1,
				'line2' => $line2,
				'line3' => $line3,
				'line4' => $line4,
				'line5' => $line5,

				'title_start' => $title_start,
				'title_end' => $title_end,

				'page_start' => '<div class="' . $class_page_start . '">', // html в начале вывода записи
				'page_end' => '</div>', // html в конце вывода записи

				'date' => $date,
				'date_start' => $date_start,
				'date_end' => $date_end,

				'cat_start' => $cat_start,
				'cat_end' => $cat_end,
				'cat_sep' => $cat_sep,

				'tag_start' => $tag_start,
				'tag_end' => $tag_end,
				'tag_sep' => $tag_sep,

				'read_start' => $read_start,
				'read_end' => $read_end,

				'comments_count_start' => $comments_count_start,
				'comments_count_end' => $comments_count_start,

				'placehold' => $placehold,
				'placehold_path' => $placehold_path,
				'placehold_pattern' => $placehold_pattern,
				'placehold_file' => $placehold_file,
				'placehold_data_bg' => $placehold_data_bg,


			));
		}

	mso_add_cache($home_cache_key, ob_get_flush(), $home_cache_time * 60);

}

# end of file