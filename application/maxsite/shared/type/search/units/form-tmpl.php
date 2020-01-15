<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="mso-page-only">
	<h1 class="mso-type-search">{{ tf('404. Ничего не найдено...') }} </h1>
	<div class="mso-page-content">
		{% if ($search_len) : %}
		<p>{{ tf('Поисковая фраза должна быть не менее 2 символов.') }}</p>
		{% endif %}
		<p>{{ tf('Попробуйте повторить поиск.') }} </p>
		<form class="mso-form" name="f_search" method="get" onsubmit="location.href='{{ getinfo('siteurl') }}search/' + encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;">
			<input type="text" name="s" size="20" placeholder="{{ tf('что искать?') }}">
			&nbsp;
			<button type="submit">{{ tf('Поиск') }}</button>
		</form>
		{{ mso_hook('page_404') }}
	</div>
</div>
