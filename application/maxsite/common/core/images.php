<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// создание миниатюры
// вспомогательные функции для использования в шаблоне
// тип формирования указывается в $type_resize
function thumb_generate($img, $width, $height, $def_img = false, $type_resize = 'resize_full_crop_center', $replace_file = false, $subdir = 'mini', $postfix = true, $quality = 90)
{
    // указана картинка, нужно сделать thumb заданного размера
    if ($img) {
        // если true, то делаем из ширину+высоту
        // если false, то постфикса не будет
        if ($postfix === true) $postfix = '-' . $width . '-' . $height;

        $t = new Thumb($img, $postfix, $replace_file, $subdir, $quality);

        if ($t->init === true) // уже есть готовое изображение в кэше
        {
            $img = $t->new_img; // сразу получаем новый адрес
        } elseif ($t->init === false) // входящий адрес ошибочен
        {
            // $img = false; // ошибка
            $img = $def_img; // ставим дефолтное изображение 
        } else {
            // получаем изображение
            if ($type_resize == 'resize_crop') {
                $t->resize_crop($width, $height);
            } elseif ($type_resize == 'crop_center') {
                $t->crop_center($width, $height);
            } elseif ($type_resize == 'crop_center_ratio_auto') {
                $t->crop_center_ratio($width, 0); // автовысота
            } elseif ($type_resize == 'crop_center_ratio') {
                $t->crop_center_ratio($width, $height); // здесь высота — это пропорция
            } elseif ($type_resize == 'crop_center_ratio_4_3') {
                $t->crop_center_ratio($width, (4 / 3));
            } elseif ($type_resize == 'crop_center_ratio_3_2') {
                $t->crop_center_ratio($width, (3 / 2));
            } elseif ($type_resize == 'crop_center_ratio_16_9') {
                $t->crop_center_ratio($width, (16 / 9));
            } elseif ($type_resize == 'crop') {
                $t->crop($width, $height);
            } elseif ($type_resize == 'resize') {
                $t->resize($width, $height);
            } elseif ($type_resize == 'resize_h_crop_center') {
                $t->resize_h_crop_center($width, $height);
            } elseif ($type_resize == 'resize_crop_center') {
                $t->resize_crop_center($width, $height);
            } elseif ($type_resize == 'resize_full_crop_top_left') {
                $t->resize_full_crop_top_left($width, $height);
            } elseif ($type_resize == 'resize_full_crop_top_center') {
                $t->resize_full_crop_top_center($width, $height);
            } elseif ($type_resize == 'resize_w') {
                $t->resize_w($width, $height);
            } elseif ($type_resize == 'resize_h') {
                $t->resize_h($width, $height);
            } elseif ($type_resize == 'zoom') {
                $t->zoom($width); // здесь $width — это масштаб
            } elseif ($type_resize == 'zoom25') {
                $t->zoom(25);
            } elseif ($type_resize == 'zoom50') {
                $t->zoom(50);
            } elseif ($type_resize == 'zoom75') {
                $t->zoom(75);
            } elseif ($type_resize == 'zoom25_crop_center_ratio_auto') {
                $t->zoom_crop_center(25, $width, 0);
            } elseif ($type_resize == 'zoom50_crop_center_ratio_auto') {
                $t->zoom_crop_center(50, $width, 0);
            } elseif ($type_resize == 'zoom75_crop_center_ratio_auto') {
                $t->zoom_crop_center(75, $width, 0);
            } else {
                $t->resize_full_crop_center($width, $height);
            }

            $img = $t->new_img; // url-адрес готового изображения
        }
    } else {
        // у записи не указано метаполе, ставим дефолт 
        $img = $def_img;
    }

    return $img;
}

/**
 *  Поворачивает изображение (файл) на заданный угол 
 *  Код ротации как для  $CI->image_lib->rotate()
 */
function thumb_rotate($fn, $rotation = 0, $quality = 90)
{
    if ($rotation) {
        $r_conf = [
            'image_library' => 'gd2',
            'source_image' => $fn,
            'new_image' => $fn,
            'rotation_angle' => $rotation,
            'quality' => $quality,
        ];

        $CI = &get_instance();
        $CI->load->library('image_lib');
        $CI->image_lib->clear();
        $CI->image_lib->initialize($r_conf);
        $CI->image_lib->rotate();
    }
}

/**
 *  Ставим водяной знак
 * Тип размещения:
 * 1||По центру 
 * 2||В левом верхнем углу
 * 3||В правом верхнем углу 
 * 4||В левом нижнем углу 
 * 5||В правом нижнем углу
 */
function thumb_watermark($fn, $fn_watermark, $watermark_type, $quality = 90)
{
    $hor = 'right';
    $vrt = 'bottom';

    if ($watermark_type == 1) {
        $hor = 'center';
        $vrt = 'middle';
    }

    if (($watermark_type == 2) or ($watermark_type == 3)) $vrt = 'top';
    if (($watermark_type == 2) or ($watermark_type == 4)) $hor = 'left';

    $r_conf = [
        'image_library' => 'gd2',
        'source_image' => $fn,
        'new_image' => $fn,
        'wm_type' => 'overlay',
        'wm_vrt_alignment' => $vrt,
        'wm_hor_alignment' => $hor,
        'wm_overlay_path' => $fn_watermark,
        'wm_opacity' => 100,
        'quality' => $quality,

        // fix CodeIgniter in PHP 7.2
        'wm_vrt_offset' => 0,
        'wm_hor_offset' => 0,
        'wm_padding' => 0,
    ];

    $CI = &get_instance();
    $CI->load->library('image_lib');
    $CI->image_lib->clear();
    $CI->image_lib->initialize($r_conf);
    $CI->image_lib->watermark();
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

    if ($color == 'rand') {
        $arr = array(
            "red" => rand(1, 255),
            "green" => rand(1, 255),
            "blue" => rand(1, 255)
        );
    } else {
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

    // первый цвет — это цвет фона
    $color = mso_hex2rgb($background_color);
    imagecolorallocate($im, $color['red'], $color['green'], $color['blue']); 

    $color = mso_hex2rgb($text_color);
    $tc = imagecolorallocate($im, $color['red'], $color['green'], $color['blue']);

    if ($text) {
        if ($text === true) $text = $width . 'x' . $height;

        $center_x = ceil((imagesx($im) - (ImageFontWidth($font_size) * mb_strlen($text))) / 2);
        $center_y = ceil(((imagesy($im) - (ImageFontHeight($font_size))) / 2));

        imagestring($im, $font_size, $center_x, $center_y,  $text, $tc);
    }

    ob_start();
    imagepng($im);
    $src = 'data:image/png;base64,' . base64_encode(ob_get_contents());
    ob_end_clean();

    imagedestroy($im);

    return $src;
}

/**
 * Функция фозвращает код поворота изображения для $CI->image_lib->rotate() 
 */
function mso_exif_rotate($fn)
{
    $ext = strtolower(substr(strrchr($fn, '.'), 1));

    $rotation = 0;

    if (in_array($ext, array('jpg', 'jpeg'))) {
        $exif = @exif_read_data($fn, 'IFD0');

        if ($exif !== false and isset($exif['Orientation'])) {
            $ort = $exif['Orientation'];

            // определяем угол поворота изображения по коду
            switch ($ort) {
                case 1: // nothing
                    $rotation = 0;
                    break;

                case 2: // horizontal flip
                    $rotation = 'hor';
                    break;

                case 3: // 180 rotate left
                    $rotation = '180';
                    break;

                case 4: // vertical flip
                    $rotation = 'vrt';
                    break;

                case 5: // vertical flip + 90 rotate right
                    $rotation = 0; // какой извращенец так камеру держит???
                    // $image->flipImage($fn, 2);
                    // $image->rotateImage($fn, -90);
                    break;

                case 6: // 90 rotate right
                    $rotation = '270';
                    break;

                case 7: // horizontal flip + 90 rotate right
                    $rotation = 0;
                    // $image->flipImage($fn,1);   
                    // $image->rotateImage($fn, -90);
                    break;

                case 8: // 90 rotate left
                    $rotation = '90';
                    break;
            }
        }
    }

    return $rotation;
}

# end of file
