<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if (!is_login()) die('no login');
if ( !mso_check_allow('admin_files') )
{
	$res = array('error' => t('Доступ запрещен'));
	json_response( $res );
	die();
}

$CI = & get_instance();
mso_checkreferer();
global $MSO;

$options = Array();
foreach ($_REQUEST as $key => $value)
{
	$options[$key] = $value;
}

$temp_folder = mso_get_option('uploads_temp_folder', 'general', 'tempfiles');
$folder = (isset($options['current_dir']) && $options['current_dir']) ? $options['current_dir'] : $temp_folder;

$folder = preg_replace('/[\/\\\]*$/msi', '', $folder); # убираем слеш в конце строки
$is_edit = false;

# Если мы редактируем страницу, $is_edit == true и не нужно морочиться с временным каталогом и файлами сессий.
if ($folder != $temp_folder)
{
	# Проверяем, что это действительно каталог вида _pages/###
	if ( preg_match ('/_pages[\/\\\]\d+$/msi', $folder) )
	{
		$is_edit = true;
	}
}

if (!$is_edit)
{
	$folder = $temp_folder;
}

if( !is_dir(getinfo('uploads_dir') . $folder) ) # Проверка существования папки для закачивания
{
	@mkdir(getinfo('uploads_dir') . $folder ,  0777); # пробуем создать
	@mkdir(getinfo('uploads_dir') . $folder . '/_mso_i', 0777);
	@mkdir(getinfo('uploads_dir') . $folder . '/mini', 0777);
}

# Обработка аплоада
if( !( isset($_REQUEST['_method']) && $_REQUEST['_method'] == 'DELETE' ) && !isset($_REQUEST['_session']) && !isset($_REQUEST['_size']) )
{
	require_once( getinfo('common_dir') . 'uploads.php' );

	$msg = '';

	$mso_upload_ar1 = array(
			'upload_path' => getinfo('uploads_dir').$folder,
			'allowed_types' => mso_get_option(
											'allowed_types',
											'general',
											'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz'
											),
			'overwrite' => false, #true,
		);

	$mso_upload_ar2 = array( // массив прочих опций
			'userfile_title' => '', // описание файла
			'fn_mso_descritions' => getinfo('uploads_dir').$folder.'/_mso_i/_mso_descriptions.dat', // файл для описаний
			'userfile_resize' => false, // нужно ли менять размер
			'userfile_water' => false, // нужен ли водяной знак
			'userfile_mini' => false, // делать миниатюру?
			'prev_size' => ( isset($options['upload_preview_width']) && $options['upload_preview_width'] > 0 ) ? $options['upload_preview_width'] : NULL , #  задаём размер превьюшки
			'message1' => '', // не выводить сообщение о загрузке каждого файла
		);

	if( isset($options['resize_img']) && $options['resize_img'] )
	{
		$mso_upload_ar2['userfile_resize'] = true;
		$mso_upload_ar2['userfile_resize_size'] = ( isset($options['resize_img_size']) && $options['resize_img_size'] <> '' ) ? $options['resize_img_size'] : mso_get_option('resize_images', 'general', '600');
	}

	if( isset($options['create_mini']) && $options['create_mini'] )
	{
		$mso_upload_ar2['userfile_mini'] = true;
		$mso_upload_ar2['userfile_mini_size'] = ( isset($options['create_mini_size']) && $options['create_mini_size'] <> '' ) ? $options['create_mini_size'] : mso_get_option('size_image_mini', 'general', '600');
		$mso_upload_ar2['mini_type'] = ( isset($options['image_mini_type']) && $options['image_mini_type'] <> '' ) ? $options['image_mini_type'] : mso_get_option('image_mini_type', 'general', '1');
	}

	if( isset($options['use_watermark']) && $options['use_watermark'] )
	{
		$mso_upload_ar2['userfile_water'] = true;
		$mso_upload_ar2['watermark_type'] = ( isset($options['watermark_type']) && $options['watermark_type'] <> '' ) ? $options['watermark_type'] : mso_get_option('watermark_type', 'general', '1');

		$water_file = getinfo('plugins_dir').basename(dirname(__FILE__)).'/images/'.'watermark.png';
		if( file_exists($water_file) )
		{
			$mso_upload_ar2['userfile_water_file'] = $water_file;
		}
		else
		{
			$water_file = getinfo('uploads_dir').'watermark.png';
			if( file_exists($water_file) )
			{
				$mso_upload_ar2['userfile_water_file'] = $water_file;
			}
			else
			{
				$mso_upload_ar2['userfile_water_file'] = false;
			}
		}
	}

	ob_start();
	$res = mso_upload($mso_upload_ar1, 'attach', $mso_upload_ar2);
	$msg = ob_get_contents();
	ob_end_clean();

	if( !$msg && $res )
	{
		$up_data = $CI->upload->data();

		# формирование ответа при загрузке нового файла
		$out = array(
						'name'	=> $up_data['file_name'], # $_FILES['attach']['name'],
						'size'	=> $up_data['file_size'], # $_FILES['attach']['size'],
						'type'	=> $up_data['file_type'], # $_FILES['attach']['type'],
						'deleteUrl'	=> getinfo('site_url').basename(dirname(__FILE__)).'/upload?file='.$up_data['file_name'].'&_method=DELETE', # $_FILES['attach']['name']
						'deleteType'=> "POST",
						'deleteWithCredentials'	=> true,
					);

		# Если мы не редактируем страницу, то создаём файл с идентификатором сессии
		if (!$is_edit)
		{
			touch( getinfo('uploads_dir').$folder.'/'.$up_data['file_name'].'.'.$MSO->data['session']['session_id'] ); # $_FILES['attach']['name']
		}
	}
	else
	{
		# Ошибка загрузки файла
		$out = array(
						'name'	=> $_FILES['attach']['name'],
						'size'	=> $_FILES['attach']['size'],
						'type'	=> $_FILES['attach']['type'],
						'error'	=> strip_tags($msg),
					);
	}
	$res = array('attach' => array($out));
}
elseif( isset($_REQUEST['_session']))
{
	if (!$is_edit)
	{
		if( $MSO->data['session']['session_id'] == $_REQUEST['_session'] && touch(getinfo('uploads_dir').$folder.'/'.$MSO->data['session']['session_id'].".sessid") )
		{
			# Время модификации файла маркера сессий было изменено на текущее
			$res = array('success' => true);
			//require( getinfo('plugins_dir').'submit_article/clean.php' ); # подключили файл для чистки мусора
		}
		else
		{
			$res = array('error' => 'Произошла ошибка во время проверки сессии!');
		}
	}
	else
	{
		$res = array('success' => true);
	}
}

json_response( $res );

die();

###
function json_response( $resp )
{
	header('Content-type: application/json');
	header('Pragma: no-cache');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Content-Disposition: inline; filename="attach.json"');
	header('X-Content-Type-Options: nosniff');        // Prevent Internet Explorer from MIME-sniffing the content-type:

	echo json_encode($resp);
}

# End file