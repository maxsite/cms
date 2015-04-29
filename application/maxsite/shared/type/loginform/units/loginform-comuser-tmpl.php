<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="mso-loginform">
	<p>
		<strong>{{ $hello }}</strong> 
		<a href="{{ getinfo('siteurl') }}users/{{ $comuser['comusers_id'] }}">{{ t('своя страница') }}</a> | 
		<a href="{{ getinfo('siteurl') }}logout">{{ t('выйти') }}</a>
	</p>
</div>
