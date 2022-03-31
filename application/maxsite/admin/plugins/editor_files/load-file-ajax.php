<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

if ( $post = mso_check_post(array('file')) )
{
	$file = base64_decode($post['file']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	
    $file = mso_check_dir_file(getinfo('template_dir'), $file);
	
    if ($file and file_exists($file)) echo file_get_contents($file);
    
	// $file = getinfo('template_dir') . $file;
    // if (file_exists($file)) echo htmlspecialchars(file_get_contents($file));
}
	
# end file