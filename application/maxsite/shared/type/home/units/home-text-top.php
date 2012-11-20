<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// если указан текст перед всеми записями, то выводим и его
if ($home_text_do = mso_get_option('home_text_do', 'templates', ''))
{ 
	echo '<div class="home-text-top">' . $home_text_do . '</div>';
}
	
# end file