<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if (!$result['result'])
{
	return;
}

$page_content = $data['page_content'];
$page_id = $result['result']['0'];

$uploads_temp        = mso_get_option('uploads_temp_folder', 'general', 'tempfiles');
$current_dir         = '_pages' . '/' . $page_id;

$uploads_temp_url    = getinfo('uploads_url') . $uploads_temp;
$uploads_current_url = getinfo('uploads_url') . $current_dir;
$uploads_temp_dir    = getinfo('uploads_dir') . $uploads_temp;
$uploads_current_dir = getinfo('uploads_dir') . $current_dir;

# Если в тексте страницы есть ссылка на файл во временном каталоге, меняем на постоянный адрес и обновляем страницу в БД.
if (strpos($page_content, $uploads_temp_url) !== false)
{
	$page_content = str_replace ($uploads_temp_url, $uploads_current_url, $page_content);

	$CI->db->where(array('page_id'=>$page_id) );
	$res = ($CI->db->update('page', Array('page_content' => $page_content))) ? '1' : '0';
}

# Если в метаданных (обычно прикреплённой картинке) есть ссылка на файл…
$page_meta_options = isset($data['page_meta_options']) ? $data['page_meta_options'] : '';
$page_meta_options = explode('##METAFIELD##', $page_meta_options);
foreach ($page_meta_options as $key=>$val)
{
	if (trim($val))
	{
		$meta_temp = explode('##VALUE##', $val);
		$meta_key = trim($meta_temp[0]);
		$meta_value = trim($meta_temp[1]);
		# Если ссылка, обновляем мету. Иначе ничего не делаем
		if ( strpos($meta_value, $uploads_temp_url) !== false )
		{
			$meta_value = str_replace ($uploads_temp_url, $uploads_current_url, $meta_value);
			mso_add_meta($meta_key, $page_id, 'page', $meta_value);
		}
	}
}

if( !is_dir($uploads_current_dir) )
{
	if( !is_dir(getinfo('uploads_dir').'_pages') )
	{
		@mkdir(getinfo('uploads_dir').'_pages', 0777);
	}

	@mkdir($uploads_current_dir, 0777);
	@mkdir($uploads_current_dir . '/_mso_i', 0777);
	@mkdir($uploads_current_dir . '/mini', 0777);
}

if( !is_dir($uploads_current_dir) )
{
	# Не удалось создать каталог для файлов страницы.
	return;
}

$CI->load->helper('file');
$tempfiles = get_filenames($uploads_temp_dir);

global $MSO;
$sessid = $MSO->data['session']['session_id'];

foreach ($tempfiles as $file)
{
	if (substr($file, strlen($file) - 32) == $sessid)
	{
		$file = substr($file, 0, strlen($file) - 33);

		# Если есть файлы, помеченные текущей сессией, то перемещаем их на постоянное место
		if( rename( $uploads_temp_dir . '/' . $file,  $uploads_current_dir . '/' . $file ) )
		{
			# Если получилось переместить файл, перемещаем его миниатюру
			if( file_exists($uploads_temp_dir . '/mini/' . $file) )
			{
			rename( $uploads_temp_dir . '/mini/' . $file, $uploads_current_dir . '/mini/' . $file );
			}

			# перемещаем иконку
			if( file_exists($uploads_temp_dir . '/_mso_i/' . $file) )
			{
			rename( $uploads_temp_dir . '/_mso_i/' . $file,  $uploads_current_dir . '/_mso_i/' . $file );
			}

			# удаляем файл сессии для аттача
			@unlink( $uploads_temp_dir . '/' . $file . '.' . $sessid );
		}

	}
}

# end file