<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) MaxSite CMS
 * http://max-3000.com/
 *
 * Функции для работы с шаблоном
 * Не копируйте этот файл в свой шаблон!
 *
 */

 

# функция возвращает массив $path_url-файлов по указанному $path - каталог на сервере
# $full_path - нужно ли возвращать полный адрес (true) или только имя файла (false)
# $exts - массив требуемых расширений. По-умолчанию - картинки
if (!function_exists('get_path_files'))
{
	function get_path_files($path = '', $path_url = '', $full_path = true, $exts = array('jpg', 'jpeg', 'png', 'gif', 'ico'))
	{
		// если не указаны пути, то отдаём пустой массив
		if (!$path or !$path_url) return array();
		if (!is_dir($path)) return array(); // это не каталог

		$CI = & get_instance(); // подключение CodeIgniter
		$CI->load->helper('directory'); // хелпер для работы с каталогами
		$files = directory_map($path, true); // получаем все файлы в каталоге
		if (!$files) return array();// если файлов нет, то выходим

		$all_files = array(); // результирующий массив с нашими файлами
		
		// функция directory_map возвращает не только файлы, но и подкаталоги
		// нам нужно оставить только файлы. Делаем это в цикле
		foreach ($files as $file)
		{
			if (@is_dir($path . $file)) continue; // это каталог
			
			$ext = substr(strrchr($file, '.'), 1);// расширение файла
			
			// расширение подходит?
			if (in_array($ext, $exts))
			{
				if (strpos($file, '_') === 0) continue; // исключаем файлы, начинающиеся с _
				
				// добавим файл в массив сразу с полным адресом
				if ($full_path)
					$all_files[] = $path_url . $file;
				else
					$all_files[] = $file;
			}
		}
		
		natsort($all_files); // отсортируем список для красоты
		
		return $all_files;
	}
}

# возвращает файлы для favicon
if (!function_exists('default_favicon'))
{
	function default_favicon()
	{
		$all = get_path_files(getinfo('template_dir') . 'images/favicons/', getinfo('template_url') . 'images/favicons/', false);
		return implode($all, '#');
	}
}

# возвращает файлы для компонент
if (!function_exists('default_components'))
{
	function default_components()
	{
		static $all = false; // запоминаем результат, чтобы несколько раз не вызывать функцию get_path_files
		
		if ($all === false)
		{
			$all = get_path_files(getinfo('template_dir') . 'components/', getinfo('template_url') . 'components/', false, array('php'));
		}
		
		return '0||' . tf('Отсутствует') . '#' . implode($all, '#');
	}
}


# возвращает файлы для css-профиля
if (!function_exists('default_profiles'))
{
	function default_profiles()
	{
		$all = get_path_files(getinfo('template_dir') . 'css/profiles/', getinfo('template_url') . 'css/profiles/', false, array('css'));
		return implode($all, '#');
	}
}

# возвращает файлы для логотипа
if (!function_exists('default_header_logo'))
{
	function default_header_logo()
	{
		$all = get_path_files(getinfo('template_dir') . 'images/logos/', getinfo('template_url') . 'images/logos/', false);
		return implode($all, '#');
	}
}


# возвращает каталоги в uploads, где могут храниться файлы для шапки 
if (!function_exists('default_header_image'))
{
	function default_header_image()
	{
		$CI = & get_instance(); // подключение CodeIgniter
		$CI->load->helper('directory'); // хелпер для работы с каталогами
		$all_dirs = directory_map(getinfo('uploads_dir'), true); // только в uploads
		
		$dirs = array();
		foreach ($all_dirs as $d)
		{
			// нас интересуют только каталоги
			if (is_dir( getinfo('uploads_dir') . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles')
			{
				$dirs[] = $d;
			}
		}
		
		natsort($dirs);
		
		return '-template-||' . tf('Каталог шаблона') . '#' . implode($dirs, '#');
	}
}


# вывод подключенных css-профилей
if (!function_exists('default_out_profiles'))
{
	function default_out_profiles()
	{
		if ($default_profiles = mso_get_option('default_profiles', 'templates', array())) // есть какие-то профили оформления
		{
			$css_out = '';
			
			// theme-профили подключаются как link rel="stylesheet
			foreach($default_profiles as $css_file)
			{
				$fn = 'css/profiles/' . $css_file;
				
				$link = strpos($css_file, 'theme-'); // это theme- ?
				
				if ($link !== false and $link === 0)
				{
					mso_add_file($fn); // подключаем внешими стилями
				}
				else
				{
					$css_out .= mso_out_css_file($fn, false, false); // получение и обработка CSS из файла
				}
			}
			
			if ($css_out) 
				echo NR . '<style>' . $css_out . '</style>' . NR;
		}
	}
}

# функция возвращает полный путь к файлу компонента для указанной опции
# $option - опция
# $def_file - файл по умолчанию
# пример использования
# if ($fn = get_component_fn('default_header_component2', 'menu.php')) require($fn);
if (!function_exists('get_component_fn'))
{
	function get_component_fn($option = '', $def_file = '')
	{
		if ($fn = mso_get_option($option, 'templates', $def_file)) // получение опции
		{
			if (file_exists(getinfo('template_dir') . 'components/' . $fn)) // проверяем если файл в наличии
				return (getinfo('template_dir') . 'components/' . $fn); // да
			else
			{
				// нет, проверяем $def_file
				if (file_exists(getinfo('template_dir') . 'components/' . $def_file))
				{
					if ($def_file) return getinfo('template_dir') . 'components/' . $def_file;
						else return false;
				}
			}
		}
		
		return false; // ничего нет
	}
}

# функция подключает файлы css-style установленных компонентов и выводит их содержимое в едином блоке <style>
# использовать в head 
# $component_options - названия опций, которыми определяются компоненты в шаблоне
# css-файл компонента находится в общем css-каталоге шаблона с именем компонента, например menu.php и menu.css
if (!function_exists('out_component_css'))
{
	function out_component_css($component_options = array('default_header_component1', 'default_header_component2', 'default_header_component3', 'default_header_component4', 'default_header_component5', 'default_footer_component1', 'default_footer_component2', 'default_footer_component3', 'default_footer_component4', 'default_footer_component5'))
	{
		
		// $css_files = array(); // результирующий массив css-файлов
		$css_out = ''; // все стили из файлов
		
		// проходимся по всем заданным опциям
		foreach($component_options as $option)
		{
			// и если они определены
			if ($fn = mso_get_option($option, 'templates', false))
			{
				// в имени файла следует заменить расширение php на css
				$fn = 'components/css/' . str_replace('.php', '.css', $fn);
				//$css_out .= mso_out_css_file($fn, false, false); // получение и обработка CSS из файла
				mso_add_file($fn); // подключаем внешими стилями
			}
		}
		
		/*
		if ($css_out) // если есть что выводить
			echo NR . '<style>' . $css_out . '</style>' . NR;
		*/	
	}
}


# типовой вывод секции HEAD
# можно использовать в header.php
if (!function_exists('mso_default_head_section'))
{
	function mso_default_head_section($options = array())
	{
		
		// ob_start(); # задел на будущее - буферизация
	// <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=8"><![endif]-->
		echo 
'<!DOCTYPE HTML>
<html' . mso_get_val('head_section_html_add') . '><head>' . mso_hook('head-start') . '
	<meta charset="UTF-8">
	<title>' . mso_head_meta('title') . '</title>
	<meta name="generator" content="MaxSite CMS">
	<meta name="description" content="' . mso_head_meta('description') . '">
	<meta name="keywords" content="' . mso_head_meta('keywords') . '">
	<link rel="shortcut icon" href="' . getinfo('template_url') . 'images/favicons/' . mso_get_option('default_favicon', 'templates', 'favicon1.png') . '" type="image/x-icon">
	';
	
		if (mso_get_option('default_canonical', 'templates', 0)) echo mso_link_rel('canonical');
	
		echo NT . '<!-- RSS -->' . NT . mso_rss();
		
		if ($fn = mso_fe('custom/head-start.php')) require($fn);

		echo NT . '<!-- CSS -->'; 
		
		// если есть style.php в шаблоне, то подключается только он, исключая все остальные файлы
		if ($fn = mso_fe('css/style.php')) 
		{
			require($fn);
		}
		else
		{
			echo NT . '<link rel="stylesheet" href="'; 
			
				if (file_exists(getinfo('template_dir') . 'css/css.php')) echo getinfo('template_url') . 'css/css.php'; 
				else 
				{
					if (file_exists(getinfo('template_dir') . 'css/my_style.css')) // если есть css/my_style.css
					{
						echo getinfo('template_url') . 'css/my_style.css'; 
					}
					else
					{ 
						if (file_exists(getinfo('template_dir') . 'css/style-all-mini.css')) // если есть style-all-mini.css
						{
							echo getinfo('template_url') . 'css/style-all-mini.css'; 
						}
						elseif (file_exists(getinfo('template_dir') . 'css/style-all.css')) // нет mini, подключаем обычный файл
						{
							echo getinfo('template_url') . 'css/style-all.css'; 
						}
						else echo getinfo('templates_url') . 'default/css/style-all-mini.css'; 
					}
				}
				
			echo '">';

			
			// подключение var_style.css
			
			// если есть var_style.php, то используем только его
			if ($fn = mso_fe('css/var_style.php')) 
			{
				require($fn);
			}
			else
			{
				$var_file = '';
				
				if (file_exists(getinfo('template_dir') . 'css/var_style.css')) 
					$var_file = getinfo('template') . '/css/var_style.css';	
				elseif (file_exists(getinfo('templates_dir') . 'default/css/var_style.css')) 
					$var_file = 'default/css/var_style.css';
				
				// если var_style.css нулевой длины, то не подключаем его
				if (filesize(getinfo('templates_dir') . $var_file))
					echo NT . '<link rel="stylesheet" href="' . getinfo('templates_url') . $var_file . '">';	
			}
		
		} // else style.php
		
		echo NT . '<link rel="stylesheet" href="' . getinfo('template_url') . 'css/print.css" media="print">';
		
		// если есть fonts.css, то подключаем его
		// файл специально используется для подгрузки шрифтов через @import 
		mso_add_file('css/fonts.css');
		
		// и import.css для каких-то других @import
		mso_add_file('css/import.css');
		
		out_component_css();
			
		echo NT . mso_load_jquery();

		echo NT . '<!-- plugins -->' . NR;
		mso_hook('head');
		echo NT . '<!-- /plugins -->' . NR;

		mso_add_file('css/add_style.css');
		
		default_out_profiles();

		if ($fn = mso_fe('custom/head.php')) require($fn);
		if ($f = mso_page_foreach('head')) require($f);
		if (function_exists('ushka')) echo ushka('head');
		
		if (file_exists(getinfo('template_dir') . 'js/my.js')) 
			echo '	<script src="' . getinfo('template_url') . 'js/my.js"></script>';
		
		if ($my_style = mso_get_option('my_style', 'templates', '')) echo NR . '<!-- custom css-my_style -->' . NR . '<style>' . NR . $my_style . '</style>';
		
		mso_hook('head-end');

		if (function_exists('ushka')) echo ushka('google_analytics_top');
		
		/*
		# буферизация на будущее
		$head = ob_get_contents();
		ob_end_clean();
		echo $head;
		*/
		
		echo NR . '</head>';
		if (!$_POST) flush();
	}
}


/* 
Вспомогательная функция mso_section_to_array
преобразование входящего текста опции в массив
по каждой секции по указанному патерну
Вход:

[slide]
link = ссылка изображения
title = подсказка
img = адрес картинки
text = текст с html без переносов. h3 для заголовка
p_line1 = пагинация 1 линия
p_line2 = пагинация 2 линия
[/slide]

Паттерн (по правилам php):
	'!\[slide\](.*?)\[\/slide\]!is'

Выход:

Array
(
	[0] => Array
		(
			[link] => ссылка изображения
			[title] => подсказка
			[img] => адрес картинки
			[text] => текст с html без переносов. h3 для заголовка
			[p_line1] => пагинация 1 линия
			[p_line2] => пагинация 2 линия
		)
 )

$array_default - стартовый массив опций на случай, если в опции нет обязательного ключа
например 
array('link'=>'', 'title'=>'', 'img'=>'', 'text'=>'', 'p_line1'=>'', 'p_line2'=>'')

Если $simple = true, то вхродящий паттерн используется как слово из которого
будет автоматом сформирован корректный паттерн по шаблону [слово]...[/слово]

*/

if (!function_exists('mso_section_to_array'))
{
	function mso_section_to_array($text, $pattern, $array_default = array(), $simple = false)
	{
	
		if ($simple) $pattern = '!\[' . $pattern . '\](.*?)\[\/' . $pattern . '\]!is';
		
		// $array_result - массив каждой секции (0 - все вхождения)
		if (preg_match_all($pattern, $text, $array_result))
		{
			// массив слайдов в $array_result[1]
			// преобразуем его в массив полей
			
			$f = array(); // массив для всех полей
			$i = 0; // счетчик 
			foreach($array_result[1] as $val)
			{
				$val = trim($val);
				
				if (!$val) continue;
				
				$val = str_replace(' = ', '=', $val);
				$val = str_replace('= ', '=', $val);
				$val = str_replace(' =', '=', $val);
				$val = explode("\n", $val); // разделим на строки
				
				$ar_val = array();
				
				$f[$i] = $array_default;
				
				foreach ($val as $pole)
				{
					$ar_val = explode('=', $pole); // строки разделены = type = select
					if ( isset($ar_val[0]) and isset($ar_val[1]))
						$f[$i][$ar_val[0]] = $ar_val[1];
				}
				
				$i++;
			}
			
			return $f;
		}
		
		return array(); // не найдено
	}
}


# получает css из указанного файла
# в css-файле можно использовать php
# осуществляется сжатие css
# автозамена [TEMPLATE_URL] на url-шаблона
# функция возвращает только стили, без обрамляющего <style>
# Если <style> нужны, то $tag_style = true
# Если нужен сразу вывод в браузер, то $echo = true
if (!function_exists('mso_out_css_file'))
{
	function mso_out_css_file($fn, $tag_style = true, $echo = true)
	{
		
		$fn = getinfo('template_dir') . $fn;
		
		$out = '';
		if (file_exists($fn)) // проверяем если ли файл в наличии
		{
			if ($r = @file_get_contents($fn)) $out .= $r . NR; // получаем содержимое
		
			if ($out) 
			{
				ob_start();
				eval( '?>' . stripslashes($out) . '<?php ');
				$out = ob_get_contents();
				ob_end_clean();
				
				$out = str_replace('[TEMPLATE_URL]', getinfo('template_url'), $out);
				$out = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $out);
				$out = str_replace(array('; ', ' {', ': ', ', '), array(';', '{', ':', ','), $out);
			}
		
			if ($tag_style) $out = NR . '<style>' . $out . '</style>' . NR;
			if ($echo) echo $out;
		}
		
		return $out;
	}
}


# формирование <script> с внешним js-файлом или
# формирование <link rel="stylesheet> с внешним css-файлом
# имя файла указывается относительно каталога шаблона
# если файла нет, то ничего не происходит
if (!function_exists('mso_add_file'))
{
	function mso_add_file($fn)
	{
		if (file_exists(getinfo('template_dir') . $fn)) 
		{
			$ext = substr(strrchr($fn, '.'), 1);// расширение файла
			if ($ext == 'js') echo NT . '<script src="' . getinfo('template_url') . $fn . '"></script>';
			elseif ($ext == 'css') echo NT . '<link rel="stylesheet" href="' . getinfo('template_url') . $fn . '">';
			elseif ($ext == 'less') echo NT . '<link rel="stylesheet/less" href="' . getinfo('template_url') . $fn . '" type="text/css">';
			
		}
	}
}

# получение адреса первой картинки IMG в тексте
# адрес обрабатывается, чтобы сформировать адрес полный (full), миниатюра (mini) и превью (prev)
# результат записит от значения $res
# если $res = true => найденный адрес или $default
# если $res = 'mini' => адрес mini
# если $res = 'prev' => адрес prev
# если $res = 'full' => адрес full
# если $res = 'all' => массив из всех сразу:
#  		[full] => http://сайт/uploads/image.jpg
#  		[mini] => http://сайт/uploads/mini/image.jpg
#  		[prev] => http://сайт/uploads/_mso_i/image.jpg
if (!function_exists('mso_get_first_image_url'))
{
	function mso_get_first_image_url($text = '', $res = true, $default = '')
	{
		$pattern = '!<img.*?src="(.*?)"!i';
		
		//$pattern = '!<img.+src=[\'"]([^\'"]+)[\'"].*>!i';
		
		preg_match_all($pattern, $text, $matches);
		
		//pr($matches);
		if (isset($matches[1][0])) 
		{
			$url = $matches[1][0];
			if(empty($url)) $url = $default;
		}
		else
			$url = $default;
		
		//_pr($url,1);
		if (strpos($url, '/uploads/smiles/') !== false) return ''; // смайлики исключаем
		
		if ($res === true) return $url;
		
		$out = array();

		// если адрес не из нашего uploads, то отдаем для всех картинок исходный адрес
		if (strpos($url, getinfo('uploads_url')) === false) 
		{
			$out['mini'] = $out['full'] = $out['prev'] = $url;
			
			if ($res == 'mini' or $res == 'prev' or $res == 'full') return $out['mini'];
				else return $out;
		
		}
		
		if (strpos($url, '/mini/') !== false) // если в адресе /mini/ - это миниатюра
		{
			$out['mini'] = $url;
			$out['full'] = str_replace('/mini/', '/', $url);
			$out['prev'] = str_replace('/mini/', '/_mso_i/', $url);
		}
		elseif(strpos($url, '/_mso_i/') !== false) // если в адресе /_mso_i/ - это превью 100х100
		{
			$out['prev'] = $url;
			$out['full'] = str_replace('/_mso_i/', '/', $url);
			$out['mini'] = str_replace('/_mso_i/', '/mini/', $url);
		}
		else // обычная картинка
		{
			$fn = end(explode("/", $url)); // извлекаем имя файла
			$out['full'] = $url;
			$out['mini'] = str_replace($fn, 'mini/' . $fn, $url);
			$out['prev'] = str_replace($fn, '_mso_i/' . $fn, $url);
		}
		
		if ($res == 'mini') return $out['mini'];
		elseif ($res == 'prev') return $out['prev'];
		elseif ($res == 'full') return $out['full'];
		else return $out;
	}
}

# Функция возвращает путь к файлу относительно текущего шаблона
# если файла нет, то возвращается false
# 	if ($fn = mso_fe('stock/page_out/page-out.php')) require($fn);
if (!function_exists('mso_fe'))
{
	function mso_fe($file)
	{
		$file = getinfo('template_dir') . $file;

		if (file_exists($file)) 
			return $file;
		else 
			return false;
	}
}

# end file