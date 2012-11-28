<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>


		<div id="MainMenu" class="MainMenu"><div class="wrap">
			<ul class="menu">
			
				<?php

					$menu = mso_get_option('top_menu', 'templates', tf('/ | Главная_NR_about | О сайте_NR_comments | Комментарии_NR_contact | Контакты_NR_sitemap | Архив_NR_feed | RSS'));
					
					if (is_login())
					{
						$menu .= NR . '[';
						$menu .= NR . 'admin | ' . getinfo('users_nik') . ' | Админ-панель | icon icon-admin';
						$menu .= NR . 'admin/page_new | Создать запись';
						$menu .= NR . 'admin/page | Список записей';
						$menu .= NR . 'admin/cat | Рубрики';
						$menu .= NR . 'admin/plugins | Плагины';
						$menu .= NR . 'admin/files | Загрузки';
						$menu .= NR . 'admin/sidebars | Сайдбары';
						$menu .= NR . 'admin/options | Основные настройки';
						$menu .= NR . 'admin/template_options | Настройка шаблона';
						$menu .= NR . 'http://max-3000.com/page/faq | ЧАВО для новичков';
						$menu .= NR . 'http://max-3000.com/help | Центр помощи';
						$menu .= NR . 'http://forum.max-3000.com/ | Форум поддержки';
						
						if (function_exists('ushka')) 
						{
							$menu .= NR . ushka('main-menu-admin');
						}
						
						$menu .= NR . 'logout | Выход';
						
						$menu .= NR . ']';
					}
					elseif (is_login_comuser())
					{
						$comuser = is_login_comuser();
						
						$menu .= NR . '[';
						
						if ($comuser['comusers_nik'])
							$menu .= NR . '# | ' . $comuser['comusers_nik'];
						else
							$menu .= NR . '# | Ваши ссылки';
						
						$menu .= NR . 'users/' . $comuser['comusers_id'] . ' | Своя страница';
						$menu .= NR . 'http://max-3000.com/page/faq | ЧАВО для новичков';
						$menu .= NR . 'http://max-3000.com/help | Центр помощи';
						$menu .= NR . 'http://forum.max-3000.com/ | Форум поддержки';
						$menu .= NR . 'logout | Выход';
						$menu .= NR . ']';

					}

					if ($menu) echo mso_menu_build($menu, 'selected', false);
				?>
			</ul>
			<div class="clearfix"></div>
	</div><!-- div class=wrap --></div><!-- div id="MainMenu" -->
