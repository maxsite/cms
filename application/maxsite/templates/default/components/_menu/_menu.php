<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

$toggle_id = 'toggle' . crc32(__FILE__);

?>
<div class="menu1 menu1-tablet mso-clearfix">
	<div class="wrap">
		<nav>
			<input class="menu-control" type="checkbox" id="<?= $toggle_id ?>">
			<label class="menu-control" for="<?= $toggle_id ?>"><i class="fas fa-bars"></i>Меню</label>
			<ul class="menu menu-no-load menu-hover menu-tablet">
				<?php

				$menu = '';

				if (is_login()) {
					$menu .= NR . '[';
					$menu .= NR . '# | | Админ-панель MaxSite CMS | | fab fa-maxcdn';
					$menu .= NR . 'admin | Консоль | | | fas fa-user';
					$menu .= NR . 'admin/page_new | Создать запись | | | fas fa-edit';
					$menu .= NR . '---';
					$menu .= NR . 'admin/page | Список записей | | | fas fa-newspaper';
					$menu .= NR . 'admin/files | Загрузки | | | fas fa-download';
					$menu .= NR . '---';
					$menu .= NR . 'admin/cat | Рубрики | | | fas fa-list-ul';
					$menu .= NR . 'admin/sidebars | Сайдбары | | | fab fa-trello';
					$menu .= NR . 'admin/sidebars/widgets | Виджеты | | | fas fa-paperclip';
					$menu .= NR . 'admin/plugins | Плагины | | | fas fa-puzzle-piece';
					$menu .= NR . '---';
					$menu .= NR . 'admin/options | Опции сайта | | | fas fa-wrench';
					$menu .= NR . 'admin/template_options | Настройки шаблона | | | fas fa-cogs';
					$menu .= NR . 'admin/editor_files | Редактор файлов | | | fas fa-code';

					if (function_exists('ushka')) {
						$menu .= NR . ushka('main-menu-admin');
					}

					$menu .= NR . '---';
					$menu .= NR . 'logout | Выход | | | fas fa-unlock';

					$menu .= NR . ']';
				} elseif (is_login_comuser()) {
					$comuser = is_login_comuser();

					$menu .= NR . '[';
					$menu .= NR . '# | | Своя страница | | fas fa-user';
					$menu .= NR . 'users/' . $comuser['comusers_id'] . ' | Своя страница | | | fas fa-address-book';

					$menu .= mso_hook('main_menu_add_comuser');

					$menu .= NR . '---';
					$menu .= NR . 'logout | Выход | | | fas fa-unlock';
					$menu .= NR . ']';
				}

				$menu .= NR .  mso_get_option('top_menu', 'templates', tf('/ | Главная ~ page/about | О сайте ~ comments | Комментарии ~ contact | Контакты ~ sitemap | Архив ~ feed | RSS'));

				if ($menu) echo mso_menu_build($menu, 'selected', false);
				?>
			</ul>
		</nav>
	</div>
</div><!-- div.wrap div.MainMenu -->