<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h3>{{ tf('Восстановление пароля') }}</h3>

<p><a href="{{ getinfo('siteurl') }}users">{{ tf('Список комментаторов') }}</a></p>

<div class="mso-recovery-form"><form class="mso-form" method="post">{{ mso_form_session('f_session_id') }}

	<p>{{ tf('Если у вас сохранился код активации, то вы можете сразу заполнить все поля. Если код активации утерян, то вначале введите только email и нажмите кнопку «Готово». На указанный email вы получите код активации. После этого вы можете вернуться на эту страницу и заполнить все поля.') }}</p>

	<p><label><span>{{ tf('Ваш email') }}</span><input type="text" name="f_comusers_email" value=""></label></p>
		
	<p><label><span>{{ tf('Ваш код активации') }}</span><input type="text" name="f_comusers_activate_key" value=""></label></p>

	<p><label><span>{{ tf('Новый пароль') }}</span><input type="text" name="f_comusers_password" value=""></label></p>

	<p><button type="submit" name="f_submit">{{  tf('Готово') }}</button></p>

</form></div>
