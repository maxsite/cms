<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

echo '<div class="info info-bottom">';
	mso_page_cat_link($page_categories, ' -&gt; ', '<span>' . tf('Рубрика') . ':</span> ', '');
	mso_page_tag_link($page_tags, ', ', '| <span>' . tf('Метки') . ':</span> ', '');
	
	mso_page_view_count($page_view_count, '<br><span>' . tf('Просмотров') . ':</span> ', '');
	if ($page_comment_allow) mso_page_feed($page_slug, tf('RSS'), ' | ', '');
	mso_page_meta('nastr', $page_meta, '<br><span>' . tf('Настроение') . ':</span> ', '');
	mso_page_meta('music', $page_meta, '<br><span>' . tf('В колонках звучит') . ':</span> ', '');
	mso_page_edit_link($page_id, 'Edit page', ' | ', '');
echo '</div>';