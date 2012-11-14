<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		if (isset($pages[0]['page_categories_detail']))
		{
			// описание рубрики
			foreach ($pages[0]['page_categories_detail'] as $_cat)
			{
				if ($_cat['category_slug'] == mso_segment(2))
				{
					if ($_cat['category_desc']) echo '<div class="category_desc">' . $_cat['category_desc'] . '</div>';
					break;
				}
			}
		}