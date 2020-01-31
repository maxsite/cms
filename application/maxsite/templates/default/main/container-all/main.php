<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * HTML-структура шаблона (общий контейнер)
 */

if ($fn = mso_fe('main/blocks/_start.php')) require $fn;
if ($fn = mso_fe('main/blocks/body-start.php')) require $fn;
?>
<div class="layout-center-wrap my-all-container">
	<div class="layout-wrap">
		<?php
		if ($fn = mso_fe('main/blocks/header.php')) require $fn;
		if ($fn = mso_fe('main/blocks/header-out.php')) require $fn;
		?>
		<div class="my-main-container">
			<div class="flex flex-wrap-tablet my-container-content-sidebar">
				<div class="w66 w100-tablet">
					<div>
						<?php if ($fn = mso_fe('main/blocks/main-start.php')) require $fn; ?>

						<div class="content">
							<?php if ($fn = mso_fe('main/blocks/content.php')) require $fn; ?>
						</div>

						<?php if ($fn = mso_fe('main/blocks/main-end.php')) require $fn; ?>
					</div>
				</div>

				<div class="w30 w100-tablet mar20-l mar0-l-tablet">
					<div class="flex-tablet-phone flex-wrap-tablet-phone flex-jc-around-tablet-phone" id="mso_show_sidebar1"><?php mso_show_sidebar('1'); ?></div>
				</div>
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
</body></html><?php if ($fn = mso_fe('main/blocks/_end.php')) require $fn; ?>