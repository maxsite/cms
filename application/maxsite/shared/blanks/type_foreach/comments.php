<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

			extract($comment);

			echo '<li><span><a href="' . getinfo('siteurl') . 'page/' . mso_slug($page_slug) . '#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $page_title . '</a>';
			echo ' | ' . $comments_url;
			echo '</span><br>' . $comments_date;
			echo '</span><br>' . $comments_content;
			echo '</li>';

?>