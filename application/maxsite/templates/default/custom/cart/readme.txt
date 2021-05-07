* MaxSite CMS
* (c) https://max-3000.com/
* 
* Корзина
* 

Как использовать функционал корзины на своем сайте. По этой инструкции вы можете создать демо-вариант за 3 шага.

1. Подключение корзины выполняется в файле custom/my-template.php. Уберите коррментарий чтобы получилось так:

if ($fn = mso_fe('custom/cart/cart.php')) require_once($fn);


2. Создайте страницу товара. На одной странице может быть любое количество товаров. Структура страниц и рубрик — произвольная. Данные товара оформляются в data-атрибутах html-тэга, например кнопки BUTTON (см. инструкцию ниже).

В тексте записи разместите этот код. Это 4 товара, блок для вывода сообщения и адрес (укажите свой) на страницу просмотра корзины.

<button class="cart-add-item" type="button" data-id="x01" data-price="100" data-name="Куртка" data-maxcount="1" data-desc="Куртка детская, зеленая, размер XL">Куртка <span class="cart-item-count" data-id="x01"></span></button> <button class="cart-add-item" type="button" data-id="x02" data-price="200" data-name="Шапка" data-maxcount="2">Шапка <span class="cart-item-count" data-id="x02"></span></button> <button class="cart-add-item" type="button" data-id="x03" data-price="300" data-name="Кроссовки" data-maxcount="3">Кроссовки <span class="cart-item-count" data-id="x03"></span></button> <button class="cart-add-item" type="button" data-id="x04" data-price="400" data-name="Джинсы" data-maxcount="10">Джинсы <span class="cart-item-count" data-id="x04"></span></button>

<div class="cart-message t-center"></div>

<a href="http://сайт/page/cart">Посмотреть корзину</a> (<span class="cart-all-count"></span>)


3. Создайте страницу корзины, например «ВашСайт/page/cart». На ней будет отображаться состояние корзины, а также форма заказа. Разместите следующий код:
 
<a href="#" class="cart-show-noedit">Корзина</a> | <a href="#" class="cart-show">Редактировать</a> | <a href="#" class="cart-clear">Очистить корзину</a> | <span class="cart-all-count"></span>

<div class="cart-message t-center"></div>
<div class="cart-table-items"></div>

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


Всё, корзина готова!

--------------------------------

Технические описания
====================


1. Добавление товара в корзину осуществляется с помощью произвольного html-тэга, например BUTTON. Параметры товара задаются в предопределенных data-атрибутах. CSS-класс кнопки должен быть «cart-add-item». Например:


<button class="cart-add-item" type="button" data-id="x01" data-price="100" data-name="Куртка" data-maxcount="1" data-desc="Куртка детская, зеленая, размер XL">Куртка</button>

data-id="x01" — код товара
data-price="100" — стоимость (только число)
data-name="Куртка" — название товара в корзине
data-maxcount="1" — максимальное колво товаров в корзине
data-desc="Куртка детская, зеленая, размер XL" — описание товара для корзины

Если необходимо вывести количество для этого товара, то используется дополнительный тэг с классом cart-item-count и data-атрибутом, где указывается код товара, например:

<span class="cart-item-count" data-id="x01"></span>

Атрибуты data желательно указывать все. Если нет обязательных атрибутов, добавление в корзину будет невозможным (выскочит сообщение об ошибке).

Верстка cart-add-item и cart-item-count произвольная. Например можно разместить span внутри button.


2. Вывод Корзины возможен на любой странице сайта, включая и страницу товара, виджета, компонента и т.п.

Сама корзина выводится в блоке cart-table-items:

<div class="cart-table-items"></div>


Блок cart-message служит для вывода различных сообщений.

<div class="cart-message t-center"></div>

Для вывода информации в блоке корзины используются кнопки/ссылки с классами («кнопки действия»):

<a href="#" class="cart-show-noedit">Корзина</a> — показать товары без редактирования
<a href="#" class="cart-show">Редактировать</a> — показать товары с редактирование колва
<a href="#" class="cart-clear">Очистить корзину</a> — очистить корзину
<span class="cart-all-count"></span> — вывести всего наименований товаров в корзине


3. Для вызова формы оформления заказа используется ссылка/кнопка cart-show-order

<a href="#" class="cart-show-order">Оформить заказ</a>

которая показывает форму с id="cart-form-order". По умолчанию форма скрыта (класс b-hide).

Сама форма содержит служебные блоки cart-form-order-hidden и cart-form-order-table, где выводится информация и формируются нужные input-элементы формы.

Завершает форму submit-кнопка, по которой происходит отправка данных на сервер. Для отмены/скрытия формы используется кнопка/ссылка с классом cart-form-order-cancel.

В форме можно разместить любые дополнительные поля. Их имена (name) должны строиться по шаблону «cart[form][ПОЛЕ]».

	<h4>Укажите дополнительную информацию</h4>
	
	<div class="mar10-tb"><label>Ваше имя: <input type="text" name="cart[form][name]" required></label></div> 
	
	<div class="mar10-tb"><label>Ваш email: <input type="email" name="cart[form][email]" required></label></div>
	
	<div class="mar10-tb"><label>Способ оплаты: <input type="text" name="cart[form][pay]" required></label></div>

Оформление формы, полей, кнопок и т.п. произвольное.
	
Результат ответа сервера выводится в блоке id="cart-form-order-result". Он должен располагаться вне блока формы. 
	
<div id="cart-form-order-result"></div>


4. Ядро корзины состоит из трёх файлов:

	cart.js — основной функционал. Его не следует редактировать.
	cart-my.js — настраиваемая часть
	cart-ajax.php — обработка аякс-запроса формы заказа
	cart.php — подключаемый к шаблону файл. 

	
5. Файл cart-my.js содержит часть кода, которая отвечает за настройки корзины, html-разметку, различные опции, а также описывает действия для кнопок/ссылок.

6. Файл cart-ajax.php является обработчиком формы заказа. Вы можете его отредактировать под свою задачу.

7. Данные корзины хранятся в браузере пользователя в localStorage. Это гарантирует его полную сохранность после закрытия браузера, обновления страниц и т.п. К тому же localStorage обладает очень большим объемом, что позволяет сохранять неограниченное количество информации из Корзины. Данные на сервер отправляются только при нажатии кнопки Отправить в форме заказа. До этого момента сервер ничего не знает о действиях пользователя.


# end of file