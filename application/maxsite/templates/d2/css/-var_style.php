<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * 
 * Компиляция less в css
	
*/

// укажем less-файл - полный путь
$less_file = getinfo('template_dir') . 'css-less/var_style.less';

// выходной файл - путь относительно каталога шаблона
$css_file = 'css/var_style.css';

// если нет var_style.less, то отдаем var_style.css
if (!file_exists($less_file))
{
	mso_add_file($css_file);
}
else
{
	echo mso_lessc(
			
			// входной less-файл (полный путь на сервере)
			$less_file, 

			// выходной css-файл (полный путь на сервере)
			getinfo('template_dir') . $css_file, 

			// полный http-адрес css-файла для подключения через <link ...> в HEAD
			getinfo('template_url') . $css_file, 

			false, 	// разрешить использование кэша LESS
					// сравнивается время var_style.less и var_style.css 
					// если первый новее, то происходит компиляция
					// при отладке кэш лучше отключить
					// на рабочем сайте, можно включить

			false, 	// использовать сжатие css-кода?

			true 	//если включено сжатие, то удалять переносы строк?
					// при false - лучше читабельность кода
			);
}

# end file