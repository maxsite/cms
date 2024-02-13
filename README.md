[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://stand-with-ukraine.pp.ua/)

MaxSite CMS
================================================================================
Система управления для сайтов, блогов, бизнес-сайтов, визиток, лендингов и т.д. Система отлично подходит обычным пользователям, фрилансерам и вебстудиям. MaxSite CMS обладает большим функционалом и высокой скоростью работы. Сделано в Украине.

[Официальный сайт](https://max-3000.com/)

ТРЕБОВАНИЯ СИСТЕМЫ
--------------------------------------------------------------------------------
* Возможность работы с .htaccess и включенный mod_rewrite.
* База данных MySQL/MariaDB: 5.6 и выше. База должна быть уже создана.
* Минимальня версия PHP: 7.1 (рекомендуется 8.x). (Для PHP 5 используйте MaxSite CMS 104, скачать которую можно на [странице релизов](https://github.com/maxsite/cms/tags).)

Дополнительная информация по установке MaxSite CMS: https://max-3000.com/doc/install

Примечание. После установки системы вы можете добавить [демо-данные](https://github.com/maxsite/demo_for_maxsite_cms).


АВТОУСТАНОВЩИК MAXSITE CMS
--------------------------------------------------------------------------------
С помощью специального автоустановщика можно как установить новую систему, так и обновить 
существующий сайт.

Автоустановщик — это несколько небольших файлов, которые нужно загрузить на свой сервер. 
В файле «key.php» необходимо будет указать ключ доступа. 

После этого в браузере нужно набрать «https://вашсайт/update-maxsite/?вашключ».
Автоустановщик сам загрузит архив последней версии MaxSite CMS на сервер, распакует его 
и выполнит обновление.

Прямая ссылка для загрузки установщика: https://max-3000.com/uploads/update-maxsite.zip

Если это новая установка MaxSite CMS, то будет предложено перейти к инсталяции системы, где нужно 
будет указать параметры доступа к базе данных и т.д.

Если это обновление существующего сайта, то перед обновлением ознакомьтесь с рекомендациями 
по обновлению MaxSite CMS, описанными ниже.


НОВАЯ УСТАНОВКА
--------------------------------------------------------------------------------
1. Загрузите все файлы на сервер.
2. Наберите в браузере «http://сайт/install» и следуйте инструкциям. 

Примечание. Если по какой-то причине при установке произошел сбой сервера, то повторную установку
можно выполнить, предварительно удалив файл «application/config/database.php», а также созданные 
таблицы с помощью phpMyAdmin.


ОБНОВЛЕНИЕ СУЩЕСТВУЮЩЕГО САЙТА
--------------------------------------------------------------------------------
В анонсе каждого выпуска MaxSite CMS может указываться рекомендуемый вариант обновления и особенности перехода к новой версии. Учитывайте это при обновлении своего сайта.

Важно:
* Если у вас версия ниже 106, то выполняйте обновление универсальным способом.
* Если меняется адрес сайта, то предварительно снимите опцию определения главного зеркала сайта в плагине Range_url. 
* После обновления желательно сбросить кэш.

MaxSite CMS не вносит изменений в базу данных при обновлении, поэтому вы можете использовать любую версию системы. Если по какой-то причине, обновление не устраивает, вы можете загрузить файлы предыдущей версии.


УНИВЕРСАЛЬНЫЙ СПОСОБ ОБНОВЛЕНИЯ
--------------------------------------------------------------------------------
Данный способ обновления подходит для любой версии MaxSite CMS.

1. Переименуйте каталоги 
	«system» в «system-old».
	«application» в «application-old».

2. Загрузите файлы новой версии MaxSite CMS на сервер.

3. Скопируйте из «application-old» в новый «application» свой шаблон «/maxsite/templates/ШАБЛОН».

4. Если вы устанавливали сторонние плагины, то скопируйте и их.

5. Перенесите конфигурацию базы данных и сайта из «application-old» в новый «application»
	/config/database.php  (используйте новый «database.php-distr»)
	/maxsite/mso_config.php (используйте новый «mso_config.php-distr»)

6. Проверьте работоспособность сайта.

7. Каталоги «system-old» и «application-old» после проверки можно удалить.

***

(с) MaxSite CMS, 2008-2024
