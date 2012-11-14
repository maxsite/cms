<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

# функция автоподключения плагина
function calendar_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('calendar_widget', t('Календарь', 'plugins' )); 
}


# функция, которая берет настройки из опций виджетов
function calendar_widget($num = 1) 
{
	$widget = 'calendar_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		else $options['header'] = '';
		
	return calendar_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function calendar_widget_form($num = 1) 
{
	$widget = 'calendar_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . '_header', 'value'=>$options['header'])));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function calendar_widget_update($num = 1) 
{

	$widget = 'calendar_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# получаем из POST
	$newoptions['header'] = mso_widget_get_post($widget . '_header');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}


# основная функция, которая берет настройки из своих параметров
function calendar_widget_custom($arg = array(), $num = 1) 
{
	# массив названий месяцев
	if ( !isset($arg['months']) ) $arg['months'] = array(t('Январь'), t('Февраль'), 
													t('Март'), t('Апрель'), t('Май'), 
													t('Июнь'), t('Июль'), t('Август'), 
													t('Сентябрь'), t('Октябрь'), t('Ноябрь'), 
													t('Декабрь'));
	# массив названий дней недели
	if ( !isset($arg['days']) ) $arg['days'] = array(t('Пн'), t('Вт'), t('Ср'), 
													t('Чт'), 
													t('Пт'), t('Сб'), t('Вс'));
	
	# оформление виджета
	if ( !isset($arg['header']) ) $arg['header'] = '<h2 class="box"><span>' . t('Календарь') . '</span></h2>';
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="calendar">';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</div>';
	
	if ( !isset($arg['elem_previous']) ) $arg['elem_previous'] = '««';
	if ( !isset($arg['elem_next']) ) $arg['elem_next'] = '»»';

	$prefs = array (
				'start_day'	  		=> 'monday',
				'month_type'	 	=> 'long',
				'day_type'	  		=> 'long',
				'show_next_prev'	=> TRUE,
				'local_time' 		=>	time(),
				'next_prev_url'	 	=> getinfo('site_url') . 'archive/'
				);
	
	$prefs['template'] = '
	   {table_open}<table border="0" cellpadding="0" cellspacing="0">{/table_open}

	   {heading_row_start}<tr>{/heading_row_start}

	   {heading_previous_cell}<th><a href="{previous_url}">' . $arg['elem_previous'] . '</a></th>{/heading_previous_cell}
	   {heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
	   {heading_next_cell}<th><a href="{next_url}">' . $arg['elem_next'] . '</a></th>{/heading_next_cell}

	   {heading_row_end}</tr>{/heading_row_end}

	   {week_row_start}<tr class="week">{/week_row_start}
	   {week_day_cell}<td>{week_day}</td>{/week_day_cell}
	   {week_row_end}</tr>{/week_row_end}

	   {cal_row_start}<tr>{/cal_row_start}
	   {cal_cell_start}<td>{/cal_cell_start}

	   {cal_cell_content}<a href="{content}">{day}</a>{/cal_cell_content}
	   {cal_cell_content_today}<div class="today-content"><a href="{content}">{day}</a></div>{/cal_cell_content_today}

	   {cal_cell_no_content}{day}{/cal_cell_no_content}
	   {cal_cell_no_content_today}<div class="today">{day}</div>{/cal_cell_no_content_today}

	   {cal_cell_blank}&nbsp;{/cal_cell_blank}

	   {cal_cell_end}</td>{/cal_cell_end}
	   {cal_row_end}</tr>{/cal_row_end}

	   {table_close}</table>{/table_close}';

	$CI = & get_instance(); 
	$CI->load->library('calendar', $prefs);
	
	
	$mktime = mktime() + getinfo('time_zone') * 60 * 60; // с учетом часового пояса ?
	
	# если это архив, то нужно показать календарь на этот год и месяц
	if (is_type('archive'))
	{
		$year = (int) mso_segment(2);
		if ($year>date('Y', $mktime) or $year<2000) $year = date('Y', $mktime);
		
		$month = (int) mso_segment(3);
		if ($month>12 or $month<1) $month = date('m', $mktime);
	}
	else // это не архив - берем текущую дату
	{
		$year = date('Y', $mktime);
		$month = date('m', $mktime);
	}
	
	# для выделения дат нужно смотреть записи, которые в этом месяце
	$CI->db->select('page_date_publish');
	$CI->db->from('page');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_type_name', 'blog');
	$CI->db->where('page_date_publish >= ', mso_date_convert_to_mysql($year, $month));
	$CI->db->where('page_date_publish < ', mso_date_convert_to_mysql($year, $month+1));
	
	# не выводить неопубликованные
	$CI->db->where('page_date_publish < ',  mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'))  );
	
	
	$query = $CI->db->get();
	
	$data = array();
	
	if ($query->num_rows() > 0)	
	{
		$pages = $query->result_array();
		foreach ($pages as $key=>$page)
		{
			$d = (int) mso_date_convert('d', $page['page_date_publish']);
			$data[$d] = getinfo('site_url') . 'archive/' . $year . '/' . $month . '/' . $d;
		}
		/*	$data = array(
				   3  => 'http://your-site.com/news/article/2006/03/',
				   7  => 'http://your-site.com/news/article/2006/07/" title="123',
				   26 => 'http://your-site.com/news/article/2006/26/'
				); */
	}
	
	$out = $CI->calendar->generate($year, $month, $data);

	$month_en = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$day_en = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	
	
	$out = str_replace($month_en, $arg['months'], $out);
	$out = str_replace($day_en, $arg['days'], $out);
	
	
	# если используется английский, то заменим большие названия на маленькие
	$out = str_replace(array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'), 
		array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'), $out);
	
	
	$out = $arg['header'] . $arg['block_start'] . $out . $arg['block_end'];

	return $out;
}

# end file