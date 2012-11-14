<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


if ( $post = mso_check_post(array('data')) )
{
	$output = $post['data'];
	
	$output = trim($output);
	$output = str_replace(chr(10), "<br>", $output);
	$output = str_replace(chr(13), "", $output);
				
	$output = mso_hook('content', $output);
	$output = mso_hook('content_auto_tag', $output);
	$output = mso_hook('content_balance_tags', $output);
	$output = mso_hook('content_out', $output);
	$output = mso_hook('content_content', $output);
	
	// стили вначале подключаем базу из preview.css
	$css_link = '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'editor_markitup/preview.css" type="text/css" media="screen">';

	// теперь остальные по алгоритму default 2
	$css_link .= NT . '<link rel="stylesheet" href="'; 
		
	if (file_exists(getinfo('template_dir') . 'css/css.php')) $css_link .= getinfo('template_url') . 'css/css.php'; 
	else 
	{
		if (file_exists(getinfo('template_dir') . 'css/my_style.css')) // если есть css/my_style.css
		{
			$css_link .= getinfo('template_url') . 'css/my_style.css'; 
		}
		else
		{ 
			if (file_exists(getinfo('template_dir') . 'css/style-all-mini.css')) // если есть style-all-mini.css
			{
				$css_link .= getinfo('template_url') . 'css/style-all-mini.css'; 
			}
			elseif (file_exists(getinfo('template_dir') . 'css/style-all.css')) // нет mini, подключаем обычный файл
			{
				$css_link .= getinfo('template_url') . 'css/style-all.css'; 
			}
			else $css_link .= getinfo('templates_url') . 'default/css/style-all-mini.css'; 
		}
	}
			
	$css_link .= '" type="text/css" media="screen">';
		
		
	// подключение var_style.css
	$var_file = '';
	
	if (file_exists(getinfo('template_dir') . 'css/var_style.css')) 
		$var_file = getinfo('template') . '/css/var_style.css';	
	elseif (file_exists(getinfo('templates_dir') . 'default/css/var_style.css')) 
		$var_file = 'default/css/var_style.css';
	
	// если var_style.css нулевой длины, то не подключаем его
	if (filesize(getinfo('templates_dir') . $var_file))
		$css_link .= NT . '<link rel="stylesheet" href="' . getinfo('templates_url') . $var_file . '" type="text/css" media="screen">';	
		

	echo <<<EOF
<!DOCTYPE HTML>
<html><head>
	<meta charset="UTF-8">
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=8"><![endif]-->
	<title>Предпросмотр</title>
	{$css_link}
	<style>
		body, div.all, div.all-wrap, div.content {background: white; margin: 0; padding: 0; box-shadow: none; border-radius: 0; float: none; width: 100%;}
		div.content {width: 90%; margin: 0 auto;}
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