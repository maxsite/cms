<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function authors_autoload($args = array())
{
	mso_register_widget('authors_widget', t('Авторы')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function authors_uninstall($args = array())
{	
	mso_delete_option_mask('authors_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function authors_widget($num = 1) 
{
	$widget = 'authors_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		else $options['header'] = '';
	
	return authors_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function authors_widget_form($num = 1) 
{
	$widget = 'authors_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])), t('Укажите заголовок виджета'));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function authors_widget_update($num = 1) 
{
	$widget = 'authors_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function authors_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'authors_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	
	// получаем всех авторов
	
	$CI = & get_instance();
	$CI->db->select('users_nik, users_id');
	$CI->db->order_by('users_nik');
	
	$query = $CI->db->get('users');
	
	if ($query->num_rows() > 0)	
	{	
		$users = $query->result_array();
		
		$out = '';
		foreach ($users as $user)
		{
			$out .= NR . '<li><a href="' . getinfo('siteurl') . 'author/' . $user['users_id'] . '">' 
						. $user['users_nik']
						. '</a></li>';
		}
		
		if ($out) $out = $options['header'] . '<ul class="is_link authors">' . $out . '</ul>' . NR;
	}
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}

# end file