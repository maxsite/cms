<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script>
	$(function() {
		$('#myMenu1').scrollToFixed({
			minWidth: 800,
			preFixed: function() { $(this).find('div.menu1').addClass('animated fadeIn'); },
			postFixed: function() { $(this).find('div.menu1').removeClass('animated fadeIn'); },
		});
	});
</script>