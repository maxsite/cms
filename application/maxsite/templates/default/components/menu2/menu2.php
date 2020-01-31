<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */
 
?>
<div class="layout-center-wrap b-clearfix"><div class="layout-wrap">
	<nav><ul class="menu-simple">
	<?php
		if ($menu = mso_get_option('menu2', 'templates', '/ | Главная ~ about | О сайте')) 
			echo mso_menu_build($menu, 'selected', false);
	?>
	</ul></nav>
</div></div>
