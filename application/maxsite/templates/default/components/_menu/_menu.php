<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>


<div class="MainMenu"><div class="wrap">
	<nav><ul class="menu menu_responsive">
	
		<?php

			$menu = mso_get_option('top_menu', 'templates', tf('/ | Главная_NR_about | О сайте_NR_comments | Комментарии_NR_contact | Контакты_NR_sitemap | Архив_NR_feed | RSS'));
			
			if (is_login())
			{
				$menu .= NR . '[';
				$menu .= NR . 'admin | ' . getinfo('users_nik') . ' | Админ-панель | icon icon-admin';
				$menu .= NR . 'admin/page_new | Создать запись | | icon page_new';
				$menu .= NR . 'admin/page | Список записей | | icon page';
				$menu .= NR . 'admin/cat | Рубрики | | icon cat';
				$menu .= NR . 'admin/plugins | Плагины | | icon plugins';
				$menu .= NR . 'admin/files | Загрузки | | icon files';
				$menu .= NR . 'admin/sidebars | Сайдбары | | icon sidebars';
				$menu .= NR . 'admin/options | Основные настройки | | icon options';
				$menu .= NR . 'admin/template_options | Настройка шаблона | | icon template_options';
				$menu .= NR . '---';
				$menu .= NR . 'http://max-3000.com/page/faq | ЧАВО для новичков | | icon faq';
				$menu .= NR . 'http://max-3000.com/help | Центр помощи | | icon help';
				$menu .= NR . 'http://forum.max-3000.com/ | Форум поддержки | | icon forum';
				
				if (function_exists('ushka')) 
				{
					$menu .= NR . ushka('main-menu-admin');
				}
				
				$menu .= NR . '---';
				$menu .= NR . 'logout | Выход | | icon logout';
				
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
				
				$menu .= NR . 'users/' . $comuser['comusers_id'] . ' | Своя страница | | icon users';
				
				$menu .= mso_hook('main_menu_add_comuser');
				
				$menu .= NR . '---';
				$menu .= NR . 'http://max-3000.com/page/faq | ЧАВО для новичков | | icon faq';
				$menu .= NR . 'http://max-3000.com/help | Центр помощи | | icon help';
				$menu .= NR . 'http://forum.max-3000.com/ | Форум поддержки | | icon forum';
				$menu .= NR . '---';
				$menu .= NR . 'logout | Выход | | icon logout';
				$menu .= NR . ']';

			}

			if ($menu) echo mso_menu_build($menu, 'selected', false);
		?>
	</ul></nav>
	<div class="clearfix"></div>
</div></div><!-- div.wrap div.MainMenu -->
