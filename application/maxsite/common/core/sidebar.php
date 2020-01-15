<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// регистрируем сайдбар
function mso_register_sidebar($sidebar = '1', $title = 'Cайдбар', $options = [])
{
	global $MSO;

	$MSO->sidebars[$sidebar] = [
		'title' => t($title),
		'options' => $options
	];
}

// вывод сайбрара
function mso_show_sidebar($sidebar = '1', $block_start = '<div class="[CLASS]mso-widget mso-widget_[NUMW] mso-widget_[SB]_[NUMW] mso-[FN] mso-[FN]_[NUMF]">', $block_end = '</div>', $echo = true)
{
	global $MSO, $page; // чтобы был доступ к параметрам страниц в условиях виджетов
	
	static $num_widget = []; // номер виджета по порядку в одном сайдбаре

	$widgets = mso_get_option('sidebars-' . $sidebar, 'sidebars', []);

	$out = '';

	if ($widgets) {
		foreach ($widgets as $widget) {
			$usl_res = 1; // предполагаем, что нет условий, то есть всегда true
			$class = ''; // дополнительный класс виджета

			// возможно указаны css-классы виджета между @класс1 класс2@
			if (preg_match('!\@(.*?)\@!is', $widget, $matches)) $class = trim($matches[1]) . ' ';

			// удаляем указанные классы
			$widget = trim(preg_replace('!\@(.*?)\@!is', "", $widget));

			// удалим возможные двойные пробелы
			$widget = str_replace('  ', ' ', $widget);

			// имя виджета может содержать номер через пробел
			$arr_w = explode(' ', $widget); // в массив

			if (sizeof($arr_w) > 1) {
				// два или больше элементов
				$widget = trim($arr_w[0]); // первый - функция
				$num = trim($arr_w[1]); // второй - номер виджета

				if (isset($arr_w[2])) {
					// есть какое-то php-условие
					$u = $arr_w; // поскольку у нас разделитель пробел, то нужно до конца все подбить в одну строчку
					$u[0] = $u[1] = '';
					$usl = trim(implode(' ', $u));

					// текст условия, is_type('home') or is_type('category')
					$usl = 'return ( ' . $usl . ' ) ? 1 : 0;';
					$usl_res = eval($usl); // выполяем
					
					if ($usl_res === false) $usl_res = 1; // возможно произошла ошибка
				}
			} else {
				$num = 0; // номер виджета не указан, значит 0
			}

			// номер функции виджета может быть не только числом, но и текстом
			// если текст, то нужно его преобразовать в slug, чтобы исключить 
			// некоректную замену [NUMF] для стилей
			$num = mso_slug($num);

			// двойной - заменим на один - защита id в форме админки
			$num = str_replace('--', '-', $num);

			if (function_exists($widget) and $usl_res === 1) {
				if ($temp = $widget($num)) {
					// выполняем виджет если он пустой, то пропускаем вывод
					if (isset($num_widget[$sidebar]['numw'])) //уже есть номер виджета
					{
						$numw = ++$num_widget[$sidebar]['numw'];
						$num_widget[$sidebar]['numw'] = $numw;
					} else {
						// нет такого = пишем 1
						$numw = $num_widget[$sidebar]['numw'] = 1;
					}

					$st = str_replace('[FN]', $widget, $block_start); // название функции виджета
					$st = str_replace('[NUMF]', $num, $st); // номер функции
					$st = str_replace('[NUMW]', $numw, $st);	//
					$st = str_replace('[SB]', $sidebar, $st); // номер сайдбара
					$st = str_replace('[CLASS]', $class, $st);
					
					$en = str_replace('[FN]', $widget, $block_end);
					$en = str_replace('[NUMF]', $num, $en);
					$en = str_replace('[NUMW]', $numw, $en);
					$en = str_replace('[SB]', $sidebar, $en);

					$out .= $st . $temp . $en;
				}
			}
		}

		if ($echo)
			echo $out;
		else
			return $out;
	}
}

// регистрируем виджет
function mso_register_widget($widget = false, $title = 'Виджет')
{
	global $MSO;

	if ($widget) $MSO->widgets[$widget] = t($title);
}


// вспомогательная функция, которая принимает глобальный _POST
// и поле $option. Использовать в _update виджетов
function mso_widget_get_post($option = '')
{
	if (isset($_POST[$option]))
		return stripslashes($_POST[$option]);
	else
		return '';
}

// функция для виджетов формирует поля формы для form.fform с необходимой html-разметкой
// каждый вызов функции - одна строчка + если есть $hint - вторая
// $form = mso_widget_create_form('Название', поле формы, 'Подсказка');
function mso_widget_create_form($name = '', $input = '', $hint = '')
{
	if ($hint)
		$out = '<p><label><span class="name_widget">' . $name . '</span>' . $input . '</label><span class="hint_widget">' . $hint . '</span></p>';
	else
		$out = '<p><label><span class="name_widget">' . $name . '</span>' . $input . '</label></p>';

	return $out;
}

# end of file
