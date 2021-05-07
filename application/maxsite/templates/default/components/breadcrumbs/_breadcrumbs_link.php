<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

// Массив $breadcrumbs который содержит все ссылки навигации
// ссылки идут в обратном порядке!
// 'название' => 'ссылка'
// если ссылки нет (на себя ссылки не ставим), то будет только название

$breadcrumbs = [];

if (is_type('page')) {
	if ($pageData = mso_get_val('mso_pages', 0, true)) {
		// есть запись
		$breadcrumbs[$pageData['page_title']] = '';

		if (isset($pageData['page_categories_detail']) and $pageData['page_categories_detail']) {
			foreach ($pageData['page_categories_detail'] as $c) {
				$breadcrumbs[$c['category_name']] = getinfo('siteurl') . 'category/' . $c['category_slug'];
			}
		}
	} else {
		$breadcrumbs['404. Ничего не найдено'] = '';
	}
	
} elseif (is_type('category')) {
	$c = mso_get_cat_from_slug('', true);
	$breadcrumbs[$c['category_name']] = '';
} elseif (is_type('tag')) {
	$breadcrumbs[mso_segment(2)] = '';
} elseif (is_type('author')) {
	if ($pageData = mso_get_val('mso_pages', 0, true)) {
		$_a = $pageData['users_nik'] ?? 'Автор';
		$breadcrumbs[$_a] = '';
	}
} elseif (is_type('users')) {
	if (isset($comuser_info[0]) and $comuser_info[0]) $breadcrumbs[$comuser_info[0]['comusers_nik']] = '';
	$breadcrumbs['Пользователи'] = getinfo('siteurl') . 'users';
} elseif (is_type('page_404'))                 $breadcrumbs['404. Ничего не найдено'] = '';
elseif (mso_segment(1) == 'sitemap')           $breadcrumbs['Карта сайта'] = '';
elseif (mso_segment(1) == 'contact')           $breadcrumbs['Контактная форма'] = '';
elseif (mso_segment(1) == 'registration')      $breadcrumbs['Регистрация на сайте'] = '';
elseif (mso_segment(1) == 'login')             $breadcrumbs['Вход'] = '';
elseif (mso_segment(1) == 'comments')          $breadcrumbs['Комментарии'] = '';
elseif (mso_segment(1) == 'archive')           $breadcrumbs['Архив сайта'] = '';
elseif (mso_segment(1) == 'guestbook')         $breadcrumbs['Гостевая книга'] = '';
elseif (mso_segment(1) == 'search')            $breadcrumbs['Поиск'] = '';
elseif (mso_segment(1) == 'map')               $breadcrumbs['Карта сайта по рубрикам'] = '';
elseif (mso_segment(1) == 'map-tags')          $breadcrumbs['Карта сайта по меткам'] = '';
elseif (mso_segment(1) == 'polls-archive')     $breadcrumbs['Архив голосований'] = '';
elseif (mso_segment(1) == 'password-recovery') $breadcrumbs['Восстановление пароля'] = '';

// можно указывать comp_breadcrumbs_add (это всегда массив) как дополнение к текущему
if ($add = mso_get_val('comp_breadcrumbs_add', false))
	$breadcrumbs = array_merge($breadcrumbs, $add);

// всегда добавляем главную
$breadcrumbs['Главная'] = getinfo('siteurl');

$breadcrumbs = array_reverse($breadcrumbs);

// теперь вывод
$out = '';

foreach ($breadcrumbs as $name => $link) {
	if ($link)
		$out .= '<a href="' . $link . '">' . htmlspecialchars($name) . '</a>    ';
	else
		$out .= $name . '    ';
}

$out = str_replace('    ', '<i class="im-angle-right icon0 mar10-rl"></i>', trim($out));

echo $out;

# end of file
