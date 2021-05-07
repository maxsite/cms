<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

$toggle_id = 'toggle' . crc32(__FILE__);

$menuAddClass = mso_get_val('menu-add-class', 'animation-fade animation-fast');
$menuControl = mso_get_val('menu-control', '<span class="button button1 im-bars mar5-tb">Меню</span>');
$menuControlClass = mso_get_val('menu-control-class', 't-right');

?>

<nav class="menu1 menu1-tablet b-clearfix">

	<input class="menu-control" type="checkbox" id="<?= $toggle_id ?>">
	<label class="menu-control <?= $menuControlClass ?>" for="<?= $toggle_id ?>">
		<?= $menuControl ?>
	</label>

	<ul class="menu menu-no-load menu-hover menu-tablet <?= $menuAddClass ?>">
		<?php

		$menu = '';

		if (is_login()) {
			$menu .= NR . '[';
			$menu .= NR . '# | | Меню MaxSite CMS | | im-user-circle';
			$menu .= NR . 'admin | Консоль | | | im-tachometer-alt';
			$menu .= NR . 'admin/page_new | Создать запись | | | im-edit';
			$menu .= NR . '---';
			$menu .= NR . 'admin/page | Список записей | | | im-book';
			$menu .= NR . 'admin/files | Загрузки | | | im-cloud-download-alt';
			$menu .= NR . '---';
			$menu .= NR . 'admin/cat | Рубрики | | | im-tags';
			$menu .= NR . 'admin/sidebars | Сайдбары | | | im-columns';
			$menu .= NR . 'admin/sidebars/widgets | Виджеты | | | im-clone';
			$menu .= NR . 'admin/plugins | Плагины | | | im-puzzle-piece';
			$menu .= NR . '---';
			$menu .= NR . 'admin/options | Опции сайта | | | im-wrench';
			$menu .= NR . 'admin/template_options | Настройки шаблона | | | im-cog';
			$menu .= NR . 'admin/editor_files | Редактор файлов | | | im-code';

			if (function_exists('ushka')) {
				$menu .= NR . ushka('main-menu-admin');
			}

			$menu .= NR . '---';
			$menu .= NR . 'logout | Выход | | | im-sign-out-alt';

			$menu .= NR . ']';
		} elseif (is_login_comuser()) {
			$comuser = is_login_comuser();

			$menu .= NR . '[';
			$menu .= NR . '# | | Своя страница | | im-user-circle';
			$menu .= NR . 'users/' . $comuser['comusers_id'] . ' | Своя страница | | | im-newspaper';

			$menu .= mso_hook('main_menu_add_comuser');

			$menu .= NR . '---';
			$menu .= NR . 'logout | Выход | | | im-sign-out-alt';
			$menu .= NR . ']';
		}

		$menu .= NR .  mso_get_option('top_menu', 'templates', tf('/ | Главная ~ page/about | О сайте ~ comments | Комментарии ~ contact | Контакты ~ sitemap | Архив ~ feed | RSS'));

		if ($menu) echo mso_menu_build($menu, 'selected', false);
		?>
	</ul>
</nav>

<?php
// удаляем значения, чтобы не влиять на повторный вызов
mso_unset_val('menu-add-class');
mso_unset_val('menu-control');
mso_unset_val('menu-control-class');
?>