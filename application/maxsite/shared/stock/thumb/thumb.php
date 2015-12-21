<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 * Класс для формирования thumb-изображений
 * указывается url входящего изображения
 * на выходе url нового изображения
 			
*/


class Thumb 
{
	protected $file = ''; // исходный файл относительно uploads
	protected $new_file = ''; // конечный файл относительно uploads
	
	var $new_img = ''; // конечный url полный

	protected $image_info = array(); // информация об изображении

	var $init = ''; // возврат при инициализации
		// true - есть готовое новое изображение (в кэше)
		// false - ошибка 
		// всё остальное - можно сделать 
		

	function __construct($url, $postfix = '-thumb', $replace_file = false, $subdir = '')
	{
		// проверим входящий url
		if (strpos($url, getinfo('uploads_url')) === false) 
		{
			// входящий адрес чужой
			$this->init = false;
			return;
		}
		
		// файл и путь файла относительно uploads
		$this->file = str_replace(getinfo('uploads_url'), '', $url);
		
		// расширение файла
		$ext = strtolower(substr(strrchr($this->file, '.'), 1));
		
		if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif')))
		{
			$this->init = false; // если это не картинка, то выходим
			return;
		}
		
		// теперь только имя без расширения
		$name = substr($this->file, 0, strlen($this->file) - strlen($ext) - 1);
		
		// если указан $subdir — подкаталог для нового файла, то добавлем его к новому имени 
		// $subdir = 'mini' => uploads/mini/
		
		// pr($name);
		
		if ($subdir)
		{
			$name = substr_replace($name, '/' . $subdir . '/', strrpos($name, '/'), 0);
		}

		// удаляем возможные лишние слеши
		$name = str_replace('//', '/', $name); // двойной слеш
		$pos_sl = strpos($name, '/'); // в начале имени
		if ($pos_sl !== false and $pos_sl === 0) $name = substr($name, 1);
		
		// pr($name);
		
		// новое имя
		
		// проверим постфикс если false то без постфикса
		if ($postfix === false) $postfix = ''; 
		elseif (!$postfix) $postfix = '-thumb';
		
		$this->new_file = $name . $postfix . '.' . $ext;
		
		
		// может новый файл уже есть?
		// нужно ли заменять уже существующий файл
		if (!$replace_file and file_exists(getinfo('uploads_dir') . $this->new_file))
		{
			// есть, отдаем его url
			$this->init = true;
			$this->new_img = getinfo('uploads_url') . $this->new_file;
			return;
		}
		
		if (!file_exists(getinfo('uploads_dir') . $this->file))
		{
			$this->init = false; // нет исходного файла
			
			return;
		}
		
		// проверим картинка ли исходный файл
		$this->image_info = GetImageSize(getinfo('uploads_dir') . $this->file);

		if (!$this->image_info) 
		{
			$this->init = false; // это не изображение - ошибка
			return;
		}
		
		// проверим существование mini-каталога нового файла
		// если его нет, попробуем создать
		$new_dir = dirname(getinfo('uploads_dir') . $this->new_file);
		
		if (!is_dir($new_dir)) @mkdir($new_dir, 0777);
	
		// сразу сформируем новый адрес
		$this->new_img = getinfo('uploads_url') . $this->new_file;
	}
	
	
	// пропорциональное уменьшение
	// если высота = 0, то она вычисляется автоматом. Аналогично и ширина
	function resize($width = 200, $height = 0, $file = false, $new_file = false)
	{
		$CI = & get_instance();
		$CI->load->library('image_lib');
		$CI->image_lib->clear();
		
		
		// функция может принимать произвольне файлы
		if ($file === false) $file = $this->file;
		if ($new_file === false) $new_file = $this->new_file;
		
		// параметры для image_lib - начальные
		$r_conf = array(
				'source_image' => getinfo('uploads_dir') . $file,
				'new_image' => getinfo('uploads_dir') . $new_file,
				'maintain_ratio' => false, // размеры по пропорции вычислим сами
			);
		
		// пропорции
		$image_info = GetImageSize(getinfo('uploads_dir') . $file); // информация о файле исходном
		
		// если задана только ширина, то высоту расчитываем пропорцией от исходного файла
		if ($height === 0)
		{
			//$image_info[0] - ширина  $image_info[1] - высота
			$ratio = $image_info[0] / $image_info[1]; // w/h
			$height = ceil($width / $ratio);
		}
		
		// аналогично расчитываем ширину, если она = 0
		if ($width === 0)
		{
			$ratio = $image_info[1] / $image_info[0]; // h/w
			$width = ceil($height / $ratio);
		}			
		
		$r_conf['width'] = $width;
		$r_conf['height'] = $height;
		
		$CI->image_lib->initialize($r_conf);
		
		if (!$CI->image_lib->resize()) return false; // произошла какая-то ошибка
		
		# $this->preview(); // сделаем превьюшку 100х100 в _mso_i - а нужно ли?
		
		return getinfo('uploads_url') . $new_file;
	
	}
	
	// кроп 
	// x и y - точка координат от верхнего левого угла
	function crop($width = 0, $height = 0, $x = 0, $y = 0, $file = false, $new_file = false)
	{
		$CI = & get_instance();
		$CI->load->library('image_lib');
		$CI->image_lib->clear();
		
		if ($file === false) $file = $this->file;
		if ($new_file === false) $new_file = $this->new_file;
		
		// параметры для image_lib - начальные
		$r_conf = array(
				'source_image' => getinfo('uploads_dir') . $file,
				'new_image' => getinfo('uploads_dir') . $new_file,
				'maintain_ratio' => false, // размеры по пропорции вычислим сами
			);
		
		$r_conf['x_axis'] = $x;
		$r_conf['y_axis'] = $y;
		
		if ($width > 0) $r_conf['width'] = $width;
		if ($height > 0) $r_conf['height'] = $height;
		
		$CI->image_lib->initialize($r_conf);
		
		if (!$CI->image_lib->crop()) return false; // произошла какая-то ошибка
		
		return getinfo('uploads_url') . $new_file;
	}

	
	// кроп по центру изображения 
	function crop_center($width = 0, $height = 0)
	{
		$x = round($this->image_info[0] / 2 - $width / 2);
		$y = round($this->image_info[1]/ 2 - $height / 2);
		
		return $this->crop($width, $height, $x, $y);
	}
	
	
	// вначале пропорциональная ширина
	// после обрезка кроп до указанных размеров
	function resize_crop($width = 0, $height = 0, $x = 0, $y = 0)
	{
		$this->resize($width, 0);
		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}
	
	// вначале пропорциональная ширина
	// после обрезка кроп до указанных размеров по центру
	function resize_crop_center($width = 0, $height = 0)
	{
		$this->resize($width, 0);
		
		$image_info = GetImageSize(getinfo('uploads_dir') . $this->new_file);
		$x = round($image_info[0] / 2 - $width / 2);
		$y = round($image_info[1]/ 2 - $height / 2);
		
		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}
	
	// вначале пропорциональная высота(!)
	// после обрезка кроп до указанных размеров по центру
	function resize_h_crop_center($width = 0, $height = 0)
	{
		$this->resize(0, $height);
		
		$image_info = GetImageSize(getinfo('uploads_dir') . $this->new_file);
		$x = round($image_info[0] / 2 - $width / 2);
		$y = round($image_info[1]/ 2 - $height / 2);
		
		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}
	
	// вначале пропорциональная высота/ширина 
	// после обрезка кроп до указанных размеров по центру
	// высота или ширина выбирается та, что больше, чтобы не было пустот в итоге
	function resize_full_crop_center($width = 0, $height = 0)
	{
		// определяем пропорции реальные к требуемым
		// от них и скачем
		$w = $this->image_info[0] / $width;
		$h = $this->image_info[1] / $height;
	
		if ($w > $h)
		{
			$this->resize(0, $height);
		}
		else
		{
			$this->resize($width, 0);
		}
		
		$image_info = GetImageSize(getinfo('uploads_dir') . $this->new_file);
		$x = round($image_info[0] / 2 - $width / 2);
		$y = round($image_info[1]/ 2 - $height / 2);
		
		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}	
	
	
	# функция готовит превьюшку в _mso_i 
	function preview()
	{
		// возможно файл в подкаталоге
		$e = strrpos($this->new_file, '/');
		
		if ($e !== false)
			$n = substr($this->new_file, $e+1); // вычлиним только имя
		else
			$n = $this->new_file;
		
		$prev_file = str_replace($n, '_mso_i/' . $n, $this->new_file);
		
		$CI = & get_instance();
		$CI->load->library('image_lib');
		$CI->image_lib->clear();
		
		// параметры для image_lib - начальные
		$r_conf = array(
				'source_image' => getinfo('uploads_dir') . $this->file,
				'new_image' => getinfo('uploads_dir') . $prev_file,
				'maintain_ratio' => false, // размеры по пропорции вычислим сами
				'width' => 100,
				'height' => 100
			);
			
		$CI->image_lib->initialize($r_conf);
		$CI->image_lib->resize();
	}
	
	
} // end  class Thumb 


// вспомогательные функции для использования в шаблоне
// тип формирования указывается в $type_resize

function thumb_generate($img, $width, $height, $def_img = false, $type_resize = 'resize_full_crop_center', $replace_file = false, $subdir = 'mini', $postfix = true)
{
	// указана картинка, нужно сделать thumb заданного размера
	if ($img) 
	{
		// если true, то делаем из ширину+высоту
		// если false, то постфикса не будет
		if ($postfix === true) $postfix = '-' . $width . '-' . $height;
		
		$t = new Thumb($img, $postfix, $replace_file, $subdir);
		
		if ($t->init === true) // уже есть готовое изображение в кэше
		{
			$img = $t->new_img; // сразу получаем новый адрес
		}
		elseif($t->init === false) // входящий адрес ошибочен
		{
			// $img = false; // ошибка
			$img = $def_img; // ставим дефолтное изображение 
		}
		else
		{	
			// получаем изображение
			
			if ($type_resize == 'resize_crop')
			{
				$t->resize_crop($width, $height);
			}
			elseif ($type_resize == 'crop_center')
			{
				$t->crop_center($width, $height);
			}
			elseif ($type_resize == 'crop')
			{
				$t->crop($width, $height);
			}
			elseif ($type_resize == 'resize')
			{
				$t->resize($width, $height);
			}
			elseif ($type_resize == 'resize_h_crop_center')
			{
				$t->resize_h_crop_center($width, $height);
			}
			elseif ($type_resize == 'resize_crop_center')
			{
				$t->resize_crop_center($width, $height);
			}
			else
			{
				$t->resize_full_crop_center($width, $height);
			}
			
			$img = $t->new_img; // url-адрес готового изображения
		}
	}
	else // у записи не указано метаполе, ставим дефолт 
	{
		$img = $def_img;
	}
	
	return $img;
}

/**
*  функция преобразования #-цвета в массив RGB
*  
*  @param $color цвет в виде #RRGGBB
*  
*  @return array
*/
function mso_hex2rgb($color)
{
	$color = str_replace('#', '', $color);
	
	if ($color == 'rand')
	{
		$arr = array(
			"red" => rand(1, 255),
			"green" => rand(1, 255),
			"blue" => rand(1, 255)
			);
	}
	else
	{
		$int = hexdec($color);
	
		$arr = array(
			"red" => 0xFF & ($int >> 0x10),
			"green" => 0xFF & ($int >> 0x8),
			"blue" => 0xFF & $int
			);
	}
	
	return $arr;
}

/**
*  создание заглушки holder для <IMG>
*  цвет задавать как в HTML в полном формате #RRGGBB
*  если цвет = rand то он формируется случайным образом
*  текст только английский (кодировка latin2)
*  если $text = true, то выводится размер изображения ШШхВВ
*  
*  @param $width ширина
*  @param $height высота
*  @param $text текст
*  @param $background_color цвет фона
*  @param $text_color цвет текста
*  @param $font_size размер шрифта от 1 до 5
*  
*  @return string
*  
*  <img src="<?= mso_holder() ? >" -> в формате data:image/png;base64, 
*  mso_holder(250, 80)
*  mso_holder(300, 50, 'My text', '#660000', '#FFFFFF')
*  mso_holder(600, 400, 'Slide', 'rand', 'rand')
*/
function mso_holder($width = 100, $height = 100, $text = true, $background_color = '#CCCCCC', $text_color = '#777777', $font_size = 5)
{
	$im = @imagecreate($width, $height) or die("Cannot initialize new GD image stream");
	
	$color = mso_hex2rgb($background_color);
	$bg = imagecolorallocate($im, $color['red'], $color['green'], $color['blue']);
	
	$color = mso_hex2rgb($text_color);
	$tc = imagecolorallocate($im, $color['red'], $color['green'], $color['blue']);
	
	if ($text)
	{
		if ($text === true) $text = $width . 'x' . $height;
		
		$center_x = ceil( ( imagesx($im) - ( ImageFontWidth($font_size) * mb_strlen($text) ) ) / 2 );
		$center_y = ceil( ( ( imagesy($im) - ( ImageFontHeight($font_size) ) ) / 2));
		
		imagestring($im, $font_size, $center_x, $center_y,  $text, $tc);
	}
	
	ob_start();
	imagepng($im);
	$src = 'data:image/png;base64,' . base64_encode(ob_get_contents());
	ob_end_clean();
	
	imagedestroy($im);
	
	return $src;
}

# end file