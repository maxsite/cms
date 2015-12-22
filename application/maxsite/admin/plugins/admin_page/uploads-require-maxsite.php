<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// Все проверки.
if (!is_login()) die('no login');

if (isset($_SERVER['HTTP_X_FILENAME']))
{
	$fn = $_SERVER['HTTP_X_FILENAME'];
}
elseif (isset($_SERVER['HTTP_X_REQUESTED_FILENAME']))
{
	$fn = $_SERVER['HTTP_X_REQUESTED_FILENAME'];
}
else die('no file');

if (isset($_SERVER['HTTP_X_FILENAME_UP_DIR']))
{
	$page_id = $_SERVER['HTTP_X_FILENAME_UP_DIR'];
}
elseif (isset($_SERVER['HTTP_X_REQUESTED_FILEUPDIR']))
{
	$page_id = $_SERVER['HTTP_X_REQUESTED_FILEUPDIR'];
}
else die('no updir');

if (!is_numeric($page_id)) die('wrong updir');

mso_checkreferer();

$ext = substr(strrchr($fn, '.'), 1);
$allowed_ext = explode('|', mso_get_option('allowed_types', 'general', 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz|bz2|tgz'));
if (!in_array(strtolower($ext), $allowed_ext)) die('not allowed');
// Закончили проверки.

// Полный путь к каталогу.
$up_dir =  getinfo('uploads_dir') . '_pages/' . $page_id . '/';

// Сама загрузка файла и создание миниатюр.
_upload($up_dir, $fn);

function _upload($up_dir, $fn, $r = array())
{
	$fn = _slug($fn);
	$ext = substr(strrchr($fn, '.'), 1);
	$name = substr($fn, 0, strlen($fn) - strlen($ext) - 1);

	// Если имя файла пустое, только расширение.
	if ($fn == '.' . $ext)
		$fn = '1' . $fn;

	// Если файл уже существует.
	if (file_exists($up_dir . $fn))
	{
		for ($i = 1; $i < 100; $i++) // Метода взята из библиотеки CI. К сожалению, while(file_exists()) у меня вешает сервер.
		{
			$fn = $name . '-' . $i . '.' . $ext;
			if (!file_exists($up_dir . $fn))
				break;
		}
	}

	file_put_contents( $up_dir . $fn, file_get_contents('php://input') );

	if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif')))
	{
		// Не картинка, загрузили, больше ничего не надо.
		return;
	}

	// С какими дефолтными параметрами ресайзим и делаем миниатюрки.
	$def = array(
					'resize_images'   => mso_get_option('resize_images',   'general', '600'),
					'size_image_mini' => mso_get_option('size_image_mini', 'general', '150'),
					'image_mini_type' => mso_get_option('image_mini_type', 'general', '1'),
					'use_watermark'   => mso_get_option('use_watermark',   'general', '0'),
					'watermark_type'  => mso_get_option('watermark_type',  'general', '1')
				);
	//Можем передать свои параметры в эту функцию.
	$r = array_merge($def, $r);

	require(getinfo('shared_dir') . 'stock/thumb/thumb.php');

	// У нас есть uploads_dir, а нужен url
	$url = str_replace(getinfo('uploads_dir'), getinfo('uploads_url'), $up_dir);

	// Если картинка больше, чем нужно, то делаем ресайз, иначе ничего не делаем.
	// $new_size пригодится, когда из новой картинки будем миниатюру делать.
	$size = $new_size = getimagesize($up_dir . $fn);
	if ($size[0] > $r['resize_images'] || $size[1] > $r['resize_images'])
	{
		if ($size[0] > $size[1])
		{
			$new_size[0] = $r['resize_images'];
			$new_size[1] = round($size[1] / ($size[0]/$new_size[0]));
		}
		else
		{
			$new_size[1] = $r['resize_images'];
			$new_size[0] = round($size[0] / ($size[1]/$new_size[1]));
		}
		//pr($new_size);
		thumb_generate($url . $fn, $new_size[0], $new_size[1], false, 'resize', true, '', false);
	}

	// Создание ватермарки, если такая опция и есть нужный файл.
	if ($r['use_watermark'] and file_exists(getinfo('uploads_dir') . 'watermark.png'))
	{
		$water_type = $r['watermark_type']; // Расположение ватермарка на картинке
		$hor = 'right'; //Инитим дефолтом.
		$vrt = 'bottom'; //Инитим дефолтом.
		if (($water_type == 2) or ($water_type == 4)) $hor = 'left';
		if (($water_type == 2) or ($water_type == 3)) $vrt = 'top';
		if ($water_type == 1) {$hor = 'center'; $vrt = 'middle';}

		$r_conf = array(
			'image_library' => 'gd2',
			'source_image' => $up_dir . $fn,
			'new_image' => $up_dir . $fn,
			'wm_type' => 'overlay',
			'wm_vrt_alignment' => $vrt,
			'wm_hor_alignment' => $hor,
			'wm_overlay_path' => getinfo('uploads_dir') . 'watermark.png'
		);

		$CI = &get_instance();
		$CI->load->library('image_lib');
		$CI->image_lib->clear();

		$CI->image_lib->initialize($r_conf );
		if (!$CI->image_lib->watermark())
			echo '<div class="error">' . t('Водяной знак:') . ' ' . $CI->image_lib->display_errors() . '</div>';
	}

	// $r['image_mini_type'] = 6;

	switch ($r['image_mini_type'])
	{
		case 1: // Пропорциональное уменьшение
				if ($size[0] > $size[1])
				{
					$new_size[0] = $r['size_image_mini'];
					$new_size[1] = round($size[1] / ($size[0]/$new_size[0]));
					thumb_generate($url . $fn, $new_size[0], $new_size[1], false, 'resize', true, 'mini', false);
				}
				else
				{
					$new_size[1] = $r['size_image_mini'];
					$new_size[0] = round($size[0] / ($size[1]/$new_size[1]));
					thumb_generate($url . $fn, $new_size[0], $new_size[1], false, 'resize', true, 'mini', false);
				}
				break;

		case 2: // Обрезки (crop) по центру
				thumb_generate($url . $fn, $r['size_image_mini'], $r['size_image_mini'], false, 'resize_full_crop_center', true, 'mini', false);
				break;

		case 3: // Обрезки (crop) с левого верхнего края
				thumb_generate($url . $fn, $r['size_image_mini'], $r['size_image_mini'], false, 'crop', true, 'mini', false);
				break;

		case 4: // Обрезки (crop) с левого нижнего края
				$thumb = new Thumb($url . $fn, $postfix = '', $replace_file = true, $subdir = 'mini');
				$thumb->crop($r['size_image_mini'], $r['size_image_mini'], 0, $new_size[1] - $r['size_image_mini']);
				//thumb_generate($url . $fn, $width, $height, false, $type_resize = 'resize_full_crop_center', true, 'mini', false);
				break;

		case 5: // Обрезки (crop) с правого верхнего края
				$thumb = new Thumb($url . $fn, $postfix = '', $replace_file = true, $subdir = 'mini');
				$thumb->crop($r['size_image_mini'], $r['size_image_mini'], $new_size[0] - $r['size_image_mini'], 0);
				//thumb_generate($url . $fn, $width, $height, false, $type_resize = 'resize_full_crop_center', true, 'mini', false);
				break;

		case 6: // Обрезки (crop) с правого нижнего края
				$thumb = new Thumb($url . $fn, $postfix = '', $replace_file = true, $subdir = 'mini');
				$thumb->crop($r['size_image_mini'], $r['size_image_mini'], $new_size[0] - $r['size_image_mini'], $new_size[1] - $r['size_image_mini']);
				//thumb_generate($url . $fn, $width, $height, false, $type_resize = 'resize_full_crop_center', true, 'mini', false);
				break;

		case 7: // Уменьшения и обрезки (crop) в квадрат
				if ($size[0] < $size[1])
					thumb_generate($url . $fn, $r['size_image_mini'], $r['size_image_mini'], false, 'resize_crop_center', true, 'mini', false);
				else
					thumb_generate($url . $fn, $r['size_image_mini'], $r['size_image_mini'], false, 'resize_h_crop_center', true, 'mini', false);
				break;
	}

	thumb_generate($url . $fn, 100, 100, false, 'resize_full_crop_center', true, '_mso_i', false);
} // End of _upload()

function _slug($slug)
{
	$repl = array(
	"А"=>"a", "Б"=>"b",  "В"=>"v",  "Г"=>"g",   "Д"=>"d",
	"Е"=>"e", "Ё"=>"jo", "Ж"=>"zh",
	"З"=>"z", "И"=>"i",  "Й"=>"j",  "К"=>"k",   "Л"=>"l",
	"М"=>"m", "Н"=>"n",  "О"=>"o",  "П"=>"p",   "Р"=>"r",
	"С"=>"s", "Т"=>"t",  "У"=>"u",  "Ф"=>"f",   "Х"=>"h",
	"Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shh", "Ъ"=>"",
	"Ы"=>"y", "Ь"=>"",   "Э"=>"e",  "Ю"=>"ju", "Я"=>"ja",

	"а"=>"a", "б"=>"b",  "в"=>"v",  "г"=>"g",   "д"=>"d",
	"е"=>"e", "ё"=>"jo", "ж"=>"zh",
	"з"=>"z", "и"=>"i",  "й"=>"j",  "к"=>"k",   "л"=>"l",
	"м"=>"m", "н"=>"n",  "о"=>"o",  "п"=>"p",   "р"=>"r",
	"с"=>"s", "т"=>"t",  "у"=>"u",  "ф"=>"f",   "х"=>"h",
	"ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ъ"=>"",
	"ы"=>"y", "ь"=>"",   "э"=>"e",  "ю"=>"ju",  "я"=>"ja",

	# украина
	"Є" => "ye", "є" => "ye", "І" => "i", "і" => "i",
	"Ї" => "yi", "ї" => "yi", "Ґ" => "g", "ґ" => "g",
	
	# беларусь
	"Ў"=>"u", "ў"=>"u", "'"=>"",
	
	# румынский
	"ă"=>'a', "î"=>'i', "ş"=>'sh', "ţ"=>'ts', "â"=>'a',
	
	"«"=>"", "»"=>"", "—"=>"-", "`"=>"", " "=>"-",
	"["=>"", "]"=>"", "{"=>"", "}"=>"", "<"=>"", ">"=>"",

	"?"=>"", ","=>"", "*"=>"", "%"=>"", "$"=>"",

	"@"=>"", "!"=>"", ";"=>"", ":"=>"", "^"=>"", "\""=>"",
	"&"=>"", "="=>"", "№"=>"", "\\"=>"", "/"=>"", "#"=>"",
	"("=>"", ")"=>"", "~"=>"", "|"=>"", "+"=>"", "”"=>"", "“"=>"",
	"'"=>"",

	"’"=>"",
	"—"=>"-", // mdash (длинное тире)
	"–"=>"-", // ndash (короткое тире)
	"™"=>"tm", // tm (торговая марка)
	"©"=>"c", // (c) (копирайт)
	"®"=>"r", // (R) (зарегистрированная марка)
	"…"=>"", // (многоточие)
	"“"=>"",
	"”"=>"",
	"„"=>"",
	
	" "=>"-",
	);
		
	$slug = strtr(trim($slug), $repl);
	$slug = htmlentities($slug); // если есть что-то из юникода
	$slug = strtr(trim($slug), $repl);
	$slug = strtolower($slug);
	
	return $slug;
}

return;

# end of file
