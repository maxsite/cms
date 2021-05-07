<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * 
 * Сайдбар 2 справа, а слева шапки + контент + подвал
 * 
 */

if ($fn = mso_fe('main/blocks/_start.php')) require $fn;
if ($fn = mso_fe('main/blocks/body-start.php')) require $fn;

?>
<style>
	@media (max-width: 768px) {
		#myNavS {
			margin-left: -300px;
			position: fixed;
			top: 0;
			bottom: 0;
			z-index: 10;
			box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.175);
		}
	}

	#myNavS::-webkit-scrollbar {
		width: 14px;
		height: 14px;
		background-color: #ccc;
	}

	#myNavS::-webkit-scrollbar-thumb {
		background-color: #999;
		border-radius: 14px;
	}

	#myNavS::-webkit-scrollbar-thumb:hover {
		background-color: #777;
	}
</style>

<script>
	function myToggleNav() {
		return {
			s: document.querySelector('#myNavS'),
			small: false,

			trigger: {
				['@click']() {
					let a = getComputedStyle(this.s);
					// console.log(JSON.stringify(a.marginLeft));
					if (a.marginLeft !== '-300px') {
						this.s.style.marginLeft = '-300px';
					} else {
						this.s.style.marginLeft = '0px';
					}
				},

				['@resize.window']() {
					this.small = (window.outerWidth < 768);

					if (!this.small) {
						this.s.style.marginLeft = '0px';
						this.s.style.position = 'relative';
					} else {
						this.s.style.marginLeft = '-300px';
						this.s.style.position = 'fixed';
					}
				},

				['@load.window']() {
					this.small = (window.outerWidth < 768);
				},
			},
		}
	}
</script>

<div class="flex" style="max-width: 1300px;">
	<section id="myNavS" class="w300px-min w300px-max bg-primary50 transition-var pos-relative">

		<button x-data="myToggleNav()" x-spread="trigger" class="button im-bars icon0 b-hide-imp show-tablet pos-absolute pos0-r pos0-t" style="transform: translateX(100%);"></button>

		<!-- <button x-data @click="let e = document.querySelector('#myNavS'); if (e.style.marginLeft == '0px' || e.style.marginLeft == '') {e.style.marginLeft = '-300px'} else {e.style.marginLeft = '0px'}" class="button im-bars icon0 b-hide-imp show-tablet pos-absolute pos0-r pos0-t" style="transform: translateX(100%);"></button> -->


		<div class="h100 w100 pad30-rl overflow-auto overscroll-behavior-contain" id="mso_show_sidebar1"><?php mso_show_sidebar('2'); ?></div>
	</section>

	<section class="flex-grow5 flex-shrink5 pos-relative">
		<?php
		if ($fn = mso_fe('main/blocks/header.php')) require $fn;
		if ($fn = mso_fe('main/blocks/header-out.php')) require $fn;
		?>

		<?php if ($fn = mso_fe('main/blocks/main-start.php')) require $fn; ?>

		<div class="content pad30-rl">
			<?php if ($fn = mso_fe('main/blocks/content.php')) require $fn; ?>
		</div>

		<?php if ($fn = mso_fe('main/blocks/main-end.php')) require $fn; ?>

		<?php
		if ($fn = mso_fe('main/blocks/footer-pre.php')) require $fn;
		if ($fn = mso_fe('main/blocks/footer.php')) require $fn;
		?>
	</section>
</div>

<?php
if ($fn = mso_fe('main/blocks/body-end.php')) require $fn;
?>
</body>

</html><?php if ($fn = mso_fe('main/blocks/_end.php')) require $fn; ?>