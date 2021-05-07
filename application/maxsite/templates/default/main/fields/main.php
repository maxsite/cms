<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * 
 * Общий контейнер для шапки, контента, подвала
 * 
 */

if ($fn = mso_fe('main/blocks/_start.php')) require $fn;
if ($fn = mso_fe('main/blocks/body-start.php')) require $fn;

?>
<div class="layout-center-wrap my-main-container bg-primary400">
	<div class="layout-wrap pos-relative pad0 bg-white b-shadow-var" style="--b-shadow: 0 0px 15px 5px rgba(0, 0, 0, 0.35);">
		<?php
		if ($fn = mso_fe('main/blocks/header.php')) require $fn;
		if ($fn = mso_fe('main/blocks/header-out.php')) require $fn;
		?>
		<div class="flex flex-wrap-tablet my-container-content-sidebar">
			<div class="w9col w100-tablet">
				<div>
					<?php if ($fn = mso_fe('main/blocks/main-start.php')) require $fn; ?>

					<div class="content pad20-rl">
						<?php if ($fn = mso_fe('main/blocks/content.php')) require $fn; ?>
					</div>

					<?php if ($fn = mso_fe('main/blocks/main-end.php')) require $fn; ?>
				</div>
			</div>

			<div class="w3col w100-tablet pad20-rl">
				<div class="flex-tablet-phone flex-wrap-tablet-phone flex-jc-around-tablet-phone" id="mso_show_sidebar1"><?php mso_show_sidebar('1'); ?></div>
			</div>
		</div>
		<?php
		if ($fn = mso_fe('main/blocks/footer-pre.php')) require $fn;
		if ($fn = mso_fe('main/blocks/footer.php')) require $fn;
		?>
	</div>
</div>
<?php
if ($fn = mso_fe('main/blocks/body-end.php')) require $fn;
?>
</body>
</html><?php if ($fn = mso_fe('main/blocks/_end.php')) require $fn; ?>