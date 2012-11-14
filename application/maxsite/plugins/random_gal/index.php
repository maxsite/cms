<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function random_gal_autoload($args = array())
{
	mso_register_widget('random_gal_widget', t('Галерея')); # регистрируем виджет
	mso_hook_add('custom_page_404', 'random_gal_custom_page_404'); # хук для подключения к шаблону
	mso_hook_add('head', 'random_gal_head');
}

# функция выполняется при деинсталяции плагина
function random_gal_uninstall($args = array())
{	
	mso_delete_option_mask('random_gal_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function random_gal_widget($num = 1) 
{
	$widget = 'random_gal_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return random_gal_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function random_gal_widget_form($num = 1) 
{
	$widget = 'random_gal_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['gal']) ) $options['gal'] = '';
	if ( !isset($options['galother']) ) $options['galother'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['style']) ) $options['style'] = '';
	if ( !isset($options['style_img']) ) $options['style_img'] = '';
	if ( !isset($options['html']) ) $options['html'] = '';
	if ( !isset($options['sort']) ) $options['sort'] = 'random';
	if ( !isset($options['filter']) ) $options['filter'] = '';
	if ( !isset($options['class']) ) $options['class'] = '';
	if ( !isset($options['type']) ) $options['type'] = 'image'; // тип вывода: слайдер или обычный
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');

	// получим все каталоги в uploads
	$all_dirs = directory_map(getinfo('uploads_dir'), true); // только в uploads
	$out = array('uploads/'=>'uploads/');
	foreach ($all_dirs as $d)
	{
		// это каталог
		if (is_dir( getinfo('uploads_dir') . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles') 
			$out[$d] = $d;
	}
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Галерея'), form_dropdown( $widget . 'gal', $out, $options['gal']), '');
	
	$form .= mso_widget_create_form(t('несколько, через |'), form_input( array( 'name'=>$widget . 'galother', 'value'=>$options['galother'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество'), form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ), '');
	
	$form .= mso_widget_create_form(t('Режим отображения'), form_dropdown( $widget . 'type', 
		array(
			'image'=>t('Картинками'), 
			'slider'=>t('Слайдер'), 
			), $options['type']), '');
	
	$form .= mso_widget_create_form(t('CSS-cтиль блока'), form_input( array( 'name'=>$widget . 'style', 'value'=>$options['style'] ) ), '');
	
	$form .= mso_widget_create_form(t('Дополнит. class'), form_input( array( 'name'=>$widget . 'class', 'value'=>$options['class'] ) ), '');
	
	$form .= mso_widget_create_form(t('CSS-cтиль IMG'), form_input( array( 'name'=>$widget . 'style_img', 'value'=>$options['style_img'] ) ), '');
	
	$form .= mso_widget_create_form(t('Свой HTML-блок'), form_input( array( 'name'=>$widget . 'html', 'value'=>$options['html'] ) ), '');
	
	$form .= mso_widget_create_form(t('Сортировка'), form_dropdown( $widget . 'sort', 
		array(
			'random'=>t('Случайно'), 
			'name_file'=>t('По именам файлов'), 
			'name_file_desc'=>t('По именам файлов (обратный порядок)'), 
			'description'=>t('По описанию'),
			'description_desc'=>t('По описанию (обратный порядок)'),
			'name_file_description'=>t('По именам, потом по описанию'),
			'description_name_file'=>t('По описанию, потом по именам'),
			'datefile'=>t('По времени создания файлов'),
			'datefile_desc'=>t('По времени создания файлов (обратный порядок)'),
			
			), $options['sort']), '');
	
	$form .= mso_widget_create_form(t('Фильтр'), form_input( array( 'name'=>$widget . 'filter', 'value'=>$options['filter'] ) ), t('Можно указать фразу, с которой должно начинаться хотя бы одно слово в описании файла.'));
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function random_gal_widget_update($num = 1) 
{
	$widget = 'random_gal_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['gal'] = mso_widget_get_post($widget . 'gal');
	$newoptions['galother'] = mso_widget_get_post($widget . 'galother');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['style'] = mso_widget_get_post($widget . 'style');
	$newoptions['style_img'] = mso_widget_get_post($widget . 'style_img');
	$newoptions['html'] = mso_widget_get_post($widget . 'html');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');
	$newoptions['filter'] = mso_widget_get_post($widget . 'filter');
	$newoptions['class'] = mso_widget_get_post($widget . 'class');
	$newoptions['type'] = mso_widget_get_post($widget . 'type');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}


# вспомогательные для сортировки массива
function random_gal_cmp_name_file($a, $b) 
{
	if ( $a['file'] == $b['file'] ) return 0;
	return ( $a['file'] > $b['file'] ) ? 1 : -1;
}

function random_gal_cmp_name_file_desc($a, $b) 
{
	if ( $a['file'] == $b['file'] ) return 0;
	return ( $a['file'] > $b['file'] ) ? -1 : 1;
}

function random_gal_cmp_description($a, $b) 
{
	if ( $a['descritions'] == $b['descritions'] ) return 0;
	return ( $a['descritions'] > $b['descritions'] ) ? 1 : -1;
}

function random_gal_cmp_description_desc($a, $b) 
{
	if ( $a['descritions'] == $b['descritions'] ) return 0;
	return ( $a['descritions'] > $b['descritions'] ) ? -1 : 1;
}

function random_gal_cmp_datefile($a, $b) 
{
	if ( $a['datefile'] == $b['datefile'] ) return 0;
	return ( $a['datefile'] > $b['datefile'] ) ? 1 : -1;
}

function random_gal_cmp_datefile_desc($a, $b) 
{
	if ( $a['datefile'] == $b['datefile'] ) return 0;
	return ( $a['datefile'] > $b['datefile'] ) ? -1 : 1;
}


function random_gal_head($args = array())
{
	echo mso_load_jquery('jquery.nivo.slider.js') 
		. mso_load_style(getinfo('plugins_url') . 'random_gal/random_gal.css');
		
	return $args;
}

# функция плагина
function random_gal_widget_custom($options = array(), $num = 1)
{
	$out = '';
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['gal']) ) $options['gal'] = 'uploads/';
	if ( !isset($options['galother']) ) $options['galother'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['style']) ) $options['style'] = ''; // стиль div блока
	if ( !isset($options['style_img']) ) $options['style_img'] = ''; // стиль каждой картинки
	if ( !isset($options['html']) ) $options['html'] = ''; // дополнительный html в конце вывода
	if ( !isset($options['sort']) ) $options['sort'] = 'random'; 
	if ( !isset($options['filter']) ) $options['filter'] = '';
		else $options['filter'] = trim(mb_strtolower($options['filter'], 'UTF-8'));
	if ( !isset($options['class']) ) $options['class'] = ''; // дополнительный class div блока
	if ( !isset($options['type']) ) $options['type'] = 'image'; // дополнительный class div блока

	if ($options['gal'] == 'uploads/') $options['gal'] = '';
	
	$CI = & get_instance();
	$CI->load->helper('file');
	$CI->load->helper('directory');	
	
	// получим список всех файлов в указаном каталоге
	if ($options['gal']) $options['gal'] .= '/';
	
	if ($options['galother']) 
	{
		if (trim($options['galother']) == '#all')
		{
			// это все каталоги в uploads
			// вручную сформируем galother
			$_dirs = directory_map(getinfo('uploads_dir'), true);
			
			$o = '/';
			foreach ($_dirs as $d) 
			{
				if (@is_dir(getinfo('uploads_dir') . $d)) // это каталог
				{
					if ($d != 'mini' and $d != 'smiles' and $d != '_mso_float' and $d != '_mso_i')
					$o .= $d . '|';
				}
			}
			$options['galother'] = $o;
		}
		
		// если указано несколько каталогов, то потрошим срочку 
		$all_dirs = explode('|', $options['galother']);
		
		foreach($all_dirs as $key=>$var)
			$all_dirs[$key] = trim($var) . '/'; 
		
	}
	else $all_dirs = array($options['gal']);
	
	$all_files = array(); // массив для всех нужных файлов
	$allowed_ext = array('gif', 'jpg', 'jpeg', 'png');	
	
	foreach($all_dirs as $one_dir) // проходимся по каждому каталогу
	{
		if ($one_dir == '//') $one_dir = '';
		
		$dir0 = getinfo('uploads_dir') . $one_dir . '/';
		$dir = getinfo('uploads_dir') . $one_dir . 'mini/';
		$dir_url = getinfo('uploads_url') . $one_dir;
		$dir_url_mini = getinfo('uploads_url') . $one_dir . 'mini/';
		
		if (!is_dir($dir)) return ''; // нет каталога
		
		$fn_mso_descritions = $dir0 . '_mso_i/_mso_descriptions.dat';
		if (file_exists( $fn_mso_descritions )) 
		{
			// массив данных: fn => описание 
			$descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
		}
		else $descritions = array();
		
		
		$files = directory_map($dir, true); // все файлы в каталоге
		if (!$files) $files = array();
		
		foreach ($files as $file)
		{
			if (@is_dir($dir . $file)) continue; // это каталог
			else
			{
				$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
				if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла

				$description = (isset($descritions[$file])) ? $descritions[$file] : '';
				
				if ($options['filter']) // нужно применить фильтр в описании
				{
					$go = false;
					
					if ($description)
					{
						$arr_desc = array_unique(explode(' ', trim(mb_strtolower($description, 'UTF-8'))));
						foreach ($arr_desc as $val)
						{
							if (strpos($val, $options['filter']) === 0) // есть вхождение
							{
								$go = true;
								break;
							}
						}
					}
				}
				else $go = true;
				
				if ($go)
					$all_files[$one_dir . $file] = 
						array(
							'file' => $file, 
							'dir' => $one_dir, 
							'descritions' => str_replace('#', '', $description),
							'datefile' => filemtime($dir . $file), 
						);
			}
		}
	}
	
	if ($options['sort'] == 'random') shuffle($all_files); // перемешиваем массив
	elseif ($options['sort'] == 'name_file') // отсортируем по ['file']
	{
		uasort($all_files, 'random_gal_cmp_name_file');
	} 
	elseif ($options['sort'] == 'description')// отсортируем по ['description']
	{
		uasort($all_files, 'random_gal_cmp_description'); 
	}
	elseif ($options['sort'] == 'name_file_description') // По именам, потом по описанию
	{
		uasort($all_files, 'random_gal_cmp_name_file');
		uasort($all_files, 'random_gal_cmp_description');
	}	
	elseif ($options['sort'] == 'description_name_file') // По описанию, потом по именам
	{
		uasort($all_files, 'random_gal_cmp_description');
		uasort($all_files, 'random_gal_cmp_name_file');
	}
	elseif ($options['sort'] == 'name_file_desc') // По именам файлов (обратный порядок)
	{
		uasort($all_files, 'random_gal_cmp_name_file_desc');
	}
	elseif ($options['sort'] == 'description_desc') // По описанию (обратный порядок)
	{
		uasort($all_files, 'random_gal_cmp_description_desc'); 
	}
	elseif ($options['sort'] == 'datefile') // По времени файла
	{
		uasort($all_files, 'random_gal_cmp_datefile'); 
	}
	elseif ($options['sort'] == 'datefile_desc') // По времени файла обратно
	{
		uasort($all_files, 'random_gal_cmp_datefile_desc'); 
	}	
	
	// pr($all_files);
	
	$all_files = array_slice($all_files, 0, (int) $options['count']); // только нужное нам количество
	
	if ($options['style_img']) $options['style_img'] = ' style="' . $options['style_img'] . '"';
	
	// если слайдер, то подключим соответстующую библиотеку, стили и блок
	if($options['type'] == 'slider') 
	{
		$out .= '<div id="random_gal_slider" class="random_gal_nivoSlider">';
	}
	
	foreach ($all_files as $key=>$val)
	{
		if ($val['descritions']) $title = ' title="' . $val['descritions'] . '"';
			else $title = '';

		$out .= '<a href="' . getinfo('uploads_url') . $val['dir'] . $val['file'] 
				. '" class="lightbox"' . $title . '><img src="' 
				. getinfo('uploads_url') . $val['dir'] . 'mini/' . $val['file'] 
				. '" alt=""' . $options['style_img'] . '></a>' . NR;
		//. '<div>' . $val['descritions'] . '</div>';

	}
	
	if ($out)
	{ 
		if($options['type'] == 'slider') 
		{
			$out .= '</div><!-- id="random_gal_slider" -->
			<script>
				$(window).load(function() {
					$("#random_gal_slider").nivoSlider({controlNav:false, pauseTime:3000, directionNav:false});
				});
			</script>
			';
		}
		
		if ($options['class']) $options['class'] = ' class="random-gal-widget ' . $options['class']. '"';
		else $options['class'] = ' class="random-gal-widget"';
		
		if ($options['style']) $options['style'] = ' style="' . $options['style'] . '"';

		$out = '<div' . $options['class'] . $options['style'] . '>' . $out . '</div>' . $options['html'];

	}
	
	if ($out and $options['header']) $out = $options['header'] . $out;

	return $out;	
}


function random_gal_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_random_gal', 'plugins', 
		array(
			'on' => array(
						'type' => 'checkbox', 
						'name' => t('Включить галереи'),
						'description' => t('Если нужно организовать другой вывод галерей, то скопируйте файл <strong>gallery.php</strong> в каталог своего шаблона.'),
						'default' => '0' // для чекбоксов только 1 и 0
						),
					
			'slug_gallery' => array(
							'type' => 'text', 
							'name' => t('Короткая ссылка на вывод галерей'),
							'description' => t('Укажите ссылку по которой будут выводиться галереи. Например:') . ' <strong>gallery</strong> -&gt; <a href="' . getinfo('site_url') . 'gallery">' . getinfo('site_url') . '<strong>gallery</strong></a>', 
							'default' => 'gallery'
						),
						
			'temp' => array(
							'type' => 'info',
							'title' => t('Определение галерей'),
							'text' => t('<p>Галереи задаются по одной в одной строчке в формате:</p>') . NR .
							t('<pre>короткая ссылка | заголовок | каталоги через % | сортировка | количество | фильтр</pre>') . '
							
							<br><p>Пример:</p>
							<pre>first | Моя галерея | / % my | name_file | 100</pre>
							
							<br>Результат:<ul>
							<li><strong>Адрес:</strong> </strong>' . getinfo('site_url') . 'gallery/<u>first</u>
							<li><strong>Название:</strong> <u>Моя галерея</u>
							<li><strong>Каталоги:</strong> <u>uploads</u> и <u>my</u> (если указать <u>#all</u>, то это все каталоги uploads)
							<li><strong>Сортировка:</strong> <u>по имени файлов</u> (все варианты: <em>random, name_file, name_file_desc, description, description_desc, name_file_description, description_name_file, datefile, datefile_desc</em>)
							<li><strong>Количество:</strong> <u>100</u>
							<li><strong>Фильтр:</strong> <u>нет</u> (фильтр - это фраза, с которой должно начинаться хотя бы одно слово в описании файла).
							</ul><br>
							', 
						),		
							
			'all' => array(
						'type' => 'textarea', 
						'name' => t('Список галерей'),
						'description' => t('Укажите галереи'),
						'default' => ''
					),
			),
		t('Настройки галерей'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

function random_gal_custom_page_404($args = array())
{
	$options = mso_get_option('plugin_random_gal', 'plugins', array());
	
	if (isset($options['on']) and $options['on'])
	{
		if ( mso_segment(1)==$options['slug_gallery'] ) 
		{
			if (file_exists(getinfo('template_dir') . 'gallery.php'))
				require( getinfo('template_dir') . 'gallery.php' ); // подключили свой файл вывода в каталоге шаблона
			else	
				require( getinfo('plugins_dir') . 'random_gal/gallery.php' ); // подключили свой файл вывода в каталоге плагина
				
			return true; // выходим с true
		}
	}

	return $args;
}

# end file
