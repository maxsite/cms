<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="loginform">
	<h3>{{ tf('Укажите данные для регистрации на сайте') }}</h3>

	<form method="post" action="{{ $action }}" name="freg">
		{{ mso_form_session('freg_session_id') }}
		<input type="hidden" value="0" name="freg_rules_ok">
		<input type="hidden" value="{{ $redirect_url }}" name="freg_redirect_url">
		
		<p><label>{{ tf('Email (используется как логин)') }} <input type="email" name="freg_email" value="{{ $vreg_email }}"></label></p>
		
		<p><label>{{ tf('Пароль (английские буквы и цифры, без пробелов, минимум 6 символов)') }} <input type="password" name="freg_password" value="{{ $vreg_password }}"></label></p>
		
		<p><label>{{ tf('Повторите пароль') }} <input type="password" name="freg_password_repeat" value="{{ $vreg_password_repeat }}"></label></p>		
		
		<p><label>{{ tf('Имя') }} <input type="text" name="freg_nik" value="{{ $vreg_nik }}"></label></p>
		
		<p><label>{{ tf('Адрес сайта (если есть)') }} <input type="text" name="freg_url" value="{{ $vreg_url }}"></label></p>
		
		<p><label><input type="checkbox" name="freg_rules_ok"> {{ tf('Обязуюсь соблюдать правила сайта') }} {{ $rules }}</label></p>
		
		<p><button type="submit" name="freg_submit">{{ tf('Зарегистрироваться') }}</button></p>
	</form>
</div>
