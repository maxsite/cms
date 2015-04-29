<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<p class="mso-comments-count">
	<span class="mso-comments-all-count">{{ tf('Комментариев') }}: {{ count($comments) }}</span> <a href="{{ mso_page_url($page['page_slug']) }}/feed" class="mso-comments-rss">RSS</a>
</p>
