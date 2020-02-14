<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function editor_trumbowyg_autoload()
{	
	mso_hook_add('editor_custom', 'editor_trumbowyg'); // хук на подключение своего редактора
}

function editor_trumbowyg($args = []) 
{
	$options = mso_get_option('editor_options', 'admin', []); // получаем опции
	
	$editor_config['url'] = getinfo('plugins_url') . 'editor_trumbowyg/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_trumbowyg/';

	if (isset($args['content'])) $editor_config['content'] = $args['content'];
	else $editor_config['content'] = '';
		
	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';
	
	if (isset($args['do_script'])) $editor_config['do_script'] = $args['do_script'];
		else $editor_config['do_script'] = '';
		
	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
		else $editor_config['posle'] = '';	
		
	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
		else $editor_config['action'] = '';
	
	if (isset($args['height'])) $editor_config['height'] = (int) $args['height'];
	else 
	{
		$editor_options = mso_get_option('editor_options', 'admin', array());
		
		if (isset($editor_options['editor_height']) and $editor_options['editor_height'] > 0)
		{
			$editor_config['height'] = (int) $editor_options['editor_height'];
			if ($editor_config['height'] < 100) $editor_config['height'] = 400;
		}
		else
		{
			$editor_config['height'] = 400;
		}
	}

	// Здесь можно произвести замены для правильного отображения контента

	// Приведение строк с <br> в первозданный вид
	// $editor_config['content'] = preg_replace('"&lt;br\s?/?&gt;"i', "\n", $editor_config['content']);
	// $editor_config['content'] = preg_replace('"&lt;br&gt;"i', "\n", $editor_config['content']);

	require($editor_config['dir'] . 'editor.php');
}

# end of file