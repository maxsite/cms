<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

# функция автоподключения плагина
function category_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('category_widget', t('Рубрики')); 
}

# функция выполняется при деинсталяции плагина
function category_uninstall($args = array())
{	
	mso_delete_option_mask('category_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}


# функция, которая берет настройки из опций виджетов
function category_widget($num = 1) 
{
	$widget = 'category_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		else $options['header'] = '';
	
	if ( isset($options['include']) ) $options['include'] = mso_explode($options['include']);
		else $options['include'] = array();
		
	if ( isset($options['exclude']) ) $options['exclude'] = mso_explode($options['exclude']);
		else $options['exclude'] = array();
	
	
	return category_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function category_widget_form($num = 1) 
{

	$widget = 'category_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['format']) ) $options['format'] = '[LINK][TITLE]<sup>[COUNT]</sup>[/LINK]<br>[DESCR]';
	if ( !isset($options['format_current']) ) $options['format_current'] = '<span>[TITLE]<sup>[COUNT]</sup></span><br>[DESCR]';
	if ( !isset($options['include']) ) $options['include'] = '';
	if ( !isset($options['exclude']) ) $options['exclude'] = '';
	if ( !isset($options['hide_empty']) ) $options['hide_empty'] = '0';
	if ( !isset($options['order']) ) $options['order'] = 'category_name';
	if ( !isset($options['order_asc']) ) $options['order_asc'] = 'ASC';
	if ( !isset($options['include_child']) ) $options['include_child'] = '0';
	if ( !isset($options['nofollow']) ) $options['nofollow'] = 0;
	if ( !isset($options['group_header_no_link']) ) $options['group_header_no_link'] = 0;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат'), form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ), t('Например: [LINK][TITLE]&lt;sup&gt;[COUNT]&lt;/sup&gt;[/LINK]&lt;br&gt;[DESCR]'));
	
	$form .= mso_widget_create_form(t('Формат текущей'), form_input( array( 'name'=>$widget . 'format_current', 'value'=>$options['format_current'] ) ), t('Например: &lt;span&gt;[TITLE]&lt;sup&gt;[COUNT]&lt;/sup&gt;&lt;/span&gt;&lt;br&gt;[DESCR]<br>Все варианты: [SLUG], [ID_PARENT], [ID], [MENU_ORDER], [TITLE], [TITLE_HTML], [COUNT], [DESCR], [DESCR_HTML], [LINK][/LINK], [URL]'));
	
	$form .= mso_widget_create_form(t('Включить только'), form_input( array( 'name'=>$widget . 'include', 'value'=>$options['include'] ) ), t('Укажите номера рубрик через запятую или пробел'));
	
	$form .= mso_widget_create_form(t('Исключить'), form_input( array( 'name'=>$widget . 'exclude', 'value'=>$options['exclude'] ) ), t('Укажите номера рубрик через запятую или пробел'));
	
	$form .= mso_widget_create_form(t('Если нет записей'), form_dropdown( $widget . 'hide_empty', array( 
		'0'=>t('Отображать рубрику (количество записей ведется без учета опубликованности)'), 
		'1'=>t('Скрывать рубрику (количество записей ведется только по опубликованным)')), 
		$options['hide_empty']), '');
	
	$form .= mso_widget_create_form(t('Сортировка'), form_dropdown( $widget . 'order', 
			array( 
				'category_name' => t('По имени рубрики'), 
				'category_id' => t('По ID рубрики'), 
				'category_menu_order' => t('По выставленному menu order'), 
				'pages_count' => t('По количеству записей')), 
				$options['order']), '');
	
	$form .= mso_widget_create_form(t('Порядок'), form_dropdown( $widget . 'order_asc', 
			array( 
				'ASC'=>t('Прямой'), 
				'DESC'=>t('Обратный')
				), $options['order_asc']), '');
	
	$form .= mso_widget_create_form(t('Включать потомков'), form_dropdown( $widget . 'include_child', 
				array( 
				'0'=>t('Всегда'), 
				'1'=>t('Только если явно указана рубрика'),
				'-1'=>t('Исключить всех')
				), $options['include_child']), '');
	
	$form .= mso_widget_create_form(t('Ссылки рубрик'), form_dropdown( $widget . 'nofollow', 
				array( 
				'0'=>t('Обычные'), 
				'1'=>t('Устанавливать как nofollow (неиндексируемые поисковиками)')
				), $options['nofollow']), '');
	
	$form .= mso_widget_create_form(t('Рубрика группы'), form_dropdown( $widget . 'group_header_no_link', 
				array( 
				'0'=>t('Ссылка'), 
				'1'=>t('Текст')
				), $options['group_header_no_link']), '');
				
				
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function category_widget_update($num = 1) 
{

	$widget = 'category_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['format_current'] = mso_widget_get_post($widget . 'format_current');
	$newoptions['include'] = mso_widget_get_post($widget . 'include');
	$newoptions['exclude'] = mso_widget_get_post($widget . 'exclude');
	$newoptions['hide_empty'] = mso_widget_get_post($widget . 'hide_empty');
	$newoptions['order'] = mso_widget_get_post($widget . 'order');
	$newoptions['order_asc'] = mso_widget_get_post($widget . 'order_asc');
	$newoptions['include_child'] = mso_widget_get_post($widget . 'include_child');
	$newoptions['nofollow'] = mso_widget_get_post($widget . 'nofollow');
	$newoptions['group_header_no_link'] = mso_widget_get_post($widget . 'group_header_no_link');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}


function category_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['include']) ) $options['include'] = array();
	if ( !isset($options['exclude']) ) $options['exclude'] = array();
	if ( !isset($options['format']) ) $options['format'] = '[LINK][TITLE]<sup>[COUNT]</sup>[/LINK]<br>[DESCR]';
	if ( !isset($options['format_current']) ) $options['format_current'] = '<span>[TITLE]<sup>[COUNT]</sup></span><br>[DESCR]';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['hide_empty']) ) $options['hide_empty'] = 0;
	if ( !isset($options['order']) ) $options['order'] = 'category_name';
	if ( !isset($options['order_asc']) ) $options['order_asc'] = 'ASC';
	if ( !isset($options['include_child']) ) $options['include_child'] = 0;
	if ( !isset($options['nofollow']) ) $options['nofollow'] = false;
	if ( !isset($options['group_header_no_link']) ) $options['group_header_no_link'] = false;
	
	$cache_key = 'category_widget' . serialize($options) . $num;
	
	$k = mso_get_cache($cache_key);
	if ($k) // да есть в кэше
	{
		$all = $k;
	}
	else 
	{
		/*
			$type = 'page', 
			$parent_id = 0, 
			$order = 'category_menu_order', 
			$asc = 'asc', 
			$child_order = 'category_menu_order', 
			$child_asc = 'asc', 
			$in = false, 
			$ex = false, 
			$in_child = false, 
			$hide_empty = false, 
			$only_page_publish = false, 
			$date_now = true, 
			$get_pages = true
		*/
		
		$all = mso_cat_array(
			'page', 
			0, 
			$options['order'], 
			$options['order_asc'], 
			$options['order'], 
			$options['order_asc'], 
			$options['include'], 
			$options['exclude'], 
			$options['include_child'], 
			$options['hide_empty'], 
			true, 
			true, 
			false
			);
		
		mso_add_cache($cache_key, $all); // сразу в кэш добавим
	}
	
	// pr($all);
	
	
	$out = mso_create_list($all, 
		array(
			'childs'=>'childs', 
			'format'=>$options['format'], 
			'format_current'=>$options['format_current'], 
			'class_ul'=>'is_link', 
			'title'=>'category_name', 
			'link'=>'category_slug', 
			'current_id'=>false, 
			'prefix'=>'category/', 
			'count'=>'pages_count', 
			'slug'=>'category_slug', 
			'id'=>'category_id', 
			'menu_order'=>'category_menu_order', 
			'id_parent'=>'category_id_parent', 
			'nofollow'=>$options['nofollow'],
			'group_header_no_link' => $options['group_header_no_link'],
			) 
	);
	
	if ($out and $options['header']) $out = $options['header'] . $out;
	
	
	return $out;
}


# end file