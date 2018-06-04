<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/
*/

if (is_type('home') and mso_current_paged() == 1)
	echo '<span class="i-home bold">' . t('Главная') .'</span>';
else
	echo '<a class="i-home" href="' . getinfo('siteurl') . '">' . t('Главная') .'</a>';


if (is_type('page'))
{
	if ($page['page_categories_detail'])
	{
		foreach($page['page_categories_detail'] as $c)
		{
			echo '<a class="i-angle-right mar10-l" href="' . getinfo('siteurl') . 'category/' . $c['category_slug'] .'">' . t($c['category_name'] ) .'</a>';
		}
	}
}

if (is_type('category'))
{
	if (mso_current_paged() == 1)
	{
		echo '<span class="i-angle-right mar10-l bold">' . htmlspecialchars(mso_get_cat_key('category_name')) .'</span>';
	}
	else
	{
		$c = mso_get_cat_from_slug('', true);
		echo '<a class="i-angle-right mar10-l bold" href="' . getinfo('siteurl') . 'category/' . $c['category_slug'] . '">' . htmlspecialchars($c['category_name']) .'</a>';
	}
}	


if (is_type('tag'))
{
	echo '<span class="i-angle-right mar10-l bold">' . htmlspecialchars(mso_segment(2)) .'</span>';
}


# end of file