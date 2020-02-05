<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function rater_autoload()
{
	if (is_type('page')) {
		mso_hook_add('body_end', 'rater_body_end');
		mso_hook_add('content_end', 'rater_content_end');
	}

	mso_register_widget('rater_widget', t('Рейтинг страниц')); # регистрируем виджет
}

function rater_body_end($args = [])
{
	echo mso_load_style(getinfo('plugins_url') . 'rater/rater.css');
	echo mso_load_script(getinfo('plugins_url') . 'rater/jquery.rater.js');

	return $args;
}

function rater_content_end($arg = [])
{
	$pageData = mso_get_val('mso_pages', 0, true);

	if (!$pageData) return $arg;

	if ($pageData['page_type_name'] !== 'blog') return $arg;

	if ($pageData['page_rating_count'] > 0)
		$curvalue = round($pageData['page_rating'] / $pageData['page_rating_count']);
	else
		$curvalue = 0;

	if ($curvalue > 10) $curvalue = 10;
	if ($curvalue < 0) $curvalue = 0;

	$page_id = $pageData['page_id'];

	$path = getinfo('ajax') . base64_encode('plugins/rater/ratings-post-ajax.php');

	echo '<div id="rater" title="' . t('Текущая оценка:') . ' ' . $curvalue . '. '
		. t('Голосов:') . ' ' . $pageData['page_rating_count']
		. '"><script>$(document).ready(function(){ $(\'#rater\').rater(\'' . $path .  '\', {maxvalue:10, style:\'basic\', curvalue:' . $curvalue . ', slug:\'' . $page_id . '\'});}) </script></div>';

	return $arg;
}

# функция выполняется при деинсталяции плагина
function rater_uninstall()
{
	mso_delete_option_mask('rater_widget_', 'plugins'); // удалим созданные опции
}

# функция, которая берет настройки из опций виджетов
function rater_widget($num = 1)
{
	$widget = 'rater_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array()); // получаем опции

	// заменим заголовок
	if (isset($options['header']) and $options['header'])
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else $options['header'] = '';

	return rater_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function rater_widget_form($num = 1)
{
	$widget = 'rater_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());

	if (!isset($options['header'])) $options['header'] = t('Рейтинг страниц');
	if (!isset($options['count'])) $options['count'] = 10;
	if (!isset($options['format'])) $options['format'] = '[A][TITLE][/A] <sup>[BALL]</sup>';

	// вывод самой формы
	$CI = &get_instance();
	$CI->load->helper('form');

	$form = mso_widget_create_form(t('Заголовок'), form_input(array('name' => $widget . 'header', 'value' => $options['header'])), '');

	$form .= mso_widget_create_form(t('Количество'), form_input(array('name' => $widget . 'count', 'value' => $options['count'])), '');

	$form .= mso_widget_create_form(t('Формат'), form_textarea(array('name' => $widget . 'format', 'value' => $options['format'])), '<em title="' . t('Название записи') . '">[TITLE]</em> <em title="' . t('Всего голосов') . '">[COUNT]</em> <em title="' . t('Общий бал (деление общего рейтинга на кол-во голосов) - округлен до целого') . '">[BALL]</em> <em title="' . t('Общий бал (дробный)') . '">[REALBALL]</em> <em title="' . t('Ссылка') . '">[A]</em>');

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function rater_widget_update($num = 1)
{
	$widget = 'rater_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');

	if ($options != $newoptions)
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function rater_widget_custom($options = [], $num = 1)
{
	$out = '';
	if (!isset($options['header'])) $options['header'] = '';
	if (!isset($options['format'])) $options['format'] = '[A][TITLE][/A] <sup>[BALL]</sup>';
	if (!isset($options['count']))  $options['count'] = 10;

	// TITLE - название записи 
	// COUNT - всего голосов page_rating_count
	// BALL -  общий бал (деление общего рейтинга на кол-во голосов) page_ball - округлен до целого
	// REALBALL -  общий бал (деление общего рейтинга на кол-во голосов) page_ball - дробный
	// [A]ссылка[/A]

	$CI = &get_instance();
	$CI->db->select('page_slug, page_rating/page_rating_count AS page_ball, page_rating, page_rating_count, page_title', false);
	$CI->db->where('page_status', 'publish');
	//$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	$CI->db->where('page_date_publish < ', 'NOW()', false);
	$CI->db->order_by('page_ball', 'desc');
	$CI->db->order_by('page_rating', 'desc');
	// $CI->db->order_by('page_rating_count', 'desc');
	$CI->db->limit($options['count']);

	$query = $CI->db->get('page');

	if ($query->num_rows() > 0) {
		$pages = $query->result_array();

		$link = '<a href="' . getinfo('siteurl') . 'page/';

		$out .= '<ul class="mso-widget-list">' . NR;

		foreach ($pages as $page) {
			$out1 = $options['format'];
			$out1 = str_replace('[TITLE]', $page['page_title'], $out1);
			$out1 = str_replace('[COUNT]', $page['page_rating_count'], $out1);
			$out1 = str_replace('[REALBALL]', (float) $page['page_ball'], $out1);
			$out1 = str_replace('[BALL]', (round((float) $page['page_ball'])), $out1);

			$out1 = str_replace(
				'[A]',
				$link . $page['page_slug']
					. '" title="' . t('Голосов:') . ' ' . $page['page_rating_count']
					. ' ' . t('Общий бал:') . ' ' . (float) $page['page_ball']
					. '">',
				$out1
			);
			$out1 = str_replace('[/A]', '</a>', $out1);

			$out .= '<li>' . $out1 . '</li>' . NR;
		}
		$out .= '</ul>' . NR;

		if ($options['header']) $out = $options['header'] . $out;
	}

	return $out;
}

# end of file
