<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

--- две колонки для двух рубрик
	
	- общий контейнер старт + первая колонка
[unit]
html = <div class="layout-center-wrap"><div class="layout-wrap"><div class="flex flex-wrap mar50-t"><div class="w46 w100-phone">
[/unit]

	- название колонки
[unit]
html = <div class="bg-gray100 pad10 mar20-b t120">НОВОСТИ</div> 
[/unit] 

	- большая запись, после неё несколько мелких
	- первая колонка
[unit]
file = last-pages.php
limit = 1
cat_id = 0

_exclude_page_allow = 0
_exclude_page_add = 0

line1 = <div class="w100">[thumb]</div>
line2 = <div>[title] [content_chars@200]... <div class="mar15-t">[read][date]</div></div>
line3 = 
line4 = 
line5 = 

block_start= <div>
block_end = </div>

page_start = <div class="pad10-b">
page_end = </div>

content = 0
content_start = 
content_end = 
content_chars = 200
content_cut = ...

thumb_class = w100
thumb_width = 640
thumb_height = 480
thumb_link_class = my-hover-img w100
thumb_add_end = <div></div>

placehold = 1
placehold_data_bg = #bbbbbb
placehold_file = data

title_start = <div class="t140 mar15-tb">
title_end = </div>

read = Читать далее...
read_start = <span class="t90 upper">
read_end = </span>

date = j F Y
date_start = <span class="b-inline b-right t90"><time datetime="[page_date_publish_iso]">
date_end = </time></span>

[/unit]

	- первая колонка: маленькие миниатюрамы

[unit]
file = last-pages.php
limit = 3
cat_id = 0

_exclude_page_allow = 0
_exclude_page_add = 0

line1 = <div class="w100px w100px-min">[thumb]</div>
line2 = <div class="flex-frow5">[title][date]</div>
line3 = 
line4 = 
line5 = 

block_start= <div class="mar20-tb">
block_end = </div>

page_start = <hr class="mar5-t mar10-b bor-gray300"><div class="flex flex-jc-start">
page_end = </div>

content = 0
content_start = 
content_end = 
content_chars = 200
content_cut = ...

thumb_class = 
thumb_width = 80
thumb_height = 80
thumb_link_class = my-hover-img
thumb_add_end = 

placehold = 1
placehold_data_bg = #bbbbbb
placehold_file = data

title_start = <div class="t110">
title_end = </div>

date = j F Y
date_start = <div class="t90 t-gray500"><time datetime="[page_date_publish_iso]">
date_end = </time></div>

[/unit]



	- вторая колонка 
[unit]
html = </div><div class="w46 w100-phone">
[/unit] 
	
	- название колонки
[unit]
html = <div class="bg-gray100 pad10 mar20-b t120">ПОЛЕЗНОЕ</div> 
[/unit] 


	- вторая колонка большая запись
[unit]
file = last-pages.php
limit = 1
cat_id = 0

_exclude_page_allow = 0
_exclude_page_add = 0

line1 = <div class="w100">[thumb]</div>
line2 = <div>[title] [content_chars@200]... <div class="mar15-t">[read][date]</div></div>
line3 = 
line4 = 
line5 = 

block_start= <div>
block_end = </div>

page_start = <div class="pad10-b">
page_end = </div>

content = 0
content_start = 
content_end = 
content_chars = 200
content_cut = ...

thumb_class = w100
thumb_width = 640
thumb_height = 480
thumb_link_class = my-hover-img w100
thumb_add_end = <div></div>

placehold = 1
placehold_data_bg = #bbbbbb
placehold_file = data

title_start = <div class="t140 mar15-tb">
title_end = </div>

read = Читать далее...
read_start = <span class="t90 upper">
read_end = </span>

date = j F Y
date_start = <span class="b-inline b-right t90"><time datetime="[page_date_publish_iso]">
date_end = </time></span>

[/unit]

	- вторая колонка: маленькие миниатюрамы

[unit]
file = last-pages.php
limit = 3
cat_id = 0

_exclude_page_allow = 0
_exclude_page_add = 0

line1 = <div class="w100px w100px-min">[thumb]</div>
line2 = <div class="flex-frow5">[title][date]</div>
line3 = 
line4 = 
line5 = 

block_start= <div class="mar20-tb">
block_end = </div>

page_start = <hr class="mar5-t mar10-b bor-gray300"><div class="flex flex-jc-start">
page_end = </div>

content = 0
content_start = 
content_end = 
content_chars = 200
content_cut = ...

thumb_class = 
thumb_width = 80
thumb_height = 80
thumb_link_class = my-hover-img
thumb_add_end = 

placehold = 1
placehold_data_bg = #bbbbbb
placehold_file = data

title_start = <div class="t110">
title_end = </div>

date = j F Y
date_start = <div class="t90 t-gray500"><time datetime="[page_date_publish_iso]">
date_end = </time></div>

[/unit]


	- общий контейнер и вторая колонка стоп
[unit]
html = </div></div></div></div>
[/unit]
