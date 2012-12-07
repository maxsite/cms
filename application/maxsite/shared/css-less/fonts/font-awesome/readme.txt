Font Awesome (c) http://fortawesome.github.com/Font-Awesome/

Список всех иконок: http://fortawesome.github.com/Font-Awesome/#all-icons


Подключение в своём less-файле. Главно указать верный путь.
-----------------------------------------------------------
@FONTAWESOMEPATH: '../../../shared/css-less/fonts/font-awesome';
@import url('../../../shared/css-less/fonts/font-awesome/font-awesome.less'); // подключение микса


Использование:
-------------
div.welcome {
	text-align: center;
	
	&:before {
		.font_awesome > .globe;
		margin-right: 10px;
	}
}

