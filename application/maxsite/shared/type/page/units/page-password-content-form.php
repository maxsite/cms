<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$form = <<<EOF
<h5><strong>{$_message}</strong></h5>
<form class="mso-form mar20-tb" method="post">
{$_sess}
<input type="hidden" name="f_page_id" value="{$_id}">
<div>{$_pass} <input type="text" name="f_password" value="" required>
<button type="submit" name="f_submit">OK</button></div>
</form>
EOF;
