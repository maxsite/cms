<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * Класс для формирования thumb-изображений
 * указывается url входящего изображения
 * на выходе url нового изображения
 */

class Thumb
{
	protected $file = ''; // исходный файл относительно uploads
	protected $new_file = ''; // конечный файл относительно uploads

	// уточнить видимость полей
	public $new_img = ''; // конечный url полный

	protected $image_info = []; // информация об изображении
	protected $image_quality = 90; // качество изображения — по-умолчанию 90%

	public $init = ''; // возврат при инициализации
	// true - есть готовое новое изображение (в кэше)
	// false - ошибка 
	// всё остальное - можно сделать новый файл

	public function __construct($url, $postfix = '-thumb', $replace_file = false, $subdir = '', $quality = 90)
	{
		// хук где можно обработать входящий url
		$url = mso_hook('thumb_in_url', $url);

		// если get-ключ thumb_in_url == true, то орбабатываем входящий url
		if (mso_get_val('thumb_in_url', true)) {
			// если есть вхождение http в начале адреса, то «выравниваем» протоколы
			if (strpos($url, 'http') === 0) {
				$url = getinfo('site_protocol') . str_replace(array('http://', 'https://'), '', $url);
			} else {
				// это относительный адрес, добавляем адрес сайта

				// если ведущий /, то его нужно удалить
				if (strpos($url, '/') === 0) $url = substr($url, 1);

				$url = getinfo('site_url') . $url;
			}
		}

		// проверим входящий url
		if (strpos($url, getinfo('uploads_url')) === false) {
			// входящий адрес чужой
			$this->init = false;
			return;
		}

		// файл и путь файла относительно uploads
		$this->file = str_replace(getinfo('uploads_url'), '', $url);

		// расширение файла
		$ext = strtolower(substr(strrchr($this->file, '.'), 1));

		if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
			$this->init = false; // если это не картинка, то выходим
			return;
		}

		// теперь только имя без расширения
		$name = substr($this->file, 0, strlen($this->file) - strlen($ext) - 1);

		// если указан $subdir — подкаталог для нового файла, то добавлем его к новому имени 
		// $subdir = 'mini' => uploads/mini/

		// pr($name);

		if ($subdir) {
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
		if (!$replace_file and file_exists(getinfo('uploads_dir') . $this->new_file)) {
			// есть, отдаем его url
			$this->init = true;
			$this->new_img = getinfo('uploads_url') . $this->new_file;

			return;
		}

		if (!file_exists(getinfo('uploads_dir') . $this->file)) {
			$this->init = false; // нет исходного файла

			return;
		}

		// проверим картинка ли исходный файл
		$this->image_info = GetImageSize(getinfo('uploads_dir') . $this->file);

		if (!$this->image_info) {
			$this->init = false; // это не изображение - ошибка

			return;
		}

		$this->image_quality = $quality;

		// проверим существование mini-каталога нового файла
		// если его нет, попробуем создать
		$new_dir = dirname(getinfo('uploads_dir') . $this->new_file);

		if (!is_dir($new_dir)) @mkdir($new_dir, 0777);

		// сразу сформируем новый адрес
		$this->new_img = getinfo('uploads_url') . $this->new_file;
	}

	// пропорциональное уменьшение
	// если высота = 0, то она вычисляется автоматом. Аналогично и ширина
	public function resize($width = 200, $height = 0, $file = false, $new_file = false)
	{
		$CI = &get_instance();
		$CI->load->library('image_lib');
		$CI->image_lib->clear();

		// функция может принимать произвольне файлы
		if ($file === false) $file = $this->file;
		if ($new_file === false) $new_file = $this->new_file;

		// параметры для image_lib - начальные
		$r_conf = [
			'source_image' => getinfo('uploads_dir') . $file,
			'new_image' => getinfo('uploads_dir') . $new_file,
			'maintain_ratio' => false, // размеры по пропорции вычислим сами
			'quality' => $this->image_quality,
		];

		// пропорции
		$image_info = GetImageSize(getinfo('uploads_dir') . $file); // информация о файле исходном

		// отрицательные значения интепретируем как положительные
		$width = abs($width);
		$height = abs($height);

		// если задана только ширина, то высоту расчитываем пропорцией от исходного файла
		if ($height === 0) {
			//$image_info[0] - ширина  $image_info[1] - высота
			$ratio = $image_info[0] / $image_info[1]; // w/h
			$height = ceil($width / $ratio);
		}

		// аналогично расчитываем ширину, если она = 0
		if ($width === 0) {
			$ratio = $image_info[1] / $image_info[0]; // h/w
			$width = ceil($height / $ratio);
		}

		$r_conf['width'] = $width;
		$r_conf['height'] = $height;

		$CI->image_lib->initialize($r_conf);

		if (!$CI->image_lib->resize()) return false; // произошла какая-то ошибка

		// $this->preview(); // сделаем превьюшку 100х100 в _mso_i - а нужно ли?

		return getinfo('uploads_url') . $new_file;
	}

	// пропорциональное уменьшение от ширины
	public function resize_w($width = 0, $height = 0, $x = 0, $y = 0)
	{
		$width = ($width > 0) ? $width : 1;

		return $this->resize($width, 0);
	}

	// пропорциональное уменьшение от высоты
	public function resize_h($width = 0, $height = 0, $x = 0, $y = 0)
	{
		$height = ($height > 0) ? $height : 1;

		return $this->resize(0, $height);
	}

	// кроп 
	// x и y - точка координат от верхнего левого угла
	public function crop($width = 0, $height = 0, $x = 0, $y = 0, $file = false, $new_file = false)
	{
		$CI = &get_instance();
		$CI->load->library('image_lib');
		$CI->image_lib->clear();

		if ($file === false) $file = $this->file;
		if ($new_file === false) $new_file = $this->new_file;

		// параметры для image_lib - начальные
		$r_conf = array(
			'source_image' => getinfo('uploads_dir') . $file,
			'new_image' => getinfo('uploads_dir') . $new_file,
			'maintain_ratio' => false, // размеры по пропорции вычислим сами
			'quality' => $this->image_quality,
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
	public function crop_center($width = 0, $height = 0)
	{
		$width = ($width > 0) ? $width : 1;
		$height = ($height > 0) ? $height : 1;

		$x = round($this->image_info[0] / 2 - $width / 2);
		$y = round($this->image_info[1] / 2 - $height / 2);

		return $this->crop($width, $height, $x, $y);
	}

	// кроп по центру, где высота определяется пропорцией
	// если $ratio = 0, то пропорция высоты от самого изображения
	public function crop_center_ratio($width = 0, $ratio = 0)
	{
		$width = ($width > 0) ? $width : 1;

		if ($ratio == 0) {
			$ratio = $this->image_info[0] / $this->image_info[1]; // w/h
			$height = ceil($width / $ratio);
		} else {
			$height = ceil($width / $ratio);
		}

		return $this->crop_center($width, $height);
	}

	// вначале пропорциональная ширина
	// после обрезка кроп до указанных размеров
	public function resize_crop($width = 0, $height = 0, $x = 0, $y = 0)
	{
		$this->resize($width, 0);

		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}

	// вначале пропорциональная ширина
	// после обрезка кроп до указанных размеров по центру
	public function resize_crop_center($width = 0, $height = 0)
	{
		$width = ($width > 0) ? $width : 1;
		$height = ($height > 0) ? $height : 1;

		$this->resize($width, 0);

		$image_info = GetImageSize(getinfo('uploads_dir') . $this->new_file);
		$x = round($image_info[0] / 2 - $width / 2);
		$y = round($image_info[1] / 2 - $height / 2);

		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}

	// вначале пропорциональная высота(!)
	// после обрезка кроп до указанных размеров по центру
	public function resize_h_crop_center($width = 0, $height = 0)
	{
		$width = ($width > 0) ? $width : 1;
		$height = ($height > 0) ? $height : 1;

		$this->resize(0, $height);

		$image_info = GetImageSize(getinfo('uploads_dir') . $this->new_file);
		$x = round($image_info[0] / 2 - $width / 2);
		$y = round($image_info[1] / 2 - $height / 2);

		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}

	// вначале пропорциональная высота/ширина 
	// после обрезка кроп до указанных размеров по центру
	// высота или ширина выбирается та, что больше, чтобы не было пустот в итоге
	public function resize_full_crop_center($width = 0, $height = 0)
	{

		$width = ($width > 0) ? $width : 1;
		$height = ($height > 0) ? $height : 1;

		// определяем пропорции реальные к требуемым
		// от них и скачем
		$w = $this->image_info[0] / $width;
		$h = $this->image_info[1] / $height;

		if ($w > $h) {
			$this->resize(0, $height);
		} else {
			$this->resize($width, 0);
		}

		$image_info = GetImageSize(getinfo('uploads_dir') . $this->new_file);
		$x = round($image_info[0] / 2 - $width / 2);
		$y = round($image_info[1] / 2 - $height / 2);

		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}

	// аналогично resize_full_crop_center, но кроп от верхнего левого угла
	public function resize_full_crop_top_left($width = 0, $height = 0)
	{
		$width = ($width > 0) ? $width : 1;
		$height = ($height > 0) ? $height : 1;

		// определяем пропорции реальные к требуемым
		// от них и скачем
		$w = $this->image_info[0] / $width;
		$h = $this->image_info[1] / $height;

		if ($w > $h)
			$this->resize(0, $height);
		else
			$this->resize($width, 0);


		// $image_info = GetImageSize(getinfo('uploads_dir') . $this->new_file);
		$x = 0;
		$y = 0;

		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}

	// аналогично resize_full_crop_center, но кроп от верхнего центра
	public function resize_full_crop_top_center($width = 0, $height = 0)
	{
		$width = ($width > 0) ? $width : 1;
		$height = ($height > 0) ? $height : 1;

		// определяем пропорции реальные к требуемым
		// от них и скачем
		$w = $this->image_info[0] / $width;
		$h = $this->image_info[1] / $height;

		if ($w > $h) {
			$this->resize(0, $height);
		} else {
			$this->resize($width, 0);
		}

		$image_info = GetImageSize(getinfo('uploads_dir') . $this->new_file);
		$x = round($image_info[0] / 2 - $width / 2);
		$y = 0;

		return $this->crop($width, $height, $x, $y, $this->new_file, $this->new_file);
	}

	// пропорциональное изменение размеров в процентах
	public function zoom($zoom = 50)
	{
		$zoom = ($zoom > 0) ? $zoom : 100;

		$w = $this->image_info[0] * $zoom / 100;
		$h = $this->image_info[1] * $zoom / 100;

		return $this->resize($w, $h);
	}

	// комбинация zoom и crop_center_ratio
	public function zoom_crop_center($zoom, $width, $ratio = 0)
	{
		$fu = $this->zoom($zoom); // результат url нового файла

		// вторая операция отдельно, поскольку это уже новый файл

		if (!$fu) return false;

		$fi = getinfo('uploads_dir') . str_replace(getinfo('uploads_url'), '', $fu);

		$image_info = GetImageSize($fi);
		$w = $image_info[0]; // ширина текущего

		// если размер меньше требуемого, то отдаем как есть без кропа
		if ($w < $width) return $fu;

		$h = $image_info[1]; // высота текущего

		if ($ratio == 0) $ratio = $w / $h;

		$height = ceil($width / $ratio); // конечная высота

		// смещение к центру
		$x = round($w / 2 - $width / 2);
		$y = round($h / 2 - $height / 2);

		// параметры для image_lib - начальные
		$r_conf = [
			'source_image' => $fi,
			'new_image' => $fi,
			'maintain_ratio' => false, // размеры по пропорции вычислим сами
			'quality' => $this->image_quality,
			'x_axis' => $x,
			'y_axis' => $y,
			'width' => $width,
			'height' => $height
		];

		$CI = &get_instance();
		$CI->load->library('image_lib');
		$CI->image_lib->clear();
		$CI->image_lib->initialize($r_conf);

		if (!$CI->image_lib->crop()) return false; // произошла какая-то ошибка

		return $fu;
	}

	// функция готовит превьюшку в _mso_i 
	public function preview()
	{
		// возможно файл в подкаталоге
		$e = strrpos($this->new_file, '/');

		if ($e !== false)
			$n = substr($this->new_file, $e + 1); // вычлиним только имя
		else
			$n = $this->new_file;

		$prev_file = str_replace($n, '_mso_i/' . $n, $this->new_file);

		$CI = &get_instance();
		$CI->load->library('image_lib');
		$CI->image_lib->clear();

		// параметры для image_lib - начальные
		$r_conf = [
			'source_image' => getinfo('uploads_dir') . $this->file,
			'new_image' => getinfo('uploads_dir') . $prev_file,
			'maintain_ratio' => false, // размеры по пропорции вычислим сами
			'width' => 100,
			'height' => 100,
			'quality' => $this->image_quality,
		];

		$CI->image_lib->initialize($r_conf);
		$CI->image_lib->resize();
	}
}

# end of file
