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

// получаем список комментариев текущей страницы
require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

// если был отправлен новый коммент, то обрабатываем его и выводим сообщение в случае ошибки
if ($out = mso_get_new_comment( array('page_title'=>$page_title) ))
{
	$out .= mso_load_jquery('jquery.scrollto.js');
	$out .= '<script>$(document).ready(function(){$.scrollTo("#comments", 500);})</script>';
	echo $out;
}
 


// получаем все разрешенные комментарии
$comments = mso_get_comments($page_id);


// в сессии проверяем может быть только что отправленный комментарий
if (isset($MSO->data['session']['comments']) and $MSO->data['session']['comments'] )
{
	$anon_comm = $MSO->data['session']['comments']; // массив: id-коммент
	
	// получаем комментарии для этого юзера
	$an_comments = mso_get_comments($page_id, array('anonim_comments'=>$anon_comm));
	
	// добавляем в вывод
	if ($an_comments) $comments = array_merge($comments, $an_comments);
}

if (is_login()) $edit_link = getinfo('siteurl') . 'admin/comments/edit/';
	else $edit_link = '';

if ($comments or $page_comment_allow) echo NR . '<div class="type type_page_comments">' . NR;

if ($f = mso_page_foreach('page-comments-do-list')) require($f); // подключаем кастомный вывод

if ($page_text_ok and $comments) // есть страницы
{ 	

	if ($f = mso_page_foreach('page-comments-do')) require($f); // подключаем кастомный вывод
	else 
	{
		echo '<div class="comments">';
		echo mso_get_val('page_comments_count_start', '<h3 class="comments">') . tf('Комментариев') . ': ' . count($comments) . mso_get_val('page_comments_count_end', '</h3>');
	}
	
	echo '<ol>';
	
	static $num_comment = 0; // номер комментария по порядку - если нужно выводить в type_foreach-файле
	
	foreach ($comments as $comment)  // выводим в цикле
	{
		$num_comment++;
		
		if ($f = mso_page_foreach('page-comments')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}
		
		extract($comment);
		
		//pr($comment);
		
		if ($users_id) $class = ' class="users"';
		elseif ($comusers_id) $class = ' class="comusers"';
		else $class = ' class="anonim"';
		
		$comments_date = mso_date_convert('Y-m-d в H:i:s', $comments_date);
		
		$comment_info = '';
		echo NR . '<li' . $class . '>';
		
		
		$comment_info .= '<span class="url">' . $comments_url . '</span>';
		
		if ($comusers_url and mso_get_option('allow_comment_comuser_url', 'general', 0))
		{
			$comment_info .= ' <a href="' 
				. $comusers_url 
				. '" rel="nofollow" class="outlink"><img src="' 
				. getinfo('template_url') 
				. 'images/outlink.png" width="16" height="16" alt="link" title="' 
				. tf('Сайт комментатора') . '"></a>';
		}
		
		$comment_info .= ' | <span class="date"><a href="#comment-' 
			. $comments_id 
			. '" id="comment-' . $comments_id . '">' . $comments_date . '</a></span>';
		
		if ($edit_link) $comment_info .= ' | <a href="' . $edit_link . $comments_id . '">edit</a>';
		
		if (!$comments_approved) $comment_info .= ' | '. tf('Ожидает модерации');
		
		
		if ($f = mso_page_foreach('page-comments-out')) 
		{
			require($f); // подключаем кастомный вывод
		}
		else
		{
			echo '<div class="comment-info">' . $comment_info . '</div>';
			
			echo '<div class="comments_content">'
				. mso_avatar($comment) 
				. mso_comments_content($comments_content) 
				. '</div>';
				
		}
		
		echo '<div class="clearfix"></div>';
		
		echo '</li>'; 
		
	//	pr($comment);
	}
	
	echo '</ol>';
	echo '</div>' . NR;
}

if ($f = mso_page_foreach('page-comments-posle-list')) require($f); // подключаем кастомный вывод

if ($page_comment_allow and $page_text_ok)
{
	// если запрещены комментарии и от анонимов и от комюзеров, то выходим
	if ( mso_get_option('allow_comment_anonim', 'general', '1') 
		or mso_get_option('allow_comment_comusers', 'general', '1') )  
	{
		if ($f = mso_page_foreach('page-comment-form-do')) require($f); // подключаем кастомный вывод
		else echo '<div class="break"></div>' . mso_get_val('leave_a_comment_start', '<h3 class="comments">') . mso_get_option('leave_a_comment', 'templates', tf('Оставьте комментарий!')). mso_get_val('leave_a_comment_end', '</h3>');
		
		if ($f = mso_page_foreach('page-comment-form')) 
		{
			require($f); // подключаем кастомный вывод
		}
		else 
		{
			// форма комментариев
			// page-comment-form.php может быть в type своего шаблона
			$fn1 = getinfo('template_dir') . 'type/page-comment-form.php'; 		 // путь в шаблоне
			$fn2 = getinfo('templates_dir') . 'default/type/page-comment-form.php'; // путь в default
			if ( file_exists($fn1) ) require($fn1); // если есть, подключаем шаблонный
			elseif (file_exists($fn2)) require($fn2); // нет, значит дефолтный
		}
	}
}

if ($comments or $page_comment_allow) echo NR . '</div><!-- class="type type_page_comments" -->' . NR;

?>