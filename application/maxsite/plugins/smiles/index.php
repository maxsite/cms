<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function smiles_autoload($args = array())
{
	mso_hook_add( 'content', 'smiles_custom'); # хук на тексты
	mso_hook_add( 'comments_content', 'smiles_custom'); # хук на тексты
}


# функции плагина
function smiles_custom($content)
{
	$CI = & get_instance();	
	$CI->load->helper('smiley');
	
	$content = parse_smileys($content, getinfo('uploads_url') . 'smiles/');

	return $content;
	
}

# end file