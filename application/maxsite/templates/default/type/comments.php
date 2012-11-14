<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


// возможно включен плагин last_comments - тогда выводим все через него
if ( function_exists('last_comments_widget_custom') )
{
	
	if ($f = mso_page_foreach('comments-head-meta')) require($f);
	else
	{ 
		mso_head_meta('title', tf('Последние комментарии') . '. ' . getinfo('title') ); //  meta title страницы
	}
	
	require(getinfo('template_dir') . 'main-start.php');
	echo NR . '<div class="type type_comments">' . NR;
	
	if ($f = mso_page_foreach('comments-do-last-comments-widget')) require($f); // подключаем кастомный вывод
	else
	{
		echo '<h1 class="comments">'. tf('Последние комментарии') .'</h1>';
		echo '<p class="info"><a href="' . getinfo('siteurl') . 'comments/feed">'. tf('Подписаться по RSS') .'</a>';
		echo '<br><a href="' . getinfo('siteurl') . 'users">'. tf('Список комментаторов') .'</a></p>';
		
		echo '<div class="comments">';
		echo last_comments_widget_custom(array(
										'count'=> 40,
										), '999');
		echo '</div>';
	}
}
else 
{ 
	// нет функции last_comments_widget_custom - выводим комменты как обычно
	
	require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

	// получаем список комментариев текущей страницы
	$comments = mso_get_comments(false, array('limit' => mso_get_option('comments_count', 'templates', '10'), 'order'=>'desc'));

	mso_head_meta('title', tf('Последние комментарии').' — ' . getinfo('title') ); //  meta title страницы

	require(getinfo('template_dir') . 'main-start.php');
	
	echo NR . '<div class="type type_comments">' . NR;

	if ($f = mso_page_foreach('comments-do')) require($f); // подключаем кастомный вывод
	else 
	{
		echo '<h1 class="comments">' . tf('Последние комментарии'). '</h1>';
		echo '<p class="info"><a href="' . getinfo('siteurl') . 'comments/feed">'. tf('Подписаться по RSS'). '</a>';
		echo '<br><a href="' . getinfo('siteurl') . 'users">'. tf('Список комментаторов'). '</a></p>';
	}
	
	echo '<div class="comments">';
	
	if ($comments) // есть страницы
	{ 	
		echo '<ul>';
		
		foreach ($comments as $comment)  // выводим в цикле
		{
			if ($f = mso_page_foreach('comments')) 
			{
				require($f); // подключаем кастомный вывод
				continue; // следующая итерация
			}

			extract($comment);

			echo '<li><span><a href="' . getinfo('siteurl') . 'page/' . mso_slug($page_slug) . '#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $page_title . '</a>';
			echo ' | ' . $comments_url;
			echo '</span><br>' . $comments_date;
			echo '</span><br>' . $comments_content;
			echo '</li>';
			
		//	pr($comment);
		}
		
		echo '</ul>';
	}

	echo '</div>';
}

echo NR . '</div><!-- class="type type_comments" -->' . NR;

require(getinfo('template_dir') . 'main-end.php'); 

?>