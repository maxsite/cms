<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * Корзина — подключение к шаблону
 */
 
// хук на подключение js-файлов в конце BODY
mso_hook_add('body_end', 'my_cart_body_end');

define('CART', 1); // флаг-признак, что используется корзина

function my_cart_body_end($a = array() )
{
	$ajax = getinfo('ajax') . base64_encode('templates/' . getinfo('template') .'/custom/cart/cart-ajax.php');
	
	echo '<script>window.cart_form_ajax = "' . $ajax .'";</script>';
	
	mso_add_file('custom/cart/js/cart.js');
	mso_add_file('custom/cart/js/cart-my.js');
	
	return $a;
}


# end of file