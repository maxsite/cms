<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// проверим залогиненность
if (!is_login()) die('no login');

// проверим разрешение на редактирование записей
if (!mso_check_allow('admin_page_edit')) die('no allow');

if ( $post = mso_check_post(array('dir')) )
{
	mso_checkreferer(); // защищаем реферер
	
	$current_dir = $post['dir'];

	$all_files_res = '';

	$uploads_dir = getinfo('uploads_dir') . $current_dir;
	$uploads_url = getinfo('uploads_url') . $current_dir;
	
	
	$CI = & get_instance();
	$CI->load->helper('directory');
	$CI->load->helper('file');
	
	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, 2); // только в текущем каталоге

	if (!$dirs) $dirs = array();

	asort($dirs);
	
	
	$fn_mso_descritions = $uploads_dir . '/_mso_i/_mso_descriptions.dat';
	if (file_exists( $fn_mso_descritions )) 
	{
		// массив данных: fn => описание )
		// получим из файла все описания
		$mso_descritions = unserialize( read_file($fn_mso_descritions) );
	}
	else $mso_descritions = array();
	
	foreach ($dirs as $file)
	{
		if (is_array($file)) continue; // каталог — это массив — нам здесь не нужен
		
		$title = $title_f = '';
		
		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		
		$this_img = ($ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png');
		
		
		if (isset($mso_descritions[$file]))
		{
			$title = $mso_descritions[$file];
			if ($title) $title_f = '<em>' . htmlspecialchars($title) . '</em><br>';
		}
		
		if ($this_img and file_exists($uploads_dir . '/mini/' . $file)) 
		{
			$mini = $uploads_url . '/mini/' . $file;
			$mini = '<a target="_blank" href="' . $uploads_url. '/' . $file . '"><img class="left" src="' . $mini . '" title="' . $title . '"></a> ';
		}
		else 
		{
			$mini = '<img class="left" src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png">';
		}
		
		if ($this_img)
		{
			if ($title)
			{
				$img = '[img ' . $title . ']' . $uploads_url . '/' . $file . '[/img]';
				
				$image = '[image=' . $uploads_url . '/mini/' . $file . ' ' . $title . ']' . $uploads_url . '/' . $file . '[/image]';
			}
			else
			{
				$img = '[img]' . $uploads_url . '/' . $file . '[/img]';
				
				$image = '[image=' . $uploads_url . '/mini/' . $file . ']' . $uploads_url . '/' . $file . '[/image]';
			}
		}
		
		$all_files_res .= '<hr><p>' 
					. $mini 
					. $title_f 
					. '<input class="w75" title="' . t('Адрес файла') . '" value="' . $uploads_url . '/' . $file . '">';
					
		if ($this_img)			
		{
			$all_files_res .= 
					  '<br><input class="w75" title="' . t('Изображение') . '" value="' . $img . '">'
					. '<br><input class="w75" title="' . t('Миниатюра') . '" value="' . $image . '">';
		}
		
		$all_files_res .= '</p><div class="clearfix"></div>'; 
		
	}
	
	echo $all_files_res;
}

# end file