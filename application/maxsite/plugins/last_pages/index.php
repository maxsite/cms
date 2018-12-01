<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function last_pages_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('last_pages_widget', t('Последние записи'));
}

# функция выполняется при деинсталяции плагина
function last_pages_uninstall($args = array())
{
	mso_delete_option_mask('last_pages_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function last_pages_widget($num = 1)
{
	$widget = 'last_pages_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
		else $options['header'] = '';

	if ( isset($options['format']) ) $options['format'] = $options['format'];
		else $options['format'] = '<h4>[TITLE]</h4><p>[DATE] [COMMENTS]</p>[IMG]<p>[TEXT]</p>';

	return last_pages_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function last_pages_widget_form($num = 1)
{
	$widget = 'last_pages_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) )			$options['header'] = t('Последние записи');
	if ( !isset($options['count']) )			$options['count'] = 7;
	if ( !isset($options['type']) )				$options['type'] = 'blog';
	if ( !isset($options['include_cat']) )		$options['include_cat'] = '';
	if ( !isset($options['sort']) )				$options['sort'] = 'page_date_publish';
	if ( !isset($options['sort_order']) )		$options['sort_order'] = 'desc';
	if ( !isset($options['order']) )			$options['order'] = 'desc';
	if ( !isset($options['date_format']) )		$options['date_format'] = 'd/m/Y';
	if ( !isset($options['format']) )			$options['format'] = '<h4>[TITLE]</h4><p>[DATE] [COMMENTS]</p>[IMG]<p>[TEXT]</p>';
	if ( !isset($options['comments_format']) )	$options['comments_format'] = ' | ' . t('Комментариев: ') . '[COUNT]';
	if ( !isset($options['page_type']) )		$options['page_type'] = 'blog';
	if ( !isset($options['img_prev_def']) )		$options['img_prev_def'] = '';
	if ( !isset($options['img_prev_attr']) )	$options['img_prev_attr'] = 'class="b-left w100"';
	if ( !isset($options['max_words']) )		$options['max_words'] = 20;
	if ( !isset($options['text_do']) ) 			$options['text_do'] = '';
	if ( !isset($options['text_posle']) ) 		$options['text_posle'] = '';

	// вывод самой формы
	$CI = & get_instance();
	
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат'), form_textarea( array( 'name'=>$widget . 'format', 'value'=>$options['format'], 'rows' => '3') ), '[TITLE] [DATE] [TEXT] [IMG] [COMMENTS] [URL]');
	
	$form .= mso_widget_create_form(t('Количество'), form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат даты'), form_input( array( 'name'=>$widget . 'date_format', 'value'=>$options['date_format'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат комментариев'), form_input( array( 'name'=>$widget . 'comments_format', 'value'=>$options['comments_format'] ) ), '');
	
	$form .= mso_widget_create_form(t('Тип страниц'), form_input( array( 'name'=>$widget . 'page_type', 'value'=>$options['page_type'] ) ), '');
	
	$form .= mso_widget_create_form(t('Включить рубрики'), form_input( array( 'name'=>$widget . 'include_cat', 'value'=>$options['include_cat'] ) ), '');
	
	$form .= mso_widget_create_form(t('Сортировка'), form_dropdown( $widget . 'sort', array( 'page_date_publish'=>t('По дате'), 'page_title'=>t('По алфавиту'), 'page_id'=>t('По ID записи'), 'page_menu_order'=>t('По menu_order'), 'page_slug'=>t('По коротким ссылкам'), 'page_last_modified'=>t('По дате последнего редактированию') ), $options['sort']), '');

	$form .= mso_widget_create_form(t('Порядок сортировки'), form_dropdown( $widget . 'sort_order', array( 'asc'=>t('Прямой'), 'desc'=>t('Обратный'), 'random'=>t('Случайно')), $options['sort_order']), '');
	
	$form .= mso_widget_create_form(t('Миниатюра по-умолчанию'), form_input( array( 'name'=>$widget . 'img_prev_def', 'value'=>$options['img_prev_def'] ) ), t('Адрес миниатюры изображения, которое будет выводиться там, где не она не указана у записи'));
	
	$form .= mso_widget_create_form(t('Атрибуты миниатюры'), form_input( array( 'name'=>$widget . 'img_prev_attr', 'value'=>$options['img_prev_attr'] ) ), t('Например можно указать class'));

	$form .= mso_widget_create_form(t('Количество слов'), form_input( array( 'name'=>$widget . 'max_words', 'value'=>$options['max_words'] ) ));
	
	$form .= mso_widget_create_form(t('Текст вверху'), form_textarea( array( 'name'=>$widget . 'text_do', 'value'=>$options['text_do'], 'rows' => '3')));
	
	$form .= mso_widget_create_form(t('Текст внизу'), form_textarea( array( 'name'=>$widget . 'text_posle', 'value'=>$options['text_posle'], 'rows' => '3')));
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function last_pages_widget_update($num = 1)
{

	$widget = 'last_pages_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['date_format'] = mso_widget_get_post($widget . 'date_format');
	$newoptions['comments_format'] = mso_widget_get_post($widget . 'comments_format');
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	$newoptions['page_type'] = mso_widget_get_post($widget . 'page_type');
	$newoptions['include_cat'] = mso_widget_get_post($widget . 'include_cat');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');
	$newoptions['sort_order'] = mso_widget_get_post($widget . 'sort_order');
	$newoptions['img_prev_def'] = mso_widget_get_post($widget . 'img_prev_def');
	$newoptions['img_prev_attr'] = mso_widget_get_post($widget . 'img_prev_attr');
	$newoptions['max_words'] = mso_widget_get_post($widget . 'max_words');
	$newoptions['text_do'] = mso_widget_get_post($widget . 'text_do');
	$newoptions['text_posle'] = mso_widget_get_post($widget . 'text_posle');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins' );
}


function last_pages_widget_custom($arg = array(), $num = 1)
{
	if (!isset($arg['count'])) 			$arg['count'] = 7;
	if (!isset($arg['page_type']))  	$arg['page_type'] = 'blog';
	if (!isset($arg['sort']))	 		$arg['sort'] = 'page_date_publish';
	if (!isset($arg['sort_order'])) 	$arg['sort_order'] = 'desc';
	if (!isset($arg['date_format'])) 	$arg['date_format'] = 'd/m/Y';
	if (!isset($arg['format'])) 		$arg['format'] = '<h4>[TITLE]</h4><p>[DATE] [COMMENTS]</p>[IMG]<p>[TEXT]</p>';
	if (!isset($arg['comments_format'])) $arg['comments_format'] = ' | ' . t('Комментариев: ') . '[COUNT]';
	if (!isset($arg['include_cat'])) 	$arg['include_cat'] = '';
	if (!isset($arg['img_prev_def'])) 	$arg['img_prev_def'] = '';
	if (!isset($arg['img_prev_attr'])) 	$arg['img_prev_attr'] = 'class="b-left w100"';
	if (!isset($arg['max_words']) ) 	$arg['max_words'] = 20;
	if (!isset($arg['text_do']) ) 	$arg['text_do'] = '';
	if (!isset($arg['text_posle']) ) 	$arg['text_posle'] = '';

	if ( !isset($arg['header']) ) $arg['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . t('Последние записи') . mso_get_val('widget_header_end', '</span></div>');

	if ( !isset($arg['block_start']) ) $arg['block_start'] = '';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '';

	
	if ($arg['sort_order'] != 'random')
	{
		$cache_key = 'last_pages_widget'. serialize($arg) . $num;
		if ($k = mso_get_cache($cache_key)) return $k; // да есть в кэше
	}
	
	$par = array( 
		'limit' => $arg['count'], 
		'cut' => '',
		'cat_order' => 'category_name', 
		'cat_order_asc' => 'asc',
		'pagination' => false,
		'cat_id' => $arg['include_cat'],
		'order' => $arg['sort'],
		'order_asc' => $arg['sort_order'],
		'type' => $arg['page_type'],
		'custom_type' => 'home',
	);
	
	
	$pages = mso_get_pages($par, $temp);
	
	$out = '';
	
	if ($pages)
	{
		foreach ($pages as $page)
		{
			// [TITLE] [DATE] [TEXT] [IMG] [COMMENTS] [URL]
			
			$title = mso_page_title($page['page_slug'], $page['page_title'], '', '', true, false, 'page');
			
			$url = getinfo('site_url') . 'page/' . $page['page_slug'];
			
			$date = mso_page_date($page['page_date_publish'], $arg['date_format'], '', '', false);
			
			$img = isset($page['page_meta']['image_for_page'][0]) ? $page['page_meta']['image_for_page'][0] : '';
			
			if (!$img and $arg['img_prev_def']) $img = $arg['img_prev_def'];
			
			if ($img)			
			{
				
				if ($image_for_page = thumb_generate($img, 330, 230, false, 'resize_full_crop_center', false, 'mini', true, 70))
				{
					$img = '<a href="' . $url . '"><img src="' . $image_for_page . '" alt="' . $page['page_title'] . '" ' . $arg['img_prev_attr'] . '></a>';
				}
				
				// старый вариант без миниатюры
				// $img = '<a href="' . $url . '"><img src="' . $img . '" alt="' . $page['page_title'] . '" ' . $arg['img_prev_attr'] . '></a>';
			}
			
			if ($page['page_count_comments'])
			{
				$comments = str_replace('[COUNT]', $page['page_count_comments'], $arg['comments_format']);
			}
			else
			{
				$comments = '';
			}
			
			$text = mso_str_word(strip_tags($page['page_content']), $arg['max_words']) . ' ...';
			
			$out_page = $arg['format'];
			
			$out_page = str_replace('[TITLE]', $title, $out_page);
			$out_page = str_replace('[DATE]', $date, $out_page);
			$out_page = str_replace('[COMMENTS]', $comments, $out_page);
			$out_page = str_replace('[URL]', $url, $out_page);
			$out_page = str_replace('[TEXT]', $text, $out_page);
			$out_page = str_replace('[IMG]', $img, $out_page);

			$out .= $out_page;
		}
		
		$out = $arg['header'] . $arg['block_start'] . $arg['text_do'] . $out . $arg['text_posle'] . $arg['block_end'];
	}
	
	if ($arg['sort_order'] != 'random') mso_add_cache($cache_key, $out); // в кэш
	
	return $out;
}

# end of file
