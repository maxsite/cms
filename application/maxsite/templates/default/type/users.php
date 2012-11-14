<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

require_once( getinfo('common_dir') . 'comments.php' ); 

// mso_get_comuser(0, array( 'limit'=> 20, 'tags'=>'<img><strong><em><i><b><u><s><font><pre><code><blockquote>' ) );

$comuser_info = mso_get_comuser(mso_segment(2)); // получим всю информацию о комюзере - номер в сегменте url

if ($f = mso_page_foreach('users-head-meta')) require($f);
else
{
	mso_head_meta('title', tf('Комментаторы') . '. ' . getinfo('title')); // meta title страницы
}

if (!$comuser_info and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

// теперь сам вывод
# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_users">' . NR;

if ($comuser_info)
{
	extract($comuser_info[0]);
	
	if ($f = mso_page_foreach('users')) require($f); // подключаем кастомный вывод
	else
	{
		$avatar_info = $comuser_info[0];
		$avatar_info['users_avatar_url'] = $avatar_info['users_email'] = '';
		$avatar = mso_avatar($avatar_info, 'style="float: right; margin: 5px 0 0 0;" class="gravatar"', false, 150); 
		
		if ($comusers_nik) echo '<h1>' . $avatar . $comusers_nik . '</h1>';
			else echo '<h1>' . $avatar . tf('Комментатор'). ' ' . $comusers_id . '</h1>';
		
		if ($comusers_activate_string != $comusers_activate_key) // нет активации
			echo '<p><span style="color: red;" class="comusers-no-activate">'. tf('Активация не завершена.'). '</span> <a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/edit">'. tf('Завершить'). '</a></p>';
		
		// выводим все данные
		if ($comusers_date_registr) echo '<p><strong>'. tf('Дата регистрации'). ':</strong> ' . $comusers_date_registr . '</p>';
		
		if ($comusers_nik) echo '<p><strong>'. tf('Ник'). ':</strong> ' . $comusers_nik . '</p>';
		
		if ($comusers_count_comments) echo '<p><strong>'. tf('Комментариев'). ':</strong> ' . $comusers_count_comments . '</p>';
		
		if ($comusers_url) echo '<p><strong>'. tf('Сайт'). ':</strong> <a rel="nofollow" href="' . $comusers_url . '">' . $comusers_url . '</a></p>';
		
		if ($comusers_icq) echo '<p><strong>'. tf('ICQ'). ':</strong> ' . $comusers_icq . '</p>';
		
		if ($comusers_msn) echo '<p><strong>'. tf('Twitter'). ':</strong> <a rel="nofollow" href="http://twitter.com/' . $comusers_msn . '">@' . $comusers_msn . '</a></p>';
		
		if ($comusers_jaber) echo '<p><strong>'. tf('Jabber'). ':</strong> ' . $comusers_jaber . '</p>';
		
		if ($comusers_skype) echo '<p><strong>'. tf('Skype'). ':</strong> ' . $comusers_skype . '</p>';
		
		if ($comusers_date_birth and $comusers_date_birth!='1970-01-01 00:00:00' and $comusers_date_birth!='0000-00-00 00:00:00'   ) 
				echo '<p><strong>'. tf('Дата рождения'). ':</strong> ' . $comusers_date_birth . '</p>';
		
		if ($comusers_description) 
		{
			$comusers_description = strip_tags($comusers_description);
			$comusers_description = str_replace("\n", '<br>', $comusers_description);
			$comusers_description = str_replace('<br><br>', '<br>', $comusers_description);
			
			echo '<p><strong>'. tf('О себе'). ':</strong> ' . $comusers_description . '</p>';
		}
		
		if ($comusers_admin_note) echo '<p><strong>'. tf('Примечание админа'). ':</strong> ' . $comusers_admin_note . '</p>';
		
		
		if (getinfo('comusers_id') == $comusers_id )
			echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/edit">'. tf('Редактировать персональные данные'). '</a></p>';
		
		if ($comments) // есть комментарии
		{
			echo '<br><h2>'. tf('Комментарии'). '</h2><ul>';
			
			foreach ($comments as $comment)
			{
				//if ($comment['comments_approved']) // только отмодерированные
				//{
					echo '<li><span><a href="' . getinfo('siteurl') . 'page/' . mso_slug($comment['page_slug']) . '#comment-' . $comment['comments_id'] . '" name="comment-' . $comment['comments_id'] . '">' . $comment['page_title'] . '</a>';
					// echo ' | ' . $comments_url;
					echo '</span><br>' . $comment['comments_date'];
					echo '</span><br>' . $comment['comments_content'];
					echo '</li>';
				//}
			}
			
			echo '</ul>';
		}
		
	} // mso_page_foreach
	
	if ($f = mso_page_foreach('users-posle')) require($f);
}
else
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, пользователь с указанным номером не найден.') . '</p>';
		echo mso_hook('page_404');
	}
}

echo NR . '</div><!-- class="type type_users" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>