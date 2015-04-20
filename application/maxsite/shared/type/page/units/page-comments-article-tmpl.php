<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<article class="{{ $a_class }} clearfix">
	
	<img src="{{ $avatar }}" alt="" class="gravatar right">
	
	<p class="comment-info">
		<span class="comment-num">№ {{ $comment_num }}</span> | 
		
		<span class="url">{{ $comments_url }}
		
		{% if ($comusers_url) : %}
			| <a href="{{ $comusers_url }}" rel="nofollow" class="comuser-url">{{ tf('Сайт') }}</a>
		{% endif %}
		
		</span>
		
		{% if ($edit_link) : %}
			| <a href="{{ $edit_link . $comments_id }}" class="comment-edit">edit</a>
		{% endif %}
		
		{% if (!$comments_approved) : %}
			| <span class="comment-approved">{{ tf('Ожидает модерации') }}</span>
		{% endif %}
		
		| <span class="comment-date"><a href="#comment-{{ $comments_id }}" id="comment-{{ $comments_id }}">{{ mso_date_convert('d-m-Y H:i', $comments_date) }}</a></span>
	</p>
	
	<div class="comment-content">{{ $comments_content }}</div>
	
</article>
<hr>
