<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Функции для загрузки файлов, создания миниатюры и описания файла.
 */

# функция преобразует $_FILES в массив, годный для mso_upload
# если используется множественная загрузка файлов 
# <input type="file" name="f_userfile[]">
function mso_prepare_files($field_userfile = 'f_userfile')
{
	$new_files = array();
	
	// алгоритм преобразования: http://code-igniter.ru/wiki/Multi_upload
	foreach ($_FILES[$field_userfile]['name'] as $index => $val)
	{
		if ($val)
		{
			foreach ($_FILES[$field_userfile] as $key => $val_arr)
			{
				$new_files[$field_userfile . $index][$key] = $val_arr[$index];
			}
		}
	}
	
	// обнуляем $_FILES
	unset($_FILES[$field_userfile]);
	return $new_files;
}


# функция загрузки
# автоматом загружает, меняет размеры, делает миниатюры, описания и т.д.
function mso_upload($config_library = array(), $field_userfile = 'f_userfile', $r = array())
{

	$CI = & get_instance();
	$CI->load->library('upload', $config_library);
	$CI->upload->initialize($config_library);
	
	// если была отправка файла, то нужно заменить поле имени с русского на что-то другое
	// это ошибка при копировании на сервере - он не понимает русские буквы
	if (isset($_FILES[$field_userfile]['name']))
	{
		$f_temp = $_FILES[$field_userfile]['name'];

		// оставим только точку
		$f_temp = str_replace('.', '__mso_t__', $f_temp);
		$f_temp = mso_slug($f_temp); // остальное как обычно mso_slug
		$f_temp = str_replace('__mso_t__', '.', $f_temp);

		$ext = str_replace('.', '', strrchr($f_temp, '.')); // расширение файла
		if ($f_temp == '.' . $ext) // имя файла состоит только из расширения «.jpg»
					$f_temp = '1' . $f_temp; // добавляем к нему единицу

		$_FILES[$field_userfile]['name'] = $f_temp;
	}
	
	$res = $CI->upload->do_upload($field_userfile);

	if (!isset($r['message1'])) $r['message1'] = '<div class="update">' . t('Загрузка выполнена') . '</div>';
	if (!isset($r['message2'])) $r['message2'] = '<div class="error">' . t('Не удалось перименовать файл в нижний регистр') . '</div>';
	
	// описание файла
	if (!isset($r['userfile_title'])) $r['userfile_title'] = false;
	
	// файл, где хранится описание
	if (!isset($r['fn_mso_descritions'])) $r['fn_mso_descritions'] = false;
	
	// нужно ли менять размер
	if (!isset($r['userfile_resize'])) $r['userfile_resize'] = true;
	if (!isset($r['userfile_resize_size'])) $r['userfile_resize_size'] = false; // какой размер
	
	// водяной знак
	if (!isset($r['userfile_water'])) $r['userfile_water'] = false; // нужен ли водяной знак
	if (!isset($r['userfile_water_file'])) $r['userfile_water_file'] = false; // файл водяного знака
	if (!isset($r['water_type'])) $r['water_type'] = 1; // тип водяного знака
	
	// миниатюры всегда хранятся в подкаталоге mini
	if (!isset($r['userfile_mini'])) $r['userfile_mini'] = true; // делать миниатюру?
	if (!isset($r['userfile_mini_size'])) $r['userfile_mini_size'] = false; // размер миниатюры
	if (!isset($r['mini_type'])) $r['mini_type'] = 1; // тип миниатюры
	
	// превьюхи всегда хранятся в подкаталоге _mso_i
	if (!isset($r['prev_size'])) $r['prev_size'] = 100; // размер превьюхи


	if ($res)
	{
		echo $r['message1'];

		// если это файл картинки, то нужно сразу сделать скриншот маленький в _mso_i 100px, который будет выводиться в
		// списке файлов
		$up_data = $CI->upload->data();
		
		// файл нужно поменять к нижнему регистру
		if ( $up_data['file_name'] != strtolower($up_data['file_name']) )
		{
			// переименуем один раз
			if (rename($up_data['full_path'], $up_data['file_path'] . strtolower('__' . $up_data['file_name'])))
			{
				// потом второй в уже нужный - это из-за бага винды
				rename($up_data['file_path'] . strtolower('__' . $up_data['file_name']),
							$up_data['file_path'] . strtolower($up_data['file_name']));

				$up_data['file_name'] = strtolower($up_data['file_name']);
				$up_data['full_path'] = $up_data['file_path'] . $up_data['file_name'];
				// echo '<div class="update">' . $up_data['full_path'] . $up_data['file_name'] . '</div>';
			}
			else echo $r['message2'];
		}
		
		// если указано описание файла и файл, где это описание хранится
		if  ($r['userfile_title'] and $r['fn_mso_descritions'])
		{
			$fn_descr = trim(strip_tags($r['userfile_title'])); // описание файла
			$fn_descr = str_replace('"', '', $fn_descr); // удалим лишнее
			$fn_descr = str_replace('\'', '', $fn_descr);

			
			if (file_exists( $r['fn_mso_descritions'] )) // файла нет, нужно его создать
			{
				// массив данных: fn => описание )
				$mso_descritions = unserialize( read_file($r['fn_mso_descritions']) ); // получим из файла все описания
			}
			else $mso_descritions = array();
	
			$mso_descritions[$up_data['file_name']] = $fn_descr;
			
			write_file($r['fn_mso_descritions'], serialize($mso_descritions) ); // сохраняем в файл
		}

		/*
			[file_name] => warfare7.jpg
			[file_type] => image/jpeg
			[file_path] => D:/xampplite/htdocs/codeigniter/uploads/
			[full_path] => D:/xampplite/htdocs/codeigniter/uploads/warfare7.jpg
			[raw_name] => warfare7
			[orig_name] => warfare.jpg
			[file_ext] => .jpg
			[file_size] => 52.09
			[is_image] => 1
			[image_width] => 450
			[image_height] => 300
			[image_type] => jpeg
			[image_size_str] => width="450" height="300"
		*/

		if ($up_data['is_image']) // это картинка
		{
			$CI->load->library('image_lib');
			$CI->image_lib->clear();


			# вначале нужно изменить размер
			# потом делаем миниатюру с указанными размерами
			# потом делаем такую же миниатюру для  _mso_i с размером 100x100

			
			# меняем размер
			if ($r['userfile_resize'] and $r['userfile_resize_size']) // нужно изменить размер
			{
				
				$size = abs((int) $r['userfile_resize_size']);

				($up_data['image_width'] >= $up_data['image_height']) ? ($max = $up_data['image_width']) : ($max = $up_data['image_height']);
				if ( $size > 1 and $size < $max ) // корректный размер
				{
					$r_conf = array(
						'image_library' => 'gd2',
						'source_image' => $up_data['full_path'],
						'new_image' => $up_data['full_path'],
						'maintain_ratio' => true,
						'width' => $size,
						'height' => $size,
					);

					$CI->image_lib->initialize($r_conf );

					if (!$CI->image_lib->resize())
						echo '<div class="error">' . t('Уменьшение изображения:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}

			}

			//Меняли или не меняли размер, но теперь проверяем, нужна ли нам ватермарка.
			if ($r['userfile_water'] and $r['userfile_water_file'])
			{ //todo — проверка, всё ли нам прислали, всё ли на месте. В идеале бы проверить размеры картинки по отношению к ватермарке.
				if (!file_exists($r['userfile_water_file']))
				{
					echo '<div class="error">' . t('Водяной знак:') . ' ' . t('файл водяного знака не найден! Загрузите его в каталог uploads/') . '</div>';
				}
				else
				{
					$water_type = $r['water_type']; // Расположение ватермарка
					$hor = 'right'; //Инитим дефолтом.
					$vrt = 'bottom'; //Инитим дефолтом.
					if (($water_type == 2) or ($water_type == 4)) $hor = 'left';
					if (($water_type == 2) or ($water_type == 3)) $vrt = 'top';
					if ($water_type == 1) {$hor = 'center'; $vrt = 'middle';}

					$r_conf = array(
						'image_library' => 'gd2',
						'source_image' => $up_data['full_path'],
						'new_image' => $up_data['full_path'],
						'wm_type' => 'overlay',
						'wm_vrt_alignment' => $vrt,
						'wm_hor_alignment' => $hor,
						'wm_overlay_path' => $r['userfile_water_file'] //Жёстко, а что делать?
					);

					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->watermark())
						echo '<div class="error">' . t('Водяной знак:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
			}
	
			# делаем миниатюру
			mso_upload_mini($up_data, $r);			
			
			# превьюшка
			mso_upload_prev($up_data, $r);
		
		}
		return true;
	}
	else
	{
		$er = $CI->upload->display_errors();
		echo '<div class="error">' . t('Ошибка загрузки файла.') . $er . '</div>';
		return false;
	}
		
}

# функция делает миниатюры
# $up_data - массив данных из $CI->upload->data()
# $r - параметры из mso_upload()
function mso_upload_mini($up_data, $r = array())
{
	# получим размеры файла
	$image_info = GetImageSize($up_data['full_path']);
	
	if (!$image_info) return; // это не изображение
	
	$image_width = $image_info[0];
	$image_height = $image_info[1];

	// нужно создать в этом каталоге mini если нет
	if (!is_dir($up_data['file_path'] . 'mini')) @mkdir($up_data['file_path'] . 'mini', 0777); // нет каталога, пробуем создать
	
	$CI = & get_instance();
	$CI->load->library('image_lib');	
	$CI->image_lib->clear();
	
	# теперь нужно сделать миниатюру указанного размера в mini
	if ($r['userfile_mini'] and $r['userfile_mini_size'])
	{
		$size = abs((int) $r['userfile_mini_size']);

		($image_width >= $image_height) ? ($max = $image_width) : ($max = $image_height);
		
		if ( $size > 1 and $size < $max ) // корректный размер
		{
			$r_conf = array(
				'image_library' => 'gd2',
				'source_image' => $up_data['full_path'],
				'new_image' => $up_data['file_path'] . 'mini/' . $up_data['file_name'],
				'maintain_ratio' => true,
				'width' => $size,
				'height' => $size,
			);

			// pr($r_conf);
			
			$mini_type = $r['mini_type']; // тип миниатюры
			/*
			1 Пропорционального уменьшения
			2 Обрезки (crop) по центру
			3 Обрезки (crop) с левого верхнего края
			4 Обрезки (crop) с левого нижнего края
			5 Обрезки (crop) с правого верхнего края
			6 Обрезки (crop) с правого нижнего края
			7 Уменьшения и обрезки (crop) в квадрат
			*/

			if ($mini_type == 2) // Обрезки (crop) по центру
			{
				$r_conf['x_axis'] = round($image_width / 2 - $size / 2);
				$r_conf['y_axis'] = round($image_height / 2 - $size / 2);
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 3) // Обрезки (crop) с левого верхнего края
			{
				$r_conf['x_axis'] = 0;
				$r_conf['y_axis'] = 0;

				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 4) // Обрезки (crop) с левого нижнего края
			{
				$r_conf['x_axis'] = 0;
				$r_conf['y_axis'] = round($image_height - $size * $image_height/$image_width);

				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 5) // Обрезки (crop) с правого верхнего края
			{
				$r_conf['x_axis'] = $image_width - $size;
				$r_conf['y_axis'] = 0;

				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 6) // Обрезки (crop) с правого нижнего края
			{
				$r_conf['x_axis'] = $image_width - $size;
				$r_conf['y_axis'] = $image_height - $size;

				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($mini_type == 7) // Уменьшения и обрезки (crop) в квадрат
			{
				if ($image_width > $image_height) // Если ширина больше высоты
				{
					$resize = round($size * $image_width / $image_height); // Для ресайза по минимальной стороне
					$r_conf['width'] = $resize;
					
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->resize())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
					
					$r_conf['x_axis'] = round(($resize - $size) / 2);
					$r_conf['y_axis'] = 0;
					$r_conf['width'] = $size;
					$r_conf['maintain_ratio'] = false;
					$r_conf['source_image'] = $r_conf['new_image'];
					
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				elseif ($image_width < $image_height) // Если высота больше ширины
				{
					$resize = round($size * $image_height / $image_width);
					$r_conf['height'] = $resize;
					
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->resize())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
					
					$r_conf['x_axis'] = 0;
					$r_conf['y_axis'] = round(($resize - $size) / 2);
					$r_conf['height'] = $size;
					$r_conf['maintain_ratio'] = false;
					$r_conf['source_image'] = $r_conf['new_image'];
					
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				else // Равны
				{
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->resize())
						echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}					
			}
			else // ничего не указано - Пропорционального уменьшения
			{
				$CI->image_lib->initialize($r_conf);
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
		}
		else
		{
			//Размер некорректный и миниатюру просто копируем из большого изображения.
			copy($up_data['full_path'], $up_data['file_path']. 'mini/'. $up_data['file_name']);
		}
	}
}

# функция делает превьюшку 100x100
# $up_data - массив данных из $CI->upload->data()
# $r - параметры из mso_upload()
function mso_upload_prev($up_data, $r = array())
{
	# получим размеры файла
	$image_info = GetImageSize($up_data['full_path']);
	if (!$image_info) return; // это не изображение
	
	$image_width = $image_info[0];
	$image_height = $image_info[1];
	
	// нужно создать в этом каталоге _mso_i если нет
	if (!is_dir($up_data['file_path'] . '_mso_i')) @mkdir($up_data['file_path'] . '_mso_i', 0777); // нет каталога, пробуем создать
	
	
	$CI = & get_instance();
	$CI->load->library('image_lib');
	$CI->image_lib->clear();
	
	# всегда делаем 100 на 100
	# алгоритм тот же, что и у миниатюры
	$size = $r['prev_size'];
	
	if ($size > 0) // если нужно делать превьюху
	{	
		$r_conf = array(
			'image_library' => 'gd2',
			'source_image' => $up_data['full_path'],
			'new_image' => $up_data['file_path'] . '_mso_i/' . $up_data['file_name'],
			'maintain_ratio' => true,
			'width' => $size,
			'height' => $size,
		);


		$mini_type = $r['mini_type']; // тип миниатюры
		/*
		1 Пропорционального уменьшения
		2 Обрезки (crop) по центру
		3 Обрезки (crop) с левого верхнего края
		4 Обрезки (crop) с левого нижнего края
		5 Обрезки (crop) с правого верхнего края
		6 Обрезки (crop) с правого нижнего края
		7 Уменьшения и обрезки (crop) в квадрат
		*/

		if ($mini_type == 2) // Обрезки (crop) по центру
		{
			$r_conf['x_axis'] = round($image_width / 2 - $size / 2);
			$r_conf['y_axis'] = round($image_height / 2 - $size / 2);

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 3) // Обрезки (crop) с левого верхнего края
		{
			$r_conf['x_axis'] = 0;
			$r_conf['y_axis'] = 0;

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 4) // Обрезки (crop) с левого нижнего края
		{
			$r_conf['x_axis'] = 0;
			$r_conf['y_axis'] = round($image_height - $size * $image_height/$image_width);

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 5) // Обрезки (crop) с правого верхнего края
		{
			$r_conf['x_axis'] = $image_width - $size;
			$r_conf['y_axis'] = 0;

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 6) // Обрезки (crop) с правого нижнего края
		{
			$r_conf['x_axis'] = $image_width - $size;
			$r_conf['y_axis'] = $image_height - $size;

			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->crop())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}
		elseif ($mini_type == 7) // Уменьшения и обрезки (crop) в квадрат
		{
			if ($image_width > $image_height) // Если ширина больше высоты
			{
				$resize = round($size * $image_width / $image_height); // Для ресайза по минимальной стороне
				$r_conf['width'] = $resize;
				
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				
				$r_conf['x_axis'] = round(($resize - $size) / 2);
				$r_conf['y_axis'] = 0;
				$r_conf['width'] = $size;
				$r_conf['maintain_ratio'] = false;
				$r_conf['source_image'] = $r_conf['new_image'];
				
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			elseif ($image_width < $image_height) // Если высота больше ширины
			{
				$resize = round($size * $image_height / $image_width);
				$r_conf['height'] = $resize;
				
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
				
				$r_conf['x_axis'] = 0;
				$r_conf['y_axis'] = round(($resize - $size) / 2);
				$r_conf['height'] = $size;
				$r_conf['maintain_ratio'] = false;
				$r_conf['source_image'] = $r_conf['new_image'];
				
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->crop())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}
			else // Равны
			{
				$CI->image_lib->initialize($r_conf );
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
			}					
		}
		else // ничего не указано - Пропорционального уменьшения
		{
			$CI->image_lib->initialize($r_conf );
			if (!$CI->image_lib->resize())
				echo '<div class="error">' . t('Создание миниатюры:') . ' ' . $CI->image_lib->display_errors() . '</div>';
		}

	}

	
	
}

# end file