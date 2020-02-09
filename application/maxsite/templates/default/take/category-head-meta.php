<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// файл выполняется в контексте type-файла category.php, поэтому здесь доступна $pages

$current_cat_id = mso_get_cat_from_slug();

if ($mytitle = mso_get_meta('mytitle', 'category', $current_cat_id, 'meta_value'))
	mso_head_meta('title', $mytitle);
else
	mso_head_meta('title', $pages, '%category_name%');

if ($mydescription = mso_get_meta('mydescription', 'category', $current_cat_id, 'meta_value'))
	mso_head_meta('description', $mydescription);
else
	mso_head_meta('description', $pages, '%category_desc%'); 


# end of file
