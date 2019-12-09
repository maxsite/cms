<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<form class="mso-form" method="post">{{ $session_id }}

	<p><label>{{ tf('Ваш email') }} <input type="email" name="f_comusers_email"></label></p>
	<p><label>{{ tf('Введите ключ активации') }} <input type="text" name="f_comusers_activate_key"></label></p>
	<p><button type="submit" name="f_submit[{{ $comusers_id }}]">{{ tf('Готово') }}</button></p>

</form>

<p>{{ tf('В случае проблем с активацией (не пришел ключ, указали ошибочный email), обращайтесь к администратору по email:') }} <em>{{ $admin_email }}</em></p>
