<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

define("NR", "\n");    // перенос строки
define("NR2", "\n\n"); // двойной перенос строки
define("TAB", "\t");   // табулятор
define("NT", "\n\t");  // перенос + табулятор

/**
 *  функция инициализации
 */
function mso_initalizing()
{
	global $MSO;
	global $MSO_CACHE_OPTIONS;

	$CI = &get_instance();

	// нет соединения с Базой данных - всё рубим, а-а-а-а-а!
	if (!$CI->db->conn_id) die('Database not connected');

	// считываем файл конфигурации
	$fn = $MSO->config['config_file'];

	if (file_exists($fn)) require_once $fn;

	$path = getinfo('cache_dir');

	$mso_cache_last = $path . '_mso_cache_last.txt';

	if (file_exists($mso_cache_last)) {
		$time = (int) trim(implode('', file($mso_cache_last)));
		$time = $time + $MSO->config['cache_time'] + 60; // запас + 60 секунд

		if (time() > $time) mso_flush_cache(); // время истекло - сбрасываем кэш
	} else {
		// файла нет > _mso_cache_last.txt < создадим - наверное совсем старый кэш
		mso_flush_cache();
	}

	// подключаем опции - они могут быть в кэше
	if ($opt = mso_get_cache('options')) // есть кэш опций
		$MSO_CACHE_OPTIONS = $opt;
	else
		mso_refresh_options(); // обновляем кэш опций

	// проверим текущий шаблон
	$template = mso_get_option('template', 'general'); // считали из опций
	$index = $MSO->config['templates_dir'] . $template . '/index.php'; // проверим в реале

	if (!file_exists($index)) {
		// нет такого шаблона - меняем на дефолтный
		mso_add_option('template', 'default', 'general');
		$MSO->config['template'] = 'default';
	} else {
		// все ок
		$MSO->config['template'] = $template;
	}

	// проверяем залогинненость юзера
	if (!isset($CI->session->userdata['userlogged']) or !$CI->session->userdata['userlogged']) {
		// не залогинен
		$CI->session->userdata['userlogged'] = 0;
	} else {
		// отмечено, что залогинен
		// нужно проверить верность данных юзера
		$CI->db->from('users'); # таблица users
		$CI->db->select('users_id, users_groups_id');
		$CI->db->limit(1); # одно значение

		// $CI->db->where( array('users_login'    => $CI->session->userdata['users_login'],
		// 					  'users_password' => $CI->session->userdata['users_password']) );

		$CI->db->where(array(
			'users_login'    => mso_de_code($CI->session->userdata['users_login'], 'decode'),
			'users_password' => mso_de_code($CI->session->userdata['users_password'], 'decode')
		));

		$query = $CI->db->get();

		if (!$query or $query->num_rows() == 0) # нет такого - возможно взлом
		{
			$CI->session->sess_destroy(); // убиваем сессию
			$CI->session->userdata['userlogged'] = 0; // отмечаем, что не залогинен
		} else {
			// есть что-то
			$row = $query->row();

			// сразу выставим группу
			$MSO->data['session']['users_groups_id'] = $row->users_groups_id;
		}
	}

	// обновляем время последней активности сессии
	// раньше было только для users, теперь делаем для всех
	// при этом сохраняем предыдущее значение
	// это значение позволяет отследить периодичность действий посетителя
	if (isset($CI->session->userdata['last_activity'])) {
		$CI->session->set_userdata('last_activity_prev', $CI->session->userdata['last_activity']);
		$CI->session->set_userdata('last_activity', time());
	} else {
		$CI->session->set_userdata('last_activity_prev', time());
		$CI->session->set_userdata('last_activity', $CI->session->userdata['last_activity_prev']);
	}

	// аналогично проверяем и комюзера, только данные из куки
	// но при этом сразу сохраняем все данные комюзера, чтобы потом не обращаться к БД

	$comuser = mso_get_cookie('maxsite_comuser', false);

	if ($comuser) {
		$comuser = unserialize($comuser);
		/*
		[comusers_id] => 1
		[comusers_password] => 037035235237852
		[comusers_email] => max-3000@list.ru
		[comusers_nik] => Максим
		[comusers_url] => http://maxsite.org/
		[comusers_avatar_url] => http://maxsite.org/avatar.jpg
		*/
		// нужно сверить с тем, что есть

		$CI->db->select('comusers_id, comusers_password, comusers_email');
		$CI->db->where('comusers_id', $comuser['comusers_id']);
		$CI->db->where('comusers_password', mso_de_code($comuser['comusers_password'], 'decode'));
		$CI->db->where('comusers_email', $comuser['comusers_email']);

		$query = $CI->db->get('comusers');
		if ($query->num_rows()) {
			// есть такой комюзер
			$CI->session->userdata['comuser'] = $comuser;
		} else {
			// неверные данные
			$CI->session->userdata['comuser'] = 0;
		}
	} else {
		$CI->session->userdata['comuser'] = 0;
	}

	// дефолтные хуки
	mso_hook_add('init', '_mso_require_functions_file'); // подключение functions.php текущего шаблона
	mso_hook_add('content_content', 'mso_shortcode_content', 1); // хук контента на шорткоды
	mso_hook_add('body_end', 'mso_add_file_body_end', 1); // хук на вывод подключенных файлов в конце BODY
	mso_hook_add('head_end', 'mso_add_preload_hook', 1); // хук на вывод preload в HEAD
}

/**
 * Подключим файл functions.php в шаблоне - если есть
 * функция срабатывает по хуку init
 *
 * @param  mixed $args
 *
 * @return void
 */
function _mso_require_functions_file($args = '')
{
	global $MSO;

	$fn = $MSO->config['templates_dir'] . $MSO->config['template'] . '/functions.php';

	if (file_exists($fn)) require_once $fn;

	return $args;
}

# end of file
