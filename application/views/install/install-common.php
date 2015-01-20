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
	
	
	if ($arg['demoposts']) 
	{
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

function mso_add_htaccess($arg = array())
{
    $htaccess_path = FCPATH . '/.htaccess';

    if (!empty($arg['subdir'])) {
        $subdir = '/' . $arg['subdir'] . '/';
    } else {
        $subdir = '/';
    }

    if ($arg['autoredirect']) {
        $autoredirect = 'RewriteCond %{HTTP_HOST} ^www\.(.*) [NC]
RewriteRule ^(.*)$ http://%1%{REQUEST_URI} [R=301,L]';
    } else {
        $autoredirect = '';
    }

    $new = 'Options +FollowSymLinks
Options -Indexes
DirectoryIndex index.php index.html
AddDefaultCharset UTF-8

#php_flag register_globals off
#php_value memory_limit 16M

<IfModule mod_rewrite.c>
RewriteEngine on
RewriteBase ' . $subdir . '
RewriteCond $1 !^(index\.php|uploads|robots\.txt|favicon\.ico)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ ' . $subdir . 'index.php/$1 [L,QSA]
' . $autoredirect . '
</IfModule>';

    $handle = fopen($htaccess_path,'w+');
    @chmod($htaccess_path,0644);
    if(is_writable($htaccess_path)) {
        if(fwrite($handle,$new)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function mso_add_robots()
{
    $robots_path = FCPATH . '/robots.txt';

    $new = 'User-agent: *
Disallow: /system
Disallow: /admin
Disallow: /login
Disallow: /logout
Disallow: /registration
Disallow: /search
Disallow: /users/*/edit
Disallow: /users/*/lost
Disallow: /password-recovery
Disallow: /ajax
Disallow: /ajax/*
Disallow: /require-maxsite
Disallow: /require-maxsite/*
Sitemap: http://'. $_SERVER['HTTP_HOST']. '/sitemap.xml

Host: '. $_SERVER['HTTP_HOST'].'
';

    $handle = fopen($robots_path,'w+');
    @chmod($robots_path,0777);
    if(is_writable($robots_path)) {
        if(fwrite($handle,$new)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function mso_add_sitemap()
{
    $sitemap_path = FCPATH . '/sitemap.xml';

    $new = '';

    $handle = fopen($sitemap_path,'w+');
    @chmod($sitemap_path,0777);
    if(is_writable($sitemap_path)) {
        if(fwrite($handle,$new)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function mso_add_db_setting($arg = array())
{
    $db_config_path = FCPATH . '/' . APPPATH . 'config/database.php';

    $database_file = file_get_contents($db_config_path);

    $new  = str_replace("%HOSTNAME%",$arg['hostname'],$database_file);
    $new  = str_replace("%USERNAME%",$arg['username'],$new);
    $new  = str_replace("%PASSWORD%",$arg['password'],$new);
    $new  = str_replace("%DATABASE%",$arg['database'],$new);

    $handle = fopen($db_config_path,'w+');
    @chmod($db_config_path,0644);
    if(is_writable($db_config_path)) {
        if(fwrite($handle,$new)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function mso_add_secret_key($secret_name)
{
    $secret_config_path = FCPATH . '/' . APPPATH . 'maxsite/mso_config.php';
    if (!file_exists($secret_config_path)) {
        if (file_exists($secret_config_path . '-distr')) {
            rename($secret_config_path . '-distr', $secret_config_path);
        }
        else exit;
    }

    $secret_file = file_get_contents($secret_config_path);

    $new  = str_replace("%SECRET_KEY%",$secret_name,$secret_file);

    $handle = fopen($secret_config_path,'w+');
    @chmod($secret_config_path,0644);
    if(is_writable($secret_config_path)) {
        if(fwrite($handle,$new)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function mso_install_success()
{
    $config_path = FCPATH . '/' . APPPATH . 'maxsite/mso_config.php';
    $secret_file = file_get_contents($config_path);

    $new  = str_replace('$mso_install = false;','$mso_install = true;',$secret_file);

    $handle = fopen($config_path,'w+');
    @chmod($config_path,0644);
    if(is_writable($config_path)) {
        if(fwrite($handle,$new)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

# end file