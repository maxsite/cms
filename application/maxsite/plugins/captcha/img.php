<?php

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
function _create_captha_img()
{

	$width = 100;
	$height = 25;
	
	$im = @imagecreate ($width, $height) or die ("Cannot initialize new GD image stream!");
	
	if (isset($_GET['image']) and isset($_GET['page'])) 
	{
		$char = md5($_GET['image'] . $_GET['page']);
	}
	else
	{
		die ("error");
	}
	
	$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('0', '5', '8', '3', '4', '7'), $char);
	$char = substr( $char, 1, 4);
	
	imagecolortransparent ($im, imagecolorallocate ($im, 205, 255, 255) );
	
	// rnd
	for ($i = 0; $i < strlen($char); $i++) 
	{
		// $text_color = imagecolorallocate ($im, rand(220,255), rand(0,40), 120);
		$text_color = imagecolorallocate ($im, 255, 0, 0);
		$x = $width / 10 + $i * ($width / 5);
		// $y = rand(0, 5);
		$y = 0;
		imagechar ($im, 4, $x, $y, chr(rand(49, 90)), $text_color);
	}	
	
	$point_color = imagecolorallocate ($im, 255, 120, 120);
	
	ImageLine($im, 3, 9, 150, 2,  $point_color);

	//output characters
	for ($i = 0; $i < strlen($char); $i++) {
		$text_color = imagecolorallocate ($im, rand(0,120), rand(0,180), rand(0,180));
		// $x = 5 + $i * 40 + rand(-5, 5);
		$x = 3 + $i * $width / 4;
		//$y = rand(0, 5);
		$y = 10;
		imagechar ($im, 5, $x, $y,	$char{$i}, $text_color);
	}

	//ouput PNG
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); //	HTTP/1.0
	
	if (function_exists("imagepng")) 
	{
	   header("Content-type: image/png");
	   imagepng($im);
	} 
	elseif (function_exists("imagegif")) 
	{
	   header("Content-type: image/gif");
	   imagegif($im);
	}
	elseif (function_exists("imagejpeg")) 
	{
	   header("Content-type: image/jpeg");
	   imagejpeg($im);
	}
	else 
	{
	   die("No image support in this PHP server!");
	}
	
	imagedestroy ($im);	   

}

_create_captha_img();

?>