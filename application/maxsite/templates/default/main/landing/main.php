<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

global $CONTENT_OUT;

if ($fn = mso_fe('main/blocks/_start.php')) require $fn;

if ($fn = mso_fe('custom/head-section.php'))
	require $fn; // подключение HEAD из файла
else
	my_default_head_section(); // подключение через функцию

echo '<body' . ((mso_get_val('body_class')) ? ' class="' . mso_get_val('body_class') . '"' : '') . '>';
echo $CONTENT_OUT;

if ($fn = mso_fe('main/blocks/body-end.php')) require $fn;

?></body></html><?php if ($fn = mso_fe('main/blocks/_end.php')) require $fn; ?>