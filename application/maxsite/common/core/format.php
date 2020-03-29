<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// формируем скрытый input для формы с текущей сессией
function mso_form_session($name_form = 'flogin_session_id')
{
	return '<input type="hidden" value="' . getinfo('session_id') . '" name="' . $name_form . '">';
}

// вывод логин-формы
// в login_form_auth_title для разделителей используется код [end] - нужен для того, чтобы перечислять через запятую
function mso_login_form($conf = [], $redirect = '', $echo = true)
{
	if ($redirect == '') $redirect = urlencode(mso_current_url());

	// готовим переменные для файла шаблонизатора loginform-common-tmpl.php
	$login = $conf['login'] ?? '';
	$password = $conf['password'] ?? '';
	$submit = $conf['submit'] ?? '';
	$submit_value = $conf['submit_value'] ?? tf('Войти');
	$submit_end = $conf['submit_end'] ?? '';
	$form_end = $conf['form_end'] ?? '';
	$login_add = $conf['login_add'] ?? '';
	$password_add = $conf['password_add'] ?? '';
	$tmpl_file = $conf['tmpl_file'] ?? 'type/loginform/units/loginform-common-tmpl.php';
	$action = getinfo('site_url') . 'login';
	$session_id = getinfo('session_id');

	$login_form_auth_title = $conf['login_form_auth_title'] ?? tf('Вход через:') . ' ';

	$hook_login_form_auth = mso_hook_present('login_form_auth') ? '<span class="login-form-auth-title">' . $login_form_auth_title . '</span>' . mso_hook('login_form_auth') : '';

	if ($hook_login_form_auth) {
		$hook_login_form_auth = trim(str_replace('[end]', '     ', $hook_login_form_auth));
		$hook_login_form_auth = '<div class="login-form-auth">' . str_replace('     ', ', ', $hook_login_form_auth) . '</div>';
	}

	if ($echo) {
		eval(mso_tmpl_ts($tmpl_file));
	} else {
		ob_start();
		eval(mso_tmpl_ts($tmpl_file));
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
}

// формируем li-элементы для меню
// элементы представляют собой текст, где каждая строчка один пункт
// каждый пункт делается так:  http://ссылка | название | подсказка | class | class_для_span | атрибуты ссылки
// на выходе так:
// <li class="selected"><a href="url"><span>ссылка</span></a></li>
// если первый символ [ то это открывает группу ul 
// если ] то закрывает - позволяет создавать многоуровневые меню
// если адрес равен # то ссылка не формируется, только текст <li class=""><span>ссылка</span></li>
// если пункт меню равен --- то формируется разделитель li.divider Имеет смысл только в подпунктах
function mso_menu_build($menu = '', $select_css = 'selected', $add_link_admin = false)
{
	// добавить ссылку на admin
	if ($add_link_admin and is_login()) $menu .= NR . 'admin|Admin';

	$menu = str_replace("\r", "", $menu); // если это windows
	$menu = str_replace("_NR_", "\n", $menu);
	$menu = str_replace(" ~ ", "\n", $menu);
	$menu = str_replace("\n\n\n", "\n", $menu);
	$menu = str_replace("\n\n", "\n", $menu);

	// в массив
	$menu = explode("\n", trim($menu));

	// обработаем меню на предмет пустых строк, корректности и подсчитаем кол-во элементов
	$count_menu = 0;
	foreach ($menu as $elem) {
		if (strlen(trim($elem)) > 1) $count_menu++;
	}

	// определим текущий url
	$current_url = getinfo('site_protocol') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$out = '';

	$i = 1; // номер пункта
	$n = 0; // номер итерации цикла 

	$group_in = false;
	$group_in_first = false;
	$group_num = 0; // номер группы
	$group_work = false; // открытая группа?
	$selected_present = false; // есть ли выделеный пункт?
	$group_elem = 0; // элемент в группе

	foreach ($menu as $elem) {
		// разобъем строчку по адрес | название
		$elem = explode('|', trim($elem));

		// должно быть два элемента
		if (count($elem) > 1) {
			$url = trim($elem[0]);  // адрес
			$name = trim($elem[1]); // название

			if (isset($elem[2]))
				$title = ' title="' . htmlspecialchars(trim($elem[2])) . '"';
			else
				$title = '';

			// если адрес = ## то не выводим ссылку
			$a_link = ($url != '##');

			// нет в адресе http:// - значит это текущий сайт
			// если начинается с # или  ? — ничего не делаем
			if (
				($url != '#')
				and strpos($url, '#') !== 0
				and strpos($url, '?') !== 0
				and strpos($url, 'http://') === false
				and strpos($url, 'https://') === false
			) {
				if ($url == '/')
					$url = getinfo('siteurl'); // это главная
				else
					$url = getinfo('siteurl') . $url;
			}

			// если текущий адрес совпал, значит мы на этой странице
			if ($url == $current_url) {
				$class = ' ' . $select_css;
				$selected_present = true;
			} else {
				$class = '';
			}

			// возможно указан css-класс
			if (isset($elem[3])) $class .= ' ' . trim($elem[3]);

			// возможно указан class_для_span
			if (isset($elem[4]))
				$class_span = ' class="' . trim($elem[4]) . '"';
			else
				$class_span = '';

			// возможно указан атрибут_для_ссылки
			$link_attr = (isset($elem[5])) ? ' ' . trim($elem[5]) : '';

			// для первого элемента добавляем класс first
			if ($i == 1) $class .= ' first';

			if ($group_in_first) {
				$class .= ' group-first';
				$group_in_first = false;
			}

			// для последнего элемента добавляем класс last
			if ($i == $count_menu) $class .= ' last';

			if ($class == ' ') $class = '';

			if ($group_in) {
				// открываем группу
				$group_num++;
				$class .= ' group-num-' . $group_num;

				if ($a_link) {
					$out .= '<li class="group' . $class . '"><a href="' . $url . '"' . $title . $link_attr . '><span' . $class_span . '>' . $name . '</span></a><ul>';
				} else {
					$out .= '<li class="group' . $class . '"><span' . $class_span . '>' . $name . '</span><ul>';
				}

				$group_in = false;
				$group_in_first = true;
			} else {
				if ($group_elem > 0 and array_key_exists($i, $menu) and isset($menu[$n + 1]) and trim($menu[$n + 1]) == ']') $class .= ' group-last';

				if ($a_link) {
					$out .= '<li class="' . trim($class) . '"><a href="' . $url . '"' . $title  . $link_attr . '><span' . $class_span . '>' . $name . '</span></a></li>';
				} else {
					$out .= '<li class="' . trim($class) . '"><span' . $class_span . '>' . $name . '</span></li>';
				}
			}

			if ($url == $current_url and $group_work) {
				// выделяем родителя группы, если в ней выделенный подпункт
				$out = str_replace('group-num-' . $group_num, 'group-num-' . $group_num . ' group-selected', $out);
				$selected_present = true;
			}

			$i++;
			$group_elem++;
		} else {
			// если это [, то это начало группы ul 
			// если ] то /ul

			if ($elem[0] == '[') {
				$group_in = true;
				$group_work = true;
				$group_elem = 0;
			}

			if ($elem[0] == ']') {
				$group_elem = 0;
				$group_in = false;
				$group_work = false;
				$out .= '</ul></li>';
			}

			if ($elem[0] == '---') {
				// разделитель
				$out .= '<li class="divider"><span></span></li>';
			}
		}

		$n++;
	}

	$out = str_replace('<li class="">', '<li>', $out);

	// если ничего не выделено, то для первой группы прописываем класс group-default
	if (!$selected_present)
		$out = str_replace('group-num-1', 'group-num-1 group-default', $out);

	//pr($out, 1);
	return $out;
}

// функция построения из массивов списка UL
// вход - массив из с [childs]=>array(...)
function mso_create_list($a = [], $options = [], $child = false)
{
	static $level = 0;

	if (!$a) return '';

	if (!isset($options['class_ul'])) $options['class_ul'] = ''; // класс UL
	if (!isset($options['class_ul_style'])) $options['class_ul_style'] = ''; // свой стиль для UL
	if (!isset($options['class_child'])) $options['class_child'] = 'child'; // класс для ребенка
	if (!isset($options['class_child_style'])) $options['class_child_style'] = ''; // свой стиль для ребенка
	if (!isset($options['class_current'])) $options['class_current'] = 'current-page'; // класс li текущей страницы
	if (!isset($options['class_current_style'])) $options['class_current_style'] = ''; // стиль li текущей страницы
	if (!isset($options['class_li'])) $options['class_li'] = ''; // класс LI
	if (!isset($options['class_li_style'])) $options['class_li_style'] = ''; // стиль LI
	if (!isset($options['format'])) $options['format'] = '[LINK][TITLE][/LINK]'; // формат ссылки
	if (!isset($options['format_current'])) $options['format_current'] = '<span>[TITLE]</span>'; // формат для текущей
	if (!isset($options['title'])) $options['title'] = 'page_title'; // имя ключа для титула
	if (!isset($options['link'])) $options['link'] = 'page_slug'; // имя ключа для слага
	if (!isset($options['descr'])) $options['descr'] = 'category_desc'; // имя ключа для описания
	if (!isset($options['id'])) $options['id'] = 'page_id'; // имя ключа для id
	if (!isset($options['slug'])) $options['slug'] = 'page_slug'; // имя ключа для slug
	if (!isset($options['menu_order'])) $options['menu_order'] = 'page_menu_order'; // имя ключа для menu_order
	if (!isset($options['id_parent'])) $options['id_parent'] = 'page_id_parent'; // имя ключа для id_parent
	if (!isset($options['count'])) $options['count'] = 'count'; // имя ключа для количества элементов
	if (!isset($options['prefix'])) $options['prefix'] = 'page/'; // префикс для ссылки
	if (!isset($options['current_id'])) $options['current_id'] = true; // текущая страница отмечается по page_id - иначе по текущему url
	if (!isset($options['childs'])) $options['childs'] = 'childs'; // поле для массива детей

	// если true, то главная рабрика выводится без ссылки в <span> 
	if (!isset($options['group_header_no_link'])) $options['group_header_no_link'] = false;

	// функция, которая сработает на [FUNCTION]
	// эта функция получает в качестве параметра текущий массив $elem
	if (!isset($options['function'])) $options['function'] = false;

	// функция которая просто срабатывают в теле цикла — 
	// принимает текущий элемент, выходную строчку и опцию function1_data
	if (!isset($options['function1'])) $options['function1'] = false;
	if (!isset($options['function1_data'])) $options['function1_data'] = [];

	if (!isset($options['nofollow']) or !$options['nofollow'])
		$options['nofollow'] = ''; // можно указать rel="nofollow" для ссылок
	else
		$options['nofollow'] = ' rel="nofollow"';

	$class_child = $class_child_style = $class_ul = $class_ul_style = '';
	$class_current = $class_current_style = $class_li = $class_li_style = '';

	// [LEVEL] - заменяется на level-текущий уровень вложенности
	if ($options['class_child']) $class_child = ' class="' . $options['class_child'] . ' [LEVEL]"';

	$class_child = str_replace('[LEVEL]', 'level' . $level, $class_child);

	if ($options['class_child_style'])
		$class_child_style = ' style="' . $options['class_child_style'] . '"';

	if ($options['class_ul'])
		$class_ul = ' class="' . $options['class_ul'] . '"';

	if ($options['class_ul_style'])
		$class_ul_style = ' style="' . $options['class_ul_style'] . '"';

	if ($options['class_current'])
		$class_current = ' class="' . $options['class_current'] . '"';

	if ($options['class_current_style'])
		$class_current_style = ' style="' . $options['class_current_style'] . '"';

	if ($options['class_li'])
		$class_li = ' class="' . $options['class_li'] . ' group"';
	else
		$class_li = ' class="group"';

	if ($options['class_li_style']) $class_li_style = ' style="' . $options['class_li_style'] . '"';

	if ($child)
		$out = NR . '	<ul' . $class_child . $class_child_style . '>';
	else
		$out = NR . '<ul' . $class_ul . $class_ul_style . '>';

	$current_url = getinfo('siteurl') . mso_current_url(); // текущий урл

	// из текущего адресу нужно убрать пагинацию
	$current_url = str_replace('/next/' . mso_current_paged(), '', $current_url);

	foreach ($a as $elem) {
		$title = $elem[$options['title']];
		$elem_slug = mso_strip($elem[$options['link']]); // slug элемента

		$url = getinfo('siteurl') . $options['prefix'] . $elem_slug;

		// если это page, то нужно проверить вхождение этой записи в элемент рубрики 
		// если есть, то ставим css-класс curent-page-cat
		$curent_page_cat_class = is_page_cat($elem_slug, false, false) ? ' class="curent-page-cat"' : '';

		$link = '<a' . $options['nofollow'] . ' href="' . $url . '" title="' . htmlspecialchars($title) . '"' . $curent_page_cat_class . '>';

		if (isset($elem[$options['descr']]))
			$descr = $elem[$options['descr']];
		else
			$descr = '';

		if (isset($elem[$options['count']]))
			$count = $elem[$options['count']];
		else
			$count = '';

		if (isset($elem[$options['id']]))
			$id = $elem[$options['id']];
		else
			$id = '';

		if (isset($elem[$options['slug']]))
			$slug = $elem[$options['slug']];
		else
			$slug = '';

		if (isset($elem[$options['menu_order']]))
			$menu_order = $elem[$options['menu_order']];
		else
			$menu_order = '';

		if (isset($elem[$options['id_parent']]))
			$id_parent = $elem[$options['id_parent']];
		else
			$id_parent = '';

		$cur = false;

		if ($options['current_id']) // текущий определяем по id страницы
		{
			if (isset($elem['current'])) {
				$e = $options['format_current'];
				$cur = true;
			} else {
				$e = $options['format'];
			}
		} else // определяем по урлу
		{
			if ($url == $current_url) {
				$e = $options['format_current'];
				$cur = true;
			} else {
				$e = $options['format'];
			}
		}

		$e = str_replace('[LINK]', $link, $e);
		$e = str_replace('[/LINK]', '</a>', $e);
		$e = str_replace('[TITLE]', $title, $e);
		$e = str_replace('[TITLE_HTML]', htmlspecialchars($title), $e);
		$e = str_replace('[DESCR]', $descr, $e);
		$e = str_replace('[DESCR_HTML]', htmlspecialchars($descr), $e);
		$e = str_replace('[ID]', $id, $e);
		$e = str_replace('[SLUG]', $slug, $e);
		$e = str_replace('[SLUG_HTML]', htmlspecialchars($slug), $e);
		$e = str_replace('[MENU_ORDER]', $menu_order, $e);
		$e = str_replace('[ID_PARENT]', $id_parent, $e);
		$e = str_replace('[COUNT]', $count, $e);
		$e = str_replace('[URL]', $url, $e);

		if ($options['function'] and function_exists($options['function'])) {
			$function = $options['function']($elem);
			$e = str_replace('[FUNCTION]', $function, $e);
		} else {
			$e = str_replace('[FUNCTION]', '', $e);
		}

		if ($options['function1'] and function_exists($options['function1'])) {
			$e = $options['function1']($e, $elem, $options['function1_data']);
		}

		if (isset($elem[$options['childs']])) {

			if ($cur) {
				$out .= NR . '<li' . $class_current . $class_current_style . '>' . $e;
			} else {
				if ($options['group_header_no_link'])
					$out .= NR . '<li' . $class_li . $class_li_style . '><span class="group_header">' . $title . '</span>';
				else
					$out .= NR . '<li' . $class_li . $class_li_style . '>' . $e;
			}

			++$level;
			$out .= mso_create_list($elem[$options['childs']], $options, true);
			--$level;

			$out .= NR . '</li>';
		} else {
			if ($child)
				$out .= NR . '	';
			else
				$out .= NR;

			// если нет детей, то уберем класс group
			$class_li_1 = str_replace('group', '', $class_li);

			if ($cur)
				$out .= '<li' . $class_current . $class_current_style . '>' . $e . '</li>';
			else
				$out .= '<li' . $class_li_1 . $class_li_style . '>' . $e . '</li>';
		}
	}

	if ($child)
		$out .= NR . '	</ul>' . NR;
	else
		$out .= NR . '</ul>' . NR;

	$out = str_replace('<li class="">', '<li>', $out);

	return $out;
}


// формирование rss в <link rel...>
// для страниц и рубрик добавляются свои RSS
function mso_rss()
{
	global $MSO;

	$out = '<link rel="alternate" type="application/rss+xml" title="'
		. tf('Все новые записи') . '" href="'
		. getinfo('rss_url') . '">' . NR;

	$out .= '<link rel="alternate" type="application/rss+xml" title="'
		. tf('Все новые комментарии') . '" href="'
		. getinfo('rss_comments_url') . '">';

	if (is_type('page') and mso_segment(2) and (isset($MSO->data['pages_is']) and $MSO->data['pages_is'])) {
		$out .= NR . '<link rel="alternate" type="application/rss+xml" title="'
			. tf('Комментарии этой записи') . '" href="'
			. getinfo('site_url') . mso_segment(1) . '/' . mso_segment(2) . '/feed">';
	} elseif (is_type('category') and mso_segment(2) and (isset($MSO->data['pages_is']) and $MSO->data['pages_is'])) {
		$out .= NR . '<link rel="alternate" type="application/rss+xml" title="'
			. tf('Записи этой рубрики') . '" href="'
			. getinfo('site_url') . mso_segment(1) . '/' . mso_segment(2) . '/feed">';
	}

	return $out;
}

// функция формирует <link rel="$REL" $ADD>
// $rel - тип rel. Если он равен canonical, то формируется канонизация
// http://www.google.com/support/webmasters/bin/answer.py?answer=139066&hl=ru
// <link rel="canonical" href="http://www.example.com/page/about">
function mso_link_rel($rel = 'canonical', $add = '', $url_only = false)
{
	if (!$rel) return; // пустой тип

	if ($rel == 'canonical') {
		
		// свой обработчик
		// если хук вернул false или '', то обработка продолжается дальше
		// иначе хук возвращает url и происходит его вывод
		if (mso_hook_present('canonicalUrl')) { 
			$rh = mso_hook('canonicalUrl');
			
			if ($rh) return '<link rel="canonical" href="' . $rh . '">';
		}
		
		if ($add) {
			return '<link rel="canonical" ' . $add . '>';
		} else {
			// для разных типов данных формируем разный канонический адрес
			// он напрямую зависит от типа
			$url = '';

			// если есть хук canonical, то выполняем его
			// если хук вернул какое-то значение, то это $url
			// если нет, то выполняем типовое определение канонического адреса
			if (mso_hook_present('canonical')) $url = mso_hook('canonical');

			if (!$url) {
				if (
					is_type('page')
					or is_type('category')
					or is_type('tag')
					or is_type('author')
					or is_type('users')
					or (mso_segment(1) == 'sitemap')
					or (mso_segment(1) == 'contact')
				) {
					if (mso_segment(2)) {
						$url = getinfo('site_url') . mso_segment(1) . '/' . urlencode(mso_segment(2));
					} else {
						$url = getinfo('site_url') . mso_segment(1);
					}
				} elseif (is_type('home')) {
					$url = getinfo('site_url');
				}
			}

			// пагинация
			if (($cur = mso_current_paged()) > 1) {
				if (is_type('home')) {
					$url .= 'home/next/' . $cur;
				} else {
					if ($url) $url .= '/next/' . $cur;
				}
			}

			if ($url) {
				if ($url_only)
					return $url;
				else
					return '<link rel="canonical" href="' . $url . '">';
			}
		}
	} else {
		if ($add) return '<link rel="' . $rel . '" ' . $add . '>';
	}
}

/**
 * формирование TITLE, meta: description, keywords
 * $info: только один из вариантов: title, description, keywords
 * $args: входящий аргумент данных, например $pages (записи)
 * $format: формат вывода. Замены для: 
 *          %title%, %page_title%, %category_name%, %category_desc%, %users_nik%, || (разделитель) 
 * $sep: разделитель
 * $only_meta == true — вывод только meta-данных
 * $add_pag == true — добавка пагинации (из опции title_pagination)
 * 
 */
function mso_head_meta($info = 'title', $args = '', $format = '%page_title%', $sep = '', $only_meta = false, $add_pag = true)
{
	global $MSO;

	// выявляем ошибочный info
	if ($info != 'title' and $info != 'description' and $info != 'keywords') return '';

	if (mso_hook_present('head_meta')) {
		// если есть хуки, то управление передаем им
		return mso_hook('head_meta', array('info' => $info, 'args' => $args, 'format' => $format, 'sep' => $sep, 'only_meta' => $only_meta));
	}

	// добавка пагинации на основе опции title_pagination и только для TITLE
	$pag = '';

	if ($add_pag and $info == 'title' and ($cur = mso_current_paged()) > 1 and $onum = mso_get_option('title_pagination', 'general', t(' - страница [NUM]'))) {
		$pag = htmlspecialchars(str_replace('[NUM]', $cur, $onum));
	}

	// измененный для вывода титле хранится в $MSO->title description или keywords
	if (!$args) // нет аргумента - выводим что есть
	{
		if (!$MSO->$info)
			$out = $MSO->$info = getinfo($info) . $pag;
		else
			$out = $MSO->$info;
	} else // есть аргументы/записи
	{
		if (is_scalar($args)) {
			$out = $args . $pag; // какая-то явная строка - отдаем её как есть
		} else // входной массив - скорее всего это страница
		{
			// %page_title% %title% %category_name%
			// || это разделитель, который = $sep
			// pr($args);

			$category_name = '';
			$category_desc = '';
			$page_title = '';
			$users_nik = '';
			$title = getinfo($info);

			if (isset($args[0]['page_categories_detail'])) {
				$slug = is_type('category') ? mso_segment(2) : '';

				foreach ($args[0]['page_categories_detail'] as $id => $val) {
					if ($slug == $val['category_slug']) {
						$category_name = $val['category_name'];
						$category_desc = $val['category_desc'];
						break;
					}
				}
			}

			if (isset($args[0]['page_title'])) $page_title = $args[0]['page_title'];
			if (isset($args[0]['users_nik']))  $users_nik = $args[0]['users_nik'];

			// если есть мета, то берем её
			if (isset($args[0]['page_meta'][$info][0]) and $args[0]['page_meta'][$info][0]) {
				if ($only_meta)
					$category_name = $category_desc = $title = $sep = '';

				$page_title = $args[0]['page_meta'][$info][0];

				if ($info != 'title') $title = $page_title;
			} else {
				// для страницы если не указаны свои keywords, попробуем указать из меток
				if ($info == 'keywords' and is_type('page') and isset($args[0]['page_meta']['tags']) and $args[0]['page_meta']['tags']) {
					$page_title = implode(', ', $args[0]['page_meta']['tags']); // разбиваем массив меток в строку
				}

				// для страниц, если не указан description вытягиваем его из самого текста
				if ($info == 'description' and is_type('page') and $w = mso_get_option('description_of_page', 'general', 50)) {
					// вообще нет поля
					if (!isset($args[0]['page_meta']['description'][0])) $d = 333;

					// поле есть, но оно пустое
					if (isset($args[0]['page_meta']['description'][0]) and !$args[0]['page_meta']['description'][0]) $d = 333;

					if ($d == 333) // если есть признак
					{
						$t = strip_tags($args[0]['page_content']);
						$t = trim(str_replace("\r", "", $t));
						$t = trim(str_replace("\n", ' ', $t));
						$t = trim(str_replace("\t", ' ', $t));
						$t = trim(str_replace('   ', ' ', $t));
						$t = trim(str_replace('  ', ' ', $t));

						$page_title = mso_str_word($t, $w);
					}
				}
			}

			$arr_key = ['%title%', '%page_title%', '%category_name%', '%category_desc%', '%users_nik%', '||'];

			$arr_val = [htmlspecialchars($title), htmlspecialchars($page_title), htmlspecialchars($category_name), htmlspecialchars($category_desc), htmlspecialchars($users_nik), $sep];

			$out = str_replace($arr_key, $arr_val, $format) . $pag;
		}
	}

	// отдаем результат, сразу же указывая измененный $info в $MSO
	$out = $MSO->$info = trim($out);

	return $out;
}

// формирует <style> из указанного адреса 
// игнорируются дубли подключений
function mso_load_style($url = '', $nodouble = true)
{
	global $MSO;

	if (!isset($MSO->data['_loaded_style'])) $MSO->data['_loaded_style'] = [];
	if ($nodouble and in_array($url, $MSO->data['_loaded_style'])) return ''; // уже была загрузка

	$MSO->data['_loaded_style'][] = $url; // добавляем в список загруженных
	$MSO->data['_loaded_style'] = array_unique($MSO->data['_loaded_style']);

	return '<link rel="stylesheet" href="' . $url . '">';
}

// формирует <script> из указанного адреса
// если скрипт был уже загружен, его подключение игнорируется
function mso_load_script($url = '', $nodouble = true, $attr = '')
{
	global $MSO;

	if (!isset($MSO->data['_loaded_script'])) $MSO->data['_loaded_script'] = [];
	if ($nodouble and in_array($url, $MSO->data['_loaded_script'])) return ''; // уже была загрузка

	$MSO->data['_loaded_script'][] = $url; // добавляем в список загруженных
	$MSO->data['_loaded_script'] = array_unique($MSO->data['_loaded_script']);

	$attr = ($attr) ? ' ' . $attr : '';

	return '<script' . $attr . ' src="' . $url . '"></script>';
}

/**
 * формирование <script> с внешним js-файлом или
 * формирование <link rel="stylesheet> с внешним css-файлом
 * имя файла указывается относительно каталога шаблона
 * если файла нет, то ничего не происходит
 * если $lazy == true, то подключение файла будет в конце BODY по хуку mso_hook('body_end')
 * если $auto_dir = __DIR__ то путь будет определен относительно каталога исполняемого php-файла
 *   например: component/header/header.php:
 *	 с $auto_dir
 *     mso_add_file('js/my.js', false, __DIR__); // component/header/js/my.js
 *
 *   или нужно указывать путь к файлу:
 *      mso_add_file('component/header/js/my.js');
 *
 * $js_attr — атрибут для js-скрипта, например 'async'
 * 
 */
function mso_add_file($fn, $lazy = false, $auto_dir = false, $js_attr = '')
{
	global $MSO;

	if ($auto_dir) {
		$fn = str_replace(str_replace('\\', '/', getinfo('template_dir')), '', str_replace('\\', '/', $auto_dir)) . '/' . $fn;
	}

	if (file_exists(getinfo('template_dir') . $fn)) {
		$ext = strtolower(substr(strrchr($fn, '.'), 1)); // расширение файла

		$out = '';

		if ($ext == 'js')
			$out = mso_load_script(getinfo('template_url') . $fn, true, $js_attr);
		elseif ($ext == 'css')
			$out = mso_load_style(getinfo('template_url') . $fn);

		if (!$lazy) {
			echo $out;
		} elseif ($out) {
			// вывод через хук body_end — mso_add_file_body_end()
			if (!isset($MSO->data['add_file_to_body_end'])) $MSO->data['add_file_to_body_end'] = [];
			
			$MSO->data['add_file_to_body_end'][] = $out;
		}
	}
}

/**
 * Функция к хуку body_end по которой выводятся все подключенные файлы через mso_add_file(файл, true)
 * Lazy-загрузка в конце BODY
 */
function mso_add_file_body_end($a)
{
	global $MSO;

	if (!isset($MSO->data['add_file_to_body_end']) or !$MSO->data['add_file_to_body_end'])
		return $a;

	echo implode($MSO->data['add_file_to_body_end']);

	return $a;
}

/**
 *  сжатие HTML
 * 
 *  @param $text входной текст
 *  @return string
 */
function mso_compress_text($text)
{
	// защищенный текст
	$text = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', '_mso_protect_pre', $text);
	$text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', '_mso_protect_pre', $text);
	$text = preg_replace_callback('!(<script.*?>)(.*?)(</script>)!is', '_mso_protect_script', $text);
	$text = preg_replace_callback('!(<style.*?>)(.*?)(</style>)!is', '_mso_protect_script', $text);

	// сжатие
	$text = str_replace("\r", "", $text);
	$text = str_replace("\t", ' ', $text);
	$text = str_replace("\n   ", "\n", $text);
	$text = str_replace("\n  ", "\n", $text);
	$text = str_replace("\n ", "\n", $text);
	$text = str_replace("\n", '', $text);
	$text = str_replace('   ', ' ', $text);
	$text = str_replace('  ', ' ', $text);

	// специфичные замены
	$text = str_replace('<!---->', '', $text);
	$text = str_replace('>    <', '><', $text);
	$text = str_replace('>   <', '><', $text);
	$text = str_replace('>  <', '><', $text);

	$text = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', function ($m) {
		return base64_decode($m[1]);
	}, $text);

	return $text;
}

/**
 *  заменяет в тексте все вхождения http:// и https:// на // 
 *  
 *  @param $text текст
 *  @return string
 */
function mso_remove_protocol($text)
{
	// защищенный текст
	$text = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', '_mso_protect_pre', $text);
	$text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', '_mso_protect_pre', $text);
	$text = preg_replace_callback('!(<script.*?>)(.*?)(</script>)!is', '_mso_protect_script', $text);
	$text = preg_replace_callback('!(<style.*?>)(.*?)(</style>)!is', '_mso_protect_script', $text);
	$text = preg_replace_callback('!(<textarea.*?>)(.*?)(</textarea>)!is', '_mso_protect_script', $text);

	$text = str_replace('https://', '//', $text);
	$text = str_replace('http://', '//', $text);

	$text = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', function ($m) {
		return base64_decode($m[1]);
	}, $text);

	return $text;
}

/**
 *  pre, которое загоняется в [html_base64]
 *  callback
 * 
 *  @param $matches matches
 *  @return string
 */
function _mso_protect_pre($matches)
{
	$text = trim($matches[2]);

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br>", "\n", $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "\n", $text);

	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('&lt;pre', '<pre', $text);
	$text = str_replace('&lt;/pre', '</pre', $text);
	$text = str_replace('pre&gt;', 'pre>', $text);

	$text = $matches[1] . "\n" . '[html_base64]' . base64_encode($text) . '[/html_base64]' . $matches[3];

	return $text;
}

/**
 *  script и style, которые загоняются в [html_base64]
 *  callback
 * 
 *  @param $matches matches
 *  @return string
 */
function _mso_protect_script($matches)
{
	$text = trim($matches[2]);
	$text = $matches[1] . '[html_base64]' . base64_encode($text) . '[/html_base64]' . $matches[3];

	return $text;
}

/**
 * получает css из указанного файла
 * в css-файле можно использовать php
 * осуществляется сжатие css
 * автозамена [TEMPLATE_URL] на url-шаблона
 * функция возвращает только стили, без обрамляющего <style>
 * Если <style> нужны, то $tag_style = true
 * Если нужен сразу вывод в браузер, то $echo = true
 * 
 * @param string $fn имя файла
 * @param type $tag_style флаг для обрамления в STYLE
 * @param type $echo флаг вывода результата по echo
 * @return string
 */
function mso_out_css_file($fn, $tag_style = true, $echo = true)
{
	$fn = getinfo('template_dir') . $fn;

	$out = '';

	if (file_exists($fn)) {
		if ($r = @file_get_contents($fn)) $out .= $r . NR; // получаем содержимое

		if ($out) {
			ob_start();
			eval('?>' . stripslashes($out) . '<?php ');
			$out = ob_get_contents();
			ob_end_clean();

			$out = str_replace('[TEMPLATE_URL]', getinfo('template_url'), $out);
			$out = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $out);
			$out = str_replace(array('; ', ' {', ': ', ', '), array(';', '{', ':', ','), $out);
		}

		if ($tag_style) $out = '<style>' . $out . '</style>';
		if ($echo) echo $out;
	}

	return $out;
}

# end of file
