<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// коммментарии только сторонней системы
// комментарии MaxSite CMS не выводятся

// если комментарии запрещены, то выходим
if (!$page['page_comment_allow']) return;
if ($page['page_status'] !== 'publish') return;

$page_text_ok = true; // разрешить вывод текста комментария в зависимости от пароля записи

if (isset($page['page_password']) and $page['page_password']) {
	// есть пароль у страницы
	$page_text_ok = (isset($page['page_password_ok'])); // нет отметки, что пароль пройден
}

if (!$page_text_ok) return; // пароль к записи неверный

echo '<span><a id="comments"></a></span>';

if ($fn = mso_page_foreach('page_comments_start')) require $fn;

mso_hook('page_comments_start');

if ($code = mso_get_option('comment_other_system_code', 'general', '')) echo $code;

# end file
