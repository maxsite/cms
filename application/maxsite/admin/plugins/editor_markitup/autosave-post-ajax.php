<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_checkreferer(); // защищаем реферер

if ( $post = mso_check_post(array('text', 'id')) )
{
	$text = $post['text'];
	$id = $post['id']; // номер записи
	
	// файл сохраняем в каталоге записи в подкаталоге /save/	
	$path = getinfo('uploads_dir') . '_pages/' . $id . '/save';
	
	if (!is_dir($path)) @mkdir($path, 0777);
	if (!is_dir($path) or !is_writable($path)) return 'ERROR PATH'; 
	
	// добавим временную метку — так получается что-то вроде истории сохранения 
	$fn = date('Y-m-d_H-i-s') . '_' . mso_md5($id) . '.txt';
	
	file_put_contents($path . '/' . $fn,  $text);
	
	echo getinfo('uploads_url') . '_pages/' . $id . '/save/' . $fn;
}
	
# end of file