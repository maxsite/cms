<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */
 
if ($fn = mso_fe('components/_menu/_menu.php')){
	echo '<div class="layout-center-wrap"><div class="layout-wrap"><div>';
	require $fn;
	echo '</div></div></div>';
}

# end of file