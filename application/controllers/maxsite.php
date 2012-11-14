<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if (!class_exists('Maxsite'))
{
	class Maxsite extends CI_Controller 
	{
		var $data_def = array();
		
		public function __construct()
		{
			parent::__construct();
			
			# подключаем свою библиотеку
			$this->load->library('maxsite_lib');
			
			# получаем к своему массиву параметров текущий URI
			$this->data_def['uri_segment'] = mso_segment_array(); 
			$this->data_def['uri_get'] = mso_url_get(); // здесь строчка _GET

			# в случае ошибки с адресами, расскоментируйте это строчку
			# $this->data_def['uri_segment'] = $this->uri->segment_array();

			# проверяем rss
			if ( ( count($this->data_def['uri_segment']) > 0 ) and 
				 ( $this->data_def['uri_segment'][count($this->data_def['uri_segment'])] == 'feed' )
				)
				$this->data_def['is_feed'] = '1';
			else
				$this->data_def['is_feed'] = '0';
				
			# инициализация + проверка залогиненности
			mso_initalizing();

			$this->data_def['session'] = $this->session->userdata;
			
			# $this->session->sess_destroy(); // для тестирования - обнуление сессии
		}
		
		function _remap($method)
		{	
			### в случае ошибок с адресами, закоментируйте эти две строчки
			if ( isset($this->data_def['uri_segment'][1]) ) $method = $this->data_def['uri_segment'][1];
			else
			{
				global $mso_install;
			
				if ($mso_install == false)
				{
					$CI = & get_instance();	
					if ( !$CI->db->table_exists('options')) return $this->install();
				}
				else $method = 'home'; // нет сегмента, значит это главная
			}
			### 
			
			if (
				($method == 'home') or
				($method == 'archive') or
				($method == 'author') or
				($method == 'category') or
				($method == 'page') or
				($method == 'users') or
				($method == 'search') or
				($method == 'tag') or
				($method == 'comments') or
				($method == 'loginform')
				)
			{
				$this->_view_i($method);
			}
			elseif ($method == 'index') $this->index();
			elseif ($method == 'feed') $this->index('home');
			elseif ($method == 'install') $this->install();
			elseif ($method == 'remote') $this->_view_i('remote', 'remote');
			elseif ($method == 'ajax') $this->_view_i('ajax', 'ajax');
			elseif ($method == 'require-maxsite') $this->_view_i('require-maxsite', 'require-maxsite');
			elseif ($method == 'admin') $this->_view_i('admin', 'admin');
			elseif ($method == 'url') $this->_view_i('url', 'url/url');
			elseif ($method == 'xmlrpc') $this->_view_i('xmlrpc', 'xmlrpc/xmlrpc');
			elseif ($method == 'xmlrpc_server') $this->_view_i('xmlrpc_server', 'xmlrpc/xmlrpc_server');
			// elseif ($method == 'trackback') $this->_view_i('trackback', 'xmlrpc/trackback');
			// elseif ($method == 'ping') $this->_view_i('ping', 'xmlrpc/ping');
			elseif ($method == 'login') $this->_view_i('login', 'login');
			elseif ($method == 'logout') $this->_view_i('logout', 'logout');
			else $this->page_404();
		}
		
		function _view_i($type = 'home', $vievers = 'index')
		{
			global $MSO;
			$data = array('type'=>$type);
			$MSO->data = array_merge($this->data_def, $data);

			// если главная страница то проверим в сессии служебный массив _add_to_cookie
			// если он есть, то внесем из него все данные в куки
			//	[_add_to_cookie] => Array
			//	(
			//		[namecooke] => Array
			//			(
			//				[value] => ru
			//				[expire] => 1221749019
			//			)
			//	)
			//	[_add_to_cookie_redirect] => http://max-3000.com/page/about
			
			if ($type == 'home' and isset($this->session->userdata['_add_to_cookie'])) // есть
			{
				foreach ($this->session->userdata['_add_to_cookie'] as $key=>$val)
				{
					if (isset($val['value']) and isset($val['expire']))
					{
						setcookie($key, $val['value'], $val['expire']); // записали в куку
					}
				}
				
				$this->session->unset_userdata('_add_to_cookie'); // удаляем добавленное
				
				mso_flush_cache();
				
				// редирект на главную страницу
				if (isset($this->session->userdata['_add_to_cookie_redirect']))
				{
					$r = $this->session->userdata['_add_to_cookie_redirect'];
					
					if (is_bool($r) or is_numeric($r)) // === true or $r === false) // логическая переменная
						mso_redirect(getinfo('siteurl'), true); // редирект на главную
					else 
						mso_redirect($r, true); // редирект по указанному адресу
				}
				else 
					mso_redirect(getinfo('siteurl'), true); // редирект на главную
					
				exit;
			}
			
			
			
			if (function_exists('mso_autoload_plugins')) mso_autoload_plugins();
			
			mso_hook('init');
			
			$this->load->view($vievers, $MSO->data);
		}
		
		function page_404()
		{
			# если страница не определена здесь, то 
			# возможно существует расширение 
			# для этого подключим нужный файл если есть
			
			if (isset($this->data_def['uri_segment'][1])) // если есть первый сегмент
				$fn = APPPATH . 'controllers/' . $this->data_def['uri_segment'][1] . EXT;
			else $fn = false;
			
			if ( $fn !== false and file_exists($fn) ) 
				require($fn);
			elseif ($fn !== false) 
			{
				// если в конфиге стоит mso_permalink_no_slug == "no", то ничего не делаем - отдаем 404-страницу
				if ($this->config->item('mso_permalink_no_slug') !== "no")
				{
					# проверим короткую ссылку - может быть это slug из page или category 
					# если это так, то выставить тип вручную
					
					$slug = $this->data_def['uri_segment'][1]; // первый сегмент

					$this->db->select('page_id');
					$this->db->where(array('page_slug'=>$slug));
					$this->db->or_where(array('page_id'=>$slug));
					$this->db->limit('1');
					
					$query = $this->db->get('page');
					if ($query->num_rows() > 0) // есть страница
					{
						# добавим недостающий сегмент в uri_segment
						array_unshift($this->data_def['uri_segment'], 'page');
						
						// в этом массиве индексы начинаются с 1, а не 0 переделываем
						$out = array();

						foreach ($this->data_def['uri_segment'] as $key => $val)
								$out[$key + 1] = $val;

						$this->data_def['uri_segment'] = $out;
						
						$this->_view_i('page');
					}
					else 
					{
						// теперь тоже самое, только с рубрикой
						$this->db->select('category_id');
						$this->db->where(array('category_slug'=>$slug));
						$query = $this->db->get('category');
						if ($query->num_rows() > 0) // есть рубрика
						{
							array_unshift($this->data_def['uri_segment'], 'category');
							$out = array();
							foreach ($this->data_def['uri_segment'] as $key => $val)
									$out[$key + 1] = $val;

							$this->data_def['uri_segment'] = $out;
							$this->_view_i('category');
						}
						else 
						{
							$this->_view_i('page_404');
						}
					}
				}
				else $this->_view_i('page_404');
			}
			else $this->_view_i('home'); // отсутствие первого сегмента подразумевает, что это home
		}
		
		function index()
		{
			global $mso_install;
			
			if ($mso_install == false)
			{
				$CI = & get_instance();	
				if ( !$CI->db->table_exists('options')) return $this->install();
			}
			
			$this->_view_i('home');
		}
		

		function install()
		{
			global $MSO, $mso_install;
			
			
			if ($mso_install == true) 
			{
				$this->_view_i('home');
				return;
			}
			
			$CI = & get_instance();
			if ($CI->db->table_exists('options')) // echo 'уже есть';
			{
				$this->_view_i('home');
				return;
			}
			
			$css = $CI->config->config['base_url'] . APPPATH . 'views/install/install.css';
			
			if ( ( count($this->data_def['uri_segment']) > 0 ) and 
				 ( $this->data_def['uri_segment'][count($this->data_def['uri_segment'])] == '2' )
				)
				$step = 2;
			elseif ( ( count($this->data_def['uri_segment']) > 0 ) and 
				 ( $this->data_def['uri_segment'][count($this->data_def['uri_segment'])] == '3' )
				)
				$step = 3;
			else $step = 1;

			$data = array('type'=>'install', 'url_css'=>$css, 'step'=>$step);
			$MSO->data = array_merge($this->data_def, $data);
			$this->load->view('install/install', $MSO->data);
		}
		
	}

} // if (!class_exists('Maxsite'))

# end if