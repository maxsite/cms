<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h3>{{ tf('Укажите свои данные') }}</h3>

<p><i>{{ tf('Все указанные данные будут публичными. При смене имени, нужно будет перелогиниться заново.') }}</i></p>

<form class="mso-form" method="post">{{ mso_form_session('f_session_id') }}
	<input type="hidden" value="{{ $comusers_email }}" name="f_comusers_email">
	<input type="hidden" value="{{ $comusers_password }}" name="f_comusers_password">

	<p class="row"><label><span>{{ tf('Отображаемое имя') }}</span> <input type="text" name="f_comusers_nik" value="{{ $comusers_nik }}"></label></p>
	
	{% if (mso_get_option('comusers_url', 'templates', 1)) : %}
		<p class="row"><label><span>{{ tf('Сайт (с http://)') }}</span> <input type="url" name="f_comusers_url" value="{{ $comusers_url }}"></label></p>
	{% else : %}
		<input type="hidden" name="f_comusers_url" value="{{ $comusers_url }}">
	{% endif %}
	
	
	{% if (mso_get_option('gravatar_only', 'templates', 0)) : %}
		<input type="hidden" name="f_comusers_avatar_url" value="{{ $comusers_avatar_url }}">
	{% else : %}
		<p class="row"><label><span>{{ tf('Аватарка (с http://, 80x80px)') }}</span> <input type="url" name="f_comusers_avatar_url" value="{{ $comusers_avatar_url }}"></label></p>
	{% endif %}
	
	
	<p class="row"><label><span>{{ tf('ICQ') }}</span> <input type="number" name="f_comusers_icq" value="{{ $comusers_icq }}"></label></p>
	
	<p class="row"><label><span>{{ tf('Twitter') }}</span> <input type="text" name="f_comusers_msn" value="{{ $comusers_msn }}"></label></p>
	
	<p class="row"><label><span>{{ tf('Jabber') }}</span> <input type="text" name="f_comusers_jaber" value="{{ $comusers_jaber }}"></label></p>
	
	<p class="row"><label><span>{{ tf('Skype') }}</span> <input type="text" name="f_comusers_skype" value="{{ $comusers_skype }}"></label></p>
	
	<p class="row"><label><span>{{ tf('Дата рождения') }}</span> <input type="text" name="f_comusers_date_birth" id="f_comusers_date_birth" value="{{ $comusers_date_birth }}"></label></p>
	
	<p class="row"><label><span>{{ tf('О себе') }} ({{ tf('HTML удаляется') }})</span>
		<textarea name="f_comusers_description">{{ $comusers_description }}</textarea></label></p>

	{%  if ($f = mso_page_foreach('users-form-edit-tmpl1')) require $f; %}
	
	<input type="hidden" value="0" name="f_comusers_meta[subscribe_my_comments]">
	<input type="hidden" value="0" name="f_comusers_meta[subscribe_other_comments]">
	<input type="hidden" value="0" name="f_comusers_meta[subscribe_new_pages]">
	<input type="hidden" value="0" name="f_comusers_meta[subscribe_admin]">
	
	<p><label><input type="checkbox" name="f_comusers_meta[subscribe_my_comments]" value="1" {{ $check_subscribe_my_comments }}> {{ tf('Уведомления на новые комментарии, где я участвую') }}</label></p>
		
	<p><label><input type="checkbox" name="f_comusers_meta[subscribe_other_comments]" value="1"{{ $check_subscribe_other_comments }}> {{ tf('Уведомления на новые комментарии, где я не участвую') }}</label></p>
	
	<p><label><input type="checkbox" name="f_comusers_meta[subscribe_new_pages]" value="1" {{ $check_subscribe_new_pages }}> {{ tf('Уведомления на новые записи сайта') }}</label></p>
	
	<p><label><input type="checkbox" name="f_comusers_meta[subscribe_admin]" value="1" {{ $check_subscribe_admin }}> {{ tf('Уведомления на рассылку администратора') }}</label></p>
	
	{%  if ($f = mso_page_foreach('users-form-edit-tmpl2')) require $f; %}
	
	<p><button type="submit" name="f_submit[{{  $comusers_id }}]">{{ tf('Сохранить') }}</button></p>
			
</form>