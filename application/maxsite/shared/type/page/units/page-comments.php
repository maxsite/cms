<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# коммментарии

$page_text_ok = true; // разрешить вывод текста комментария в зависимости от пароля записи

if (isset($page['page_password']) and $page['page_password']) // есть пароль у страницы
{
	$page_text_ok = (isset($page['page_password_ok'])); // нет отметки, что пароль пройден
}

echo '<span><a id="comments"></a></span>';

if ($f = mso_page_foreach('page_comments_start')) require($f);
mso_hook('page_comments_start');

// получаем список комментариев текущей страницы
require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

// если был отправлен новый коммент, то обрабатываем его и выводим сообщение в случае ошибки
if ($out = mso_get_new_comment( array('page_title'=>$page['page_title']) ))
{
	$out .= mso_load_jquery('jquery.scrollto.js');
	$out .= '<script>$(document).ready(function(){$.scrollTo("#comments", 500);})</script>';
	echo $out;
}

// получаем все разрешенные комментарии
$comments = mso_get_comments($page['page_id']);

// в сессии проверяем может быть только что отправленный комментарий
if (isset($MSO->data['session']['comments']) and $MSO->data['session']['comments'] )
{
	$anon_comm = $MSO->data['session']['comments']; // массив: id-коммент
	
	// получаем комментарии для этого юзера
	$an_comments = mso_get_comments($page['page_id'], array('anonim_comments'=>$anon_comm));
	
	// добавляем в вывод
	if ($an_comments) $comments = array_merge($comments, $an_comments);
}

if (is_login()) $edit_link = getinfo('siteurl') . 'admin/comments/edit/';
	else $edit_link = '';

if ($comments or $page['page_comment_allow']) echo NR . '<div class="mso-type-page-comments">' . NR;

if ($f = mso_page_foreach('page-comments-do-list')) require($f);

if ($page_text_ok and $comments) // есть страницы
{ 	
	
	echo '<div class="mso-comments">';

	eval(mso_tmpl_ts('type/page/units/page-comments-count-tmpl.php'));
	
	echo '<section>';
	
	static $comment_num = 0; // номер комментария по порядку - если нужно выводить в type_foreach-файле
	
	if ($fn = mso_find_ts_file('type/page/units/page-comments-article-tmpl.php')) 
		$tmpl = mso_tmpl($fn);
		
	foreach ($comments as $comment)  // выводим в цикле
	{
		$comment_num++;
		
		if ($f = mso_page_foreach('page-comments')) 
		{
			require($f);
			continue; // следующая итерация
		}
		
		extract($comment);
		
		if ($comment_num & 1) $a_class = 'mso-comment-odd'; // нечетное
			else $a_class = 'mso-comment-even'; // четное
		
		if ($users_id) $a_class .= ' mso-comment-users';
		elseif ($comusers_id) $a_class .= ' mso-comment-comusers';
		else $a_class .= ' mso-comment-anonim';
		
		$avatar = mso_avatar($comment, '', false,  false, true); // только адрес граватарки

		// $comments_content = mso_comments_content($comments_content);
		
		if (!$comusers_url or !mso_get_option('allow_comment_comuser_url', 'general', 0))
			$comusers_url = '';
			
		eval($tmpl); // выполнение через шаблонизатор
	}
	
	echo '</section>';
	echo '</div>' . NR;
}

if ($f = mso_page_foreach('page-comments-posle-list')) require($f);

if ($page['page_comment_allow'] and $page_text_ok)
{
	// если запрещены комментарии и от анонимов и от комюзеров, то выходим
	if ( mso_get_option('allow_comment_anonim', 'general', '1') 
		or mso_get_option('allow_comment_comusers', 'general', '1') )  
	{
		
		$to_login = tf('Вы можете <a href="#LOG#">войти</a> под своим логином или <a href="#REG#"> зарегистрироваться</a> на сайте.');
		$to_login = str_replace('#LOG#', getinfo('site_url') . 'login', $to_login);
		$to_login = str_replace('#REG#', getinfo('site_url') . 'registration', $to_login);
		
		
		if (mso_get_option('new_comment_anonim_moderate', 'general', '1') )
			$to_moderate = mso_get_option('form_comment_anonim_moderate', 'general', tf('Комментарий будет опубликован после проверки'));
		else
			$to_moderate = mso_get_option('form_comment_anonim', 'general', tf('Используйте нормальные имена'));
		
		// если запрещены комментарии от анонимов и при этом нет залогиненности, то форму при простой форме не выводим
		if ( !mso_get_option('allow_comment_anonim', 'general', '1') and !is_login() and !is_login_comuser() and mso_get_option('form_comment_easy', 'general', '0')) 
		{
			if (mso_get_option('allow_comment_comusers', 'general', '1')) 
			{
				eval(mso_tmpl_ts('type/page/units/page-comment-to-login-tmpl.php')); 
			}
		}
		else
		{
			eval(mso_tmpl_ts('type/page/units/page-comment-form-tmpl.php')); 
		}
	}
}

if ($comments or $page['page_comment_allow']) 
	echo NR . '</div><!-- /div.mso-type-page-comments -->' . NR;

# end file