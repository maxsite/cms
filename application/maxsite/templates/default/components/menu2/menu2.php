<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// Дополнительное меню. Может использоваться как подкомпонет

?>
<div class="MainMenu2"><div class="wrap">
	<ul class="menu">
		<?php
			if ($menu = mso_get_option('menu2', 'templates', '/ | Главная_NR_about | О сайте')) 
				echo mso_menu_build($menu, 'selected', false);
		?>
	</ul>
	<div class="clearfix"></div>
</div></div><!-- div class="MainMenu2" -->
