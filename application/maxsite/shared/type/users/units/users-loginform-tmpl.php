<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h3>{{ tf('Для редактирования введите свой email и пароль') }}</h3>

<div class="mso-comusers-form"><form class="mso-form" method="post">{{ mso_form_session('flogin_session_id') }}

	<input type="hidden" value="{{ $login_redirect }}" name="flogin_redirect">

	<p><label><span>{{ tf('Ваш email') }}</span> <input type="email" name="flogin_user"></label></p>

	<p><label><span>{{ tf('Ваш пароль') }}</span> <input type="password" name="flogin_password"></label></p>

	<p><button type="submit" name="flogin_submit">{{ tf('Отправить') }}</button>
	<a class="mso-i-forgot-password" href="{{ $lost_link }}">{{ tf('Я забыл пароль') }}</a></p>

</form></div>
