<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// дефолтные опции компонента lightslider
// изображения с https://picsum.photos/images

my_set_opt('lightslider', '[options]
block_start = <div class="bg-white pad20">
block_end = </div>
[/options]

[js]
item: 1,
auto: true,
loop: true,
speed: 1000,
pause: 4000,
mode: "slide",
[/js]

[slide]
<div class="ls-fullwidth h400px h200px-phone" style="background-image: url(https://picsum.photos/1000/400?image=65);"><div class="ls-text t-white">Free <a href="#">text</a></div></div>
[/slide]

[slide]
<div class="ls-fullwidth h400px h200px-phone" style="background-image: url(https://picsum.photos/1000/400?image=976);"><div class="ls-text t-white">Free <a href="#">text</a></div></div>
[/slide]

[slide]
<div class="ls-fullwidth h400px h200px-phone" style="background-image: url(https://picsum.photos/1000/400?image=564);"><div class="ls-text t-white">Free <a href="#">text</a></div></div>
[/slide]
');

my_set_opt('lightslider_rules_output', "is_type('home') and mso_current_paged() == 1");	


# end of file