<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

[unit]
file = last-pages.php

limit = 6
cat_id = 1..10
pagination = 1

line1 = <div class="w100">[thumb]</div>
line2 = <div class="w100 flex-grow2 pad10-t h200px-min">
line3 = [title]
line4 = </div>
line5 = <div class="w100 t90 t-gray bor1 bor-gray300 bor-solid-t pad20-t"><i class="im-eye"></i>[view_count] [plur@page_view_count|просмотр|просмотра|просмотров] <span class="b-inline b-right mar10-l"><i class="im-comment"></i>[comments_count] [plur@page_count_comments|комментарий|комментария|комментариев]</span></div>

block_start = <div class="layout-center-wrap mar50-tb"><div class="layout-wrap"><div class="flex flex-wrap">
block_end = </div></div></div>

page_start = <div class="w31 w48-tablet w100-phone bg-white mar50-b pad20-b"><div class="b-flex flex-column h100">
page_end = </div></div>

title_start = <h1 class="t120 t-gray800 links-no-color mar0">
title_end = </h1>

content_chars = 205
content_start = <p class="mar10-t t90">
content_end = </p>
content_cut = ...

thumb_width = 640
thumb_height = 480
thumb_class = w100
thumb_link_class = my-hover-img

[/unit]
