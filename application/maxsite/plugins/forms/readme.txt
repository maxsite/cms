Пример формы:


[form]
[email=mylo@sait.com]
[redirect=http://site.com/]
[subject=Пожелания по сайту # Нашел ошибку на сайте # Подскажите, пожалуйста]
[ushka=ушка, которая выведется после формы]
[nocopy]
[noreset]   


[field]
require = 0   
type = text
description = Ваш город
tip = Указывайте вместе со страной
value = значение по-умолчанию
attr = class="gorod" (атрибуты поля)
[/field]

[field] 
require = 0   
type = text
type_text = url
description = Сайт
tip = Вы можете указать адрес своего сайта (если есть)
placeholder = Адрес сайта
[/field]

[field] 
require = 0   
type = text
description = Телефон
tip = Телефон лучше указывать с кодом города/страны
placeholder = Введите свой телефонный номер
[/field] 


[field] 
require = 1 
type = textarea 
description = Ваш вопрос
placeholder = О чем вы хотите написать?
[/field]

[/form]


ПАРАМЕТРЫ ПОЛЕЙ field
---------------------

type - тип поля
	text
	textarea
	select
	hidden


type_text - тип для text (input)
	type_text = url
	type_text = email
	type_text = password
	type_text = search
	type_text = search
	type_text = number

placeholder = Подсказка для поля

tip = Подсказка к полю

value = значение по-умолчанию

attr = class="gorod"
	html-атрибуты элемента


ПАРАМЕТРЫ ФОРМЫ
---------------
[email=mylo@sait.com] - куда отправляем письмо
[redirect=http://site.com/] - куда редиректим после отправки
[subject=Пожелания по сайту # Нашел ошибку на сайте # Подскажите, пожалуйста] - темы письма черз #
[ushka=ушка] - ушка, которая выведется после формы
[nocopy] - отключить вывод «Отправить копию на ваш email»
[noreset] - отключить вывод кнопки «Сбросить форму»


ПРОЧЕЕ
------
[subject] если указывается через #, то формируется выпадающий select. Если нет #, то это
обычный редактируемый input. Если тема письма начинается с _ то это скрытый input.

	Выпадающий select
	[subject=Пожелания по сайту # Нашел ошибку на сайте # Подскажите, пожалуйста]

	
	Редактируемое поле
	[subject=Пожелания по сайту]

	Скрытое поле
	[subject=_Обратная связь]
	
 
