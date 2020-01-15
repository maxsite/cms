<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// содание разрешения для действия
function mso_create_allow($act = '', $desc = '')
{
	// global $MSO;

	// считываем опцию
	$d = mso_get_option('groups_allow', 'general');

	if (!$d) {
		// нет таких опций вообще
		$d = [$act => $desc]; // создаем массив
		mso_add_option('groups_allow', $d, 'general'); // добавляем опции
		return;
	} else {
		// есть опции
		if (isset($d[$act]) and ($d[$act] == $desc)) {
			return; // ничего не изменилось
		} else {
			// что-то новенькое
			$d[$act] = $desc; // добавляем
			mso_add_option('groups_allow', $d, 'general');
			return;
		}
	}
}

// удалить действие/функцию
function mso_remove_allow($act = '')
{
	// global $MSO;

	$d = mso_get_option('groups_allow', 'general');

	if (isset($d[$act])) {
		unset($d[$act]);
		mso_delete_option('groups_allow', 'general');
		mso_add_option('groups_allow', $d, 'general');
	}
}

# end of file
