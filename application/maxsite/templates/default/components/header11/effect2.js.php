<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

mso_add_file('assets/js/jquery.pursuingnav.js', true);

?>
<script>
window.addEventListener("load", () => {
	$('#myHeader').pursuingNav();
  
	var h = $('#myHeader').outerHeight();
	$('#myHeaderOffset').css('height', h); 
  
	$(window).resize(function(){
		var h = $('#myHeader').outerHeight();
		$('#myHeaderOffset').css('height', h);
	});
});
</script>
