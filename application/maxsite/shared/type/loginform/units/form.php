<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 
$out = <<<EOF
	<form method="post" action="{$action}" name="flogin" class="flogin fform">
		<input type="hidden" value="{$redirect}" name="flogin_redirect">
		<input type="hidden" value="{$session_id}" name="flogin_session_id">
		
		<p>
			<label><span class="nocell ftitle">{$login}</span>
			<input type="text" value="" name="flogin_user" class="flogin_user"{$login_add}>
			</label>
		</p>
		
		<p>
			<label><span class="nocell ftitle">{$password}</span>
			<input type="password" value="" name="flogin_password" class="flogin_password"{$password_add}>
			</label>
		</p>
		
		<p>
			<span>{$submit}<button type="submit" name="flogin_submit" class="flogin_submit">{$submit_value}</button>{$submit_end}</span>
		</p>
		
		{$hook_login_form_auth}
		{$form_end}
	</form>
EOF;

# end file