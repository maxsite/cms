<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// получение нужного значения
function getinfo($info = '')
{
	global $MSO;
	static $ajax = false;

	$out = '';

	switch ($info):

		case 'admin_url':
			$out = $MSO->config['admin_url']; // [admin_url] => http://localhost/application/maxsite/admin/
			break;

		case 'admin_dir':
			$out = $MSO->config['admin_dir'];
			break;

		case 'admin_plugins_dir':
			$out = $MSO->config['admin_plugins_dir'];
			break;

		case 'ajax':
			if ($ajax !== false) {
				$out = $ajax;
			} else {
				if (isset($MSO->config['DecodePunycodeIDN'])) {
					require_once $MSO->config['base_dir'] . 'common/idna.php';

					$host = parse_url($MSO->config['site_url']);
					$out = $ajax = '//' . mso_DecodePunycodeIDN($host['host']) . '/ajax/';
				} else {
					$out = $ajax = '//' . str_replace(array('http://', 'https://'), '', $MSO->config['site_url']) . 'ajax/';
				}
			}
			break;

		case 'base_dir': // каталог /maxsite/
			$out = $MSO->config['base_dir'];
			break;

		case 'cache_dir':
			$out = $MSO->config['cache_dir'];
			break;

		case 'comments_rss2_url':
			$out = $MSO->config['site_url'] . 'comments/feed';
			break;

		case 'common_dir':
			$out = $MSO->config['common_dir'];
			break;

		case 'common_url':
			$out = $MSO->config['common_url'];
			break;

		case 'comusers_id':
			$out = $MSO->data['session']['comuser']['comusers_id'] ?? '';
			break;

		case 'comusers_nik':
			$out = $MSO->data['session']['comuser']['comusers_nik'] ?? '';
			break;

		case 'description_site':
			$out = mso_get_option('description_site', 'general');
			break;

		case 'description':
			$out = htmlspecialchars(mso_get_option('description', 'general'));
			break;

		case 'feed':
		case 'rss_url':
			$out = $MSO->config['site_url'] . 'feed';
			break;

		case 'FCPATH':
			$out = $MSO->config['FCPATH'];
			break;

		case 'keywords':
			$out = htmlspecialchars(mso_get_option('keywords', 'general'));
			break;

		case 'name_site':
			$out = htmlspecialchars(mso_get_option('name_site', 'general'));
			break;

		case 'plugins_url':
			$out = '//' . str_replace(array('http://', 'https://'), '', $MSO->config['plugins_url']);
			break;

		case 'plugins_dir':
			$out = $MSO->config['plugins_dir'];
			break;

		case 'require-maxsite':
			$out = $MSO->config['site_url'] . 'require-maxsite/';
			break;

		case 'rss_comments_url':
			$out = $MSO->config['site_url'] . 'comments/feed';
			break;

		case 'remote_key':
			$out = $MSO->config['remote_key'];
			break;

		case 'site_url':
		case 'siteurl':
			$out = $MSO->config['site_url'];
			break;

		case 'stylesheet_url':
		case 'template_url':
			$out = $MSO->config['templates_url'] . $MSO->config['template'] . '/';
			break;

		case 'site_admin_url':
			$out = $MSO->config['site_admin_url']; // [site_admin_url] => http://localhost/admin/
			break;

		case 'shared_dir':
			$out = $MSO->config['base_dir'] . 'shared/';
			break;

		case 'shared_url':
			$out = $MSO->config['base_url'] . 'shared/';
			break;

		case 'site_protocol': // по какому протоколу работает сайт http://
			$out = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
			break;

		case 'session_id': // текущая сессия
			$out = $MSO->data['session']['session_id'];
			break;

		case 'session_users_password':
			if (isset($MSO->data['session']['users_password']))
				$out = mso_de_code($MSO->data['session']['users_password'], 'decode');
			else
				$out = '';
			break;

		case 'session_users_login':
			if (isset($MSO->data['session']['users_login']))
				$out = mso_de_code($MSO->data['session']['users_login'], 'decode');
			else
				$out = '';
			break;

		case 'session':
			$out = $MSO->data['session'];
			break;

		case 'title':
			$out = mso_get_option('title', 'general');
			break;

		case 'title_current': // текущий титул
			$out = $MSO->title;
			break;

		case 'time_zone':
			$out = (string) mso_get_option('time_zone', 'general', '0');
			break;

		case 'template':
			$out = $MSO->config['template'];
			break;

		case 'template_dir':
			$out = $MSO->config['templates_dir'] . $MSO->config['template'] . '/';
			break;

		case 'template_name':
			$fn_info = $MSO->config['templates_dir'] . $MSO->config['template'] . '/info.php';

			if (file_exists($fn_info)) {
				require $fn_info;
				$out = $info['name'];
			} else {
				$out = '';
			}
			break;

		case 'templates_dir':
			$out = $MSO->config['templates_dir'];
			break;

		case 'templates_url':
			$out = $MSO->config['templates_url'];
			break;

		case 'type':
			$out = $MSO->data['type'];
			break;

		case 'type_foreach_file':
			$out = $MSO->data['type_foreach_file'] ?? '';
			break;

		case 'url_new_comment':
			$out = $MSO->config['site_url'] . 'newcomment';
			break;

		case 'uploads_url':
			$out = $MSO->config['uploads_url'];
			break;

		case 'uploads_dir':
			$out = $MSO->config['uploads_dir'];
			break;

		case 'users_nik':
			$out = $MSO->data['session']['users_nik'] ?? '';
			break;

		case 'users_id':
			$out = $MSO->data['session']['users_id'] ?? '';
			break;

		case 'uri_get':
			$out = $MSO->data['uri_get'];
			break;

		case 'version':
			$out = $MSO->version;
			break;
            
        case 'storage_dir':
			$out = $MSO->config['application_dir'] . 'storage/';
			break;

	endswitch;

	return $out;
}

// проверка залогиннености юзера
function is_login()
{
	global $MSO;

	return ($MSO->data['session']['userlogged'] == 1) ? true : false;
}

// проверка залогиннености комюзера
// если есть, то возвращает массив данных
function is_login_comuser()
{
	global $MSO;

	if (isset($MSO->data['session']['comuser']) and ($comuser = $MSO->data['session']['comuser']))
		return $comuser;
	else
		return false;
}

// проверка типа страницы, который определился в контролере
function is_type($type)
{
	global $MSO;

	return ($MSO->data['type'] == $type) ? true : false;
}

// возвращает true или false при проверке $MSO->data['uri_segment'], то есть по сегментам URL
// где например [1] => page  [2] => about
// что означает type = page  slug=about
// http://localhost/page/about
// можно указать только тип или только slug
// тогда неуказанный параметр не учитывается (всегда true)
function is_type_slug($type = '', $slug = '')
{
	global $MSO;

	$rt = $rs = '';

	$type = urlencode($type);
	$slug = urlencode($slug);

	// тип
	if ($type and isset($MSO->data['uri_segment'][1])) $rt = $MSO->data['uri_segment'][1];

	// slug
	if ($slug and isset($MSO->data['uri_segment'][2])) $rs = $MSO->data['uri_segment'][2];

	return ($rt == $type and $rs == $slug);
}

// проверяем рубрику у страницы
// если это page и есть указанная рубрика, то возвращаем true
// если это не page или нет указанной рубрики, то возвращаем false
// если $and_id = true , то ищем и по id
// если $and_name = true , то ищем и по category_name
function is_page_cat($slug = '', $and_id = true, $and_name = true)
{
	global $page;

	if (!$slug) return false; // slug не указан
	if (!is_type('page')) return false; // тип не page
	if (!isset($page['page_categories_detail'])) return false; // нет информации о рубриках

	$result = false;

	// информация о slug, id и name в массиве $page['page_categories_detail']
	foreach ($page['page_categories_detail'] as $id => $val) {
		if ($val['category_slug'] == $slug) $result = true; // slug совпал
		if (!$result and $and_id and $id == $slug) $result = true; // можно искать по $id
		if (!$result and $and_name and $val['category_name'] == $slug) $result = true; // category_name совпал

		if ($result) break;
	}

	return $result;
}

// проверка если feed
function is_feed()
{
	global $MSO;

	return $MSO->data['is_feed'] ? true : false;
}

// получаем данные юзера по его логину/паролю
function mso_get_user_data($login = false, $password = false)
{
	if (!$login or !$password) return false;

	$CI = &get_instance();
	$CI->db->select('*');
	$CI->db->limit(1); // одно значение
	$CI->db->where('users_login', $login); // where 'users_login' = $login
	$CI->db->where('users_password', $password);  // where 'users_password' = $password

	$query = $CI->db->get('users');

	if ($query->num_rows() > 0) {
		// есть такой юзер
		$r = $query->result_array();

		return $r[0];
	} else {
		return false;
	}
}

// получение информации об авторе по его номеру из url http://localhost/author/1
// или явно указанному номеру
function mso_get_author_info($id = 0)
{
	if (!$id) $id = mso_segment(2);
	if (!$id or !is_numeric($id)) return []; // неверный id

	$key_cache = 'mso_get_author_info_' . $id;
	if ($k = mso_get_cache($key_cache)) return $k; // да есть в кэше

	$out = [];

	$CI = &get_instance();
	$CI->db->select('*');
	$CI->db->where('users_id', $id);
	$query = $CI->db->get('users');

	if ($query->num_rows() > 0) {
		// есть такой юзер
		$out = $query->result_array();
		$out = $out[0];
	}

	mso_add_cache($key_cache, $out);

	return $out;
}

# end of file
