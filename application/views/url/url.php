<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	# http://localhost/codeigniter/url/maxsite.org
	
	global $MSO;
	
	if ( count($MSO->data['uri_segment']) > 1 )
	{
		
		$url = strip_tags($MSO->data['uri_segment']['2']);
		$strip = array('%0d', '%0a');
		$url = 'http://' . str_replace($strip, '', $url);
		
		if ( preg_match('#(http?|ftp)://\S+[^\s.,>)\];\'\"!?]#i', $url) )
		{
			header("Location: $url"); 
		}
	}
	
	exit();
	
?>