<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

[unit]
file = last-pages.php

limit = 10
-cat_id = 1

-order = page_view_count
-order_asc = desc

-exclude_page_allow = 0
-exclude_page_add = 0

line1 = [thumb]
line2 = [title]
line3 = 
line4 = 
line5 = 

block_start= 
block_end = 

page_start = <div class="pos-relative">
page_end = </div>

content = 0

thumb_width = 640
thumb_height = 480
thumb_class = 
thumb_link_class = 
-thumb_type_resize = resize_crop_center

title_start = <div class="t-primary800 t90 links-no-color t-center">
title_end = </div>
[/unit]
