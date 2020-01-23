<?php if (!defined('INSTALLER')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// default values
$PV['username'] = '';
$PV['password'] = randomPassword();
$PV['email'] = '';
$PV['site_name'] = '';
$PV['demo'] = '1';

$PV['db_hostname'] = 'localhost';
$PV['db_username'] = '';
$PV['db_password'] = '';
$PV['db_database'] = '';
$PV['db_dbprefix'] = 'mso_';

checkWritable('application/cache');
checkWritable('application/cache/db');
checkWritable('uploads');

newRobots();
newHtaccess();
newSitemap();
newMsoConfig();

if (!checkSecretKey()) $showForm = false;

if (file_exists(MSODIR . 'application/config/database.php')) {
	echo '<p class="t-green bold">✔ ' . t('db is exists') . '</p>';
	echo '<p><a href="../">' . t('go site') . '</a></p>';

	$showForm = false;
}

# end of file
