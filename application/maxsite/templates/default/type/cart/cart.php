<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */
 
mso_head_meta('title', 'Корзина'); //  meta title страницы

// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn; ?>

<div class=""><section>
<h1 class="mar30-b">Корзина</h1>

<a href="#" class="cart-show-noedit">Корзина</a> | <a href="#" class="cart-show">Редактировать</a> | <a href="#" class="cart-clear">Очистить корзину</a> | <span class="cart-all-count"></span>

<div class="cart-message t-center"></div>
<div class="cart-table-items t-green700"></div>

<a href="#" class="cart-show-order">Оформить заказ</a>

<form id="cart-form-order" class="b-hide">
	<div class="cart-form-order-hidden"></div>
	<div class="cart-form-order-table"></div>
	
	<h4>Укажите дополнительную информацию</h4>
	<div class="mar10-tb"><label>Ваше имя: <input type="text" name="cart[form][name]" required></label></div> 
	<div class="mar10-tb"><label>Ваш email: <input type="email" name="cart[form][email]" required></label></div>
	<div class="mar10-tb"><label>Способ оплаты: <input type="text" name="cart[form][pay]" required></label></div>

	<button class="mar20-tb" type="submit">Отправить</button> <a href="#" class="cart-form-order-cancel mar20-l">или отменить</a>
</form>

<div id="cart-form-order-result"></div>

</section></div>
<?php
// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;
	
# end of file