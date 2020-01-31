<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
@USE_PHP@

# заголовок блока
[unit]
html = _START_
<div class="layout-center-wrap mar50-t"><div class="layout-wrap">
	<div class="t200 t-color1 t-center">Feugiat</div>
</div></div>
_END_
[/unit] 


# класс слайдера
{% $slider_id = 'slider2'; %}

# подключение слайдера
[unit]
file = distr/owl-carousel/owl-carousel.php
[/unit]

# js-параметры cлайдера
# классы анимации: https://daneden.github.io/animate.css/
// animateOut: 'slideOutLeft',
// animateIn: 'flipInX',	

[unit]
html = _START_
<script>$(function(){ $(".{{ $slider_id }}").owlCarousel({
loop: true,
margin: 0,
autoplay: false,
autoplayTimeout: 5000,
dots: true,
items: 1,
videoHeight: true,
});
});</script>
_END_
[/unit]


# вывод
[unit]
html = _START_
<div class="layout-center-wrap mar30-b"><div class="layout-wrap pad0 pos-relative"><div class="owl-carousel owl-theme {{ $slider_id }} pad15-rl h500px-max overflow-hidden">

<div class="w700px-max b-center">
	<div class="">Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non. Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.</div>
	
	<div class="mar20-t italic t-right">Feugiat</div>
</div>

<div class="w700px-max b-center">
	<div class="">Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non. Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.</div>
	
	<div class="mar20-t italic t-right">Venenatis</div>
</div>

<div class="w700px-max b-center">
	<div class="">Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non. Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.</div>
	
	<div class="mar20-t italic t-right">Feugiat</div>
</div>

<div class="w700px-max b-center">
	<div class="">Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non. Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.</div>
	
	<div class="mar20-t italic t-right">Venenatis</div>
</div>

<div class="w700px-max b-center">
	<div class="">Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non. Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.</div>
	
	<div class="mar20-t italic t-right">Feugiat</div>
</div>

<div class="w700px-max b-center">
	<div class="">Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non. Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.</div>
	
	<div class="mar20-t italic t-right">Tempus</div>
</div>


</div></div></div>

_END_

[/unit]

[unit]
html = _START_
<style>
.{{ $slider_id }} .owl-dots .owl-dot span {
  width: 25px !important;
  background: #cccccc !important;
}
.{{ $slider_id }} .owl-dots .owl-dot.active span, 
.{{ $slider_id }} .owl-dots .owl-dot:hover span {
  background: #7a81b6 !important;
}
</style>
_END_
[/unit]
