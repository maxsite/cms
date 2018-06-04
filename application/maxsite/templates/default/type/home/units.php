<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

===================== 
<первая запись>

[unit]

file = last-pages.php
limit = 1

line1 = [thumb]
line2 = [title][cat]
line3 = 
line4 = [read]
line5 = 

block_start = <div class="bg-gray800 pad20 mar30-b t-white bor3 bor-solid-b bor-red">
block_end = </div>

page_start = <div class="">
page_end = </div>

content_start = <div>
content_end = 
content_chars = 350
content_cut = 

read = <span class="icon0 i-angle-double-right mar10-l t-white bg-gray700 hover-bg-red pad15-rl" title="Читать дальше"></span>
read_start = [...]
read_end = </div>

thumb_class = w100
thumb_link_class = my-hover-img
thumb_width = 670
thumb_height = 310
thumb_add_end = <div></div>

title_start = <div class="mar10-tb links-no-color t180">
title_end = </div>

cat_start = <div class="t90 mar10-b links-no-color t-gray500 i-folder-open-o">
cat_end = </div>

[/unit]

</>
=====================



=====================
<записи в 2 колонки>

[unit]
file = last-pages.php
limit = 6

line1 = [thumb]
line2 = [title]
line3 = 
line4 = [read]
line5 = [cat]

block_start = <div class="flex flex-wrap mar30-tb">
block_end = </div>

page_start = <div class="w48 w100-phone mar30-b t90 links-no-underline">
page_end = </div>

content_start = <div>
content_end = 
content_chars = 220
content_cut = 

read = <span class="icon0 i-angle-double-right mar10-l t-white bg-gray700 hover-bg-red pad15-rl" title="Читать дальше"></span>
read_start = [...]
read_end = </div>

thumb_class = w100
thumb_link_class = my-hover-img
thumb_width = 260
thumb_height = 190
thumb_add_end = <div></div>

title_start = <div class="t110 t-gray800 mar10-t mar5-b">
title_end = </div>

cat_start = <div class="mar10-tb pad5-t t90 i-folder-open-o bor-gray300 bor-solid-t bor1">
cat_end = </div>
[/unit]
</>
=====================



=====================
<Слайдер записей>

- подключение
[unit]
file = lightslider.php
element = .lightslider1
adaptiveHeight = 1
auto = 0
controls = 1
item = 3
loop = 1
mode = slide
pager = 1
pause = 3000
rtl = 0
slideMargin = 15
speed = 1000
vertical = 0
[/unit]

- вывод
[unit]
file = last-pages.php
limit = 7

block_start= <ul class="lightslider1 inline mar10-tb" style="overflow: hidden; max-height: 200px;">
block_end = </ul>

line1 = [thumb]
line2 = [title]
line3 = 
line4 = 
line5 = 

page_start = <li class="pos-relative">
page_end = </li>

content = 0

thumb_class = 
thumb_width = 300
thumb_height = 200

placehold = 1
placehold_data_bg = #bbbbbb
placehold_file = data

title_start = <div class="pos-absolute pos0-b w100 bg-op30 pad10 t80 t-white links-no-color">
title_end = </div>

[/unit]
</>
=====================
