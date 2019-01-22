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
	
		echo '<div class="update pos-fixed w200px pad10 pos20-r pos0-t t-center">' . t('Сохранено!') . '</div>';
	}
	else
	{
		echo '<div class="error pos-fixed w200px pad10 pos20-r pos0-t t-center">' . t('Файл не найден!') . '</div>';
	}
}

# end of file