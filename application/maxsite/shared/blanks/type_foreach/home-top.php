<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		extract($page);
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);
	
		echo '<div class="page_content">';
			mso_page_content($page_content);
			mso_page_content_end();
			echo '<div class="break"></div>';
		echo '</div>';

?>