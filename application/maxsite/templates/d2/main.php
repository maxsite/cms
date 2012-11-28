<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# основной файл html-структуры

# секция HEAD
if ($fn = mso_fe('custom/head-section.php')) require($fn); // подключение HEAD из файла
	else mso_default_head_section(); // подключение через функцию

?>

<body<?= (mso_get_val('body_class')) ? ' class="' . mso_get_val('body_class') . '"' : ''; ?>>
<!-- end header -->
<?php 
	mso_hook('body_start');
	if (function_exists('ushka')) echo ushka('body_start');
	if ($fn = mso_fe('custom/body-start.php')) require($fn);
?>

<div class="all"><div class="all-wrap">
	<div class="header-main">

		<?php 
			if (function_exists('ushka')) echo ushka('header-pre'); 
			if ($fn = mso_fe('custom/header-pre.php')) require($fn);
		?>

		<div class="header"><div class="header-wrap">
			<?php 
				if (function_exists('ushka')) echo ushka('header-start');
				if ($fn = mso_fe('custom/header-start.php')) require($fn);
				
				if ($fn = mso_fe('custom/header_components.php')) require($fn);
				else
				{
					if ($fn = get_component_fn('header_component1', 'menu')) require($fn);
					if ($fn = get_component_fn('header_component2')) require($fn);
					if ($fn = get_component_fn('header_component3')) require($fn);
					if ($fn = get_component_fn('header_component4')) require($fn);
					if ($fn = get_component_fn('header_component5')) require($fn);
				}

				if (function_exists('ushka')) echo ushka('header-end');
				if ($fn = mso_fe('custom/header-end.php')) require($fn);

			?>
		</div></div><!-- /div.header-wrap /div.header -->

		<?php if (function_exists('ushka')) echo ushka('header-out'); ?>

		<div class="main"><div class="main-wrap">
			<?php 
				if (function_exists('ushka')) echo ushka('main-start');
				if ($fn = mso_fe('custom/main-start.php')) require($fn);
			?>

			<div class="content"><div class="content-wrap">
				<?php 
			
					if (function_exists('ushka')) echo ushka('content-start');
					if ($fn = mso_fe('custom/content-start.php')) require($fn);
					
					if ($fn = mso_fe('custom/content-out.php')) require($fn);
					else
					{ 
						global $CONTENT_OUT; 
						echo $CONTENT_OUT; 
					}
					
					if (function_exists('ushka')) echo ushka('content-end');
					if ($fn = mso_fe('custom/content-end.php')) require($fn);
				?>
			</div></div><!-- /div.content-wrap /div.content -->

			<?php
				if ($fn = mso_fe('custom/sidebars.php')) require($fn);
				else
				{
					echo NR . '<div class="sidebar sidebar1"><div class="sidebar1-wrap">';
					mso_show_sidebar('1');
					echo NR . '</div></div><!-- /div.sidebar1-wrap /div.sidebar sidebar1 -->';
				}

				
				if (function_exists('ushka')) echo ushka('main-end');
				if ($fn = mso_fe('custom/main-end.php')) require($fn);
			?>

			<div class="clearfix"></div>
		</div></div><!-- /div.main-wrap /div.main -->
	</div><!-- /div.header-main -->

	<div class="footer-do-separation"></div>
	<?php if (function_exists('ushka')) echo ushka('footer-pre'); ?>

	<div class="footer"><div class="footer-wrap">
		<?php 
			if (function_exists('ushka')) echo ushka('footer-start');
			if ($fn = mso_fe('custom/footer-start.php')) require($fn);
			
			if ($fn = mso_fe('custom/footer_components.php')) require($fn);
			else
			{
				if ($fn = get_component_fn('footer_component1', 'footer-copy-stat')) require($fn);
				if ($fn = get_component_fn('footer_component2')) require($fn);
				if ($fn = get_component_fn('footer_component3')) require($fn);
				if ($fn = get_component_fn('footer_component4')) require($fn);
				if ($fn = get_component_fn('footer_component5')) require($fn); 
			}
			
			if (function_exists('ushka')) echo ushka('footer-end');
			if ($fn = mso_fe('custom/footer-end.php')) require($fn);
		?>
	</div></div><!-- /div.footer-wrap /div.footer -->
	
</div></div><!-- /div.all-wrap /div.all -->

<?php 
	if ($fn = mso_fe('custom/body-end.php')) require($fn); 

	if (function_exists('ushka')) 
	{
		echo ushka('google_analytics'); 
		echo ushka('body_end');
	} 
	
	mso_hook('body_end'); 
?>
</body></html>