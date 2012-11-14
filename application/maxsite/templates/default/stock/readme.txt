Здесь могут находиться произвольные скрипты/изображения/стили и т.д. для 
использования в своём шаблоне. 

Каждый набор в своем подкаталоге.

ПРИМЕРЫ
-------

* Подключать PHP, например, так:

	if (file_exists(getinfo('template_dir') . 'stock/myscript/myscript.php')) 
			require(getinfo('template_dir') . 'stock/myscript/myscript.php');

	Если файл находится в текущем шаблоне, то можно так:
	
	if ($fn = mso_fe('stock/myscript/myscript.php')) require($fn);


* Подключение css-стилей или js возможно в HEAD-секции (custom/head.php):

	# вывод css-кода из указанного файла в <style>
	mso_out_css_file('stock/myscript/myscript.css');

	# подключение внешнего js или css-файла 
	mso_add_file('stock/myscript/myscript.css');
	mso_add_file('stock/myscript/myscript.js');


* Подключение в css/var_style.less файла /stock/less/mso-button/mso-button.less

	@import url('../stock/less/mso-button/mso-button.less');
