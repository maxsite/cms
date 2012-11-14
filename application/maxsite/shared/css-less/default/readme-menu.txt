// типовой блок стилей меню для var_style.less

ul.menu {
	background: #AAA; // фон меню
	
	@menu_color1: white; // цвет верхнего пункта
	
	@select_color: red; // цвет при наведении и выбранного пункта
	@select_background: #BBB; // фон при наведении и выбранного пункта
	
	@height_menu: 32px; // высота меню
	
	li {
		height: @height_menu;
		line-height: @height_menu;
		
		a {
			font-weight: normal; // начертание
			color: @menu_color1; // цвет пункта
			padding: 0 10px 0 10px; // отступ между пунктами
			
			// при наведении
			&:hover {
				background: @select_background; 
				color: @select_color;
			}
		}
		
		// выделенный пункт
		&.selected a {
			background: @select_background;
			color: @select_color;
		}
		
		// подпункты
		&.group ul li a {
			padding: 0 5px 0 10px; // внутренние отступы
			background: #EEE; // фон
			color: #404040;  // цвет
			border-top: 1px solid #BBB; // верхняя границы 
			// font-weight: normal; // начертание

			// при наведении
			&:hover {
				color: white; // цвет
				background: #CCC; // фон
			}
		}
		
		// тень для выпадающего подпункта
		&.group ul {
			.box_shadow(1px, 1px, 2px, gray);
		}
	}
} // end стили меню
