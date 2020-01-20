<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// функция залогирования
// перенесена из application\views\login.php
function _mso_login()
{
	global $MSO;

	// обрабатываем POST если есть 
	if ($_POST) $_POST = mso_clean_post([
		'flogin_submit' => 'base',
		'flogin_redirect' => 'base',
		'flogin_user' => 'base',
		'flogin_password' => 'trim|xss|strip_tags',
		'flogin_session_id' => 'base'
	]);

	if (
		$_POST 	and isset($_POST['flogin_submit'])
		and isset($_POST['flogin_redirect'])
		and isset($_POST['flogin_user'])
		and isset($_POST['flogin_password'])
		and isset($_POST['flogin_session_id'])
	) {
		sleep(3); // задержка - примитивная защита от подбора пароля

		$flogin_session_id = $_POST['flogin_session_id'];

		// защита сесии
		if ($MSO->data['session']['session_id'] != $flogin_session_id) mso_redirect('loginform/error');

		$flogin_redirect = urldecode($_POST['flogin_redirect']);

		if ($flogin_redirect == 'home') $flogin_redirect = getinfo('siteurl');

		$flogin_user = $_POST['flogin_user'];
		$flogin_password = $_POST['flogin_password'];

		// проверяем на strip - запрещенные символы
		if (!mso_strip($flogin_user, true) or !mso_strip($flogin_password, true)) mso_redirect('loginform/error');

		$flogin_password = mso_md5($flogin_password);

		$CI = &get_instance();

		// если это комюзер, то логин = email 
		// проверяем валидность email и если он верный, то ставим куку на этого комюзера 
		// и редиректимся на главную (куку ставить только на главную!)
		// если же это обычный юзер-автор, то проверяем логин и пароль по базе

		if (mso_valid_email($flogin_user)) {
			// если в логине мыло, то проверяем сначала в таблице авторов
			$CI->db->from('users'); # таблица users
			$CI->db->select('*'); # все поля
			$CI->db->limit(1); # одно значение

			$CI->db->where('users_email', $flogin_user); // where 'users_login' = $flogin_user
			$CI->db->where('users_password', $flogin_password);  // where 'users_password' = $flogin_password

			$query = $CI->db->get();

			if ($query->num_rows() > 0) {
				// есть такой юзер
				$userdata = $query->result_array();

				# добавляем юзера к сессии
				$CI->session->set_userdata('userlogged', '1');

				$data = [
					'users_id' => $userdata[0]['users_id'],
					'users_nik' => $userdata[0]['users_nik'],
					'users_login' => mso_de_code($userdata[0]['users_login'], 'encode'),
					'users_password' => mso_de_code($userdata[0]['users_password'], 'encode'),
					'users_groups_id' => $userdata[0]['users_groups_id'],
					'users_last_visit' => $userdata[0]['users_last_visit'],
					'users_show_smiles' => $userdata[0]['users_show_smiles'],
					'users_time_zone' => $userdata[0]['users_time_zone'],
					'users_language' => $userdata[0]['users_language'],
					'users_avatar_url' => $userdata[0]['users_avatar_url'],
					'users_email' => $userdata[0]['users_email'],
					// 'users_levels_id' => $userdata[0]['users_levels_id'],
					// 'users_skins' => $userdata[0]['users_skins']
				];

				$CI->session->set_userdata($data);

				// сразу же обновим поле последнего входа
				$CI->db->where('users_id', $userdata[0]['users_id']);
				$CI->db->update('users', ['users_last_visit' => date('Y-m-d H:i:s')]);

				mso_redirect($flogin_redirect, true);
			} else {
				// это не автор, значит это комюзер
				$CI->db->select('comusers_id, comusers_password, comusers_email, comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
				$CI->db->where('comusers_email', $flogin_user);
				$CI->db->where('comusers_password', $flogin_password);
				$query = $CI->db->get('comusers');

				if ($query->num_rows()) {
					// есть такой комюзер
					$comuser_info = $query->row_array(1); // вся инфа о комюзере

					$comuser_info['comusers_password'] = mso_de_code($comuser_info['comusers_password'], 'encode');

					// сразу же обновим поле последнего входа
					$CI->db->where('comusers_id', $comuser_info['comusers_id']);
					$CI->db->update('comusers', array('comusers_last_visit' => date('Y-m-d H:i:s')));

					$expire  = time() + 60 * 60 * 24 * 365; // 365 дней

					$name_cookies = 'maxsite_comuser';
					$value = serialize($comuser_info);

					mso_add_to_cookie($name_cookies, $value, $expire, $flogin_redirect); // в куку для всего сайта

					exit();
				} else {
					// неверные данные
					mso_redirect('loginform/error');
					exit;
				}
			}
		} else {
			// это обычный автор

			$CI->db->from('users'); // таблица users
			$CI->db->select('*'); // все поля
			$CI->db->limit(1); // одно значение

			$CI->db->where('users_login', $flogin_user); // where 'users_login' = $flogin_user
			$CI->db->where('users_password', $flogin_password);  // where 'users_password' = $flogin_password

			$query = $CI->db->get();

			if ($query->num_rows() > 0) {
				// есть такой юзер
				$userdata = $query->result_array();

				// добавляем юзера к сессии
				$CI->session->set_userdata('userlogged', '1');

				$data = array(
					'users_id' => $userdata[0]['users_id'],
					'users_nik' => $userdata[0]['users_nik'],
					'users_login' => mso_de_code($userdata[0]['users_login'], 'encode'),
					'users_password' => mso_de_code($userdata[0]['users_password'], 'encode'),
					'users_groups_id' => $userdata[0]['users_groups_id'],
					'users_last_visit' => $userdata[0]['users_last_visit'],
					'users_show_smiles' => $userdata[0]['users_show_smiles'],
					'users_time_zone' => $userdata[0]['users_time_zone'],
					'users_language' => $userdata[0]['users_language'],
					'users_avatar_url' => $userdata[0]['users_avatar_url'],
					'users_email' => $userdata[0]['users_email'],
					// 'users_levels_id' => $userdata[0]['users_levels_id'],
					// 'users_skins' => $userdata[0]['users_skins']
				);

				$CI->session->set_userdata($data);

				// сразу же обновим поле последнего входа
				$CI->db->where('users_id', $userdata[0]['users_id']);
				$CI->db->update('users', ['users_last_visit' => date('Y-m-d H:i:s')]);

				mso_redirect($flogin_redirect, true);
			} else {
				mso_redirect('loginform/error');
			}
		} // автор
	} else {
		$MSO->data['type'] = 'loginform';
		$template_file = $MSO->config['templates_dir'] . $MSO->config['template'] . '/index.php';

		if (file_exists($template_file))
			require $template_file;
		else
			show_error('Ошибка - отсутствует файл шаблона index.php');
	}
}

// функция разлогирования
// перенесена из application\views\logout.php
function _mso_logout()
{
	$ci = &get_instance();
	$ci->session->sess_destroy();
	$url = (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']) ? mso_clean_str($_SERVER['HTTP_REFERER'], 'xss') : '';

	// проверяем, чтобы url был текущего сайта
	$pos = strpos($url, getinfo('site_url'));
	if ($pos === false or $pos > 0) $url = ''; // чужой, сбрасываем переход

	// сразу же удаляем куку комюзера
	$comuser = mso_get_cookie('maxsite_comuser', false);

	if ($comuser) {
		$name_cookies = 'maxsite_comuser';
		$expire  = time() - 31500000;
		$value = '';

		if (isset($ci->session->userdata['comuser'])) unset($ci->session->userdata['comuser']);

		//_pr($url);
		// mso_add_to_cookie('mso_edit_form_comuser', '', $expire); 
		//mso_add_to_cookie($name_cookies, $value, $expire, getinfo('siteurl') . mso_current_url()); // в куку для всего сайта
		mso_add_to_cookie($name_cookies, $value, $expire, $url); // в куку для всего сайта

	} elseif ($url) {
		mso_redirect($url, true);
	} else {
		mso_redirect(getinfo('site_url'), true);
	}
}

# end of file
