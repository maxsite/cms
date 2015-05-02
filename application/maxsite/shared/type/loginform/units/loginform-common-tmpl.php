<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="mso-loginform-common"><form method="post" action="{{ $action }}" name="flogin">
	<input type="hidden" value="{{ $redirect }}" name="flogin_redirect">
	<input type="hidden" value="{{ $session_id }}" name="flogin_session_id">
	
	<p><label><span>{{ $login }}</span><input type="text" value="" name="flogin_user" {{ $login_add }}></label></p>
	
	<p><label><span>{{ $password }}</span><input type="password" value="" name="flogin_password" {{ $password_add }}></label></p>
	
	<p><span>{{ $submit }}</span><button type="submit" name="flogin_submit">{{ $submit_value }}</button>{{ $submit_end }}</p>
	
	{{ $hook_login_form_auth }}
	{{ $form_end }}
</form></div>
