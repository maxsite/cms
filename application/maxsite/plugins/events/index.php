<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function events_autoload($args = array())
{
	mso_register_widget('events_widget', t('События')); # регистрируем виджет
}


# функция выполняется при деинсталяции плагина
function events_uninstall($args = array())
{	
	mso_delete_option_mask('events_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function events_widget($num = 1) 
{
	$widget = 'events_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		else $options['header'] = '';
	
	return events_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function events_widget_form($num = 1) 
{
	$widget = 'events_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['events']) ) $options['events'] = '';
	if ( !isset($options['format_date']) ) $options['format_date'] = 'l, j F';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат даты'), form_input( array( 'name'=>$widget . 'format_date', 'value'=>$options['format_date'] ) ), t('Как это <a href="http://ru.php.net/date" target="_blank">принято в PHP</a>'));
	
	$form .= mso_widget_create_form(t('События'), form_textarea( array( 'name'=>$widget . 'events', 'value'=>$options['events'] ) ), t('Указывайте по одному событию в каждом абзаце в формате:<br><strong>дата | до | после | текст события</strong><br><strong>дата</strong> в формате yyyy-mm-dd<br><strong>до</strong> - выводить событие до наступления N-дней<br><strong>после</strong> - выводить событие после прошествия N-дней<br><strong>В тексте события</strong> можно использовать HTML<br><strong>ПРИМЕР:</strong> 2008-09-01 | 3 | 1 | Пора в школу!'));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function events_widget_update($num = 1) 
{
	$widget = 'events_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['events'] = mso_widget_get_post($widget . 'events');
	$newoptions['format_date'] = mso_widget_get_post($widget . 'format_date');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function events_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'events_widget_custom'. serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	if ( !isset($options['events']) ) return ''; // нет событий, выходим
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['format_date']) ) $options['format_date'] = 'l, j F';
	
	/*
	дата | до | после | событие
	2008-09-01 | 5 | 1 | Пора в школу! 
	*/
	
	// для перевода дат
	$day_en = array(
				'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
				'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun',
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 
				'August', 'September', 'October', 'November', 'December',
				'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
				
	$day_ru = array(
				t('Понедельник'), t('Вторник'), t('Среда'), t('Четверг'), t('Пятница'), t('Суббота'), t('Воскресенье'), 
				
				t('Пн'), t('Вт'), t('Ср'), t('Чт'), t('Пт'), t('Сб'), t('Вс'), 
				
				t('января'), t('февраля'), t('марта'), t('апреля'), t('мая'), t('июня'), t('июля'), t('августа'), t('сентября'), t('октября'), t('ноября'), t('декабря'), 
				
				t('янв'), t('фев'), t('мар'), t('апр'), t('май'), t('июн'), t('июл'), t('авг'), t('сен'), t('окт'), t('ноя'), t('дек'));
	
	$events = explode("\n", trim($options['events']));
	asort($events);
	
	$curdate = time() + getinfo('time_zone') * 60 * 60;
	//pr($curdate); pr('=========');
	
	foreach ($events as $event)
	{
		$event = trim($event);
		if (!$event) continue;
		
		$event = explode('|', $event);

		if (count($event)<4) continue; // неверные данные
		
		$date = strtotime(trim($event[0])) + getinfo('time_zone') * 60 * 60; // дата учитываем смещение времени для сайта
		$do = strtotime("-" . trim($event[1]) . " day", $date);  // до указаной даты
		$posle = strtotime("+" . trim($event[2]) . " day", $date);  // после указанной даты
		
		// pr($date); pr($do); pr($posle); pr('----------');

		if ($curdate >= $do and $curdate <= $posle) // событие в диапазоне дат - можно выводить
		{

			$od = date($options['format_date'], $date); // форматируем вывод даты
			$od = str_replace($day_en, $day_ru, $od);   // переводим на русский
			
			// if ($curdate > $date and ( ( $curdate - $date ) > 86400 ) ) 
			if ( ( $curdate - $date ) > 86400 ) // уже прошло и разница больше суток
			{
				$out .= '<li><del><span>' . $od . '</span> - ' . $event[3] . '</del></li>' . NR;
			}
			else 
				$out .= '<li><span>' . $od . '</span> - ' . $event[3] . '</li>' . NR;
		}
	}
	
	if ($out) $out = $options['header'] . '<ul class="is_link events">' . NR . $out . '</ul>' . NR;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}

# end file