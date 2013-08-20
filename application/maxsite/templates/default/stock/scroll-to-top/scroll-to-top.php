<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
подключать в custom/footer-end.php

if ($fn = mso_fe('stock/scroll-to-top/scroll-to-top.php')) require($fn);

*/

?>

<div id="to_top" style="position: fixed; right: 10px; bottom: 10px; width: 35px; height: 35px; cursor: pointer; background: url(<?= getinfo('template_url') ?>stock/scroll-to-top/images/scroll-to-top.png) no-repeat;" title="Вверх!"></div>

<script>
$("#to_top").hide();
$(function () {$(window).scroll(function () {if ($(this).scrollTop() > 400) {$("#to_top").fadeIn();} else {$("#to_top").fadeOut(); } }); $("#to_top").click(function() {$("body,html").animate({scrollTop: 0}, 800); return false; }); });
</script>
