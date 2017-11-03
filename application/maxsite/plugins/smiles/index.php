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
	
	// https://github.com/maxsite/cms/pull/130/commits/a092395d0564f15e0964b8e37ca5ff4f380820e7
	
	$check_tag_explode = preg_split('~(<pre.+?</pre>)~s', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
	
	$content = '';
	
	foreach ($check_tag_explode as $check_tag)
	{
		if (!preg_match('/<pre/i', $check_tag))
		{
			$check_tag = parse_smileys($check_tag, getinfo('uploads_url') . 'smiles/');
		}
		
		$content .= $check_tag;
	}
	
	// $content = parse_smileys($content, getinfo('uploads_url') . 'smiles/');

	return $content;
}

# end of file