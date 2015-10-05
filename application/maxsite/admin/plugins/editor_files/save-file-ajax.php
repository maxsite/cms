<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

if ( $post = mso_check_post(array('file_path', 'content')) )
{
	$file = base64_decode($post['file_path']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$file = getinfo('template_dir') . $file;
	
	// бэкап делать нужно???
	
	if (file_exists($file)) 
	{
		file_put_contents($file, $post['content']);
	
		echo '<span class="i-check mar10-l t-green t130"></span>Сохранено';
	}
	else
	{
		echo '<span class="i-check mar10-l t-red t130"></span>Файл не найден';
	}
}

# end of file