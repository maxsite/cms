<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	echo <<<EOF
	<form method="post" action="{$action}" name="freg" class="freg fform">
		<input type="hidden" value="{$session_id}" name="freg_session_id">
		<input type="hidden" value="0" name="freg_rules_ok">
		<input type="hidden" value="{$redirect_url}" name="freg_redirect_url">
		
		<p>
			<label><span class="nocell ftitle">{$email}</span>
			<input type="email" name="freg_email" value="{$vreg_email}">
			</label>
		</p>
		
		<p>
			<label><span class="nocell ftitle">{$password}</span>
			<input type="password" name="freg_password" value="{$vreg_password}">
			</label>
		</p>
		
		<p>
			<label><span class="nocell ftitle">{$password_repeat}</span>
			<input type="password" name="freg_password_repeat" value="{$vreg_password_repeat}">
			</label>
		</p>		
		
		<p>
			<label><span class="nocell ftitle">{$nik}</span>
			<input type="text" name="freg_nik" value="{$vreg_nik}">
			</label>
		</p>
		
		<p>
			<label><span class="nocell ftitle">{$url}</span>
			<input type="text" name="freg_url" value="{$vreg_url}">
			</label>
		</p>
		
		<p>
			<label><input type="checkbox" name="freg_rules_ok"> {$rules_ok} {$rules}</label>
		</p>
		
		<hr>
		
		<p>
			<span><button type="submit" name="freg_submit">{$submit_value}</button></span>
		</p>
	</form>
</div><!-- /.loginform -->

EOF;

# end file