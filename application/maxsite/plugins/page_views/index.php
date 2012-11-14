<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function page_views_autoload($args = array())
{
	mso_register_widget('page_views_widget', t('Самое читаемое')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function page_views_uninstall($args = array())
{
	mso_delete_option_mask('page_views_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function page_views_widget($num = 1)
{
	$widget = 'page_views_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] )
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	return page_views_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function page_views_widget_form($num = 1)
{
	$widget = 'page_views_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 10;
	if ( !isset($options['page_type']) ) $options['page_type'] = 0;
	if ( !isset($options['format']) ) $options['format'] = '[A][TITLE][/A] <sup>[COUNT]</sup>';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$CI->db->select('page_type_id, page_type_name');
	$query = $CI->db->get('page_type');
	$types = array(0 => t('Все типы'));
	if ($query->num_rows() > 0)
	{
		foreach ($query->result_array() as $page)
		$types[$page['page_type_id']] = $page['page_type_name'];
	}

	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество записей'), form_input( array( 'name'=>$widget . 'limit', 'value'=>$options['limit'] ) ), '');
	
	$form .= mso_widget_create_form(t('Тип записей'), form_dropdown( $widget . 'page_type', $types, array( 'value'=>$options['page_type'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат'), form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ), t('<strong>[TITLE]</strong> - название записи<br><strong>[COUNT]</strong> - просмотров в день<br><strong>[ALLCOUNT]</strong> - всего просмотров<br><strong>[A]</strong>ссылка<strong>[/A]</strong>'));


	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function page_views_widget_update($num = 1)
{
	$widget = 'page_views_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['limit'] = (int) mso_widget_get_post($widget . 'limit');
	$newoptions['page_type'] = mso_widget_get_post($widget . 'page_type');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins' );
}


#  Вспомогательная функция для page_views_widget_custom - сортировка массива
function page_views_cmp($a, $b)
{
	if ( $a['sutki'] == $b['sutki'] ) return 0;
	return ( $a['sutki'] > $b['sutki'] ) ? -1 : 1;
}

# функции плагина
function page_views_widget_custom($options = array(), $num = 1)
{
	// кэш
	$cache_key = 'page_views_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше

	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 10;
	if ( !isset($options['page_type']) ) $options['page_type'] = 0;
	if ( !isset($options['format']) ) $options['format'] = '[A][TITLE][/A] <sup>[COUNT]</sup>';

	# получаем все записи как есть
	# в полученном массиве меняем общее кол-во прочтений на кол-во прочтений в сутки
	# сортируем массив по новомк значению

	$curdate = time();

	$CI = & get_instance();
	$CI->db->select('page_slug, page_title, page_id, page_view_count, page_date_publish');
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_view_count > ', '0');
	if ( $options['page_type'] ) $CI->db->where('page_type_id', $options['page_type']);
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	$CI->db->order_by('page_id', 'desc');

	$query = $CI->db->get('page');

	if ($query->num_rows() > 0)
	{
		$pages = $query->result_array();

		foreach ($pages as $key=>$val)
		{
			// если еще сутки не прошли, то ставим общее колво прочтений
			if ( $curdate - strtotime($val['page_date_publish']) > 86400 )
				$pages[$key]['sutki'] = round( $val['page_view_count'] / ($curdate - strtotime($val['page_date_publish'])) * 86400);
			else
				$pages[$key]['sutki'] = $val['page_view_count'];
		}

		usort($pages, 'page_views_cmp'); // отсортируем по ['sutki']

		// сам вывод
		$link = '<a href="' . getinfo('siteurl') . 'page/';

		$i = 1;


		foreach ($pages as $page)
		{
			if ($page['sutki'] > 0)
			{
				if ($i>$options['limit']) break; // лимит

				$out1 = $options['format'];

				$out1 = str_replace('[TITLE]', $page['page_title'], $out1);
				$out1 = str_replace('[COUNT]', $page['sutki'], $out1);
				$out1 = str_replace('[ALLCOUNT]', $page['page_view_count'], $out1);

				$out1 = str_replace('[A]', $link . $page['page_slug']
						. '" title="' . t('Просмотров в сутки: ') . $page['sutki'] . '">'
						, $out1);

				$out1 = str_replace('[/A]', '</a>', $out1);

				$out .= '<li>' . $out1 . '</li>' . NR;

				$i++;
			}
			else break; // всё

		}

		if ($out)
		{
			$out = '<ul class="is_link page_views">' . NR . $out . '</ul>' . NR;
			if ($options['header']) $out = $options['header'] . $out;
		}
	}

	mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}

# end file