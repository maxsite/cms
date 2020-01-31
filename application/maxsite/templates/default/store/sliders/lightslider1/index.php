<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>


# подключение слайдера

[unit]
file = lightslider.php
element = .lightslider1
adaptiveHeight = 1
auto = 1
controls = 0
item = 2
loop = 1
mode = slide
pager = 1
pause = 3000
rtl = 0
slideMargin = 25
speed = 1000
vertical = 0
[/unit]


# вывод

[unit]
limit = 5
cat_id = 0
order_asc = random
exclude_page_allow = 0
exclude_page_add = 0
block_start= <div class="layout-center-wrap mar50-tb"><div class="layout-wrap"><ul class="lightslider1 inline">
block_end = </ul></div></div>

file = last-pages.php

line1 = [thumb]
line2 = [title]
line3 = 
line4 = 
line5 = 

page_start = <li class="pos-relative">
page_end = </li>

content = 0

thumb_class = 
thumb_width = 400
thumb_height = 300

placehold = 1
placehold_data_bg = #bbbbbb
placehold_file = data

title_start = <div class="pos-absolute pos0-b w100 bg-op30 pad10 t90 t-white links-no-color">
title_end = </div>
[/unit]

