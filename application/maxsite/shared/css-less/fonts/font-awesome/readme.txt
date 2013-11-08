Font Awesome (c) http://fortawesome.github.com/Font-Awesome/

Список всех иконок: http://fortawesome.github.io/Font-Awesome/icons/

Скопировать font-awesome.less в css-less каталог своего шаблона.
css-less/fonts/font-awesome.less

Подключение в своём less-файле. Главное указать верный путь.

Файлы шрифта (ttf, уще и т.д.) копировать не нужно!

-----------------------------------------------------------
@FONTAWESOMEPATH: '../../../shared/css-less/fonts/font-awesome';
@import url('fonts/font-awesome.less'); // подключение микса


Использование:
-------------
div.welcome {
	text-align: center;
	
	&:before {
		.font_awesome > .globe;
		margin-right: 10px;
	}
}

