<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

if (mso_get_option('default_description_home_cat', 'templates', 0))
{
	echo '<div class="description-cat">' . $all_cats[$cat_id]['category_desc'] . '</div>';
}