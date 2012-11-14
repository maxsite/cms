<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# основной файл html-структуры

# секция HEAD
if ($fn = mso_fe('custom/head-section.php')) require($fn); // подключение HEAD из файла
elseif (function_exists('mso_default_head_section')) mso_default_head_section(); // подключение через функцию

?>

<body<?= (mso_get_val('body_class')) ? ' class="' . mso_get_val('body_class') . '"' : ''; ?>>
<!-- end header -->
<?php 
	mso_hook('body_start');
	if (function_exists('ushka')) echo ushka('body_start');
	if ($fn = mso_fe('custom/body-start.php')) require($fn);
?>

<div class="all">
	<div class="all-wrap">
		<div class="section header-main">

			<?php if (function_exists('ushka')) echo ushka('header-pre'); ?>

			<div class="header">
				<div class="header-wrap">
				<?php 
					if ($fn = mso_fe('custom/header-start.php')) require($fn);
				
					if (function_exists('ushka')) echo ushka('header-start');
					
					if ($fn = mso_fe('custom/header_components.php')) require($fn);
					else
					{
						if ($fn = get_component_fn('default_header_component1', 'logo-links.php')) require($fn);
						if ($fn = get_component_fn('default_header_component2', 'menu.php')) require($fn);
						if ($fn = get_component_fn('default_header_component3', 'image-slider.php')) require($fn);
						if ($fn = get_component_fn('default_header_component4')) require($fn);
						if ($fn = get_component_fn('default_header_component5')) require($fn);
					}
					
					if (function_exists('ushka')) echo ushka('header-end');
					
					if ($fn = mso_fe('custom/header-end.php')) require($fn);

				?>
				</div><!-- div class="header-wrap" -->
			</div><!-- div class="header" -->

			<?php if (function_exists('ushka')) echo ushka('header-out'); ?>
			
			<div class="section article main">
				<div class="main-wrap">
					<?php 
						if ($fn = mso_fe('custom/content-start.php')) require($fn);
						if (function_exists('ushka')) echo ushka('content-start');
					?>
					
					<div class="content">
						<div class="content-wrap">
						<?php 
					
							if (function_exists('ushka')) echo ushka('main-out-start');
							if ($fn = mso_fe('custom/main-out-start.php')) require($fn);
							
							if ($fn = mso_fe('custom/main-out.php')) require($fn);
							else
							{ 
								global $MAIN_OUT; 
								echo $MAIN_OUT; 
							}
							
							if (function_exists('ushka')) echo ushka('main-out-end');
							if ($fn = mso_fe('custom/main-out-end.php')) require($fn);
						?>
						</div><!-- div class="content-wrap" -->
					</div><!-- div class="content" -->
					
					<?php
						if ($fn = mso_fe('custom/sidebars.php')) require($fn);
						else
						{
							echo '<div class="aside sidebar sidebar1"><div class="sidebar1-wrap">';
							mso_show_sidebar('1');
							echo '</div><!-- div class="sidebar1-wrap" --></div><!-- div class="aside sidebar sidebar1" -->';
						}
					?>

					<div class="clearfix"></div>
				</div><!-- div class="main-wrap" -->
			</div><!-- div class="section article main" -->
		</div><!-- div class="section header-main" -->

		<div class="footer-do-separation"></div>
		
		<?php if (function_exists('ushka')) echo ushka('footer-pre'); ?>
		
		<div class="footer">
			<div class="footer-wrap">
			<?php 
				if ($fn = mso_fe('custom/footer-start.php')) require($fn);
				
				if (function_exists('ushka')) echo ushka('footer-start');
				
				if ($fn = mso_fe('custom/footer_components.php')) require($fn);
				else
				{
					if ($fn = get_component_fn('default_footer_component1', 'footer-copyright.php')) require($fn);
					if ($fn = get_component_fn('default_footer_component2', 'footer-statistic.php')) require($fn);
					if ($fn = get_component_fn('default_footer_component3')) require($fn);
					if ($fn = get_component_fn('default_footer_component4')) require($fn);
					if ($fn = get_component_fn('default_footer_component5')) require($fn); 
				}
				
				if (function_exists('ushka')) echo ushka('footer-end');
				
				if ($fn = mso_fe('custom/footer-end.php')) require($fn);
			?>
			</div><!-- div class="footer-wrap" -->
		</div><!-- div class="footer" -->
	</div><!-- div class="all-wrap" -->
</div><!-- div class="all" -->

<?php if ($fn = mso_fe('custom/body-end.php')) require($fn); ?>
			
<?php 
	if (function_exists('ushka')) 
	{
		echo ushka('google_analytics'); 
		echo ushka('body_end');
	} 
	
	mso_hook('body_end'); 
?>
</body></html>