<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
		
		extract($comment);
		
		if ($users_id) $class = ' class="users"';
		elseif ($comusers_id) $class = ' class="comusers"';
		else $class = ' class="anonim"';
		
		$comments_date = mso_date_convert('Y-m-d в H:i:s', $comments_date);
		
		echo NR . '<li style="clear: both"' . $class . '><div class="comment-info"><span class="date"><a href="#comment-' . $comments_id . '" id="comment-' . $comments_id . '">' . $comments_date . '</a></span>';
		echo ' | <span class="url">' . $comments_url . '</span>';
		
		if ($edit_link) echo ' | <a href="' . $edit_link . $comments_id . '">edit</a>';
		
		if (!$comments_approved) echo ' | '. tf('Ожидает модерации');
		
		echo '</div>';
		
		echo '<div class="comments_content">' 
			. mso_avatar($comment) 
			. mso_comments_content($comments_content) 
			. '</div>';
		
		echo '</li>'; 


# end file