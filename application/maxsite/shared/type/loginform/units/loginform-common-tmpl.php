<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<form method="post" action="{{ $action }}" name="flogin">
	<input type="hidden" value="{{ $redirect }}" name="flogin_redirect">
	<input type="hidden" value="{{ $session_id }}" name="flogin_session_id">
	
	<p><label>{{ $login }} <input type="text" value="" name="flogin_user" {{ $login_add }}></label></p>
	
	<p><label>{{ $password }} <input type="password" value="" name="flogin_password" {{ $password_add }}></label></p>
	
	<p>{{ $submit }}<button type="submit" name="flogin_submit">{{ $submit_value }}</button>{{ $submit_end }}</p>
	
	{{ $hook_login_form_auth }}
	{{ $form_end }}
</form>
