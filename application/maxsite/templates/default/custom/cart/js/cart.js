/*
 * (c) MaxSite CMS
 * https://max-3000.com/
 *
 * Корзина — основой объект
 * Используется jQuery
 */

// указывается ключ localStorage, где хранится корзина
// cartVar, cartS — массивы опций
function Cart(key, cartVar, cartS) 
{
	var self = this;
	var total = 0;
	var currentItemCount = 0; // колво последнего добавленного товара  
	
	this.getCartData = function()
	{
		return JSON.parse(localStorage.getItem(key)) || {};
	}
	
	this.setCartData = function(data) 
	{
		localStorage.setItem(key, JSON.stringify(data));
	}
	
	this.clearCart = function()
	{
		localStorage.setItem(key, '{}');
	}
	
	this.log = function()
	{
		console.log(self.getCartData());
	}
	
	this.addToCart = function(itemId, itemName = '', itemPrice = 0, itemMaxCount = 1, itemDesc = '')
	{
		var cartData = self.getCartData();
		var dt = new Date();
		var count = 1;

		if(cartData.hasOwnProperty(itemId)) 
		{
			count = Number(cartData[itemId]['count']) + 1;
			if (count > itemMaxCount) { count = itemMaxCount; }
		} 
		else 
		{
			count = 1;
		}
		
		cartData[itemId] = {
			'name': itemName,
			'desc': itemDesc,
			'price': itemPrice,
			'count': count,
			'maxcount': itemMaxCount,
			'time': dt.toISOString()
		};
		
		self.currentItemCount = count;
		
		// console.log(cartData);
		
		self.setCartData(cartData); // записываем данные
	}
	
	// получить число уникальных товаров в корзине
	this.getCountItems = function()
	{
		var cartData = self.getCartData();

		return Object.keys(cartData).length;
	}
	
	// вывести корзину
	this.showItems = function(format, count0, empty = true)
	{
		var cartData = self.getCartData();
		var out = '';
		var name, price, count, sum, maxcount, desc, f1, c0;
		
		self.total = 0;
		
		// console.log(format);
		
		for (var key in cartData) 
		{
			// console.log(key);
			// console.log(cartData[key]);
			// console.log(cartData[key]['name']);
			// console.log(cartData[key]['price']);
			// console.log(cartData[key]['count']);
			// console.log(cartData[key]['maxcount']);
			
			name = cartData[key]['name'];
			desc = cartData[key]['desc'];
			price = cartData[key]['price'];
			maxcount = cartData[key]['maxcount'];
			count = cartData[key]['count'];
			sum = price * count;
			
			// если стоит флаг, то если колво=0, то не добавляем в вывод
			if (!empty)
			{
				if (count == 0) continue;
			}
			
			if (count == 0) 
			{ 
				c0 = count0; 
			}
			else
			{
				c0 = '';
			}
			
			if (desc) 
			{ 
				desc = cartVar['desc_start'] + desc + cartVar['desc_end']; 
			}
			
			f1 = format;
			f1 = f1.replace(/{key}/ig, key);
			f1 = f1.replace(/{name}/ig, name);
			f1 = f1.replace(/{price}/ig, price);
			f1 = f1.replace(/{count}/ig, count);
			f1 = f1.replace(/{maxcount}/ig, maxcount);
			f1 = f1.replace(/{desc}/ig, desc);
			f1 = f1.replace(/{sum}/ig, sum);
			f1 = f1.replace(/{count0}/ig, c0);
			
			out = out + f1;
				
			self.total = self.total + sum;
		}
		
		if (!out) { out = cartVar['cart_empty']; }

		return out;
	}
	
	// изменяется количество для указанного id
	this.changeCount = function(itemId, itemCount)
	{
		var cartData = self.getCartData();
		
		cartData[itemId]['count'] = itemCount;
		
		// console.log(cartData);
		
		self.setCartData(cartData); // записываем данные
	}
	
	
	// проставим колво каждого товара
	this.showItemsCount = function()
	{
		var items = $(cartS['showItemsCount']);
		var cartData = self.getCartData();

		items.each(function(i, e){
			var item = $(e);
			var itemId = item.data('id');
			var count = 0;
			
			if (itemId)
			{
				if(cartData.hasOwnProperty(itemId)) 
				{
					count = Number(cartData[itemId]['count']);
					item.html(cartVar['item_count_start'] + count + cartVar['item_count_end']);
				}
				else
				{
					item.html('');
				}
			}
		});
	}
	
	// показать саму корзину (с полями редактирования)
	this.showCart = function(t1 = '', t2 = '')
	{
		$(cartS['tableItems']).html(
			t1
			+ self.showItems(cartVar['format'], cartVar['count0'])
			+ cartVar['total_start'] 
			+ self.total + cartVar['currency']
			+ cartVar['total_end']
			+ t2
		);
	}
	
	// показать саму корзину (без полей редактирования)
	this.showCartNoEdit = function(t1 = '', t2 = '')
	{
		$(cartS['tableItems']).html(
			t1
			+ self.showItems(cartVar['format_noedit'], cartVar['count0'])
			+ cartVar['total_start'] 
			+ self.total + cartVar['currency']
			+ cartVar['total_end']
			+ t2
		);
	}
	
	// показать саму корзину без пустых товаров и без редактирования
	this.showCartNoEmpty = function(selector, t1 = '', t2 = '')
	{
		$(selector).html(
			t1
			+ self.showItems(cartVar['format_noedit'], cartVar['count0'], false)
			+ cartVar['total_start'] 
			+ self.total + cartVar['currency']
			+ cartVar['total_end']
			+ t2
		);
	}
	
	
	// добавить товар
	this.addItem = function(e)
	{
		var itemId = e.data('id');
		var itemName = e.data('name');
		var itemPrice = e.data('price');
		var itemDesc = e.data('desc');
		var itemMaxCount = e.data('maxcount');
		
		if (!itemId || !itemName || !itemPrice) 
		{ 
			alert('Ошибка добавления товара в корзину (код ошибки add1)');
			return false;
		}

		if (!itemMaxCount) { itemMaxCount = 1 }; 
		if (!itemDesc) { itemDesc = '' }; 
		
		self.addToCart(itemId, itemName, itemPrice, itemMaxCount, itemDesc);
		
		return true;
	}
	
	// показать всего товаров в корзине
	this.showAllCount = function(t1 = '', t2 = '')
	{
		$(cartS['allCount']).html(
			t1
			+ cartVar['showItemsCount_start'] 
			+ self.getCountItems() 
			+ cartVar['showItemsCount_end']
			+ t2
		);
	}
	
	// показать сообщение
	this.showMessage = function(m, css_class = '', duration = 3000)
	{
		var e = $(cartS['message']);
				
		if (css_class)
		{
			e.html(m).addClass(css_class).show().animate({opacity: "hide"}, duration, (function(){ e.removeClass(css_class); }));
		}
		else
		{
			e.html(m).show().animate({opacity: "hide"}, duration);
		}
	}
	
	
	
	// получить input#hidden всех товаров
	this.setHiddenForOrder = function()
	{
		var cartData = self.getCartData();
		var out = '';
		var total = 0;
		var name, price, count, sum, maxcount, f1, total;
		
		for (var key in cartData) 
		{
			count = cartData[key]['count'];
			
			if (count == 0) continue;
			
			name = cartData[key]['name'];
			price = cartData[key]['price'];
			desc = cartData[key]['desc'];
			maxcount = cartData[key]['maxcount'];
			time = cartData[key]['time'];
			sum = price * count;
			
			f1 =      '<input type="hidden" name="cart[items][' + key + '][key]" value="' + key + '">';
			f1 = f1 + '<input type="hidden" name="cart[items][' + key + '][name]" value="' + name + '">';
			f1 = f1 + '<input type="hidden" name="cart[items][' + key + '][desc]" value="' + desc + '">';
			f1 = f1 + '<input type="hidden" name="cart[items][' + key + '][price]" value="' + price + '">';
			f1 = f1 + '<input type="hidden" name="cart[items][' + key + '][count]" value="' + count + '">';
			f1 = f1 + '<input type="hidden" name="cart[items][' + key + '][maxcount]" value="' + maxcount + '">';
			f1 = f1 + '<input type="hidden" name="cart[items][' + key + '][sum]" value="' + sum + '">';
			f1 = f1 + '<input type="hidden" name="cart[items][' + key + '][time]" value="' + time + '">';
			
			out = out + f1;
				
			total = total + sum;
		}
		
		out = out + '<input type="hidden" name="cart[total]" value="' + total + '">';
		

		return out;
	}
	
	
	return this;
	
} // Cart
