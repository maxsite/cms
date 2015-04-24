<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<p class="comments-count">
	<span class="comments-all-count">{{ tf('Комментариев') }}: {{ count($comments) }}</span> <a href="{{ mso_page_url($page['page_slug']) }}/feed" class="comments-rss">RSS</a>
</p>
