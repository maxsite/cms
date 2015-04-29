<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h3>{{ tf('Укажите свои данные') }}</h3>

<form method="post">{{ mso_form_session('f_session_id') }}
	<input type="hidden" value="{{ $comusers_email }}" name="f_comusers_email">
	<input type="hidden" value="{{ $comusers_password }}" name="f_comusers_password">

	<p><label>{{ tf('Отображаемый ник') }} <input type="text" name="f_comusers_nik" value="{{ $comusers_nik }}"></label></p>
	
	{% if (mso_get_option('comusers_url', 'templates', 1)) : %}
		<p><label>{{ tf('Сайт (с http://)') }} <input type="url" name="f_comusers_url" value="{{ $comusers_url }}"></label></p>
	{% else : %}
		<input type="hidden" name="f_comusers_url" value="{{ $comusers_url }}">
	{% endif %}
	
	
	{% if (mso_get_option('gravatar_only', 'templates', 0)) : %}
		<input type="hidden" name="f_comusers_avatar_url" value="{{ $comusers_avatar_url }}">
	{% else : %}
		<p><label>{{ tf('Аватарка (с http://, 80x80px)') }} <input type="url" name="f_comusers_avatar_url" value="{{ $comusers_avatar_url }}"></label></p>
	{% endif %}
	
	
	<p><label>{{ tf('ICQ') }} <input type="number" name="f_comusers_icq" value="{{ $comusers_icq }}"></label></p>
	
	<p><label>{{ tf('Twitter') }} <input type="text" name="f_comusers_msn" value="{{ $comusers_msn }}"></label></p>
	
	<p><label>{{ tf('Jabber') }} <input type="text" name="f_comusers_jaber" value="{{ $comusers_jaber }}"></label></p>
	
	<p><label>{{ tf('Skype') }} <input type="text" name="f_comusers_skype" value="{{ $comusers_skype }}"></label></p>
	
	<p><label>{{ tf('Дата рождения') }} <input type="text" name="f_comusers_date_birth" value="{{ $comusers_date_birth }}"></label></p>
	
	<p><label>{{ tf('О себе') }} ({{ tf('HTML удаляется') }})
		<br><textarea name="f_comusers_description">{{ $comusers_description }}</textarea></label></p>

	<input type="hidden" value="0" name="f_comusers_meta[subscribe_my_comments]">
	<input type="hidden" value="0" name="f_comusers_meta[subscribe_other_comments]">
	<input type="hidden" value="0" name="f_comusers_meta[subscribe_new_pages]">
	<input type="hidden" value="0" name="f_comusers_meta[subscribe_admin]">
	
	<p><label><input type="checkbox" name="f_comusers_meta[subscribe_my_comments]" value="1" {{ $check_subscribe_my_comments }}> {{ tf('Уведомления на новые комментарии, где я участвую') }}</label></p>
		
	<p><label><input type="checkbox" name="f_comusers_meta[subscribe_other_comments]" value="1"{{ $check_subscribe_other_comments }}> {{ tf('Уведомления на новые комментарии, где я не участвую') }}</label></p>
	
	<p><label><input type="checkbox" name="f_comusers_meta[subscribe_new_pages]" value="1" {{ $check_subscribe_new_pages }}> {{ tf('Уведомления на новые записи сайта') }}</label></p>
	
	<p><label><input type="checkbox" name="f_comusers_meta[subscribe_admin]" value="1" {{ $check_subscribe_admin }}> {{ tf('Уведомления на рассылку администратора') }}</label></p>
		
	<p><button type="submit" name="f_submit[{{  $comusers_id }}]">{{ tf('Отправить') }}</button></p>
			
</form>