<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/
*/

// если в опции явно указан адрес лого, то берем его
$logo = trim(mso_get_option('default_header_logo_custom', 'templates', false));

if (!$logo) $logo = getinfo('stylesheet_url') . 'assets/images/logos/' . mso_get_option('default_header_logo', 'templates', 'logo01.png');

$logo = '<img src="' . $logo . '" alt="' . getinfo('name_site') . '" title="' . getinfo('name_site') . '">';

if (!is_type('home')) $logo = '<a href="' . getinfo('siteurl') . '">' . $logo . '</a>';

?>

<div class="menu-icons flex flex-vcenter bg-gray900 pad15-rl">
	<div class="">
		<ul class="menu menu2">
		<?php
			if ($menu = mso_get_option('menu2', 'templates', '/ | Главная ~ about | О сайте')) 
				echo mso_menu_build($menu, 'selected', false);
		?>
		</ul>
	</div>
	
	<div class="t15px t-gray500 links-no-color links-hover-t-gray100">
	<?php
		if ($fn = mso_fe('components/_social/_social.php')) require($fn);
	?>
	</div>
</div>

<div class="logo-block flex flex-vcenter pad20">
	<div class=""><?= $logo ?></div>
	<div class=""><?= mso_get_option('top1_block', 'default', '') ?></div>
</div>

<div class="menu-search flex flex-vcenter mar20-rl bg-gray800 flex-wrap-tablet">
	
	<div class="w100-tablet"><?php if ($fn = mso_fe('components/_menu/_menu.php')) require($fn); ?></div>
	
	<div class="">
		<form name="f_search" class="f_search" method="get">
			<input class="my-search my-search--hidden" type="search" name="s" id="sss" placeholder="Поиск..."><label class="label-search i-search icon-square bg-gray500 t-gray200 cursor-pointer" for="sss"></label>
		</form>
		<script>
			var searchForm = $(".f_search");
			var searchInput = $(".my-search");
			var searchLabel = $(".label-search");

			searchForm.submit(function (e) {
				e.preventDefault();
				window.location.href = "<?= getinfo('siteurl') ?>search/" + encodeURIComponent(searchInput.val()).replace(/%20/g, '+');
			});

			searchLabel.click(function (e) {
				if (searchInput.val() === "") {
					searchInput.toggleClass("my-search--hidden");
				}
				else {
					e.preventDefault();
					searchForm.submit();
				}
			});

			$(document).click(function(e) {
				if ( !$(e.target).hasClass("label-search") && !$(e.target).hasClass("my-search") && (searchInput.val() === "") ) {
					searchInput.addClass("my-search--hidden");
				}
			});
		</script>
	</div>
</div>
