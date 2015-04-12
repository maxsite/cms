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
	<div class=""><?= mso_get_option('top1_block', 'templates', '') ?></div>
</div>

<div class="menu-search flex flex-vcenter mar20-rl bg-gray800 flex-wrap-tablet">
	
	<div class="w100-tablet"><?php if ($fn = mso_fe('components/_menu/_menu.php')) require($fn); ?></div>
	
	<div class="fs">
		<form class="f_search" name="f_search" method="get" onsubmit="location.href='<?= getinfo('siteurl') ?>search/' + encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;">
			<input class="my-search" type="search" name="s" id="sss" placeholder="Поиск..."><label class="label-search i-search icon-square bg-gray700 t-gray300 cursor-pointer" for="sss"></label>
		</form>
		<style>
			.f_search .my-search {
				display: inline-block;
				padding-left: 0;
				padding-right: 0;
				width: 0;
			}

			.slided-input .my-search {
				padding-left: 6px;
				padding-right: 6px;
				width: 180px;
			}

			@media (max-width: 480px) {
				.fs {
					width: 100%
				}

				.f_search {
					display: flex;
					justify-content: flex-end;
				}

				.slided-input .my-search {
					box-sizing: border-box;
					width: calc(100% - 48px);
				}
			}
		</style>
		<script>
			$(document).on("click", function(e) {
				var searchForm = $(".f_search");
				var searchInput = $(".my-search");

				if ( $(e.target).hasClass("label-search") ) {
					if ( !searchInput.hasClass("visible") ) {
						searchForm.addClass("slided-input");
						searchInput.addClass("visible");
					}
					else {
						searchForm.removeClass("slided-input");
						searchInput.removeClass("visible");
					}
				}
				else if ( !$(e.target).hasClass("label-search") && !$(e.target).hasClass("my-search") ) {
					searchForm.removeClass("slided-input");
					searchInput.removeClass("visible");
				}
			});
		</script>
	</div>
</div>
