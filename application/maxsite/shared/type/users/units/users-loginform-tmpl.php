<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h3>{{ tf('Для редактирования введите свой email и пароль') }}</h3>

<form method="post" class="mso-comusers-form">{{ mso_form_session('flogin_session_id') }}

	<input type="hidden" value="{{ $login_redirect }}" name="flogin_redirect">

	<p><label>{{ tf('Ваш email') }} <input type="email" name="flogin_user"></label></p>

	<p><label>{{ tf('Ваш пароль') }} <input type="password" name="flogin_password"></label></p>

	<p><button type="submit" name="flogin_submit">{{ tf('Отправить') }}</button>
	<a href="{{ $lost_link }}">{{ tf('Я забыл пароль') }}</a></p>

</form>