<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

class Maxsite_lib 
{
	public $version = '0.78';
	public $config = array();
	public $data = array();
	public $hooks = array();
	public $active_plugins = array();
	public $sidebars = array();
	public $widgets = array();
	public $title = '';
	public $description = '';
	public $keywords = '';
	public $language = false;
	public $current_lang_dir = false;
	public $cache = array();
	
	
	public function __construct()
	{
		$CI =& get_instance();
		
		$this->config['site_url'] = $CI->config->config['base_url'];
		$this->config['application_url'] = $this->config['site_url'] . APPPATH;
		$this->config['base_url'] = $this->config['site_url'] . APPPATH . 'maxsite/';
		$this->config['common_url'] = $this->config['base_url'] . 'common/';
		$this->config['templates_url'] = $this->config['base_url'] . 'templates/';
		$this->config['plugins_url'] = $this->config['base_url'] . 'plugins/';
		$this->config['admin_plugins_url'] = $this->config['base_url'] . 'admin/plugins/';
		$this->config['uploads_url'] = $this->config['site_url'] . 'uploads/';
		$this->config['admin_url'] = $this->config['base_url'] . 'admin/';
		$this->config['site_admin_url'] = $this->config['site_url'] . 'admin/';
		
		$this->config['base_dir'] = FCPATH . APPPATH . 'maxsite/';
		$this->config['application_dir'] = FCPATH . APPPATH;
		$this->config['uploads_dir'] = FCPATH . 'uploads/';
		
		$this->config['cache_dir'] = $CI->config->config['cache_path'];
		if (!$this->config['cache_dir'])
			$this->config['cache_dir'] = FCPATH . 'application/cache/';		
		
		$this->config['common_dir'] = $this->config['base_dir'] . 'common/';
		$this->config['templates_dir'] = $this->config['base_dir'] . 'templates/';
		$this->config['plugins_dir'] = $this->config['base_dir'] . 'plugins/';
		$this->config['admin_plugins_dir'] = $this->config['base_dir'] . 'admin/plugins/';
		$this->config['admin_dir'] = $this->config['base_dir'] . 'admin/';
		$this->config['config_file'] = $this->config['base_dir'] . 'mso_config.php';
		
		$this->config['cache_time'] = 86400; // в секундах = 24 часа
		$this->config['template'] = 'default';
		
		$this->config['secret_key'] = $this->config['site_url'];
		$this->config['remote_key'] = '0'; // ключ удаленного постинга
		
		# контроль. не использовать в проектах!
		# константы определяет CodeIgniter
		# меняются от версии
		$this->config['FCPATH'] = FCPATH;	
		$this->config['EXT'] = EXT;	
		$this->config['SELF'] = SELF;	
		$this->config['BASEPATH'] = BASEPATH;	
		$this->config['APPPATH'] = APPPATH;	
			
	}
}

global $MSO;

if ( !isset($MSO) ) $MSO = new Maxsite_lib();

require_once($MSO->config['common_dir'] . 'common.php');

# end file