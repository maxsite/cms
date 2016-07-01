<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


if ( $post = mso_check_post(array('data')) )
{
	$output = $post['data'];

	$output = trim($output);
	$output = str_replace(chr(10), "<br>", $output);
	$output = str_replace(chr(13), "", $output);
	$output = str_replace('[cut]', '', $output);
				
	$output = mso_hook('content', $output);
	$output = mso_hook('content_auto_tag', $output);
	$output = mso_hook('content_balance_tags', $output);
	$output = mso_hook('content_out', $output);
	$output = mso_hook('content_content', $output);
	
	// стили
	$css_link = '';
	// $css_link .= mso_load_style(getinfo('admin_url') . 'plugins/editor_markitup/preview.css');
	
	if (file_exists(getinfo('template_dir') . 'assets/css/style.css'))
		$css_link .= mso_load_style(getinfo('template_url') . 'assets/css/style.css');
	
	echo <<<EOF
<!DOCTYPE HTML>
<html><head>
	<meta charset="UTF-8">
	<title>Предпросмотр</title>
	{$css_link}
	<style>
		body, div.all, div.all-wrap, div.content {background: white; margin: 0; padding: 0; box-shadow: none; border-radius: 0; float: none; width: 99%;}
		div.content {width: 100%; margin: 0 auto; padding: 10px;}
	</style>
</head><body>
<div class="all">
	<div class="all-wrap">
		<div class="content">
{$output}
		</div>
	</div><!-- div class=class-wrap -->
</div><!-- div class=all -->
</body></html>
EOF;

	
}

# end of file