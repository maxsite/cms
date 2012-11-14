<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

echo '<div class="comment-info">' . $comment_info . '</div>';
			
echo '<div class="comments_content">'
	. mso_avatar($comment) 
	. mso_comments_content($comments_content) 
	. '</div>';