<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_add_file('assets/js/jquery-scrolltofixed.min.js', true);

?>
<script>
	window.addEventListener("load", () => {
		$('#myMenu1').scrollToFixed({
			minWidth: 800,
			preFixed: function() { $(this).find('div.menu1').addClass('animation-fade'); },
			postFixed: function() { $(this).find('div.menu1').removeClass('animation-fade'); },
		});
	});
</script>