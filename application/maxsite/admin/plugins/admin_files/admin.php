<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<h1><?= t('Загрузки. Файлы. Галереи') ?></h1>
<p class="info"><?= t('Здесь вы можете выполнить необходимые операции с файлами.') ?></p>

<?php

	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	
	$CI->load->helper('directory');
	$CI->load->helper('form');

	// разрешенные типы файлов
	$allowed_types = mso_get_option('allowed_types', 'general', 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz');

	// по сегменту определяем текущий каталог в uploads
	// если каталога нет, скидываем на дефолтный ''

	$current_dir = $current_dir_h2 = mso_segment(3);
	if ($current_dir) $current_dir .= '/';

	$path = getinfo('uploads_dir') . $current_dir;
	if ( ! is_dir($path) ) // нет каталога
	{
		$path = getinfo('uploads_dir');
		$current_dir = $current_dir_h2 = '';
	}
	else
	{
		if ($current_dir_h2) $current_dir_h2 = '/' . $current_dir_h2;
	}

	//echo '<h2>' . t('Текущий каталог:') . ' uploads' . $current_dir_h2 . '</h2>';
	
	
	


	# новый каталог - создаем до того, как отобразить навигацию
	if ( $post = mso_check_post(array('f_session3_id', 'f_cat_name', 'f_newcat_submit')) )
	{
		mso_checkreferer();

		$f_cat_name = mso_slug($post['f_cat_name']);

		if (!$f_cat_name)
			echo '<div class="error">' . t('Нужно ввести имя каталога') . '</div>';
		else
		{
			$new_dir = getinfo('uploads_dir') . $f_cat_name;

			if ( is_dir($new_dir) ) // уже есть
			{
				echo '<div class="error">' . t('Такой каталог уже есть!') . '</div>';
			}
			else
			{
				@mkdir($new_dir, 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/_mso_i', 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/mini', 0777); // нет каталога, пробуем создать
				echo '<div class="update">' . sprintf(t('Каталог <strong>%s</strong> создан!'), $f_cat_name)
					. '</div>';
			}
		}
	}


	// нужно вывести навигацию по каталогам в uploads
	$all_dirs = directory_map(getinfo('uploads_dir'), true); // только в uploads
	asort($all_dirs);
	$out = '';
	
	
	echo '<p class="admin_files_nav"><b>' . t('Каталог:') . '</b> ';
	
	echo '<select class="admin_file_filtr">';
	
	$selected = (mso_segment(3)) ? '' : ' selected';
	
	echo '<option value="' . getinfo('site_admin_url') . 'files"' . $selected . '>uploads</option>';
	
	foreach ($all_dirs as $d)
	{
		// это каталог
		if (is_dir( getinfo('uploads_dir') . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles')
		{
			
			$selected = (mso_segment(3) == $d) ? ' selected' : '';
			
			echo '<option value="' . getinfo('site_admin_url'). 'files/' . $d .'"' . $selected . '>' . $d . '</option>';
			
			
			/*
			if (mso_segment(3) == $d)
				$out .= '<a href="'. $MSO->config['site_admin_url'] . 'files/' . $d . '"><strong>' . $d . '</strong></a> | ';
			else
				$out .= '<a href="'. $MSO->config['site_admin_url'] . 'files/' . $d . '">' . $d . '</a> | ';
			*/
		}
	}
	/*
	if ($out)
	{
		if (!mso_segment(3))
			$out = '<a href="' . $MSO->config['site_admin_url'] . 'files"><strong>uploads</strong></a> | ' . $out;
		else
			$out = '<a href="' . $MSO->config['site_admin_url'] . 'files">uploads</a> | ' . $out;

		$out = '<div class="admin_files_nav"><span>' . t('Навигация:') . '</span> ' . $out . '</div>';
		echo $out;
	}
	
	*/
	echo '</select></p>';
	
	
	//  переход на указанный url
	echo '<script>
	$("select.admin_file_filtr").change(function(){
		window.location = $(this).val();
	});
	</script>';
	
	
	// нужно создать в этом каталоге _mso_i и mini если нет
	if ( ! is_dir($path . '_mso_i') ) @mkdir($path . '_mso_i', 0777); // нет каталога, пробуем создать
	if ( ! is_dir($path . 'mini') ) @mkdir($path . 'mini', 0777); // нет каталога, пробуем создать



	// описания файлов хранятся в виде серилизованного массива в
	// uploads/_mso_i/_mso_descritions.dat
	$fn_mso_descritions = $path . '_mso_i/_mso_descriptions.dat';

	if (!file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
		write_file($fn_mso_descritions, serialize(array())); // записываем в него пустой массив

	if (file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
	{
		// массив данных: fn => описание )
		$mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	}
	else $mso_descritions = array();

	# Добавление Рамира -  редактирование описания
	if ( $post = mso_check_post(array('f_session_id', 'f_file_name', 'f_file_description', 'f_edit_submit')) )
	{
		mso_checkreferer();

			// удалим описание из _mso_i/_mso_descriptions.dat
			unset($mso_descritions[$post['f_file_name']]);
			$mso_descritions[$post['f_file_name']]=$post['f_file_description'];
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем файл

		echo '<div class="update">' . t('Описание обновлено!') . '</div>';
	}
	# Конец Добавление Рамира

	# удаление выделенных файлов
	if ( $post = mso_check_post(array('f_session_id', 'f_check_files', 'f_delete_submit')) )
	{
		mso_checkreferer();

		foreach ($post['f_check_files'] as $file)
		{
			@unlink(getinfo('uploads_dir') . $current_dir . $file);
			@unlink(getinfo('uploads_dir') . $current_dir . '_mso_i/' . $file);
			@unlink(getinfo('uploads_dir') . $current_dir . 'mini/' . $file);

			// удалим описание из _mso_i/_mso_descriptions.dat
			unset($mso_descritions[$file]);
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем файл
		}
		echo '<div class="update">' . t('Выполнено') . '</div>';
	}


	# обновление всех миниатюр в каталоге
	if ( $post = mso_check_post(array('f_session2_id', 'f_update_mini_submit')) )
	{
		mso_checkreferer();
		
		require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 

		// получаем все файлы в каталоге
		$uploads_dir = getinfo('uploads_dir') . $current_dir;

		// все файлы в массиве $dirs
		$dirs = directory_map($uploads_dir, true); // только в текущем каталоге
		if (!$dirs) $dirs = array();

		$allowed_ext = explode('|', $allowed_types);

		foreach ($dirs as $file)
		{
			if (@is_dir($uploads_dir . $file)) continue; // это каталог
			$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
			if (!in_array($ext, $allowed_ext)) continue; // запрещенный тип файла
			
			if ($ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png')
			{
				$up_data = array();
				$up_data['full_path'] = $uploads_dir . $file;
				$up_data['file_path'] = $uploads_dir;
				$up_data['file_name'] = $file;
				
				$r = array();
				$r['userfile_mini'] = 1; // делать миниатюру
				$r['userfile_mini_size'] = $post['f_userfile_mini_size'];
				$r['mini_type'] = $post['f_mini_type'];
				$r['prev_size'] = 100;
				
				mso_upload_mini($up_data, $r); // миниатюра 
				mso_upload_prev($up_data, $r); // превьюшка
			}
		}

		echo '<div class="update">' . t('Выполнено') . '</div>';
	}
	
	# загрузка нового файла
	if ( $post = mso_check_post(array('f_session2_id', 'f_upload_submit')) )
	{
		mso_checkreferer();
		
		require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
		
		// параметры для mso_upload
		$mso_upload_ar1 = array( // конфиг CI-библиотеки upload
				'upload_path' => getinfo('uploads_dir') . $current_dir,
				'allowed_types' => $allowed_types,
			);
			
		$mso_upload_ar2 = array( // массив прочих опций
				'userfile_title' => $post['f_userfile_title'], // описание файла
				'fn_mso_descritions' => $fn_mso_descritions, // файл для описаний
				'userfile_resize' => isset($post['f_userfile_resize']), // нужно ли менять размер
				'userfile_resize_size' => $post['f_userfile_resize_size'], // размер
				'userfile_water' => isset($post['f_userfile_water']), // нужен ли водяной знак
				'userfile_water_file' => getinfo('uploads_dir') . 'watermark.png', // файл водяного знака
				'water_type' => $post['f_water_type'], // тип водяного знака
				'userfile_mini' => isset($post['f_userfile_mini']), // делать миниатюру?
				'userfile_mini_size' => $post['f_userfile_mini_size'], // размер миниатюры
				'mini_type' => $post['f_mini_type'], // тип миниатюры
				'prev_size' => 100, // размер превьюхи
				'message1' => '', // не выводить сообщение о загрузке каждого файла
				// 'message2' => '',
				
			);
		
		// запомним указанные размеры и выставим их для полей формы вновь
		$f_userfile_resize = isset($post['f_userfile_resize']);
		$f_userfile_resize_size = $post['f_userfile_resize_size'];
		$f_userfile_water = isset($post['f_userfile_water']);
		$f_water_type = $post['f_water_type'];
		$f_userfile_mini = isset($post['f_userfile_mini']);
		$f_userfile_mini_size = $post['f_userfile_mini_size'];
		$f_mini_type = $post['f_mini_type'];		
		
		
		// подготовим массив $_FILES - у нас множественная загрузка
		$new_files = mso_prepare_files('f_userfile');
		
		$res = false; // результат загрузки
		// формируем поэлементно с загрузкой файлов
		foreach ($new_files as $key => $val)
		{
			$_FILES[$key] = $val; // формируем $_FILES для одиночного файла
			$res = mso_upload($mso_upload_ar1, $key, $mso_upload_ar2);
			unset($_FILES[$key]);
		}
		
		if ($res) echo '<div class="update">' . t('Загрузка выполнена') . '</div>';
			else echo '<div class="error">' . t('Возникли ошибки при загрузке') . '</div>';
		
		
		// после загрузки сразу обновим массив описаний - он ниже используется
		if (file_exists( $fn_mso_descritions )) // файла нет, нужно создать массив
		{
			// массив данных: fn => описание )
			$mso_descritions = unserialize(read_file($fn_mso_descritions)); // получим из файла все описания
		}
		else $mso_descritions = array();
		
	}

	// форма нового каталога
	echo '
		<div class="new_cat_upload">
		<form method="post">' . mso_form_session('f_session3_id') .
		'<p><b>'. t('Новый каталог'). ':</b> <input type="text" name="f_cat_name" value="">
		<input type="submit" name="f_newcat_submit" value="'. t('Создать'). '" onClick="if(confirm(\'' . t('Создать каталог в uploads?') . '\')) {return true;} else {return false;}" ></p>
		</form></div>';

	// размер
	if (!isset($f_userfile_resize_size)) // это значение было введено при загрузке предудущего файла
		$resize_images = (int) mso_get_option('resize_images', 'general', 600);
	else
		$resize_images = $f_userfile_resize_size;

	if ($resize_images < 1) $resize_images = 600;
	
	// менять размер?
	if (!isset($f_userfile_resize) or $f_userfile_resize)
		$f_userfile_resize = ' checked="checked"';
	else 
		$f_userfile_resize = '';
	
	// миниатюра
	if (!isset($f_userfile_mini_size)) 
		$size_image_mini = (int) mso_get_option('size_image_mini', 'general', 150);
	else
		$size_image_mini = $f_userfile_mini_size;
	
	if ($size_image_mini < 1) $size_image_mini = 150;
	
	
	if (!isset($f_userfile_mini) or $f_userfile_mini) 
		$f_userfile_mini = ' checked="checked"';
	else
		$f_userfile_mini = '';
	
	// водяной знак
	if (!isset($f_water_type)) 
		$watermark_type = mso_get_option('watermark_type', 'general', 1);
	else
		$watermark_type = $f_water_type;
		
	if (!isset($f_userfile_water)) 
		$use_watermark = mso_get_option('use_watermark', 'general', 0);
	else
		$use_watermark = $f_userfile_water;
	
	// тип миниатюры
	if (!isset($f_mini_type)) 
		$mini_type = mso_get_option('image_mini_type', 'general', 1);
	else 
		$mini_type = $f_mini_type;
	
	$admin_files_field_count = (int) mso_get_option('admin_files_field_count', 'general', 3);
	if ($admin_files_field_count < 1) $admin_files_field_count = 3;
	if ($admin_files_field_count > 50) $admin_files_field_count = 50;
	
	
	
	// форма загрузки
	echo '
		<div class="upload_file">
		<h2>' . t('Загрузка файлов') . '</h2>
		<p>' . t('Для загрузки файла нажмите кнопку «Обзор», выберите файл на своем компьютере. После этого нажмите кнопку «Загрузить». Размер файла не должен превышать') . ' ' . ini_get ('post_max_size') . '.</p>
		<form method="post" enctype="multipart/form-data" class="admin_uploads_form">' . mso_form_session('f_session2_id') .
		'<p>';
	
	for ($i = 1; $i <= $admin_files_field_count; $i++)
	{
		echo '<input type="file" name="f_userfile[]" size="90">';
		if ($i < $admin_files_field_count) echo '<br>';
	}	
	
	
	
	echo '&nbsp;<input type="submit" name="f_upload_submit" value="' . t('Загрузить') . '">&nbsp;<input type="reset" value="' . t('Сбросить') . '"></p>
		<p>' . t('Описание файла:') . ' <input type="text" name="f_userfile_title" class="description_file" value="" size="80"></p>

		<p><label><input type="checkbox" name="f_userfile_resize" ' . $f_userfile_resize . 'value=""> ' . t('Для изображений изменить размер до') . '</label>
			<input type="text" name="f_userfile_resize_size" style="width: 50px" maxlength="4" value="' . $resize_images . '"> ' . t('px (по максимальной стороне).') . '</p>

		<p><label><input type="checkbox" name="f_userfile_mini" ' . $f_userfile_mini . 'value=""> ' . t('Для изображений сделать миниатюру размером') . '</label>
			<input type="text" name="f_userfile_mini_size" style="width: 50px" maxlength="4" value="' . $size_image_mini . '"> ' . t('px (по максимальной стороне).') . ' <br><em>' . t('Примечание: миниатюра будет создана в каталоге') . ' <strong>uploads/' . $current_dir . 'mini</strong></em></p>


		<p>' . t('Миниатюру делать путем:') . ' <select name="f_mini_type">
		<option value="1"'.(($mini_type == 1)?(' selected="selected"'):('')).'>' . t('Пропорционального уменьшения') . '</option>
		<option value="2"'.(($mini_type == 2)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) по центру') . '</option>
		<option value="3"'.(($mini_type == 3)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с левого верхнего края') . '</option>
		<option value="4"'.(($mini_type == 4)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с левого нижнего края') . '</option>
		<option value="5"'.(($mini_type == 5)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с правого верхнего края') . '</option>
		<option value="6"'.(($mini_type == 6)?(' selected="selected"'):('')).'>' . t('Обрезки (crop) с правого нижнего края') . '</option>
		<option value="7"'.(($mini_type == 7)?(' selected="selected"'):('')).'>' . t('Уменьшения и обрезки (crop) в квадрат') . '</option>
		</select>
		
		&nbsp;<input type="submit" name="f_update_mini_submit" value="' . t('Обновить миниатюры') . '" onClick="if(confirm(\'' . t('Обновить старые миниатюры (создать для тех файлов, у которых их нет) для всех изображений каталога?') . '\')) {return true;} else {return false;}" >
		
		</p>

		<p><label><input type="checkbox" name="f_userfile_water" value="" '
			. ((file_exists(getinfo('uploads_dir') . 'watermark.png')) ? '' : ' disabled="disabled"') 
			. ($use_watermark ? (' checked="checked"') : (''))
			. '> ' . t('Для изображений установить водяной знак') . '</label>
			<br><em>' . t('Примечание: водяной знак должен быть файлом <strong>watermark.png</strong> и находиться в каталоге') . ' <strong>uploads</strong></em></p>

		<p>' . t('Водяной знак устанавливается:') . ' <select name="f_water_type">
		<option value="1"'.(($watermark_type == 1)?(' selected="selected"'):('')).'>' . t('По центру') . '</option>
		<option value="2"'.(($watermark_type == 2)?(' selected="selected"'):('')).'>' . t('В левом верхнем углу') . '</option>
		<option value="3"'.(($watermark_type == 3)?(' selected="selected"'):('')).'>' . t('В правом верхнем углу') . '</option>
		<option value="4"'.(($watermark_type == 4)?(' selected="selected"'):('')).'>' . t('В левом нижнем углу') . '</option>
		<option value="5"'.(($watermark_type == 5)?(' selected="selected"'):('')).'>' . t('В правом нижнем углу') . '</option>
		</select></p>
		</form>
		</div><hr>
		';

	// как выводим файлы
	$admin_view_files = mso_get_option('admin_view_files', 'general', 'mini');
	$admin_sort_files = mso_get_option('admin_sort_files', 'general', 'name_asc');
	
	
	if ($admin_view_files == 'table')
	{
		$CI->load->library('table');
		$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="110">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

		$CI->table->set_template($tmpl); // шаблон таблицы
		// заголовки
		$CI->table->set_heading('&bull;', t('Коды для вставки'));
	}

	// проходимся по каталогу аплоада и выводим их списком

	$uploads_dir = getinfo('uploads_dir') . $current_dir;
	$uploads_url = getinfo('uploads_url') . $current_dir;

	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, true); // только в текущем каталоге

	if (!$dirs) $dirs = array();
	
	
	// сортировка файлов
	$dirs0 = array();
	$i = 1; // счетчик для случаев, если время файлов совпадает
	foreach ($dirs as $file)
	{
		if (@is_dir($uploads_dir . $file)) continue; // это каталог
		$dirs0[filemtime($uploads_dir . $file) . 'START' . $i . 'END'] = $file;
		$i++;
	}
	$dirs = $dirs0;
	
		
	if ($admin_sort_files == 'name_asc') // по имени
		asort($dirs);
	elseif ($admin_sort_files == 'name_dest') // по имени обратно
		arsort($dirs);
	elseif ($admin_sort_files == 'date_asc') // по дате
		ksort($dirs);
	else 
		krsort($dirs); // по дате обратно


	$allowed_ext = explode('|', $allowed_types);

	$out_all = ''; // весь вывод

	foreach ($dirs as $datefile=>$file)
	{
		if (@is_dir($uploads_dir . $file)) continue; // это каталог
		
		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла

		$cod = '<p>';
		$title = '';
		$title_f = '';
		
		if (isset($mso_descritions[$file]))
		{
			$title = $mso_descritions[$file];
			if ($title) $title_f = '<br><em>' . htmlspecialchars($title) . '</em>';
		}
		
		if (!$title) $title = $file;
		
		$datefile = preg_replace('!START(.*)END!', '', $datefile);

		$sel = '<label title="' . htmlspecialchars($title) . '">'. form_checkbox('f_check_files[]', $file, false,
			'class="f_check_files"')
			. ' ' . $file . $title_f . '</label>'
			. '<br><i class="date">' . date("Y-m-d H:i:s", $datefile) . '</i>';
			

		$cod1 = stripslashes(htmlspecialchars( $uploads_url . $file ) );

		# if ($title) $cod .= '<input type="text" style="width: 300px;" value="' . $title . '">';


		# $cod .= '<p><input type="text" style="width: 99%;" value="' . $cod1 . '">';

		$cod .= '<a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=4>' . $cod1 . '</textarea>\', \'' . t('Адрес файла') . '\'); return false;">' . t('Адрес') . '</a>';

		# $cod .= '<p><textarea style="width: 99%;">' . $cod1 . '</textarea>';

		/*
		//Если картинка - делаем ссылку превьюшкой, иначе титлом или именем файла.
		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  ) {
			$cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . 'mini/' . $file . '"></a>') );
		} else {
			if ($title) $cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $title . '</a>') );
				else $cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $file . '</a>') );
		}*/
		
		$title_alt = str_replace('"', '&amp;quot;', $title);
		$title_alt = str_replace('<', '&amp;lt;', $title_alt);
		$title_alt = str_replace('>', '&amp;gt;', $title_alt);
		$title_alt = str_replace('\'', '&amp;#039;', $title_alt);
	
		//Если картинка - делаем ссылку превьюшкой, иначе титлом или именем файла.
		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  ) 
		{
			$title_alt = str_replace('"', '&amp;quot;', $title);
			$title_alt = str_replace('<', '&amp;lt;', $title_alt);
			$title_alt = str_replace('>', '&amp;gt;', $title_alt);
			$title_alt = str_replace('\'', '&amp;#039;', $title_alt);
			
			// проверим есть ли миниатюра
			if (file_exists($uploads_dir . 'mini/' . $file)) $mini = 'mini/';
				else $mini = '';
			
			if ($title) 
				$cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . $mini . $file . '" alt="' . $title_alt . '" title="' . $title_alt . '"></a>') );
			else 
				$cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . $mini . $file . '" alt=""></a>') );
		}
		else 
		{
			if ($title) 
				$cod2 = stripslashes(htmlspecialchars( '<a title="' . $title_alt . '" href="' . $uploads_url . $file . '">' . $title . '</a>') );
			else 
				$cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $file . '</a>') );
		}
      
      
		$cod .= ' | <a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=5>' . $cod2 . '</textarea>\', \'' . t('HTML-ссылка файла') . '\'); return false;">' . t('HTML-ссылка') . '</a>';


		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  )
		{
			if (file_exists( $uploads_dir . '_mso_i/' . $file  )) 
			{
				$_f = '_mso_i/' . $file;
				$cod_prev = $uploads_url . '_mso_i/' . $file;
			}
			else 
			{
				$_f = $file;
				$cod_prev = '';
			}

			if (file_exists( $uploads_dir . 'mini/' . $file  ))
				$file_mini = '=' . $uploads_url . 'mini/' . $file;
			else $file_mini = '=' . $uploads_url . $file;


			if ($title)
			{
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ' ' . trim(str_replace('\'', '&#039;', $title)) . ']' . $uploads_url . $file . '[/image]') );
				
				// [img название]адрес[/img]
				$cod4 = stripslashes(htmlspecialchars( '[img ' . trim(str_replace('\'', '&#039;', $title)) . ']' . $uploads_url . $file . '[/img]') );
			}
			else
			{
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ']' . $uploads_url . $file . '[/image]') );
				
				// [img]адрес[/img]
				$cod4 = stripslashes(htmlspecialchars( '[img]' . $uploads_url . $file . '[/img]') );
			}

			$cod .= '<br><a href="#" onClick = "jAlert(\'<textarea cols=60 rows=6>' . $cod3 . '</textarea>\', \'' . t('Код [image] файла') . '\'); return false;">[image]</a>';
			
			
			$cod .= ' | <a href="#" onClick = "jAlert(\'<textarea cols=60 rows=6>' . $cod4 . '</textarea>\', \'' . t('Код [img] файла') . '\'); return false;">[img]</a>';
			
			if ($cod_prev)
				$cod .= '<br><a href="#" onClick = "jAlert(\'<textarea cols=60 rows=6>' . $cod_prev . '</textarea>\', \'' . t('Адрес превью (100x100)') . '\'); return false;">' . t('Превью (100x100)') . '</a>';			

			$predpr = '<a class="lightbox" href="' . $uploads_url . $file . '" target="_blank" title="' . htmlspecialchars($title) . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . $uploads_url . $_f . '"></a>';

		}
		else
		{
			if ( $ext == 'mp3')
			{
				$predpr = '<a href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . getinfo('admin_url') . 'plugins/admin_files/mp3.png"></a>';

				$cod .= ' | <a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=6>' . stripslashes(htmlspecialchars( '[audio=' . $uploads_url . $file . ']') ) . '</textarea>\', \'' . t('Код [audio] файла') . '\'); return false;">' . t('Код [audio]') . '</a>';

			}
			else
			{
				$predpr = '<a href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png"></a>';
			}
		}

		// nicothin добавил:
		$cod .= '<br><a href="#" class="edit_descr_link" onClick="return false;">' . t('Изменить описание') . '</a>';
		// конец добавления

		$out_all .= '<div class="cornerz"><div class="wrap">' . $sel . $predpr . $cod . '</div></div>';

		if ($admin_view_files == 'table') $CI->table->add_row($predpr, $sel . $cod);
	}

	// добавляем форму, а также текущую сессию
	if ($out_all != '') 
	{
		echo '<form method="post">' . mso_form_session('f_session_id');
		if ($admin_view_files == 'table') 
			echo $CI->table->generate(); // вывод подготовленной таблицы
		else
		{
			echo '<div class="float-parent" style="width:100%">';
			echo $out_all;
			echo '<div style="clear:both"></div></div>';
		}

		echo '<p class="br"><input type="submit" name="f_delete_submit" value="' . t('Удалить') . '" onClick="if(confirm(\'' . t('Выделенные файы будут безвозвратно удалены! Удалять?') . '\')) {return true;} else {return false;}" ></p>
			<p class="br"><input type="button" id="check-all" value="' . t('Инвертировать выделение') . '"></p>
			</form>';

		$n = '\n';
		$up = $uploads_url;

		$mess = t('Предварительно нужно выделить файлы для галереи');
		$session = mso_form_session('f_session_id');
		$save_button = t('Сохранить');

		echo <<<EOF
<script>
function toggleAll() {
	var allCheckboxes = $("input.f_check_files:enabled");
	var notChecked = allCheckboxes.not(':checked');
	allCheckboxes.removeAttr('checked');
	notChecked.attr('checked', 'checked');
}

$(function()
{
	$("#check-all").click(function(){
		toggleAll()
	});

	//nicothin добавления
	if ($('script[src$="jquery/cornerz.js"]').size()) 
	{ 
		$('div.cornerz').cornerz({radius:10, background: "#FFFFFF"}); 
	}
	$('.edit_descr_link').toggle(function () 
	{
		if (!$(this).parent().parent().children('.edit_descr').size())
		{
			var file_name = $(this).parent().parent().children('label').children('input:checkbox').val();
			
			var old_descr = $(this).parent().parent().children('label').children('em').text();
			var form_code = '<div class="edit_descr" style="width: 100%;" style="display:none"><form method="post">{$session}<input type="hidden" name="f_file_name" value="' + file_name + '"><textarea name="f_file_description" >' + old_descr + '</textarea><br><input type="submit" name="f_edit_submit" value="{$save_button}"></form></div>';
			$(this).parent().parent().append(form_code);
		}
		$(this).parent().parent().find('.edit_descr').slideDown('fast');
	},
	function () {
		$(this).parent().parent().find('.edit_descr').slideUp('fast');
	});
	// nicothin конец добавления
	
	$('#gallerycodeclick').click(function()
	{
		$('#gallerycode').html('');

		codegal = '';
		$("input[name='f_check_files[]']").each( function(i)
		{
			if (this.checked)
			{
				t = this.title;
				if (!t) { t = ''; }
				else { t = ' ' + t; }
				title = $(this).parent('label').find('em').text();
				if (title) title = ' ' + title;
				
				title = title.replace(/((\s*\S+)*)\s*/, "$1");
				
				codegal = codegal + '[gal={$up}mini/' + this.value + t + title +']{$up}'+ this.value +'[\/gal]{$n}';
			}
		});

		if ( codegal )
		{
			n = $('#gallerycodename').val();
			if (n) { n = '[galname]' + n + '[/galname]';}
			else { n = ''; }

			codegal = '[gallery]' + n + '{$n}'+ codegal + '[/gallery]';
			$('#gallerycode').html(codegal);
			$('#gallerycode').css({ background: '#F0F0F0', width: '100%', height: '150px',
									border: '1px solid gray', margin: '20px 0',
									'font-family': 'Courier New',
									'font-size': '9pt'});
			$('#gallerycode').fadeIn('slow');
			$('#gallerycode').select();
		}
		else
		{
			$('#gallerycode').hide();
			alert('{$mess}');
		}
	});
});
</script>
<hr class="br">
EOF;
		echo '<h2 class="br">' . t('Создание галереи') . '</h2>
		<p>' . t('Выделите нужные файлы. (У вас должен быть активирован плагин <strong>LightBox</strong>)') . '</p>
		<p>' . t('Название:') . ' <input type="text" id="gallerycodename" value=""> ' . t('(если нужно)') . '<br><input class="br" type="button" id="gallerycodeclick" value="' . t('Генерировать код галереи') . '">
		</p>
		<p><textarea id="gallerycode" style="display: none"></textarea>
		';
	}
	else
	{
		echo '<p>' . t('Нет загруженных файлов') . '</p>';
	}

?>