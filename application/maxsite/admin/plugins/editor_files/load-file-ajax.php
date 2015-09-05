<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

if ( $post = mso_check_post(array('file')) )
{
	$file = base64_decode($post['file']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$file = getinfo('template_dir') . $file;
	
	// if (file_exists($file)) echo htmlspecialchars(file_get_contents($file));
	if (file_exists($file)) echo file_get_contents($file);
}
	
# end file