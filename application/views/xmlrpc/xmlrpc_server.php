<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	# получение данных по Xmlrpc
	
	# !!! Не работает !!!
	
	# функция логгинга
	function _log($msg, $clip = true) 
	{

		$_logging = false; // разрешить вести лог

		if ($_logging) 
		{
			$fn = realpath(dirname(FCPATH)) . '/' . APPPATH . '/views/xmlrpc/_log.txt';
			$fp = fopen( $fn, "a+");
			$date = gmdate("Y-m-d H:i:s ");
			
			if ( !is_scalar($msg) )
			{
				ob_start();
				print_r($msg);
				$msg = ob_get_contents();
				ob_end_clean();
			}
			
			if ( $clip ) $msg = substr($msg, 0, 1200);
			fwrite($fp,  '====================' . "\n" 
						. $date . '  ip: ' . $_SERVER['REMOTE_ADDR'] . "\n" 
						. $msg . "\n\n");
			fclose($fp);
		}
		return true;
	}



# !!! Имя пользователя и пароль - проверяется всегда !!!

class Xmlrpc_server extends Controller 
{

	function Xmlrpc_server ()
	{
		parent::Controller();
		
		// global $CI;
		// pr($CI);
		$CI = & get_instance();
		
		// $CI->load->database();
		// $CI->load->library('session');
		$CI->load->library('xmlrpc');
		$CI->load->library('xmlrpcs');
		
		_log('1');
	}
	
	function index ()
	{
		// $CI = & get_instance();
		//$CI->load->library('session');
		// $CI->load->library('xmlrpc');
		// $CI->load->library('xmlrpcs');
		// global $CI;
		$CI = & get_instance();
		
		$conf['functions']['Hello'] = array('function' => 'Xmlrpc_server.hello');

		_log('2');
		$CI->xmlrpcs->initialize($conf);
		_log('3');
		$CI->xmlrpcs->serve();
		_log('4');
	}
	
	
	function hello($request)
	{
		$CI = & get_instance();
		
		// $parameters = $request->output_parameters();
		$response = array(
							array(
								'result' => '1',
								'description'=>'Hello'
							), 'struct');

		return $CI->xmlrpc->send_response($response);
	}
}

// global $mso_Xmlrpc_server;

$mso_Xmlrpc_server = new Xmlrpc_server();
$mso_Xmlrpc_server->index();

?>