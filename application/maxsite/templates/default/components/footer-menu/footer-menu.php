<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/
	
	Вывод меню3
*/
?>

<div class="layout-center-wrap bg-red"><div class="layout-wrap">
	<nav><ul class="menu-simple t-right t-white t80 links-no-color upper">
		<?php
			if ($menu = mso_get_option('menu3', 'templates', '/ | Главная ~ about | О сайте')) 
				echo mso_menu_build($menu, 'selected', false);
		?>
	</ul></nav>
</div></div>
