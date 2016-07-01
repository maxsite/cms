<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


function editor_markitup($args = array()) 
{
	$options = mso_get_option('editor_options', 'admin', array() ); // получаем опции
	
	if (!isset($options['preview'])) $options['preview'] = 0;
	if (!isset($options['previewautorefresh'])) $options['previewautorefresh'] = 0;
	if (!isset($options['previewPosition'])) $options['previewPosition'] = 'after';
	
	if ($options['preview']) $editor_config['preview'] = 'previewInWindow: "",';
		else $editor_config['preview'] = 'previewInWindow: "width=960, height=800, resizable=yes, scrollbars=yes",';
	
	if ($options['previewautorefresh']) $editor_config['previewautorefresh'] = 'previewAutoRefresh: true,';
	else $editor_config['previewautorefresh'] = 'previewAutoRefresh: false,';
	
	if ($options['previewPosition'] == 'before') 
		$editor_config['previewPosition'] = 'previewPosition: "before",';
	else 
		$editor_config['previewPosition'] = 'previewPosition: "after",';
	
	$editor_config['url'] = getinfo('admin_url') . 'plugins/editor_markitup/';
	$editor_config['dir'] = getinfo('admin_dir') . 'plugins/editor_markitup/';

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

	# Приведение строк с <br> в первозданный вид
	$editor_config['content'] = preg_replace('"&lt;br\s?/?&gt;"i', "\n", $editor_config['content']);
	$editor_config['content'] = preg_replace('"&lt;br&gt;"i', "\n", $editor_config['content']);


	// смайлы - код из comment_smiles
	$image_url = getinfo('uploads_url').'smiles/';
	$CI = & get_instance();
	$CI->load->helper('smiley_helper');
	$smileys = _get_smiley_array();
	$used = array();
	$smiles = '';
	
	foreach ($smileys as $key => $val)
	{
		// Для того, чтобы для смайлов с одинаковыми картинками (например :-) и :))
		// показывалась только одна кнопка
		if (isset($used[$smileys[$key][0]]))
		{
		  continue;
		}
		
		$im = "<img src='" . $image_url . $smileys[$key][0] . "' title='" . $key . "'>";
		$smiles .= '{name:"' .  addcslashes($im, '"') . '", notitle: "1", replaceWith:"' . $key . '", className:"col1-0" },' . NR;
		
		$used[$smileys[$key][0]] = TRUE;
	}
	if ($smiles)
	{
		$smiles = NR . "{name:'" . t('Смайлы') . "', openWith:':-)', closeWith:'', className:'smiles', dropMenu: [" 
				. $smiles
				. ']},';
	}
	
	require($editor_config['dir'] . 'editor-bb.php');
}

# end file