<?php
#	Landing Page Framework (LPF) | (c) MAX — http://lpf.maxsite.com.ua/
#	Putin Huilo! Crimea this Ukraine!

	define('BASEPATH', dirname(realpath(__FILE__)) . '/');
	
	if (file_exists(BASEPATH . 'environment/environment.php')) 
		require(BASEPATH . 'environment/environment.php');
	else 
		define('ENGINE_DIR',  BASEPATH . 'engine/');
	
	require_once(ENGINE_DIR . 'engine.php');
	
	if ($fn = mso_fe(BASEPATH . 'environment/config.php')) require($fn);
	init();
	if ($fn = mso_fe(BASEPATH . 'environment/my.php')) require($fn);
	if ($fn = mso_fe(CURRENT_PAGE_DIR . 'variables.php')) require($fn);
	if ($fn = mso_fe(CURRENT_PAGE_DIR . 'functions.php')) require($fn);
	
	if ($VAR['no_output_only_file'] and $fn = mso_fe(CURRENT_PAGE_DIR . $VAR['no_output_only_file'])) 
	{ 
		require($fn);
		exit;
	}
	
	if ($VAR['generate_static_page']) ob_start();
	if ($VAR['before_file'] and $fn = mso_fe($VAR['before_file'])) require($fn);

?><!DOCTYPE HTML>
<html<?= ($VAR['html_attr']) ? ' ' . $VAR['html_attr'] : '' ?>><head>
<meta charset="UTF-8">
<title><?= $TITLE ?></title>
<?php 
	mso_meta();
	mso_head();
	if ($fn = mso_fe($VAR['head_file'])) require($fn);
?>
</head>
<body<?= ($VAR['body_attr']) ? ' ' . $VAR['body_attr'] : '' ?>>
<?php
	if ($fn = mso_fe($VAR['start_file'])) require($fn);
	mso_output_text();
	if ($fn = mso_fe($VAR['end_file'])) require($fn);
	if ($VAR['after_file'] and $fn = mso_fe($VAR['after_file'])) require($fn);
	mso_stat_out();
	if ($fn = $VAR['generate_static_page']) file_put_contents($fn, str_replace(BASE_URL, $VAR['generate_static_page_base_url'], ob_get_flush()));

# end of file