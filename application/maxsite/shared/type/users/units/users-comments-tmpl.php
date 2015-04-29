<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h2>{{ tf('Комментарии') }}</h2>
<ul>
{% foreach ($comments as $comment) : %}
	<li><span><a href="{{ getinfo('siteurl') . 'page/' . mso_slug($comment['page_slug']) . '#comment-' . $comment['comments_id'] }}" id="comment-{{ $comment['comments_id'] }}">{{ $comment['page_title'] }}</a></span><br>{{ $comment['comments_date'] }}<br>{{ $comment['comments_content'] }}</li>
{% endforeach %}
</ul>
