<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="mso-registration-form">
	<h3>{{ tf('Укажите данные для регистрации на сайте') }}</h3>

	<form method="post" action="{{ $action }}" name="freg">
		{{ mso_form_session('freg_session_id') }}
		<input type="hidden" value="0" name="freg_rules_ok">
		<input type="hidden" value="{{ $redirect_url }}" name="freg_redirect_url">
		
		<p><label><span>{{ tf('Email (используется как логин)') }}</span><input type="email" name="freg_email" value="{{ $vreg_email }}" required></label></p>
		
		<p><label><span>{{ tf('Пароль (английские буквы и цифры, без пробелов, минимум 6 символов)') }}</span><input type="password" name="freg_password" value="{{ $vreg_password }}" required></label></p>
		
		<p><label><span>{{ tf('Повторите пароль') }}</span><input type="password" name="freg_password_repeat" value="{{ $vreg_password_repeat }}" required></label></p>		
		
		<p><label><span>{{ tf('Имя') }}</span><input type="text" name="freg_nik" value="{{ $vreg_nik }}" required></label></p>
		
		<p><label><span>{{ tf('Адрес сайта (если есть)') }}</span><input type="text" name="freg_url" value="{{ $vreg_url }}"></label></p>
		
		<p><label><input type="checkbox" name="freg_rules_ok"> {{ tf('Обязуюсь соблюдать правила сайта') }} {{ $rules }}</label></p>
		
		<p><button type="submit" name="freg_submit">{{ tf('Зарегистрироваться') }}</button></p>
	</form>
</div>
