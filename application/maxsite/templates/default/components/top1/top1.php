<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/
*/

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option('top1_rules_output', getinfo('template'), '')))
{
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

$logo = trim(mso_get_option('top1_header_logo', getinfo('template'), getinfo('template_url') . 'assets/images/logos/logo01.png'));

$logo_width = (int) mso_get_option('top1_header_logo_width', getinfo('template'), 0);
$logo_height = (int) mso_get_option('top1_header_logo_height', getinfo('template'), 0);
$logo_type_resize = mso_get_option('top1_header_logo_type_resize', getinfo('template'), 'resize_full_crop_center');

$logo_attr = mso_get_option('top1_header_logo_attr', getinfo('template'), '');
$logo_attr = $logo_attr ? ' ' . $logo_attr : '';

// задан размер по ширине и высоте, значит пробуем кропнуть указанное изображение и получить новое
if ($logo_width or $logo_height)
{
	require_once(getinfo('shared_dir') . 'stock/thumb/thumb.php');
	
	if ($new_image = thumb_generate($logo, $logo_width, $logo_height, false, $logo_type_resize, false, 'mini', '-' . $logo_width . '-' . $logo_height . '-' . $logo_type_resize))
	{
		$logo = $new_image;
	}
}

if ($logo) $logo = '<img src="' . $logo . '" alt="' . getinfo('name_site') . '" title="' . getinfo('name_site') . '"' . $logo_attr . '>';

if (!is_type('home')) $logo = '<a href="' . getinfo('siteurl') . '">' . $logo . '</a>';

$top1_block = mso_get_option('top1_block', getinfo('template'), '');

?>
<div class="layout-center-wrap bg-red"><div class="layout-wrap flex flex-wrap-phone">
	<nav class="flex-grow5 w100-tablet"><ul class="menu-simple t-center-phone t-white t80 links-no-color upper">
		<?php
			if ($menu = mso_get_option('menu2', 'templates', '/ | Главная ~ about | О сайте')) 
				echo mso_menu_build($menu, 'selected', false);
		?>
	</ul></nav>

	<div class="flex-grow1 t-right t-white t120 links-no-color t-center-phone t-nowrap">
	<?php
		if ($fn = mso_fe('components/_social/_social.php')) require($fn);
	?>
	</div>
</div></div>

<div class="layout-center-wrap bg-white"><div class="layout-wrap pad5-tb">
	<?php
	if ($logo) 
	{ 
		echo '<div class="logo-block flex flex-wrap flex-vcenter pad10"><div class="w100-max">' .  $logo . '</div>';
		
		if ($top1_block) 
		{
			echo '<div class="flex-grow3">';
			eval(mso_tmpl_prepare($top1_block));
			echo '</div>';
		}
		echo '</div>';
	}
	else
	{
		echo '<div class="logo-block">';
		if ($top1_block) eval(mso_tmpl_prepare($top1_block));
		echo '</div>';
	}

	?>
</div></div>

<div class="layout-center-wrap bg-color5 menuToFixed"><div class="layout-wrap">
	<?php if ($fn = mso_fe('components/_menu/_menu.php')) require($fn); ?>
</div></div>

<div class="layout-center-wrap bg-gray200"><div class="layout-wrap pad10-tb pad20-rl flex flex-vcenter">
	
	<div class="w70 mar10-tb t-gray700 links-no-color t90">
	<?php
		if ($fn = mso_fe('components/_breadcrumbs/_breadcrumbs.php')) require($fn);
	?>
	</div>
	
	<div class="flex-grow2 t-right t-nowrap">
		<form name="f_search" class="f_search" method="get">
			<input class="my-search" type="search" name="s" id="sss" placeholder="<?= tf('Поиск...') ?>"><label class="label-search i-search icon-square bg-gray700 t-gray200 cursor-pointer" for="sss"></label>
		</form>
		<script> window.search_url = "<?= getinfo('siteurl') ?>search/"; </script>
	</div>

</div></div>
