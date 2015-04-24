<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<article class="comment-article {{ $a_class }} clearfix">
	
	<img src="{{ $avatar }}" alt="" class="gravatar">
	
	<p class="comment-info">
		<span class="comment-num">{{ $comment_num }}</span>
		
		<span class="comment-author">{{ $comments_url }}</span>
		
		{% if ($comusers_url) : %}
			<a href="{{ $comusers_url }}" rel="nofollow" class="comuser-url">{{ tf('Сайт') }}</a>
		{% endif %}
		
		<a href="#comment-{{ $comments_id }}" id="comment-{{ $comments_id }}" class="comment-date">{{ mso_date_convert('d-m-Y H:i', $comments_date) }}</a>
		
		{% if (!$comments_approved) : %}
			<span class="comment-approved">{{ tf('Ожидает модерации') }}</span>
		{% endif %}
		
		{% if ($edit_link) : %}
			<a href="{{ $edit_link . $comments_id }}" class="comment-edit">edit</a>
		{% endif %}
		
	</p>
	
	<div class="comment-content">{{ $comments_content }}</div>
	
</article>
