<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function page_comments_autoload($args = array())
{
	mso_register_widget('page_comments_widget', t('Самое комментируемое')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function page_comments_uninstall($args = array())
{	
	mso_delete_option_mask('page_comments_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function page_comments_widget($num = 1)
{
	$widget = 'page_comments_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return page_comments_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function page_comments_widget_form($num = 1) 
{
	$widget = 'page_comments_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 10;
	if ( !isset($options['format']) ) $options['format'] = '[A][TITLE][/A] <sup>[COUNT]</sup>';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество записей'), form_input( array( 'name'=>$widget . 'limit', 'value'=>$options['limit'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат'), form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ), t('<strong>[TITLE]</strong> - название записи<br><strong>[COUNT]</strong> - количество комментариев<br><strong>[A]</strong>ссылка<strong>[/A]</strong>'));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function page_comments_widget_update($num = 1) 
{
	$widget = 'page_comments_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['limit'] = (int) mso_widget_get_post($widget . 'limit');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function page_comments_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'page_comments_widget_custom'. serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 10;
	if ( !isset($options['format']) ) $options['format'] = '[A][TITLE][/A] <sup>[COUNT]</sup>';
	
	$CI = & get_instance();
	
	$CI->db->select('page_slug, page_title, COUNT(comments_id) AS page_count_comments', false);
	$CI->db->from('page');
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	// $CI->db->where('page_date_publish < ', 'NOW()', false);
	$CI->db->where('page_status', 'publish');
	
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	$CI->db->order_by('page_count_comments', 'desc');
	$CI->db->limit($options['limit']);
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$pages = $query->result_array();
		
		$link = '<a href="' . getinfo('siteurl') . 'page/';
		$out .= '<ul class="is_link page_comments">' . NR;
		foreach ($pages as $page) 
		{
			if ($page['page_count_comments'] > 0)
			{
				$out1 = $options['format'];

				$out1 = str_replace('[TITLE]', $page['page_title'], $out1);
				$out1 = str_replace('[COUNT]', $page['page_count_comments'], $out1);
				
				$out1 = str_replace('[A]', $link . $page['page_slug'] 
						. '" title="Комментариев: ' . $page['page_count_comments'] . '">'
						, $out1);
						
				$out1 = str_replace('[/A]', '</a>', $out1);
				
				$out .= '<li>' . $out1 . '</li>' . NR;
			}
		}
		
		$out .= '</ul>' . NR;
		if ($options['header']) $out = $options['header'] . $out;
	}
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}

# end file