<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */


# функция автоподключения плагина
function catclouds_autoload()
{
	mso_register_widget('catclouds_widget', t('Облако рубрик')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function catclouds_uninstall()
{
	mso_delete_option_mask('catclouds_widget_', 'plugins'); // удалим созданные опции
}

# функция, которая берет настройки из опций виджетов
function catclouds_widget($num = 1)
{
	$widget = 'catclouds_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array()); // получаем опции

	// заменим заголовок
	if (isset($options['header']) and $options['header'])
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else
		$options['header'] = '';

	return catclouds_widget_custom($options, $num);
}

# форма настройки виджета 
# имя функции = виджет_form
function catclouds_widget_form($num = 1)
{
	$widget = 'catclouds_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());

	if (!isset($options['header'])) $options['header'] = '';

	if (!isset($options['block_start'])) $options['block_start'] = '<div class="mso-catclouds">';
	if (!isset($options['block_end'])) $options['block_end'] = '</div>';

	if (!isset($options['min_size'])) $options['min_size'] = 90;
	else $options['min_size'] = (int) $options['min_size'];

	if (!isset($options['max_size'])) $options['max_size'] = 230;
	else $options['max_size'] = (int) $options['max_size'];

	if (!isset($options['cat_id'])) $options['cat_id'] = 0;
	else $options['cat_id'] = (int) $options['cat_id'];

	if (!isset($options['format']))
		$options['format'] = '<span style="font-size: [SIZE]%"><a href="[URL]">[CAT]</a><sub style="font-size: 12px;">[COUNT]</sub></span>';

	if (!isset($options['current']))
		$options['current'] = '<span style="font-size: [SIZE]%"><b><a href="[URL]">[CAT]</a></b><sub style="font-size: 12px;">[COUNT]</sub></span>';

	if (!isset($options['sort']))
		$options['sort'] = 0;
	else
		$options['sort'] = (int) $options['sort'];

	// вывод самой формы
	$CI = &get_instance();
	$CI->load->helper('form');


	$form = mso_widget_create_form(t('Заголовок'), form_input(array('name' => $widget . 'header', 'value' => $options['header'])), '');

	$form .= mso_widget_create_form(t('Формат'), form_textarea(array('name' => $widget . 'format', 'value' => $options['format'], 'rows' => 3)), '[SIZE] [URL] [CAT] [COUNT] [CURRENT]');

	$form .= mso_widget_create_form(t('Формат текущей'), form_textarea(array('name' => $widget . 'current', 'value' => $options['current'], 'rows' => 3)), '[SIZE] [URL] [CAT] [COUNT] [CURRENT]');

	$form .= mso_widget_create_form(t('Мин. размер (%)'), form_input(array('name' => $widget . 'min_size', 'value' => $options['min_size'])), '');

	$form .= mso_widget_create_form(t('Макс. размер (%)'), form_input(array('name' => $widget . 'max_size', 'value' => $options['max_size'])), '');

	$form .= mso_widget_create_form(t('Номер рубрики'), form_input(array('name' => $widget . 'cat_id', 'value' => $options['cat_id'])), '');

	$form .= mso_widget_create_form(t('Начало блока'), form_input(array('name' => $widget . 'block_start', 'value' => $options['block_start'])), '');

	$form .= mso_widget_create_form(t('Конец блока'), form_input(array('name' => $widget . 'block_end', 'value' => $options['block_end'])), '');

	$form .= mso_widget_create_form(t('Сортировка'), form_dropdown(
		$widget . 'sort',
		array(
			'0' => t('По количеству записей (обратно)'),
			'1' => t('По количеству записей'),
			'2' => t('По алфавиту'),
			'3' => t('По алфавиту (обратно)')
		),
		$options['sort']
	), '');

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function catclouds_widget_update($num = 1)
{
	$widget = 'catclouds_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', []);

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['block_start'] = mso_widget_get_post($widget . 'block_start');
	$newoptions['block_end'] = mso_widget_get_post($widget . 'block_end');
	$newoptions['min_size'] = mso_widget_get_post($widget . 'min_size');
	$newoptions['max_size'] = mso_widget_get_post($widget . 'max_size');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['current'] = mso_widget_get_post($widget . 'current');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');
	$newoptions['cat_id'] = mso_widget_get_post($widget . 'cat_id');

	if ($options != $newoptions)
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function catclouds_widget_custom($options = [], $num = 1)
{
	$out = '';

	if (!isset($options['header'])) $options['header'] = '';
	if (!isset($options['block_start'])) $options['block_start'] = '<div class="mso-catclouds">';
	if (!isset($options['block_end']))   $options['block_end'] = '</div>';

	if (!isset($options['min_size']))
		$min_size = 90;
	else
		$min_size = (int) $options['min_size'];

	if (!isset($options['max_size']))
		$max_size = 230;
	else
		$max_size = (int) $options['max_size'];

	if (!isset($options['cat_id']))
		$cat_id = 0;
	else
		$cat_id = (int) $options['cat_id'];

	if (!isset($options['format']))
		$options['format'] = '<span style="font-size: [SIZE]%"><a href="[URL]">[CAT]</a><sub style="font-size: 12px;">[COUNT]</sub></span>';

	if (!isset($options['current']))
		$options['current'] = '<span style="font-size: [SIZE]%"><b><a href="[URL]">[CAT]</a></b><sub style="font-size: 12px;">[COUNT]</sub></span>';

	if (!isset($options['sort']))
		$sort = 0;
	else
		$sort = (int) $options['sort'];

	$url = getinfo('siteurl') . 'category/';

	// кэш — в нем только catcloud
	$cache_key = 'catclouds_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);

	if (!$k) {
		// catcloud нет в кэше
		$all_cat = mso_cat_array_single('page', 'category_name', 'ASC', 'blog');

		$catcloud = [];

		foreach ($all_cat as $key => $val) {
			if ($cat_id) // указана рубрика
			{
				// выводим саму рубрику и всех её детей
				if ($val['category_id'] == $cat_id or $val['category_id_parent'] == $cat_id) {
					if (count($val['pages']) > 0) // кол-во страниц в этой рубрике > 0
						$catcloud[$val['category_name']] = array('count' => count($val['pages']), 'slug' => $val['category_slug']);
				}
			} else // рубрика не указана - выводим все что есть
			{
				if (count($val['pages']) > 0) // кол-во страниц в этой рубрике > 0
					$catcloud[$val['category_name']] = array('count' => count($val['pages']), 'slug' => $val['category_slug']);
			}
		}

		asort($catcloud);
		$min = reset($catcloud);
		$min = $min['count'];
		$max = end($catcloud);
		$max = $max['count'];

		if ($max == $min) $max++;

		// сортировка перед выводом
		if ($sort == 0) arsort($catcloud); // по количеству обратно
		elseif ($sort == 1) asort($catcloud); // по количеству 
		elseif ($sort == 2) ksort($catcloud); // по алфавиту
		elseif ($sort == 3) krsort($catcloud); // обратно по алфавиту
		else arsort($catcloud); // по умолчанию

		// сохраним в массиве слкужебный элемент MIN_MAX — они нужный для вывода
		$catcloud['_MSO_CATCLOUD_MIN_MAX']['MIN'] = $min;
		$catcloud['_MSO_CATCLOUD_MIN_MAX']['MAX'] = $max;

		mso_add_cache($cache_key, $catcloud); // в кэш

		unset($catcloud['_MSO_CATCLOUD_MIN_MAX']);
	} else {
		// catcloud есть в кэше
		$catcloud = $k;

		$min = $catcloud['_MSO_CATCLOUD_MIN_MAX']['MIN'];
		$max = $catcloud['_MSO_CATCLOUD_MIN_MAX']['MAX'];
		unset($catcloud['_MSO_CATCLOUD_MIN_MAX']);
	}

	// определим текущую рубрику
	$current_cat_slug = []; // здесь все slug рубрик

	if (is_type('page')) {
		$pageData = mso_get_val('mso_pages', 0, true);

		if ($pageData) {
			// нас интересует только slug
			foreach ($pageData['page_categories_detail'] as $i => $d) {
				if ($d['category_slug']) $current_cat_slug[] = $d['category_slug'];
			}
		}
	} elseif (is_type('category')) // это рубрика, просто смотрим сегмент
	{
		$current_cat_slug[] = mso_segment(2);
	}

	foreach ($catcloud as $cat => $ar) {
		$count = $ar['count'];
		$slug = $ar['slug'];

		$font_size = round((($count - $min) / ($max - $min)) * ($max_size - $min_size) + $min_size);

		$f = in_array($slug, $current_cat_slug) ? $options['current'] : $options['format'];

		$af = str_replace(
			array('[SIZE]', '[URL]', '[CAT]', '[COUNT]'),
			array($font_size, $url . $slug, $cat, $count),
			$f
		);

		$out .= $af . ' ';
	}

	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'];

	return $out;
}

# end of file
