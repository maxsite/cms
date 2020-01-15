<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="mso-loginform">
	<p><strong>{{ tf('Привет,') }}  {{ getinfo('users_nik') }}!</strong></p>
	<p><a href="{{ getinfo('site_admin_url') }}">{{ tf('Админ-панель') }}</a> | 
		<a href="{{ getinfo('siteurl') }}logout">{{ tf('Выйти') }}</a></p>
</div>
