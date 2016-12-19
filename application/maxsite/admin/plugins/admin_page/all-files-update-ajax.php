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

	// отсортировать файлы в обратно порядке по времени загрузки — новые в начало списка!
	$dirs0 = array();
	foreach ($dirs as $file)
	{
		if (is_array($file)) continue; // это каталог, пропускаем
		
		// ключ = время.файл чтобы учесть одно и тоже время разных файлов 
		$dirs0[filemtime($uploads_dir . '/' . $file) . $file] = $file;
	}
	
	krsort($dirs0);
	$dirs = $dirs0;
	
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
		
		$time_file = date(" | Y-m-d H:i:s", filemtime($uploads_dir . '/' . $file));
		
		
		
		if (isset($mso_descritions[$file]))
		{
			$title = $mso_descritions[$file] . ' / ' . htmlspecialchars($file);
		}
		else
		{
			$title = htmlspecialchars($file);
		}
		
		if ($this_img and file_exists($uploads_dir . '/mini/' . $file)) 
		{
			$mini = $uploads_url . '/mini/' . $file;
			
			$mini_100 = $uploads_url . '/_mso_i/' . $file;
			
			$mini = '<a class="lightbox" target="_blank" title="' . $title  . $time_file . '" href="' . $uploads_url. '/' . $file . '"><img src="' . $mini_100 . '"></a> ';
		}
		else 
		{
			$mini = '<img src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png" title="' . $title . $time_file . '">';
		}
		
		if ($this_img)
		{
			if ($title and $title != $file)
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
		
		$all_files_res .= '<div class="all-files-image">' 
					. '<div class="all-files-image-mini">' . $mini . '</div>' 
					. '<div class="all-files-image-actions"><span title="' . t('Получить URL-адрес файла') . '" onclick="jAlert(\'<textarea cols=70 rows=3>' . $uploads_url . '/' . $file . '</textarea>\', \'' . t('Адрес файла') . '\'); return false;">URL</span>';
					
		if ($this_img)			
		{
			$all_files_res .= '
					<span title="' . t('Вставить в текст код изображения') . '" onclick="addSmile(\'' . $img . '\', \'f_content\');">[img]</span>
					<span title="' . t('Вставить в текст код миниатюры') . '" onclick="addSmile(\'' . $image . '\', \'f_content\');">[image]</span>
					<span title="' . t('Использовать как изображение записи') . '" onclick="addImgPage(\'' . $uploads_url . '/' . $file . '\');">page</span>
					';
		}

		$all_files_res .= '</div></div>'; 
		
	}
	
	echo $all_files_res . '<div class="clearfix"></div>';
}

# end of file