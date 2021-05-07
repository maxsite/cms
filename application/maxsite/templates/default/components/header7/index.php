<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// логотип меню социконки

// $is_link_logo = (!is_type('home') or !mso_current_paged() > 1);

$component = basename(dirname(__FILE__));

//mso_delete_option_mask($component . '-', getinfo('template'));

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option($component . '-rules_output', getinfo('template'), ''))) {
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');

	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

$optionsINI = mso_get_defoptions_from_ini(__DIR__ . '/options.ini');

// это лого
$logo = mso_get_option($component . '-logo', '', '', $optionsINI);
$site = mso_get_option($component . '-site', '', '', $optionsINI);
$social_class = mso_get_option($component . '-social_class', '', '', $optionsINI);
$menu = mso_get_option($component . '-menu', '', '', $optionsINI);

$logo = str_replace('[siteurl]', getinfo('siteurl'), $logo);
$logo = str_replace('[templateurl]', getinfo('template_url'), $logo);
$logo = str_replace('[template_url]', getinfo('template_url'), $logo);

$site = str_replace('[siteurl]', getinfo('siteurl'), $site);
$site = str_replace('[template_url]', getinfo('template_url'), $site);
$site = str_replace('[templateurl]', getinfo('template_url'), $site);
$site = str_replace('[name_site]', getinfo('name_site'), $site);
$site = str_replace('[description_site]', getinfo('description_site'), $site);

?>
<div class="layout-center-wrap">
	<div class="layout-wrap pad0">
		<div class="flex flex-wrap-tablet flex-vcenter pad30-t">
			<div class="w80 w100-phone flex flex-wrap-phone flex-vcenter t-center-phone">
				<?= $logo ?>
				<div class="flex-grow5 pad20-rl">
					<?= $site ?>
				</div>
			</div>

			<div class="flex-grow0 flex-order2-tablet w30-tablet w100-phone mar10-tb pad20-rl t-center-tablet">
				<?php
				mso_set_val('my_social_class', $social_class); // передаём класс в _social.php

				if ($fn = mso_fe('components/_social/_social.php')) require $fn;
				?>
			</div>
		</div>

		<div class="<?= $menu ?>">
			<?php
			// дополнительные данные для меню
			mso_set_val('menu-add-class', 'animation-zoom animation-fast mar15-rl');
			mso_set_val('menu-control', '<span class="button button1 im-bars mar5-tb w100">Меню</span>');
			mso_set_val('menu-control-class', 't-center pad5-rl');

			if ($fn = mso_fe('components/_menu/_menu.php')) require $fn;
			?>
		</div>

	</div>
</div>