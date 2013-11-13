<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * 
 * Компиляция less в css
 *
*/

// укажем less-файл - полный путь
$less_file = getinfo('template_dir') . 'css-less/style.less';

// выходной файл - путь относительно каталога шаблона
$css_file = 'css/style.css';

// если нет style.less, то отдаем style.css
if (!file_exists($less_file))
{
	mso_add_file($css_file);
}
else
{
	echo mso_lessc(
			$less_file, 
			getinfo('template_dir') . $css_file, 
			getinfo('template_url') . $css_file, 
			true, 	// разрешить использование кэша LESS
			true, 	// использовать сжатие css-кода?
			true 	// если включено сжатие, то удалять переносы строк?
			);
}

# end file