<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */
 
if ($_POST and isset($_POST['myform'])) 
{
	$out = '';
	
	// код заказа на основе даты
	$cod_z = date('y-m-d-H-i');
	
	$out .=  'Код сообщения: ' . $cod_z . "\r\n\r\n";
	
	// прочие данные формы, если есть
	if (isset($_POST['myform']['form']))
	{
		foreach($_POST['myform']['form'] as $key=>$val)
		{
			$out .=  $key . ': ' . $val .  "\r\n";
		}
	}
		
	$out .=  "\r\n";
	
	// адрес получателя	
	// $email = 'admin@site.com'; 
	$email = mso_get_option('admin_email', 'general', '');
	
	$subject = 'Обратная связь ' . $cod_z; // тема письма
	
	mso_mail($email, $subject, $out);
	
	// сообщение на странице
	echo '<div class="mar30-tb">Спасибо! Ваше сообщение отправлено! Код: <b>' . $cod_z . '</b></div>';

}


# end of file