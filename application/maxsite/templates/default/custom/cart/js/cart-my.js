/*
 * (c) MaxSite CMS
 * https://max-3000.com/
 *
 * Корзина — настраиваемая часть
 * Файл должен быть загружен после cart.js
 *
 */
	
document.addEventListener("DOMContentLoaded", () => {

	// селекторы/css-классы блоков вывода 
	var cartS = {};
	
	cartS['showItemsCount'] = '.cart-item-count'; // блок для вывода колва одного товара
	cartS['allCount'] = '.cart-all-count'; // блок для вывода колво наименований всех товаров
	cartS['tableItems'] = '.cart-table-items'; // блок, где выводится таблица всех товаров
	cartS['message'] = '.cart-message'; // блок, где выводятся сообщения
	
	
	// прочие настройки
	var cartVar = {};
	
	// формат вывода одного товара корзины с возможностью редактирования колва
	// input.cart-item-count-change — input, где меняется колво товара
	// для основного блока контейнера обязательно указывать id="cart-item{key}"
	cartVar['format'] = '<div class="flex pad5 {count0}" id="cart-item{key}">'
		+ '<div class="flex-grow5">{name}{desc}</div>'
		+ '<div class="w10"><input class="w100 cart-item-count-change" type="number" min="0" max="{maxcount}" value="{count}" data-id="{key}"></div>'
		+ '<div class="w10 t-right">{price}</div>'
		+ '<div class="w10 mar10-l pad5-rl t-bold t-right bg-gray100">{sum}</div>'
		+ '</div>';
	
	// тоже самое, только без возможности редактирования
	cartVar['format_noedit'] = '<div class="flex pad5 {count0}" id="cart-item{key}">'
		+ '<div class="flex-grow5">{name}{desc}</div>'
		+ '<div class="w10">{count} шт.</div>'
		+ '<div class="w10 t-right">{price}</div>'
		+ '<div class="w10 mar10-l pad5-rl t-bold t-right bg-gray100">{sum}</div>'
		+ '</div>';
		
	// класс для input, где можно менять колво товаров. Указывается в format
	cartVar['item_count_change'] = '.cart-item-count-change';
	
	// класс если колво товара == 0 — в формате {count0} заменяется на этот текст
	cartVar['count0'] = 'bg-gray100 t-red';
	
	// до и после для описания товара, если есть
	cartVar['desc_start'] = '<div class="t90 italic mar10-tb">';
	cartVar['desc_end'] = '</div>';
	
	
	// блок ИТОГО сумма
	cartVar['total_start'] = '<div class="bg-gray200 t-right mar10-t mar30-b pad5">Итого: <b>';
	cartVar['total_end'] = '</b></div>';

	// всего Товаров в корзине см. cartS['showItemsCount']
	cartVar['showItemsCount_start'] = 'Товаров в корзине: ';
	cartVar['showItemsCount_end'] = '';
	
	// сообщение об очистке корзины
	cartVar['clear'] = 'Корзина очищена';
	
	// сообщение что корзина пуста
	cartVar['cart_empty'] = 'Корзина пуста';
	
	// колво каждого товара в таблице 
	cartVar['item_count_start'] = ' (';
	cartVar['item_count_end'] = ' шт.)';
	
	// знак валюты — добавляется в конце {sum}
	cartVar['currency'] = ' грн.';


	// инициализация корзины
	mycart = new Cart('cart', cartVar, cartS);
	// mycart.log();
	
	// проставим внутри .cart-item-count колво товаров
	mycart.showItemsCount();
	
	mycart.showAllCount(); // всего товаров в корзине
	
	// показать текущую корзину без редактирования и пустых товаров
	mycart.showCartNoEmpty('.cart-table-items', '<h4>Ваша корзина</h4>');
	
	// действия по кнопкам
	
	// кнопка показать корзину
	$(".cart-show").click(function() {
		
		mycart.showCart();
		
		return false;
	});
	
	// кнопка показать корзину без полей редактирования
	$(".cart-show-noedit").click(function() {
		
		mycart.showCartNoEdit();
		
		return false;
	});
	
	// кнопка добавить в корзину
	$(".cart-add-item").click(function() {
		
		var e = $(this);
		
		if (mycart.addItem(e)) // если было добавление
		{
			mycart.showAllCount(); // всего товаров в корзине
			mycart.showCartNoEdit(); // обновляем таблицу товаров
			mycart.showItemsCount(); // проставим колво каждого товара
			mycart.showMessage('«' + e.data('name') + '» в корзину добавлен!', 'bg-green100 pad10');
		}
		
		return false;
	});
	
	// кнопка очистить корзину
	$(".cart-clear").click(function() {
		
		mycart.clearCart(); // очистка
		mycart.showAllCount(); // всего товаров в корзине
		$(cartS['tableItems']).html(''); // блок корзины теперь пустой
		mycart.showMessage(cartVar['clear'], 'bg-yellow pad10'); // сообщение
		mycart.showItemsCount(); // проставим колво каждого товара
		
		return false;
	});
	
	// input-number где можно изменить кол-во
	$(cartS['tableItems']).on('change', cartVar['item_count_change'], function(e) {
		
		var t = $(this);
		var count = t.val();
		var itemId = t.data('id');
		
		mycart.changeCount(itemId, count); // изменяет колво
		mycart.showCart(); // обновляем таблицу товаров
		
		// найдем заново input для фокуса, поскольку он был переписан html(html)
		$("#cart-item"+itemId).find(cartVar['item_count_change']).focus();
		mycart.showItemsCount(); // проставим колво каждого товара
		
		return false;
	});
	
	// кнопка оформить заказ
	$(".cart-show-order").click(function() {
		
		// форма имеет фиксированный id
		$("#cart-form-order").show().removeClass('b-hide'); // показываем форму
		
		$('.cart-form-order-hidden').html(mycart.setHiddenForOrder()); // все hidden элементы
		
		// корзина без пустых товаров
		mycart.showCartNoEmpty('.cart-form-order-table', '<h4>Ваш заказ</h4>');
		
		// console.log();;
		
		return false;
	});
	
	// отмена/скрытие формы заказа
	$(".cart-form-order-cancel").click(function() {
		$("#cart-form-order").hide();
		return false;
	});
	
	// ajax отправка формы заказа
	$('#cart-form-order').submit(function(){
		
		// console.log();
		// alert('Отправка формы');
		
		$.post(
			window.cart_form_ajax,
			$("#cart-form-order").serialize(),       
			function(msg) {
				$('.cart-show-order').hide(); // кнопка Оформить заказ
				$('#cart-form-order').slideUp('slow'); // форма отправки
				$('#cart-form-order-result').html(msg); // результат
				
				// и очистить корзину
				mycart.clearCart(); // очистка
				mycart.showAllCount(); // всего товаров в корзине
				$(cartS['tableItems']).html(''); // блок корзины теперь пустой
				mycart.showItemsCount(); // проставим колво каждого товара
			}
		);
		
		return false;
	});
	
});
