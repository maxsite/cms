<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * Корзина — AJAX
 */
 
if ($_POST and isset($_POST['cart']) and isset($_POST['cart']['items']) and isset($_POST['cart']['total'])) 
{
	$out = '';
	
	// код заказа на основе даты
	$cod_z = date('y-m-d-H-i');
	
	$out .=  'Код заказа: ' . $cod_z . "\r\n\r\n";
	
	// формируем тело письма
	foreach($_POST['cart']['items'] as $item)
	{		
		$out .= 
			  'Название: ' . $item['name'] . "\r\n"
			. 'Код: ' . $item['key'] . "\r\n"
			. 'Описание: ' . $item['desc'] . "\r\n"
			. 'Цена: ' . $item['price'] . "\r\n"
			. 'Количество: ' . $item['count'] . "\r\n"
			. 'Максимум: ' . $item['maxcount'] . "\r\n"
			. 'Сумма: ' . $item['sum'] . "\r\n"
			. 'Метка времени: ' . $item['time'] . "\r\n\r\n";
	}
	
	$out .=  'ИТОГО ПО ЗАКАЗУ: ' . $_POST['cart']['total'] . "\r\n";
	
	// прочие данные формы, если есть
	if (isset($_POST['cart']['form']))
	{
		// pr($_POST['cart']['form']);
		$out .=  "\r\n" . 'ПРОЧИЕ ДАННЫЕ' . "\r\n";
		
		foreach($_POST['cart']['form'] as $key=>$val)
		{
			$out .=  $key . ': ' . $val .  "\r\n";
		}
	}
		
		
	$out .=  "\r\n";

	// pr($out);
	
	// отправка на email
	
	// адрес получателя
	// $email = 'admin@site.com'; 
	// или из опций
	
	$email = mso_get_option('admin_email_server', 'general', '');
	
	$subject = 'Заказ ' . $cod_z; // тема письма
	
	mso_mail($email, $subject, $out);
	
	// сообщение на странице
	echo '<div class="t-green mar30-tb">Спасибо! Ваш заказ отправлен! Код заказа: <b>' . $cod_z . '</b></div>';
	
}


# end of file