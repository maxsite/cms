<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 
function _mso_install_sqlfile($sql_file)
{
	$file = fopen($sql_file, "r");
	$sql = fread($file, filesize($sql_file));
	fclose($file);
	return explode('###', $sql);
}

function mso_install_newsite($arg = array())
{
	
	$CI = & get_instance();	
	$prefix = $CI->db->dbprefix;
	
	$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
	$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
	$charset_collate = ' DEFAULT CHARACTER SET ' . $charset .' COLLATE '. $collate;
	
	$sql_file = FCPATH . '/' . APPPATH . 'views/install/model.sql';
	$sql = _mso_install_sqlfile($sql_file);
	
	foreach($sql as $s)
	{
		$s = trim($s);
		if (!empty($s))
		{
			$s = str_replace('_PREFIX_', $prefix, $s);
			$s = str_replace('_CHARSETCOLLATE_', $charset_collate, $s);
			$s = str_replace('_USERNAME_', $arg['username'] , $s);
			$s = str_replace('_USERPASSWORD_', $arg['userpassword'], $s);
			$s = str_replace('_USEREMAIL_', $arg['useremail'], $s);
			$s = str_replace('_NAMESITE_', $arg['namesite'], $s);
			$s = str_replace('_IP_', $arg['ip'], $s);
			$CI->db->query($s);
		}
	}
	
	mso_add_option('admin_nick', $arg['username'], 'general');
	mso_add_option('name_site', $arg['namesite'], 'general');
	mso_add_option('template', 'default', 'general');
	mso_add_option('description', t('Очередной сайт на Maxsite CMS', 'install'), 'general');
	mso_add_option('keywords', '', 'general');
	
	
	if ($arg['demoposts']) {
		$sql_file = FCPATH . '/' . APPPATH . 'views/install/demo.sql';
		$sql = _mso_install_sqlfile($sql_file);
		foreach($sql as $s)
		{
			$s = trim($s);
			if (!empty($s))
			{
				$s = str_replace('_PREFIX_', $prefix, $s);
				$s = str_replace('_CHARSETCOLLATE_', $charset_collate, $s);
				$CI->db->query($s);
			}
		}
	}
	
	$res = '<p class="res"><strong>' . t('Логин:', 'install') . '</strong> ' . $arg['username'] . '</p>';
	$res .= '<p class="res"><strong>' . t('Пароль:', 'install') . '</strong> ' . $arg['userpassword_orig'] . '</p>';
	$res .= '<p class="res"><strong>' . t('Email:', 'install') . '</strong> ' . $arg['useremail'] . '</p>';

	return $res;
}

?>