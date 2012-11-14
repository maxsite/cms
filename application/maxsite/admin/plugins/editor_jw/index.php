<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// 

function editor_jw_admin_header($args = '') 
{
	global $MSO;
	
	echo NR . '<link rel="stylesheet" href="' 
			. $MSO->config['admin_plugins_url'] 
			. 'editor_jw/jw/jquery.wysiwyg.css" type="text/css" media="screen">' . NR;
	
	mso_hook('editor_controls_extra_css');
}

function editor_jw($args = array()) 
{
	global $MSO;
	
	$editor_config['url'] = $MSO->config['admin_plugins_url'] . 'editor_jw/';
	$editor_config['dir'] = $MSO->config['admin_plugins_dir'] . 'editor_jw/';
	
	// if (isset($args['content'])) $editor_config['content'] = mso_text_to_html($args['content']);
	if (isset($args['content'])) $editor_config['content'] = $args['content'];
		else $editor_config['content'] = '';
		
	if (!$editor_config['content']) $editor_config['content'] = '<br>';
	
	$editor_config['content'] = mso_hook('editor_content', $editor_config['content']);

	
	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';
		
	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
		else $editor_config['posle'] = '';	
		
	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
		else $editor_config['action'] = '';
			
	if (isset($args['height'])) $editor_config['height'] = (int) $args['action'];
	else 
	{
		$editor_options = mso_get_option('editor_options', 'admin', array());
		
		if (isset($editor_options['editor_height']))
		{
			$editor_config['height'] = (int) $editor_options['editor_height'];
		}
		else $editor_config['height'] = 400;
		
		if ($editor_config['height'] < 100) $editor_config['height'] = 400;
	}
		
	
	mso_hook_add( 'admin_head', 'editor_jw_admin_header');
	
	require($editor_config['dir'] . 'editor.php');

}

# end file