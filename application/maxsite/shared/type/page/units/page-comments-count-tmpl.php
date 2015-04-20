<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<p class="comments-count">
	{{ tf('Комментариев') }}: {{ count($comments) }} | <span class="comments-rss"><a href="{{ mso_page_url($page['page_slug']) }}/feed">RSS</a></span>
</p>
