<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if ($fn = mso_page_foreach('comments-head-meta'))
	require $fn;
else
	mso_head_meta('title', tf('Последние комментарии') . '. ' . getinfo('title')); //  meta title страницы

if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-comments"><div class="mso-page-only"><div class="mso-page-content mso-type-comments-content">';

if ($fn = mso_page_foreach('comments-do')) {
	require $fn; // подключаем кастомный вывод
} else {
	echo '<h1 class="comments">' . tf('Последние комментарии') . '</h1>';
	echo '<p class="info"><a href="' . getinfo('siteurl') . 'comments/feed">' . tf('Подписаться по RSS') . '</a>';
	echo '<br><a href="' . getinfo('siteurl') . 'users">' . tf('Список комментаторов') . '</a></p>';
}

if (function_exists('last_comments_widget_custom')) {
	if ($fn = mso_page_foreach('comments-do-last-comments-widget')) {
		require $fn; // подключаем кастомный вывод
	} else {
		echo '<div class="comments">';
		echo last_comments_widget_custom(array('count' => 40), '999');
		echo '</div>';
	}
} else {
	// нет функции last_comments_widget_custom - выводим комменты как обычно

	require_once getinfo('common_dir') . 'comments.php'; // функции комментариев

	// получаем список комментариев текущей страницы
	$comments = mso_get_comments(false, ['limit' => mso_get_option('comments_count', 'templates', '10'), 'order' => 'desc']);

	echo '<div class="comments">';

	if ($comments) {
		echo '<ul>';

		foreach ($comments as $comment) {
			if ($fn = mso_page_foreach('comments')) {
				require $fn; // подключаем кастомный вывод
				continue; // следующая итерация
			}

			extract($comment);

			echo '<li><span><a href="' . getinfo('siteurl') . 'page/' . mso_slug($page_slug) . '#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $page_title . '</a>';
			echo ' | ' . $comments_url;
			echo '</span><br><span>' . $comments_date;
			echo '</span><br>' . $comments_content;
			echo '</li>';
		}

		echo '</ul>';
	}

	echo '</div>';
}


echo '</div></div></div><!-- class="mso-type-comments" -->';

if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

# end of file
