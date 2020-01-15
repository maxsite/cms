<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

/**
 * загружаем включенные плагины
 *
 * @return void
 */
function mso_autoload_plugins()
{
	global $MSO;

	// функция mso_autoload_custom может быть в mso_config.php
	if (function_exists('mso_autoload_custom')) mso_autoload_custom();

	$d = mso_get_option('active_plugins', 'general');

	if (!$d) $d = $MSO->active_plugins;

	foreach ($d as $load) {
		mso_plugin_load($load);
	}
}

/**
 * подключение плагина
 *
 * @param  mixed $plugin
 *
 * @return void
 */
function mso_plugin_load($plugin = '')
{
	global $MSO;

	$fn_plugin = $MSO->config['plugins_dir'] . $plugin . '/index.php';

	if (!file_exists($fn_plugin)) {
		return false;
	} else {
		//_mso_profiler_start($plugin);
		require_once $fn_plugin;

		$auto_load = $plugin . '_autoload';
		
		if (function_exists($auto_load)) $auto_load();

		//_mso_profiler_end($plugin);

		// добавим плагин в список активных
		$MSO->active_plugins[] = $plugin;
		$MSO->active_plugins = array_unique($MSO->active_plugins);
		sort($MSO->active_plugins);

		return true;
	}
}

/**
 * подключение admin-плагина - выполняется только при входе в админку
 *
 * @param  mixed $plugin
 *
 * @return void
 */
function mso_admin_plugin_load($plugin = '')
{
	global $MSO;

	$fn_plugin = $MSO->config['admin_plugins_dir'] . $plugin . '/index.php';

	if (!file_exists($fn_plugin)) {
		return false;
	} else {
		require_once $fn_plugin;
		$auto_load = $plugin . '_autoload';
		
		if (function_exists($auto_load)) $auto_load();

		return true;
	}
}

# end of file
