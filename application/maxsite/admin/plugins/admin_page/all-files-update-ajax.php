<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// проверим залогиненность
if (!is_login()) die('no login');

// проверим разрешение на редактирование записей
if (!mso_check_allow('admin_page_edit')) die('no allow');


if ( $post = mso_check_post(array('dir', 'deletefile')) )
{
    mso_checkreferer(); // защищаем реферер
    
    $current_dir = $post['dir'];
    $deletefile = $post['deletefile'];
    $uploads_dir = getinfo('uploads_dir') . $current_dir . '/';
    
    $file = mso_check_dir_file($uploads_dir, $deletefile);
    
    if ($file) {
        @unlink($file);
		
		$mini = mso_check_dir_file($uploads_dir . 'mini/', $deletefile);
		$mini_100 = mso_check_dir_file($uploads_dir . '_mso_i/', $deletefile);
		
        if ($mini) @unlink($mini);
        if ($mini_100) @unlink($mini_100);
    }
}

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
    
    foreach ($dirs as $file)
    {
        if (is_array($file)) continue; // каталог — это массив — нам здесь не нужен
        
        $title = '';
        
        $ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
        
        $this_img = ($ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png');
        
        $time_file = date(" | Y-m-d H:i:s", filemtime($uploads_dir . '/' . $file));
        
        $title = htmlspecialchars($file);
        
        $u_file = $mini = $mini_100 = $uploads_url. '/' . $file;
        
        // адрес относительно сайта
        $u_file_site = str_replace(getinfo('site_url'), '', $u_file);
        
        if ($this_img) 
        {
            if (file_exists($uploads_dir . '/_mso_i/' . $file))
                $mini_100 = $mini = $uploads_url . '/_mso_i/' . $file;
            
            if (file_exists($uploads_dir . '/mini/' . $file))
                $mini = $uploads_url . '/mini/' . $file;
        }
        
        if ($this_img) 
        {
            $mini_html = '<a class="lightbox" target="_blank" title="' . $title  . $time_file . '" href="' . $u_file . '"><img class="w100px-max" src="' . $mini_100 . '"></a> ';
            
            $img = '[img]' . $u_file . '[/img]';
            
            $image = '[image=' . $mini . ']' . $u_file . '[/image]';
        }
        else 
        {
            $mini_html = '<a target="_blank" href="' . $u_file . '"><img src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png" title="' . $title . $time_file . '"></a>';
        }
        
        $all_files_res .= '<div class="all-files-image">' 
                    . '<div class="all-files-image-mini">' . $mini_html . '<div class="mar5-t"><span title="' . t('Получить URL-адрес файла') .' - ' . $u_file . '" onclick="jAlert(\'<textarea cols=70 rows=3>' . $u_file . '</textarea>\', \'' . t('Адрес файла') . '\'); return false;">URL</span></div></div>' 
                    . '<div class="all-files-image-actions">';
                    
        if ($this_img)          
        {
            $all_files_res .= '
                    <span title="' . t('Вставить в текст код изображения') . '" onclick="addSmile(\'' . $img . '\', \'f_content\');">[img]</span>
                    <span title="' . t('Вставить в текст код миниатюры') . '" onclick="addSmile(\'' . $image . '\', \'f_content\');">[image]</span>
                    <span title="' . t('Использовать как изображение записи') . '" onclick="addImgPage(\'' . $u_file_site . '\');">page</span>
                    ';
        }
        else
        {
            $all_files_res .= '
                    <span title="' . t('Вставить в текст адрес файла') . '" onclick="addSmile(\'' . $u_file . '\', \'f_content\');">Link</span>
                    ';
        }

        $all_files_res .= '
                    <span title="' . t('Удалить файл') . '" onclick="del_file(\'' . $file .'\')">' . t('Удалить') . '</span>
                    ';
                    
        $all_files_res .= '</div></div>'; 
        
    }
    
    echo $all_files_res . '<div class="clearfix"></div>';
}

# end of file