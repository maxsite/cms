<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<article class="mso-comment-article {{ $a_class }} clearfix">
	
	{% if ($avatar) : %}
		<img src="{{ $avatar }}" alt="" class="mso-gravatar">
	{% endif %}

	<p class="mso-comment-info">
		<span class="mso-comment-num">{{ $comment_num }}</span>
		
		<span class="mso-comment-author">{{ $comments_url }}</span>
		
		{% if ($comusers_url) : %}
			<a href="{{ $comusers_url }}" rel="nofollow" class="mso-comuser-url">{{ tf('Сайт') }}</a>
		{% endif %}
		
		<a href="#comment-{{ $comments_id }}" id="comment-{{ $comments_id }}" class="mso-comment-date">{{ mso_date_convert('d-m-Y H:i', $comments_date) }}</a>
		
		{% if (!$comments_approved) : %}
			<span class="mso-comment-approved">{{ tf('Ожидает модерации') }}</span>
		{% endif %}
		
		{% if ($edit_link) : %}
			<a href="{{ $edit_link . $comments_id }}" class="mso-comment-edit">edit</a>
		{% endif %}
		
	</p>
	
	<div class="mso-comment-content">{{ $comments_content }}</div>
	
</article>
