<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function random_pages_autoload($args = array())
{
	mso_register_widget('random_pages_widget', t('Случайные статьи')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function random_pages_uninstall($args = array())
{
	mso_delete_option_mask('random_pages_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function random_pages_widget($num = 1) 
{
	$widget = 'random_pages_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return random_pages_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function random_pages_widget_form($num = 1) 
{
	$widget = 'random_pages_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['page_type']) ) $options['page_type'] = 'blog';
	if ( !isset($options['page_content']) ) $options['page_content'] = false;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество'), form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ), '');
	
	$form .= mso_widget_create_form(t('Тип страниц'), form_input( array( 'name'=>$widget . 'page_type', 'value'=>$options['page_type'] ) ), '');
	
	$form .= mso_widget_create_form(' ', form_checkbox( array( 'name'=>$widget . 'page_content', 'checked'=>$options['page_content'], 'value'=>'page_content')) . ' ' . t('Показывать содержимое'), '');
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function random_pages_widget_update($num = 1) 
{
	$widget = 'random_pages_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['page_type'] = mso_widget_get_post($widget . 'page_type');
	$newoptions['page_content'] = mso_widget_get_post($widget . 'page_content');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function random_pages_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['page_type']) ) $options['page_type'] = 'blog';
	if ( !isset($options['page_content']) ) $options['page_content'] = false;
	
	$CI = & get_instance();
	
	if (!$options['page_content']) $CI->db->select('page_slug, page_title');
		else $CI->db->select('page_slug, page_content');
	//$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	$CI->db->where('page_date_publish < ', 'NOW()', false);
	$CI->db->where('page_status', 'publish');
	if ($options['page_type']) $CI->db->where('page_type_name', $options['page_type']);
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->from('page');
	$CI->db->order_by('page_id', 'random');
	$CI->db->limit($options['count']);
	
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$pages = $query->result_array();
		
		if (!$options['page_content'])
		{
			$link = '<a href="' . getinfo('siteurl') . 'page/';
			$out .= '<ul class="is_link random_pages">' . NR;
			foreach ($pages as $page) 
			{
				$out .= '<li>' . $link . $page['page_slug'] . '">' . $page['page_title'] . '</a>' . '</li>' . NR;
			}
			$out .= '</ul>' . NR;
		}
		else
		{
			$out .= '<div class="random_pages">' . NR;
			foreach ($pages as $page) 
			{
				$out .= '<div class="page_content">' . mso_hook('content', $page['page_content']) . '</div>' . NR;
			}
			$out .= '</div>' . NR;
		}
		
		if ($options['header']) $out = $options['header'] . $out;
	}
	
	return $out;
}

# end file
