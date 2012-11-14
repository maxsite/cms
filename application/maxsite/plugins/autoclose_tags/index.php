<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function autoclose_tags_autoload($args = array())
{
	mso_hook_add( 'content_content', 'autoclose_tags_custom');
}


function autoclose_tags_custom($content = '')
{
	if (function_exists('tidy_repair_string'))
		return tidy_repair_string(
									$content,
									array(
											'clean' => true,
											'drop-font-tags' => true,
											'drop-proprietary-attributes' => true,
											'enclose-text' => true
										),
									'utf8');

	preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $content, $result);
	$openedtags = $result[1];

	
	preg_match_all("#</([a-z]+)>#iU", $content, $result);
	$closedtags = $result[1];
	$len_opened = count($openedtags);

	if(count($closedtags) == $len_opened)
	{
		return $content;
	}

	$openedtags = array_reverse($openedtags);
	
	for ($i=0; $i < $len_opened; $i++) 
	{
		if (!in_array($openedtags[$i], $closedtags)) 
		{
			if (!in_array($openedtags[$i], array('img', 'br', 'hr', 'input', 'col', 'meta', 'link')))
				$content .= '</' . $openedtags[$i] . '>';
		} 
		else 
		{
			unset($closedtags[array_search($openedtags[$i],$closedtags)]);
		}
	}

	return $content;
}

# end file