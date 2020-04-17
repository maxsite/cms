<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

/**
 * функция для отладки кода pr($любая_переменная)
 * $html == true - преобразование спецсимволов в HTML, иначе отдается как есть
 * $echo == true - сразу вывод в браузер, иначе возврат по return
 */
function pr($var, $html = true, $echo = true)
{
	if (!$echo)
		ob_start();
	else
		echo '<pre>';

	if (is_bool($var)) {
		if ($var)
			echo 'TRUE';
		else
			echo 'FALSE';
	} else {
		if (is_scalar($var)) {
			if (!$html) {
				echo $var;
			} else {
				$var = str_replace('<br />', "<br>", $var);
				$var = str_replace('<br>', "<br>\n", $var);
				$var = str_replace('</p>', "</p>\n", $var);
				$var = str_replace('<ul>', "\n<ul>", $var);
				$var = str_replace('<li>', "\n<li>", $var);
				$var = htmlspecialchars($var);
				$var = wordwrap($var, 300);

				echo $var;
			}
		} else {
			if (!$html)
				print_r($var);
			else
				echo htmlspecialchars(print_r($var, true));
		}
	}

	if (!$echo) {
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	} else {
		echo '</pre>';
	}
}

/**
 * Аналогична pr, только завершающаяся die() 
 * используется для отладки с помощью прерывания
 */
function _pr($var, $html = true, $echo = true)
{
	pr($var, $html, $echo);
	die();
}


// функция, формирующая sql-запрос
// используется для отладки перед $CI->db->get()
function _sql()
{
	$CI = &get_instance();
	$sql = $CI->db->_compile_select();
	
	return $sql;
}

# end of file
