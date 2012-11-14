<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function favorites_autoload($args = array())
{
	mso_register_widget('favorites_widget', t('Избранное')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function favorites_uninstall($args = array())
{	
	mso_delete_option_mask('favorites_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function favorites_widget($num = 1) 
{
	$widget = 'favorites_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		else $options['header'] = '';
	
	return favorites_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function favorites_widget_form($num = 1) 
{
	$widget = 'favorites_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['favorites']) ) $options['favorites'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Ссылки'), form_textarea( array( 'name'=>$widget . 'favorites', 'value'=>$options['favorites'] ) ), t('Указывайте по одной ссылке в каждом абзаце в формате: <strong>тип/ссылка | название</strong><br><strong>тип/ссылка</strong> - указывается от адреса сайта, например<br><strong>page/about</strong>, <strong>category/news</strong><br>Для главной страницы укажите: <strong> / | Главная</strong>'));


	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function favorites_widget_update($num = 1) 
{
	$widget = 'favorites_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['favorites'] = mso_widget_get_post($widget . 'favorites');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function favorites_widget_custom($options = array(), $num = 1)
{
	$out = '';
	
	$siteurl = getinfo('siteurl'); // адрес сайта
	$current_url = mso_current_url(); // текущая страница относительно сайта
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( isset($options['favorites']) ) 
	{
		$favorites = explode("\n", $options['favorites']); // разбиваем по строкам
		
		foreach ($favorites as $row)
		{
			$ar = explode('|', $row); // разбиваем по |
			
			// всего должно быть 2 элемента
			if ( isset($ar[0]) and trim($ar[0]) ) // если есть первый элемент
			{
				$href = '//' . trim($ar[0]) . '//'; // адрес
				
				// удалим ведущий и конечные слэши, если есть
				$href = trim( str_replace('/', ' ', $href) );
				$href = str_replace(' ', '/', $href);
				
				if ( isset($ar[1]) and trim($ar[1]) ) // если есть название
				{
					$title = trim($ar[1]); // название
					
					if ($href == $current_url) $class = ' class="current-page" '; // мы на этой странице
							else $class = '';
					
					$out .= NR . '<li' . $class . '><a href="' . $siteurl . $href . '" title="' . $title . '">' 
							. $title . '</a></li>';
				}
			}
		}
	}
	
	if ($out) $out = $options['header'] . NR . '<ul class="is_link favorites">' . $out . NR . '</ul>' .NR ;
	
	return $out;
}

# end file