<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

# последняя(-ии) запись (на всю ширину, поэтому лучше, когда есть сайдбар)

[unit]
file = last-pages.php

rules = mso_current_paged() == 1

limit = 1
cat_id = 0

-exclude_page_allow = 0
-exclude_page_add = 0

line1 = [thumb]
line2 = [title]

line3 = <div class="mar15-t mar25-b t-gray600 t90 clearfix t-center"><span class="im-calendar">[date]</span ><span class="mar10-rl im-user">[view_count] [plur@page_view_count|просмотр|просмотра|просмотров]</span> <span class="mar10-rl im-eye">[comments_count] [plur@page_count_comments|комментарий|комментария|комментариев]</span>[cat][tag]</div>

line4 = 
line5 = 

block_start = <div class="layout-center-wrap mar30-tb"><div class="layout-wrap">
block_end = </div></div>

page_start = <div class="mar50-b">
page_end = </div>

title_start = <h1 class="t-gray800 mar0 t250 t180-phone t-center t-robotoslab hover-no-underline">
title_end = </h1>

content_chars = 350
content_start = <p class="mar10-t">
content_end = </p>
content_cut = [...]

thumb_width = 800
thumb_height = 600
thumb_class = w100 
thumb_link_class = 
thumb_add_start = <div class="reyes hover-no-filter mar25-b">
thumb_add_end = </div>

date = j F Y г.
date_start = <time datetime="[page_date_publish_iso]">
date_end = </time>

cat_start = <span class="im-bookmark mar10-rl">
cat_end = </span>
cat_sep = ,&NBSP;

tag_start = <div><span class="im-bookmark mar10">
tag_end = </span></div>
tag_sep = / 
		
[/unit]
