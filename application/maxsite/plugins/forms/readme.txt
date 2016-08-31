Пример формы

[form]

[options]
email = admin@site.com
[/options]

[files]
file_count = 1
file_type = jpg|jpeg|png|svg
file_max_size = 200
file_description = Скриншоты
file_tip = Выберите для загрузки файлы (jpg, jpeg, png, svg) размером до 200 Кб
[/files]

[field] 
require = 1
type = select
description = Тема письма
values = Пожелания по сайту # Нашел ошибку на сайте # Подскажите, пожалуйста
default = Пожелания по сайту 
subject = 1
[/field]

[field]
require = 1   
type = text
description = Ваше имя
placeholder = Ваше имя
[/field]

[field]
require = 1   
type = text
clean = email
description = Ваш email
placeholder = Ваш email
from = 1
[/field]

[field] 
require = 0   
type = url
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


Форма состоит из секций

[form]

	[options]
		опции
	[/options]

	[files]
		опции, если нужно загружать файлы
	[/files]

	[field] 
		поле 1
	[/field]

	[field] 
		поле 2 и т.д.
	[/field]

[/form]


ПАРАМЕТРЫ options
-----------------
email = mylo@sait.com - куда отправляем письмо
redirect = http://site.com/ - куда редиректим после отправки
redirect_pause = 2 —  пауза перед редиректом секунд
ushka = ушка - ушка, которая выведется после формы
reset = 1 - вывод кнопки «Сбросить форму»
require_title = * - текст для обязательного поля
subject = Обратная связь - тема email-письма
antispam = Наберите число — текст антиспама
captcha = 1 - если указать эту опцию, то будет вместо стандартной проверки antispam будет использоваться активный плагин капчи.
subject = Тема письма — тема письма. Если пусто, то используется из [field]
from = bill@gates.us — from (от кого) Если пусто, то используется из [field]


ПАРАМЕТРЫ files
-----------------
file_count = 1 - сколько файлов можно приложить
file_type = jpg|jpeg|png|svg - разрешённые типы файлов
file_max_size = 200  - максимально допустимый размер файла в килобайтах (Кб)
file_description = Скриншоты - заголовок полей для выбора файла(-ов)
file_tip = Выберите для загрузки файлы (jpg, jpeg, png, svg) размером до 200 Кб - текст подсказки
	
	
ПАРАМЕТРЫ ПОЛЕЙ field
---------------------
type - тип поля
	textarea
	select
	checkbox
	text url email password search number hidden... (из HTML5) (формируется как input)

placeholder = Подсказка для поля
tip = Подсказка к полю внизу
value = значение по-умолчанию
attr = class="gorod" — html-атрибуты элемента

values - для select'а значения через #: Первый # Второй # Третий
default - для select'а дефолтное значение:  Второй
default - для checkbox'а дефолтное значение: 0 или 1

subject = 1 — значит значение поля используется для subject email-письма
from = 1 - это поле подставляется как from (от кого)

clean = base — фильтрация поля
clean = xss|htmlspecialchars
	способ валидации поля, согласно функции mso_clean_str() 
	Правила указываются через | (без пробелов)
	По-умолчанию все поля обрабатываются как base
	Варианты:
	 	xss - xss-обработка
	 	trim - удаление ведущих и конечных пустых символов
	 	integer или int - преобразовать в число
	 	strip_tags - удалить все тэги
	 	htmlspecialchars - преобразовать в html-спецсимволы
		valid_email или email - если это неверный адрес, вернет пустую строчку
		not_url - удалить все признаки url
	
	если правило равно base, то cработают правила: trim|xss|strip_tags|htmlspecialchars
