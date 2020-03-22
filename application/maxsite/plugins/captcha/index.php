<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function captcha_autoload($args = array())
{	
	if ( !is_login() and !is_login_comuser() )
	{
		mso_hook_add('comments_content_end', 'captcha_go'); # хук на отображение картинки
		mso_hook_add('comments_new_captcha', 'captcha_new_comment'); # хук на обработку капчи
	}
}

function captcha_new_comment($args = array()) 
{
	global $MSO;
	
	if (isset($_POST['comments_captha']))
	{
		$captcha = $_POST['comments_captha']; // это введенное значение капчи
		$char = mso_md5($MSO->data['session']['session_id'] . mso_current_url());
		$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('1', '5', '8', '2', '7', '9'), $char);
		$char = substr($char, 1, 4);
		return ($captcha == $char); // сравниваем
	}
	else
	{
		return false; // нет капчи, хотя должна быть!
	}
}

# выводим картинку капчи
function captcha_go($args = array()) 
{
	global $MSO;

	echo '
		<div class="captcha"><label for="comments_captha">' . tf('Введите нижние символы') . '</label>
		<input type="text" name="comments_captha" id="comments_captha" value="" maxlength="4" class="comments_captha" autocomplete="off"> <img src="' 
			. create_captha_img(mso_md5($MSO->data['session']['session_id'] . mso_current_url()))
			. '" alt="" title="' . tf('Защита от спама: введите только нижние символы') . '"> <span>' . t('(обязательно)') . '</span><br><br></div>
		';
}

function create_captha_img($char)
{
	$width = 100;
	$height = 25;
	
	$im = @imagecreate ($width, $height) or die ("Cannot initialize new GD image stream!");

	$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('1', '5', '8', '2', '7', '9'), $char);
	$char = substr($char, 1, 4);
	
	imagecolortransparent($im, imagecolorallocate ($im, 205, 255, 255) );
	
	for ($i = 0; $i < strlen($char); $i++) 
	{
		$text_color = imagecolorallocate ($im, rand(200, 255), rand(0,120), rand(0,120));
		$x = $width / 10 + $i * ($width / 5);
		$y = 0;
		imagechar ($im, 4, $x, $y, chr(rand(65, 90)), $text_color);
	}
	
	imageline($im, 1, rand(5, 10), $width - 10, rand(5, 10), imagecolorallocatealpha($im, rand(180, 255), rand(0,120), rand(0,120), 60));

	for ($i = 0; $i < strlen($char); $i++) 
	{
		$text_color = imagecolorallocate ($im, rand(0,180), rand(0,180), rand(0,180));

		$x = rand(0, 5) + $i * $width / rand(4, 5);
		$y = rand(8, 12);
		
		imagechar ($im, 5, $x, $y,	$char[$i], $text_color);
	}

	ob_start();
	imagepng($im);
	$src = 'data:image/png;base64,' . base64_encode(ob_get_contents());
	ob_end_clean();

	imagedestroy ($im);	   
	
	return $src;
}

# end file